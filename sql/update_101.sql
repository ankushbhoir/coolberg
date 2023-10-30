update  it_permission set pagecode = 'shiptoparty', pagename = 'Ship To Party', pageuri = 'ship/to/party' where id = 36;
update it_ship_to_party set createtime = updatetime  ;

update  it_permission set pagecode = 'skumaster', pagename = 'SKU Master', pageuri = 'sku/master' where id = 38;
update it_ean_sku_mapping set createtime = updatetime;

update  it_permission set pagecode = 'vendormaster', pagename = 'Vendor Master', pageuri = 'vendor/master' where id = 37;
update it_vendor_plant_mapping set createtime = updatetime ;
	