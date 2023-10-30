alter table it_po_items add ttk_qty double after cost_price;

alter table it_po_items add ttk_uom varchar(10) after pack_type;



CREATE TABLE `it_vendor_plant_mapping` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `master_dealer_id` bigint(20) DEFAULT NULL,
  `vendor_number` varchar(256) DEFAULT NULL,
  `plant` varchar(256) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);


CREATE TABLE `it_ship_to_party` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `master_dealer_id` bigint(20) DEFAULT NULL,
  `site` varchar(256) DEFAULT NULL,
  `ship_to_party` varchar(256) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);


CREATE TABLE `it_ean_sku_mapping` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ean` bigint(50) DEFAULT NULL,
  `sku` bigint(50) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE (`ean`,`sku`)
);

alter table it_po_items add po_hsn varchar(10) after po_eancode;