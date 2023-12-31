<?php
require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";
require_once("lib/sms/clsSMSHelper.php");
require_once("lib/codes/clsVouchers.php");
//require_once("lib/messages/clsFanMessage.php");

class clsCodes extends dbobject {

	public function getCodeInfo($code) {
		$code = $this->safe($code);
		$codeInfo = $this->fetchObject("select * from it_codes where code=$code");
		return $codeInfo;
	}

	public function getCodeInfoById($codeid) {
		$codeInfo = $this->fetchObject("select * from it_codes where id=$codeid");
		return $codeInfo;
	}

	public function insert($code, $act_code_rowid, $owner, $creator, $incomingid) {
		$code = $this->safe($code);

		$query = "insert into it_codes set code=$code";
		if ($owner) { $query .= ", owner=$owner"; }
		if ($creator) { $query .= ", creator=$creator"; }
		if ($incomingid) { $query .= ", incomingid=$incomingid"; }
		$codeid = $this->execInsert($query);

		$query = "update it_actcodes set codeid=$codeid";
		if ($incomingid) { $query .= ", incomingid=$incomingid"; }
		$query .= " where id=$act_code_rowid";
		$this->execUpdate($query);
	}

	public function getFanInfo($codeid, $userid) {
		$obj = $this->fetchObject("select * from it_fans where codeid=$codeid and userid=$userid");
		return $obj;
	}

	public function fanExists($codeid, $userid) {
		$fanInfo = $this->getFanInfo($codeid, $userid);
		if ($fanInfo && $fanInfo->inactive == 0) { return true; }
		else { return false; }
	}

	public function addFan($codeid, $userid, $incomingid) {
		$fanInfo = $this->getFanInfo($codeid, $userid);
		if ($fanInfo) {
			$this->execUpdate("update it_fans set inactive=0, isrejoin=1 where id=$fanInfo->id");
		} else {
			$this->execInsert("insert into it_fans set codeid=$codeid, userid=$userid, incomingid=$incomingid");
		}
	}

	public function addFanProcMsg($codeInfo, $userInfo, $incomingid) {
		$fanInfo = $this->getFanInfo($codeInfo->id, $userInfo->id);
		if ($fanInfo) { // fan exists - no need to send sms
			if ($fanInfo->inactive == 1) {
				$this->execUpdate("update it_fans set inactive=0, isrejoin=1 where id=$fanInfo->id");
			}
			return false;
		}

		// first time fan - send sms
		$query = "insert into it_fans set codeid=$codeInfo->id, userid=$userInfo->id, incomingid=$incomingid";
		$this->execInsert($query);
		$smsMessage = false;
		if ($codeInfo->signup_offerid) {
			$signupOffer = $this->getOffer($codeInfo->signup_offerid);
			if ($signupOffer) {
				$offer_text = $signupOffer->offer_text;
				$clsVouchers = new clsVouchers();
				$voucher = $clsVouchers->makeVoucher($codeInfo->id, $codeInfo->signup_offerid, $userInfo->id, $offer_text);
				$smsMessage = clsFanMessage::addFanSignupOffer($codeInfo->store_name, $userInfo->intouchno, $offer_text, $voucher->vcode)->getMessage($userInfo->locale);
			}
		} else
		if ($codeInfo->signupmsg) {
			$smsMessage = $codeInfo->signupmsg;
		} else {
			$smsMessage = clsFanMessage::addFanNoSignupOffer($codeInfo->store_name, $userInfo->intouchno)->getMessage($userInfo->locale);
		}

		return $smsMessage;
	}

	public function addFanProc($codeInfo, $userInfo, $incomingid) {

		$smsMessage = $this->addFanProcMsg($codeInfo, $userInfo, $incomingid);
		if ($smsMessage) {
			// send signup message to customer
			$smsHelper = new clsSMSHelper();
			$retval = $smsHelper->sendOne($userInfo->phoneno, $smsMessage);
			$clsLogger = new clsLogger();
			$clsLogger->logInfo($smsMessage."|".$retval, $incomingid);
			sleep(1); // smsgupshup problem with sending successive sms requests
		}
	}

