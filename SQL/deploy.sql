-- 22/04/25 --
ALTER TABLE `plant` ADD `misc` VARCHAR(5) NOT NULL DEFAULT '1' AFTER `locals`;

INSERT INTO `status` (`id`, `status`, `prefix`, `misc_id`, `deleted`) VALUES (NULL, 'Misc', 'M', '4', '0');

-- 23/04/25 --
ALTER TABLE `weight` ADD `seal_no` VARCHAR(50) NULL AFTER `invoice_no`;

-- 24/04/25 --
CREATE TABLE `Weight_Container` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `transaction_status` varchar(100) NOT NULL,
  `weight_type` varchar(100) NOT NULL,
  `customer_type` varchar(100) DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `lorry_plate_no1` varchar(100) DEFAULT NULL,
  `lorry_plate_no2` varchar(100) DEFAULT NULL,
  `supplier_weight` varchar(100) DEFAULT NULL,
  `order_weight` varchar(100) DEFAULT NULL,
  `plant_code` varchar(50) DEFAULT NULL,
  `plant_name` varchar(50) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `agent_code` varchar(50) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(50) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(50) DEFAULT NULL,
  `product_description` varchar(150) DEFAULT NULL,
  `ex_del` varchar(5) DEFAULT 'EX',
  `raw_mat_code` varchar(50) DEFAULT NULL,
  `raw_mat_name` varchar(100) DEFAULT NULL,
  `container_no` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `seal_no` varchar(50) DEFAULT NULL,
  `purchase_order` varchar(50) DEFAULT NULL,
  `delivery_no` varchar(50) DEFAULT NULL,
  `transporter_code` varchar(50) DEFAULT NULL,
  `transporter` varchar(50) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `gross_weight1` varchar(100) NOT NULL,
  `gross_weight1_date` datetime NOT NULL,
  `tare_weight1` varchar(100) DEFAULT NULL,
  `tare_weight1_date` datetime DEFAULT NULL,
  `nett_weight1` varchar(100) NOT NULL,
  `gross_weight2` varchar(100) DEFAULT NULL,
  `gross_weight2_date` datetime DEFAULT NULL,
  `tare_weight2` varchar(100) DEFAULT NULL,
  `tare_weight2_date` datetime DEFAULT NULL,
  `nett_weight2` varchar(100) DEFAULT NULL,
  `reduce_weight` varchar(100) NOT NULL,
  `final_weight` varchar(150) DEFAULT NULL,
  `weight_different` varchar(100) DEFAULT NULL,
  `is_complete` varchar(100) NOT NULL DEFAULT 'N',
  `is_cancel` varchar(100) NOT NULL DEFAULT 'N',
  `is_approved` varchar(3) NOT NULL DEFAULT 'Y',
  `manual_weight` varchar(100) NOT NULL,
  `indicator_id` varchar(100) NOT NULL,
  `weighbridge_id` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` varchar(50) NOT NULL,
  `indicator_id_2` varchar(50) DEFAULT NULL,
  `unit_price` varchar(10) DEFAULT NULL,
  `sub_total` varchar(10) NOT NULL DEFAULT '0.00',
  `sst` varchar(10) NOT NULL DEFAULT '0.00',
  `total_price` varchar(10) NOT NULL DEFAULT '0.00',
  `load_drum` varchar(4) DEFAULT NULL,
  `no_of_drum` int(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `approved_by` int(5) DEFAULT NULL,
  `approved_reason` text DEFAULT NULL,
  `cancelled_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Weight_Container` ADD PRIMARY KEY (`id`);

ALTER TABLE `Weight_Container` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `TRG_UPD_WEIGHT_CONTAINER` BEFORE UPDATE ON `Weight_Container` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Container_Log table
    INSERT INTO Weight_Container_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, 
        NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

CREATE TABLE `Weight_Container_Log` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `transaction_status` varchar(100) DEFAULT NULL,
  `weight_type` varchar(100) DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `lorry_plate_no1` varchar(100) DEFAULT NULL,
  `lorry_plate_no2` varchar(100) DEFAULT NULL,
  `supplier_weight` varchar(100) DEFAULT NULL,
  `order_weight` varchar(100) DEFAULT NULL,
  `plant_code` varchar(50) DEFAULT NULL,
  `plant_name` varchar(50) DEFAULT NULL,
  `site_code` varchar(50) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `agent_code` varchar(50) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(50) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(50) DEFAULT NULL,
  `product_description` varchar(150) DEFAULT NULL,
  `ex_del` varchar(5) DEFAULT NULL,
  `raw_mat_code` varchar(50) DEFAULT NULL,
  `raw_mat_name` varchar(100) DEFAULT NULL,
  `container_no` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `purchase_order` varchar(50) DEFAULT NULL,
  `delivery_no` varchar(50) DEFAULT NULL,
  `transporter_code` varchar(50) DEFAULT NULL,
  `transporter` varchar(50) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `gross_weight1` varchar(100) DEFAULT NULL,
  `gross_weight1_date` datetime DEFAULT NULL,
  `tare_weight1` varchar(100) DEFAULT NULL,
  `tare_weight1_date` datetime DEFAULT NULL,
  `nett_weight1` varchar(100) DEFAULT NULL,
  `gross_weight2` varchar(100) DEFAULT NULL,
  `gross_weight2_date` datetime DEFAULT NULL,
  `tare_weight2` varchar(100) DEFAULT NULL,
  `tare_weight2_date` datetime DEFAULT NULL,
  `nett_weight2` varchar(100) DEFAULT NULL,
  `reduce_weight` varchar(100) DEFAULT NULL,
  `final_weight` varchar(150) DEFAULT NULL,
  `weight_different` varchar(100) DEFAULT NULL,
  `is_complete` varchar(100) DEFAULT NULL,
  `is_cancel` varchar(100) DEFAULT NULL,
  `is_approved` varchar(3) DEFAULT NULL,
  `manual_weight` varchar(100) DEFAULT NULL,
  `indicator_id` varchar(100) DEFAULT NULL,
  `weighbridge_id` varchar(100) DEFAULT NULL,
  `indicator_id_2` varchar(50) DEFAULT NULL,
  `unit_price` varchar(10) DEFAULT NULL,
  `sub_total` varchar(10) DEFAULT NULL,
  `sst` varchar(10) DEFAULT NULL,
  `total_price` varchar(10) DEFAULT NULL,
  `load_drum` varchar(4) DEFAULT NULL,
  `no_of_drum` int(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `approved_by` int(5) DEFAULT NULL,
  `approved_reason` text DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Weight_Container_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Weight_Container_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 03/05/2025 --
UPDATE status SET `prefix` = 'D' WHERE status = 'Sales';

UPDATE status SET `prefix` = 'R' WHERE status = 'Purchase';

UPDATE status SET `prefix` = 'I' WHERE status = 'Local';

UPDATE status SET `prefix` = 'M' WHERE status = 'Misc';

ALTER TABLE `Weight` ADD `container_no2` VARCHAR(50) NULL AFTER `seal_no`, ADD `seal_no2` VARCHAR(50) NULL AFTER `container_no2`;

ALTER TABLE `Weight_Container` ADD `container_no2` VARCHAR(50) NULL AFTER `seal_no`, ADD `seal_no2` VARCHAR(50) NULL AFTER `container_no2`;

ALTER TABLE `Weight` ADD `gross_weight_by1` VARCHAR(50) NULL AFTER `gross_weight1_date`;

ALTER TABLE `Weight` ADD `tare_weight_by1` VARCHAR(50) NULL AFTER `tare_weight1_date`;

ALTER TABLE `Weight` ADD `gross_weight_by2` VARCHAR(50) NULL AFTER `gross_weight2_date`;

ALTER TABLE `Weight` ADD `tare_weight_by2` VARCHAR(50) NULL AFTER `tare_weight2_date`;

ALTER TABLE `Weight_Container` ADD `gross_weight_by1` VARCHAR(50) NULL AFTER `gross_weight1_date`;

ALTER TABLE `Weight_Container` ADD `tare_weight_by1` VARCHAR(50) NULL AFTER `tare_weight1_date`;

ALTER TABLE `Weight_Container` ADD `gross_weight_by2` VARCHAR(50) NULL AFTER `gross_weight2_date`;

ALTER TABLE `Weight_Container` ADD `tare_weight_by2` VARCHAR(50) NULL AFTER `tare_weight2_date`;

-- 14/05/2025 --
ALTER TABLE `Customer` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Customer` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Customer_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Customer_Log` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Supplier` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Supplier` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Supplier_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Supplier_Log` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Transporter` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Transporter` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Transporter_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Transporter_Log` ADD `contact_name` VARCHAR(100) NULL AFTER `fax_no`, ADD `ic_no` VARCHAR(100) NULL AFTER `contact_name`, ADD `tin_no` VARCHAR(100) NULL AFTER `ic_no`;

ALTER TABLE `Company` CHANGE `created_date` `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `Company` CHANGE `modified_date` `modified_date` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `Company` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Company` ADD `tin_no` VARCHAR(100) NULL AFTER `fax_no`, ADD `mobile_no` VARCHAR(50) NULL AFTER `tin_no`;

ALTER TABLE `Company_Log` DROP COLUMN `created_date`;

ALTER TABLE `Company_Log` DROP COLUMN `created_by`;

ALTER TABLE `Company_Log` DROP COLUMN `modified_date`;

ALTER TABLE `Company_Log` DROP COLUMN `modified_by`;

ALTER TABLE `Company_Log` ADD `new_reg_no` VARCHAR(100) NULL AFTER `company_reg_no`;

ALTER TABLE `Company_Log` ADD `tin_no` VARCHAR(100) NULL AFTER `fax_no`, ADD `mobile_no` VARCHAR(50) NULL AFTER `tin_no`;