	public function removeFan($codeid, $userid) {
		$fanInfo = $this->getFanInfo($codeid, $userid);
		if ($fanInfo) {
			$this->execUpdate("update it_fans set inactive=1, isrejoin=0 where id=$fanInfo->id");
		}
	}

	public function getActivationCode($act_code) {
		$act_code = $this->safe($act_code);
		$obj = $this->fetchObject("select * from it_actcodes where act_code=$act_code");
		return $obj;
	}

	public function newActivationCode() {
		do {
			$act_code=mt_rand(10000000,1000000000);
			$query = "select * from it_actcodes where act_code='$act_code'";
			$obj = $this->fetchObject($query);
		} while ($obj != null);
		$this->execInsert("insert into it_actcodes set act_code='$act_code'");
		return $act_code;
	}

	public function getFanSummary($codeid) {
		// default numbers to 0's
		$summary = array("totalFans" => 0, "activeFans" => 0);
		$query = "select inactive, count(*) total from it_fans where codeid = $codeid group by inactive";
		$arr = $this->fetchObjectArray($query);
		$total = 0;
		foreach ($arr as $obj) {
			if ($obj->inactive == 0) { 
				$summary["activeFans"] = $obj->total;
			}
			$total += $obj->total;
		}
		$summary["totalFans"] = $total;
		return (object) $summary; // make anonymous object out of assoc array
	}

	public function getMyStores($userid) {
		$query = "select c.* from it_fans f, it_codes c where f.userid=$userid and f.codeid = c.id";
		$arr = $this->fetchObjectArray($query);
		return $arr;
	}

	public function updateOffer($codeid, $offer) {
		$offer = $this->safe($offer);
		$this->execUpdate("update it_codes set signupmsg=$offer where id=$codeid");
	}

	public function isAuthentic($storecode, $password) {
		$storecode = $this->safe($storecode);
//		$password = $this->safe(md5($password));
//                $password2 = password_hash($password, PASSWORD_BCRYPT);
//                password_verify($password, $password2);
//                print_r(password_verify($password, $password2));
//                print_r($password2); exit();
                $str=NULL;
//		$query = "select * from it_users where username=$storecode and password=$password and inactive = 0 ";
		$query = "select * from it_users where username=$storecode and inactive = 0 ";
//		return $this->fetchObject($query);
		$obj = $this->fetchObject($query);
                if(password_verify($password, $obj->password)){
                
                $dbLogic = new DBLogic();
     		$obj1 = $dbLogic->getPassPolicyDays();
                if(isset($obj) && !empty($obj) && isset($obj1) && !empty($obj1)){
                   if(trim($obj->password_updated_at)!=""){
                    $current_pwd_set_date = $obj->password_updated_at;
                    $pass_policy_days = '+'.$obj1->pass_policy_days.' days';

                    $pwd_exp_date = date('Y-m-d',strtotime($current_pwd_set_date.$pass_policy_days));
                    $today = date('Y-m-d');
//                    echo $pwd_exp_date."<br>";
//                    echo $today."<br>";
                    if(strtotime($today) <= strtotime($pwd_exp_date)){
                        $str = $this->fetchObject($query);
                    //    $str->is_valid=1;
                     //   $is_valid=1;
                    }
                   }else{
                       $today = $this->safe(date('Y-m-d'));
                       $this->execUpdate("update it_users set password_updated_at=$today where id=$obj->id");
                       $str = $obj;
                   //    $str->is_valid=1;
                     //  $is_valid=1;
                   }
                }
                }
               // $query = "select * from it_users where username=$storecode and password=$password and is_active = 1 ";
                print_r($str);
		return $str;
	}

	public function getUserById($userid) {
		$userid = $this->safe($userid);
		$query = "select * from it_users where id=$userid";
		return $this->fetchObject($query);
	}

	public function alreadyCheckedin($userInfo, $codeInfo, $repeatHours) {
		$query = "select * from it_checkins where userid=$userInfo->id and storeid=$codeInfo->id and TIMESTAMPDIFF(HOUR,checkintime,now()) < $repeatHours";
		return $this->fetchObject($query);
	}

	public function checkin($userInfo, $codeInfo, $server_name) {
		$server_name = $this->safe($server_name);
		return $this->execInsert("insert into it_checkins set userid=$userInfo->id, storeid=$codeInfo->id, server_name=$server_name");
	}

	public function getOffers($codeid) {
		$query = "select * from it_storeoffers where storeid=$codeid and isactive=1 and (end_date is null or end_date > now())";
		return $this->fetchObjectArray($query);
	}

	public function getOffer($offerid) {
		return $this->fetchObject("select * from it_storeoffers where id=$offerid");
	}

	public function getOfferRedemptionDate($userInfo, $codeInfo, $offerid) {
		$query = "select redeemtime from it_offers_redeemed where userid=$userInfo->id and storeid=$codeInfo->id and offerid=$offerid";
		$obj = $this->fetchObject($query);
		if ($obj) { return $obj->redeemtime; }
		else { return false; }
	}

	public function redeemOffer($userInfo, $codeInfo, $offerid) {
		$query = "insert into it_offers_redeemed set userid=$userInfo->id, storeid=$codeInfo->id, offerid=$offerid";
		$this->execInsert($query);
	}

	public function getTotalPoints($userid, $codeid) {
		$query = "select sum(points_earned) as total from it_points where userid=$userid and storeid=$codeid";
		$obj = $this->fetchObject($query);
		$total = 0;
		if ($obj) { $total = $obj->total; }
		return $total;
	}

	public function addPoints($userInfo, $codeInfo, $orderid, $billno, $billamt) {
		$billno = $this->safe($billno);
		$pointsAdderClass = "cls_".strtolower($codeInfo->code)."_pointsAdder";
		require_once("pointsadder/$pointsAdderClass.php");
		$adder = new $pointsAdderClass;
		$pointsArr = $adder->compute($userInfo->id, $codeInfo->id, $billamt);
		$query = "insert into it_points set userid=$userInfo->id, storeid=$codeInfo->id, orderid=$orderid, bill_amount=$billamt, points_earned=$pointsArr[0], bill_no=$billno";
		$purchaseId = $this->execInsert($query);
		return array($purchaseId, $pointsArr[0], $pointsArr[1]);
	}

	public function redeemPoints($storeid, $userid, $numpoints, $voucherid) {
		$points = 0-$numpoints;
		$query = "insert into it_points set userid=$userid, storeid=$storeid, voucherid=$voucherid, bill_amount=0, points_earned=$points";
		$this->execInsert($query);
	}

	public function addPurchases($purchaseId, $userInfo, $codeInfo, $products) {
		if (!$products || count($products) == 0) { return; }
		foreach ($products as $product) {
			$query = "insert into it_purchases set purchaseid=$purchaseId, storeid=$codeInfo->id, userid=$userInfo->id, productid=$product[0]";
			$this->execInsert($query);
		}
	}

	public function getPointsInfo($pointsid) {
		$pointsInfo = $this->fetchObject("select * from it_points where id=$pointsid");
		return $pointsInfo;
	}

	public function updateReview($pointsid, $reviewtext) {
		$reviewtext = $this->safe($reviewtext);
		$this->execUpdate("update it_points set reviewtext=$reviewtext where id=$pointsid");
	}

	public function getBillPatterns($storeid) {
		return $this->fetchObjectArray("select * from it_billpatterns where storeid=$storeid");
	}

	public function updateSyncInfo($codeid, $lastinfo) {
		$lastinfo = $this->safe($lastinfo);
		$this->execUpdate("update it_codes set order_format=$lastinfo where id=$codeid");
	}

	public function pointsIssued($orderid) {
		return $this->fetchObject("select 1 from it_points where orderid=$orderid");
	}
}

?>
