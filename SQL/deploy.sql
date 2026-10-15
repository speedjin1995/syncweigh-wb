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

-- 14/06/2025 --
ALTER TABLE `Vehicle` ADD `supplier_code` VARCHAR(50) NOT NULL AFTER `customer_name`, ADD `supplier_name` VARCHAR(100) NOT NULL AFTER `supplier_code`;

ALTER TABLE `Vehicle_Log` ADD `supplier_code` VARCHAR(50) NOT NULL AFTER `customer_name`, ADD `supplier_name` VARCHAR(100) NOT NULL AFTER `supplier_code`;

-- 15/06/2025 --
ALTER TABLE `Weight_Container` ADD `lorry_no2_weight` VARCHAR(100) NULL AFTER `nett_weight1`, ADD `empty_container2_weight` VARCHAR(100) NULL AFTER `lorry_no2_weight`;

ALTER TABLE `Weight_Container_Log` ADD `lorry_no2_weight` VARCHAR(100) NULL AFTER `nett_weight1`, ADD `empty_container2_weight` VARCHAR(100) NULL AFTER `lorry_no2_weight`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT_CONTAINER` BEFORE UPDATE ON `Weight_Container` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Container_Log table
    INSERT INTO Weight_Container_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, 
        NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight` ADD `lorry_no2_weight` VARCHAR(100) NULL AFTER `nett_weight1`, ADD `empty_container2_weight` VARCHAR(100) NULL AFTER `lorry_no2_weight`;

ALTER TABLE `Weight_Log` ADD `lorry_no2_weight` VARCHAR(100) NULL AFTER `nett_weight1`, ADD `empty_container2_weight` VARCHAR(100) NULL AFTER `lorry_no2_weight`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Vehicle` CHANGE `supplier_code` `supplier_code` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Vehicle` CHANGE `supplier_name` `supplier_name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Vehicle_Log` CHANGE `supplier_code` `supplier_code` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Vehicle_Log` CHANGE `supplier_name` `supplier_name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Weight` CHANGE `gross_weight1` `gross_weight1` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Weight` CHANGE `gross_weight1_date` `gross_weight1_date` DATETIME NULL;

ALTER TABLE `Weight_Log` CHANGE `gross_weight1` `gross_weight1` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Weight_Log` CHANGE `gross_weight1_date` `gross_weight1_date` DATETIME NULL;

ALTER TABLE `Weight` ADD `replacement_container` VARCHAR(100) NULL AFTER `empty_container2_weight`;

ALTER TABLE `Weight_Log` ADD `replacement_container` VARCHAR(100) NULL AFTER `empty_container2_weight`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, 
        NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight_Container` ADD `replacement_container` VARCHAR(100) NULL AFTER `empty_container2_weight`;

ALTER TABLE `Weight_Container_Log` ADD `replacement_container` VARCHAR(100) NULL AFTER `empty_container2_weight`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT_CONTAINER` BEFORE UPDATE ON `Weight_Container` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Container_Log table
    INSERT INTO Weight_Container_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container,
        NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, 
        NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 22/06/2025 --
CREATE TABLE `weight_product` (
  `id` int(11) NOT NULL,
  `weight_id` int(11) DEFAULT NULL,
  `product` varchar(100) DEFAULT NULL,
  `product_packing` varchar(100) DEFAULT NULL,
  `product_gross` varchar(100) DEFAULT NULL,
  `product_tare` varchar(100) DEFAULT NULL,
  `product_nett` varchar(100) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Weight_Product` ADD PRIMARY KEY (`id`);

ALTER TABLE `Weight_Product` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 25/06/2025 --
ALTER TABLE `Plant_Log` ADD `misc` VARCHAR(5) NULL AFTER `locals`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PLANT` AFTER INSERT ON `Plant` FOR EACH ROW INSERT INTO Plant_Log (
    plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, misc, do_no, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.misc, NEW.do_no, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PLANT` BEFORE UPDATE ON `Plant` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Plant_Log table
    INSERT INTO Plant_Log (
        plant_id, plant_code, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, sales, purchase, locals, misc, do_no, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.plant_code, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.sales, NEW.purchase, NEW.locals, NEW.misc, NEW.do_no, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 27/06/2025 --
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_CUSTOMER` AFTER INSERT ON `Customer` FOR EACH ROW 
INSERT INTO Customer_Log (
    customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_CUSTOMER` BEFORE UPDATE ON `Customer` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Customer_Log table
    INSERT INTO Customer_Log (
        customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_DESTINATION` AFTER INSERT ON `Destination` FOR EACH ROW 
INSERT INTO Destination_Log (
    destination_id, destination_code, name, description, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.destination_code, NEW.name, NEW.description, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_DESTINATION` BEFORE UPDATE ON `Destination` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Destination_Log table
    INSERT INTO Destination_Log (
        destination_id, destination_code, name, description, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.destination_code, NEW.name, NEW.description, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PRODUCT` AFTER INSERT ON `Product` FOR EACH ROW 
INSERT INTO Product_Log (
    product_id, product_code, name, price, description, variance, high, low, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.product_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PRODUCT` BEFORE UPDATE ON `Product` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Product_Log table
    INSERT INTO Product_Log (
    product_id, product_code, name, price, description, variance, high, low, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.product_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_RAW_MAT` AFTER INSERT ON `Raw_Mat` FOR EACH ROW 
INSERT INTO Raw_Mat_Log (
    raw_mat_id, raw_mat_code, name, price, description, variance, high, low, type, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.raw_mat_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, NEW.type, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_RAW_MAT` BEFORE UPDATE ON `Raw_Mat` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Raw_Mat_Log table
    INSERT INTO Raw_Mat_Log (
        raw_mat_id, raw_mat_code, name, price, description, variance, high, low, type, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.raw_mat_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, NEW.type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SUPPLIER` AFTER INSERT ON `Supplier` FOR EACH ROW 
INSERT INTO Supplier_Log (
    supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_SUPPLIER` BEFORE UPDATE ON `Supplier` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Supplier_Log table
    INSERT INTO Supplier_Log (
        supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_VEH` AFTER INSERT ON `Vehicle` FOR EACH ROW 
INSERT INTO Vehicle_Log (
    vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, supplier_code, supplier_name, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.ex_del, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_VEH` BEFORE UPDATE ON `Vehicle` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Vehicle_Log table
    INSERT INTO Vehicle_Log (
        vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, ex_del, customer_code, customer_name, supplier_code, supplier_name, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.ex_del, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_TRANSPORTER` AFTER INSERT ON `Transporter` FOR EACH ROW 
INSERT INTO Transporter_Log (
    transporter_id, transporter_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.transporter_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_TRANSPORTER` BEFORE UPDATE ON `Transporter` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Transporter_Log table
    INSERT INTO Transporter_Log (
        transporter_id, transporter_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.transporter_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_USER` AFTER INSERT ON `Users` FOR EACH ROW 
INSERT INTO Users_Log (
    user_id, employee_code, username, name, useremail, password, plant_id, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.password, NEW.plant_id, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_USER` BEFORE UPDATE ON `Users` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Users_Log table
    INSERT INTO Users_Log (
        user_id, employee_code, username, name, useremail, password, plant_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.password, NEW.plant_id, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 19/10/25--
ALTER TABLE `Weight` ADD `weight_different_perc` VARCHAR(50) NULL AFTER `weight_different`;

ALTER TABLE `Weight_Log` ADD `weight_different_perc` VARCHAR(50) NULL AFTER `weight_different`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight_Container` ADD `weight_different_perc` VARCHAR(50) NULL AFTER `reduce_weight`;

ALTER TABLE `Weight_Container_Log` ADD `weight_different_perc` VARCHAR(50) NULL AFTER `reduce_weight`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT_CONTAINER` BEFORE UPDATE ON `Weight_Container` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Container_Log table
    INSERT INTO Weight_Container_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container,
        NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, 
        NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 20/05/2026 --
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('clear_all_code', 'Clear All', '清除全部', 'Kosongkan Semua', 'அனைத்தையும் அழி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('cancelled_code', 'Cancelled', '已取消', 'Dibatalkan', 'ரத்து செய்யப்பட்டது');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('address_line_code', 'Address Line', '地址行', 'Baris Alamat', 'முகவரி வரி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('company_reg_no_code', 'Company Reg No.', '公司注册号', 'No. Pendaftaran Syarikat', 'நிறுவன பதிவு எண்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('company_name_code', 'Company Name', '公司名称', 'Nama Syarikat', 'நிறுவன பெயர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('company_address_code', 'Company Address', '公司地址', 'Alamat Syarikat', 'நிறுவன முகவரி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('company_phone_code', 'Company Phone', '公司电话', 'Telefon Syarikat', 'நிறுவன தொலைபேசி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('company_fax_code', 'Company Fax', '公司传真', 'Faks Syarikat', 'நிறுவன தொலைநகல்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('update_code', 'Update', '更新', 'Kemaskini', 'புதுப்பி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('indicator_code', 'Indicator', '指示器', 'Penunjuk', 'காட்டி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('serial_port_code', 'Serial Port', '串行端口', 'Port Bersiri', 'தொடர் துறை');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('baud_rate_code', 'Baud Rate', '波特率', 'Kadar Baud', 'பாட் வீதம்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('data_bits_code', 'Data Bits', '数据位', 'Bit Data', 'தரவு பிட்கள்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('parity_code', 'Parity', '奇偶校验', 'Pariti', 'சமநிலை');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('stop_bits_code', 'Stop Bits', '停止位', 'Bit Henti', 'நிறுத்த பிட்கள்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('data_category_code', 'Data Category', '数据类别', 'Kategori Data', 'தரவு வகை');
UPDATE `message_resource` SET `en`='ID No', `zh`='身份证号码', `my`='No. Kad Pengenalan', `ne`='அடையாள எண்' WHERE `message_key_code`='ic_code';

-- 01/06/2026 --
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('error_log_code', 'Error Log', '错误日志', 'Log Ralat', 'பிழை பதிவு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('please_fill_in_the_field_code', 'Please fill in the field.', '请填写此字段。', 'Sila isi medan ini.', 'இந்த புலத்தை நிரப்பவும்.');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('message_code_code', 'Message Code', '消息代码', 'Kod Mesej', 'செய்தி குறியீடு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('pending_bin_code', 'Pending Bin', '待处理箱', 'Tong Menunggu', 'நிலுவையில் உள்ள பெட்டி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('new_empty_bin_code', 'New Empty Bin', '新空箱', 'Tong Kosong Baru', 'புதிய காலி பெட்டி');

DROP TABLE IF EXISTS `Product_RawMat`;
DROP TABLE IF EXISTS `Bitumen`;
DROP TABLE IF EXISTS `Agents`;
DROP TABLE IF EXISTS `Agents_Log`;
DROP TABLE IF EXISTS `Inventory`;
DROP TABLE IF EXISTS `Site`;
DROP TABLE IF EXISTS `Site_Log`;
DROP TABLE IF EXISTS `Unit`;
DROP TABLE IF EXISTS `Unit_Log`;
DROP TABLE IF EXISTS `Weight_Product`;

ALTER TABLE `Product` DROP `price`;
ALTER TABLE `Product_Log` DROP `price`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PRODUCT` AFTER INSERT ON `Product` FOR EACH ROW 
INSERT INTO Product_Log (
    product_id, product_code, name, description, variance, high, low, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.variance, NEW.high, NEW.low, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PRODUCT` BEFORE UPDATE ON `Product` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Product_Log table
    INSERT INTO Product_Log (
    product_id, product_code, name, description, variance, high, low, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.product_code, NEW.name, NEW.description, NEW.variance, NEW.high, NEW.low, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Raw_Mat` DROP `price`;
ALTER TABLE `Raw_Mat` DROP `type`;
ALTER TABLE `Raw_Mat_Log` DROP `price`;
ALTER TABLE `Raw_Mat_Log` DROP `type`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_RAW_MAT` AFTER INSERT ON `Raw_Mat` FOR EACH ROW 
INSERT INTO Raw_Mat_Log (
    raw_mat_id, raw_mat_code, name, description, variance, high, low, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.raw_mat_code, NEW.name, NEW.description, NEW.variance, NEW.high, NEW.low, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_RAW_MAT` BEFORE UPDATE ON `Raw_Mat` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Raw_Mat_Log table
    INSERT INTO Raw_Mat_Log (
        raw_mat_id, raw_mat_code, name, description, variance, high, low, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.raw_mat_code, NEW.name, NEW.description, NEW.variance, NEW.high, NEW.low, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Vehicle` DROP `ex_del`;
ALTER TABLE `Vehicle_Log` DROP `ex_del`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_VEH` AFTER INSERT ON `Vehicle` FOR EACH ROW 
INSERT INTO Vehicle_Log (
    vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_VEH` BEFORE UPDATE ON `Vehicle` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Vehicle_Log table
    INSERT INTO Vehicle_Log (
        vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight` DROP `agent_code`;
ALTER TABLE `Weight` DROP `agent_name`;
ALTER TABLE `Weight_Log` DROP `agent_code`;
ALTER TABLE `Weight_Log` DROP `agent_name`;
ALTER TABLE `Weight` DROP `site_code`;
ALTER TABLE `Weight` DROP `site_name`;
ALTER TABLE `Weight_Log` DROP `site_code`;
ALTER TABLE `Weight_Log` DROP `site_name`;
ALTER TABLE `Weight` DROP `ex_del`;
ALTER TABLE `Weight_Log` DROP `ex_del`;
ALTER TABLE `Weight` DROP `load_drum`;
ALTER TABLE `Weight_Log` DROP `load_drum`;
ALTER TABLE `Weight` DROP `no_of_drum`;
ALTER TABLE `Weight_Log` DROP `no_of_drum`;
ALTER TABLE `Weight` DROP `approved_by`;
ALTER TABLE `Weight_Log` DROP `approved_by`;
ALTER TABLE `Weight` DROP `approved_reason`;
ALTER TABLE `Weight_Log` DROP `approved_reason`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code, raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name,
        NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight_Container` DROP `agent_code`;
ALTER TABLE `Weight_Container` DROP `agent_name`;
ALTER TABLE `Weight_Container_Log` DROP `agent_code`;
ALTER TABLE `Weight_Container_Log` DROP `agent_name`;
ALTER TABLE `Weight_Container` DROP `site_code`;
ALTER TABLE `Weight_Container` DROP `site_name`;
ALTER TABLE `Weight_Container_Log` DROP `site_code`;
ALTER TABLE `Weight_Container_Log` DROP `site_name`;
ALTER TABLE `Weight_Container` DROP `ex_del`;
ALTER TABLE `Weight_Container_Log` DROP `ex_del`;
ALTER TABLE `Weight_Container` DROP `load_drum`;
ALTER TABLE `Weight_Container_Log` DROP `load_drum`;
ALTER TABLE `Weight_Container` DROP `no_of_drum`;
ALTER TABLE `Weight_Container_Log` DROP `no_of_drum`;
ALTER TABLE `Weight_Container` DROP `approved_by`;
ALTER TABLE `Weight_Container_Log` DROP `approved_by`;
ALTER TABLE `Weight_Container` DROP `approved_reason`;
ALTER TABLE `Weight_Container_Log` DROP `approved_reason`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW 
INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code, raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT_CONTAINER` BEFORE UPDATE ON `Weight_Container` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Container_Log table
    INSERT INTO Weight_Container_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name,
        NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 13/10/2026 --
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('locations_code', 'Locations', '位置', 'Lokasi', 'இடங்கள்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('location_code_code', 'Location Code', '位置代码', 'Kod Lokasi', 'இடத்தின் குறியீடு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('location_name_code', 'Location Name', '位置名称', 'Nama Lokasi', 'இடத்தின் பெயர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('weighing_count_code', 'Weighing Count', '称重次数', 'Kiraan Timbang', 'கூட்டல் எண்ணிக்கை');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('add_new_location_code', 'Add New Location', '添加新位置', 'Tambah Lokasi Baru', 'புதிய இடத்தைச் சேர்');

CREATE TABLE `Location` (
  `id` int(11) NOT NULL,
  `location_code` varchar(30) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `port_id` int(11) DEFAULT NULL,
  `weighing_count` int(11) NOT NULL DEFAULT 2,
  `plant_id` int(5) DEFAULT 1,
  `status` int(3) DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Location` ADD PRIMARY KEY (`id`);

ALTER TABLE `Location` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Location_Log` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `location_code` varchar(30) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `port_id` int(11) DEFAULT NULL,
  `weighing_count` int(11) NOT NULL DEFAULT 2,
  `plant_id` int(5) DEFAULT 1,
  `action_id` int(11) DEFAULT NULL,
  `action_by` varchar(50) DEFAULT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Location_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Location_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

DELIMITER $$
CREATE TRIGGER `TRG_INS_LOCATION` AFTER INSERT ON `Location` FOR EACH ROW INSERT INTO Location_Log (
    location_id, location_code, location_name, port_id, weighing_count, plant_id, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.location_code, NEW.location_name, NEW.port_id, NEW.weighing_count, NEW.plant_id, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `TRG_UPD_LOCATION` BEFORE UPDATE ON `Location` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Location_Log table
    INSERT INTO Location_Log (
        location_id, location_code, location_name, port_id, weighing_count, plant_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.location_code, NEW.location_name, NEW.port_id, NEW.weighing_count, NEW.plant_id, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Port_Log` MODIFY `id` int(11) NOT NULL;

ALTER TABLE `Port_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Port_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PORT` AFTER INSERT ON `Port` FOR EACH ROW INSERT INTO Port_Log (
    port_id, com_port, bits_per_second, data_bits, parity, stop_bits, indicator_id, weighbridge_id, indicator, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.com_port, NEW.bits_per_second, NEW.data_bits, NEW.parity, NEW.stop_bits, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PORT` BEFORE UPDATE ON `Port` FOR EACH ROW BEGIN
    -- Port has no status column, so every update is logged as action_id 2
    INSERT INTO Port_Log (
        port_id, com_port, bits_per_second, data_bits, parity, stop_bits, indicator_id, weighbridge_id, indicator, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.com_port, NEW.bits_per_second, NEW.data_bits, NEW.parity, NEW.stop_bits, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator, 2, NEW.modified_by, NOW()
    );
END
$$
DELIMITER ;

CREATE TABLE `Login_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_login_log_username` (`username`),
  KEY `idx_login_log_event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14/10/2026 --
ALTER TABLE `Weight` ADD `location_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight` ADD `is_printed` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `is_approved`;
ALTER TABLE `Weight_Log` ADD `location_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight_Log` ADD `is_printed` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `is_approved`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code, raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.location_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name,
        NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.location_id,
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight_Container` ADD `location_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight_Container` ADD `is_printed` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `is_approved`;
ALTER TABLE `Weight_Container_Log` ADD `location_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight_Container_Log` ADD `is_printed` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `is_approved`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW 
INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code, raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.location_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT_CONTAINER` BEFORE UPDATE ON `Weight_Container` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Container_Log table
    INSERT INTO Weight_Container_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name,
        NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.location_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Vehicle` ADD `max_entry_per_day` INT(11) NOT NULL DEFAULT 0 AFTER `supplier_name`;
ALTER TABLE `Vehicle_Log` ADD `max_entry_per_day` INT(11) NOT NULL DEFAULT 0 AFTER `supplier_name`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_VEH` AFTER INSERT ON `Vehicle` FOR EACH ROW 
INSERT INTO Vehicle_Log (
    vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.max_entry_per_day, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_VEH` BEFORE UPDATE ON `Vehicle` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Vehicle_Log table
    INSERT INTO Vehicle_Log (
        vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.max_entry_per_day, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

CREATE TABLE `Vehicle_Entry_Limit` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `veh_number` varchar(50) NOT NULL,
  `limit_date` date NOT NULL,
  `max_entry` int(11) NOT NULL DEFAULT 0,
  `status` int(3) NOT NULL DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Vehicle_Entry_Limit` ADD PRIMARY KEY (`id`), ADD KEY `idx_vehicle_limit_date` (`vehicle_id`, `limit_date`);

ALTER TABLE `Vehicle_Entry_Limit` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Vehicle_Entry_Limit_Log` (
  `id` int(11) NOT NULL,
  `limit_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `veh_number` varchar(50) NOT NULL,
  `limit_date` date NOT NULL,
  `max_entry` int(11) NOT NULL DEFAULT 0,
  `status` int(3) NOT NULL DEFAULT 0,
  `action_id` int(11) DEFAULT NULL,
  `action_by` varchar(50) DEFAULT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Vehicle_Entry_Limit_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Vehicle_Entry_Limit_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_VEH_ENTRY_LIMIT` AFTER INSERT ON `Vehicle_Entry_Limit` FOR EACH ROW
INSERT INTO Vehicle_Entry_Limit_Log (
    limit_id, vehicle_id, veh_number, limit_date, max_entry, status, action_id, action_by, event_date
)
VALUES (
    NEW.id, NEW.vehicle_id, NEW.veh_number, NEW.limit_date, NEW.max_entry, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_VEH_ENTRY_LIMIT` BEFORE UPDATE ON `Vehicle_Entry_Limit` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Vehicle_Entry_Limit_Log table
    INSERT INTO Vehicle_Entry_Limit_Log (
        limit_id, vehicle_id, veh_number, limit_date, max_entry, status, action_id, action_by, event_date
    )
    VALUES (
        NEW.id, NEW.vehicle_id, NEW.veh_number, NEW.limit_date, NEW.max_entry, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

CREATE TABLE `Vehicle_Entry_Log` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `veh_number` varchar(50) NOT NULL,
  `entry_date` date NOT NULL,
  `action` varchar(20) NOT NULL COMMENT 'IN = new weighing, REVERSE = weighing cancelled',
  `weight_id` int(11) DEFAULT NULL,
  `weight_table` varchar(30) DEFAULT NULL COMMENT 'Weight / Weight_Container',
  `transaction_id` varchar(50) DEFAULT NULL,
  `max_entry` int(11) NOT NULL DEFAULT 0 COMMENT 'Limit in force at the time, 0 = unlimited',
  `entry_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Entries used for entry_date after this action',
  `is_override` varchar(3) NOT NULL DEFAULT 'N',
  `approved_by` varchar(50) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `action_by` varchar(50) DEFAULT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Vehicle_Entry_Log` ADD PRIMARY KEY (`id`), ADD KEY `idx_vehicle_entry_date` (`vehicle_id`, `entry_date`), ADD KEY `idx_weight` (`weight_table`, `weight_id`);

ALTER TABLE `Vehicle_Entry_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('location_code', 'Location', '位置', 'Lokasi', 'இடம்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('entry_limit_code', 'Entry Limit', '进场限制', 'Had Kemasukan', 'நுழைவு வரம்பு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('default_limit_code', 'Default Limit', '默认限制', 'Had Lalai', 'இயல்புநிலை வரம்பு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('date_limit_code', 'Date Limit', '日期限制', 'Had Tarikh', 'தேதி வரம்பு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('max_entry_per_day_code', 'Max Entry / Day', '每日最大进场次数', 'Kemasukan Maksimum / Hari', 'ஒரு நாளுக்கான அதிகபட்ச நுழைவு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('max_entry_per_day_hint_code', 'Default for every day. 0 = unlimited.', '每天的默认值。0 = 无限制。', 'Lalai untuk setiap hari. 0 = tanpa had.', 'ஒவ்வொரு நாளுக்குமான இயல்புநிலை. 0 = வரம்பற்றது.');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('zero_unlimited_code', '0 = Unlimited', '0 = 无限制', '0 = Tanpa Had', '0 = வரம்பற்றது');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('limit_date_code', 'Limit Date', '限制日期', 'Tarikh Had', 'வரம்பு தேதி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('max_entry_for_date_code', 'Max Entry for Date', '当日最大进场次数', 'Kemasukan Maksimum untuk Tarikh', 'தேதிக்கான அதிகபட்ச நுழைவு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('max_entry_for_date_hint_code', 'Overrides the default for this date only. Blank = no date limit.', '仅覆盖此日期的默认值。留空 = 无日期限制。', 'Menggantikan had lalai untuk tarikh ini sahaja. Kosong = tiada had tarikh.', 'இந்தத் தேதிக்கு மட்டும் இயல்புநிலையை மாற்றும். காலியாக = தேதி வரம்பு இல்லை.');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('blank_use_default_code', 'Blank = use default', '留空 = 使用默认值', 'Kosong = guna lalai', 'காலியாக = இயல்புநிலையைப் பயன்படுத்து');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('used_code', 'Used', '已使用', 'Digunakan', 'பயன்படுத்தப்பட்டது');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('unlimited_code', 'Unlimited', '无限制', 'Tanpa Had', 'வரம்பற்றது');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('date_limit_records_code', 'Date Limit Records', '日期限制记录', 'Rekod Had Tarikh', 'தேதி வரம்பு பதிவுகள்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('max_entry_code', 'Max Entry', '最大进场次数', 'Kemasukan Maksimum', 'அதிகபட்ச நுழைவு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('created_by_code', 'Created By', '创建者', 'Dicipta Oleh', 'உருவாக்கியவர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('modified_by_code', 'Modified By', '修改者', 'Diubah Oleh', 'மாற்றியவர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('modified_date_code', 'Modified Date', '修改日期', 'Tarikh Diubah', 'மாற்றிய தேதி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('view_log_code', 'View Log', '查看日志', 'Lihat Log', 'பதிவைக் காண்க');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('action_by_code', 'Action By', '操作者', 'Tindakan Oleh', 'செயல்படுத்தியவர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('action_date_code', 'Action Date', '操作日期', 'Tarikh Tindakan', 'செயல் தேதி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('create_code', 'Create', '创建', 'Cipta', 'உருவாக்கு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('no_log_found_code', 'No log found.', '未找到日志。', 'Tiada log dijumpai.', 'பதிவு எதுவும் இல்லை.');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('insufficient_entry_title_code', 'Insufficient Entry - Confirm Password', '进场次数不足 - 确认密码', 'Kemasukan Tidak Mencukupi - Sahkan Kata Laluan', 'போதிய நுழைவு இல்லை - கடவுச்சொல்லை உறுதிப்படுத்தவும்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('insufficient_entry_code', 'Insufficient entry for {vehicle} ({count}/{max} used today).', '{vehicle} 进场次数不足（今日已使用 {count}/{max}）。', 'Kemasukan tidak mencukupi untuk {vehicle} ({count}/{max} digunakan hari ini).', '{vehicle} க்கு போதிய நுழைவு இல்லை (இன்று {count}/{max} பயன்படுத்தப்பட்டது).');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('proceed_code', 'Proceed?', '是否继续？', 'Teruskan?', 'தொடரவா?');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('password_code', 'Password', '密码', 'Kata Laluan', 'கடவுச்சொல்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('please_enter_your_password_code', 'Please enter your password.', '请输入您的密码。', 'Sila masukkan kata laluan anda.', 'உங்கள் கடவுச்சொல்லை உள்ளிடவும்.');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('confirm_code', 'Confirm', '确认', 'Sahkan', 'உறுதிப்படுத்து');

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('vehicle_entry_log_code', 'Vehicle Entry Log', '车辆进场记录', 'Log Kemasukan Kenderaan', 'வாகன நுழைவு பதிவு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('entry_date_code', 'Entry Date', '进场日期', 'Tarikh Kemasukan', 'நுழைவு தேதி');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('entry_no_code', 'Entry No.', '进场次数', 'No. Kemasukan', 'நுழைவு எண்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('entry_in_code', 'Entry In', '进场', 'Masuk', 'நுழைவு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('entry_reversed_code', 'Reversed', '已撤销', 'Dibatalkan', 'திரும்பப் பெறப்பட்டது');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('override_code', 'Override', '超限放行', 'Kelulusan Lebihan', 'மேலெழுதல்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('approved_by_code', 'Approved By', '批准者', 'Diluluskan Oleh', 'அங்கீகரித்தவர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('remarks_code', 'Remarks', '备注', 'Catatan', 'குறிப்புகள்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('invalid_password_code', 'Invalid password. Please try again.', '密码错误，请重试。', 'Kata laluan tidak sah. Sila cuba lagi.', 'தவறான கடவுச்சொல். மீண்டும் முயற்சிக்கவும்.');

UPDATE `message_resource` SET `en`='Bin No.', `zh`='料仓编号', `my`='No. Tong', `ne`='தொட்டி எண்' WHERE `message_key_code`='container_no2_code';
UPDATE `message_resource` SET `en`='Bin Type', `zh`='料仓类型', `my`='Jenis Tong', `ne`='தொட்டி வகை' WHERE `message_key_code`='seal_no2_code';

CREATE TABLE `Features` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `feature_code` VARCHAR(50) NOT NULL,
  `feature_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `status` VARCHAR(3) NOT NULL DEFAULT '0',
  `created_by` VARCHAR(50) NULL,
  `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` VARCHAR(50) NULL,
  `modified_date` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Company_Features` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) NOT NULL,
  `feature_code` VARCHAR(50) NOT NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `created_by` VARCHAR(50) NULL,
  `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` VARCHAR(50) NULL,
  `modified_date` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Features` (`feature_code`, `feature_name`, `description`, `created_by`) VALUES ('container_weighing', 'Container Weighing', 'Show Primer Mover / Primer Mover + Container weight types', 'SYSTEM');
INSERT INTO `Features` (`feature_code`, `feature_name`, `description`, `created_by`) VALUES ('different_bin_weighing', 'Different Bin Weighing', 'Show Primer Mover + Different Bins / Primer Mover + Container weight types', 'SYSTEM');
INSERT INTO `Company_Features` (`company_id`, `feature_code`, `enabled`, `created_by`) VALUES (1, 'container_weighing', 0, 'SYSTEM');
INSERT INTO `Company_Features` (`company_id`, `feature_code`, `enabled`, `created_by`) VALUES (1, 'different_bin_weighing', 0, 'SYSTEM');

-- 15/10/2026 --
CREATE TABLE `Contractor` (
  `id` int(11) NOT NULL,
  `contractor_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(3) NOT NULL DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Contractor` ADD PRIMARY KEY (`id`);

ALTER TABLE `Contractor` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Contractor_Log` (
  `id` int(11) NOT NULL,
  `contractor_id` int(11) NOT NULL,
  `contractor_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(3) NOT NULL DEFAULT 0,
  `action_id` int(11) DEFAULT NULL,
  `action_by` varchar(50) DEFAULT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `Contractor_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Contractor_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_CONTRACTOR` AFTER INSERT ON `Contractor` FOR EACH ROW
INSERT INTO Contractor_Log (
    contractor_id, contractor_code, name, status, action_id, action_by, event_date
)
VALUES (
    NEW.id, NEW.contractor_code, NEW.name, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_CONTRACTOR` BEFORE UPDATE ON `Contractor` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Contractor_Log table
    INSERT INTO Contractor_Log (
        contractor_id, contractor_code, name, status, action_id, action_by, event_date
    )
    VALUES (
        NEW.id, NEW.contractor_code, NEW.name, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('contractor_code', 'Contractor', '承包商', 'Kontraktor', 'ஒப்பந்ததாரர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('contractor_code_code', 'Contractor Code', '承包商代码', 'Kod Kontraktor', 'ஒப்பந்ததாரர் குறியீடு');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('contractor_name_code', 'Contractor Name', '承包商名称', 'Nama Kontraktor', 'ஒப்பந்ததாரர் பெயர்');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('add_new_contractor_code', 'Add New Contractor', '添加新承包商', 'Tambah Kontraktor Baru', 'புதிய ஒப்பந்ததாரரைச் சேர்');

ALTER TABLE `Vehicle` ADD `vehicle_type` VARCHAR(50) NULL AFTER `max_entry_per_day`;
ALTER TABLE `Vehicle_Log` ADD `vehicle_type` VARCHAR(50) NULL AFTER `max_entry_per_day`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_VEH` AFTER INSERT ON `Vehicle` FOR EACH ROW
INSERT INTO Vehicle_Log (
    vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, vehicle_type, action_id, action_by, event_date
)
VALUES (
    NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.max_entry_per_day, NEW.vehicle_type, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_VEH` BEFORE UPDATE ON `Vehicle` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Vehicle_Log table
    INSERT INTO Vehicle_Log (
        vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, vehicle_type, action_id, action_by, event_date
    )
    VALUES (
        NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.max_entry_per_day, NEW.vehicle_type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES ('vehicle_type_code', 'Vehicle Type', '车辆类型', 'Jenis Kenderaan', 'வாகன வகை');

ALTER TABLE `Vehicle` ADD `contractor_id` INT(11) NULL AFTER `vehicle_type`, ADD `raw_mat_code` VARCHAR(50) NULL AFTER `contractor_id`, ADD `raw_mat_name` VARCHAR(255) NULL AFTER `raw_mat_code`;
ALTER TABLE `Vehicle_Log` ADD `contractor_id` INT(11) NULL AFTER `vehicle_type`, ADD `raw_mat_code` VARCHAR(50) NULL AFTER `contractor_id`, ADD `raw_mat_name` VARCHAR(255) NULL AFTER `raw_mat_code`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_VEH` AFTER INSERT ON `Vehicle` FOR EACH ROW
INSERT INTO Vehicle_Log (
    vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, vehicle_type, contractor_id, raw_mat_code, raw_mat_name, action_id, action_by, event_date
)
VALUES (
    NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.max_entry_per_day, NEW.vehicle_type, NEW.contractor_id, NEW.raw_mat_code, NEW.raw_mat_name, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_VEH` BEFORE UPDATE ON `Vehicle` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Vehicle_Log table
    INSERT INTO Vehicle_Log (
        vehicle_id, veh_number, vehicle_weight, transporter_code, transporter_name, customer_code, customer_name, supplier_code, supplier_name, max_entry_per_day, vehicle_type, contractor_id, raw_mat_code, raw_mat_name, action_id, action_by, event_date
    )
    VALUES (
        NEW.id, NEW.veh_number, NEW.vehicle_weight, NEW.transporter_code, NEW.transporter_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.max_entry_per_day, NEW.vehicle_type, NEW.contractor_id, NEW.raw_mat_code, NEW.raw_mat_name, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'planned_schedule_code', 'Planned Schedule', '计划排程', 'Jadual Dirancang', 'திட்டமிடப்பட்ட அட்டவணை' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'planned_schedule_code');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'trips_per_day_code', 'Trips / Day', '每日车次', 'Trip / Hari', 'ஒரு நாளைக்கான பயணங்கள்' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'trips_per_day_code');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'start_date_code', 'Start Date', '开始日期', 'Tarikh Mula', 'தொடக்க தேதி' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'start_date_code');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'end_date_code', 'End Date', '结束日期', 'Tarikh Tamat', 'முடிவு தேதி' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'end_date_code');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'waste_code', 'Waste', '废料', 'Sisa', 'கழிவு' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'waste_code');

ALTER TABLE `Vehicle_Log` MODIFY `max_entry_per_day` INT(11) NULL DEFAULT NULL;
ALTER TABLE `Vehicle` MODIFY `max_entry_per_day` INT(11) NULL DEFAULT NULL;
ALTER TABLE `Vehicle_Entry_Log` MODIFY `max_entry` INT(11) NULL DEFAULT NULL COMMENT 'Limit in force at the time, NULL = no limit, 0 = blocked';
UPDATE `Vehicle` SET `max_entry_per_day` = NULL WHERE `max_entry_per_day` = 0;

UPDATE `message_resource` SET `en` = 'Blank = No Limit', `zh` = '留空 = 无限制', `my` = 'Kosong = Tanpa Had', `ne` = 'காலியாக = வரம்பற்றது' WHERE `message_key_code` = 'zero_unlimited_code';
UPDATE `message_resource` SET `en` = 'Default for every day. Blank = no limit, 0 = blocked.', `zh` = '每天的默认值。留空 = 无限制，0 = 禁止进场。', `my` = 'Lalai untuk setiap hari. Kosong = tiada had, 0 = disekat.', `ne` = 'ஒவ்வொரு நாளுக்குமான இயல்புநிலை. காலியாக = வரம்பற்றது, 0 = தடுக்கப்பட்டது.' WHERE `message_key_code` = 'max_entry_per_day_hint_code';
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'vehicle_blocked_code', 'Vehicle {vehicle} is blocked today (daily limit 0).', '车辆 {vehicle} 今日被禁止进场（每日限制为 0）。', 'Kenderaan {vehicle} disekat hari ini (had harian 0).', '{vehicle} இன்று தடுக்கப்பட்டது (தினசரி வரம்பு 0).' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'vehicle_blocked_code');

ALTER TABLE `Vehicle` MODIFY `max_entry_per_day` INT(11) NULL DEFAULT 0;

ALTER TABLE `Weight` ADD `contractor_id` INT(11) NULL AFTER `raw_mat_name`;
ALTER TABLE `Weight_Log` ADD `contractor_id` INT(11) NULL AFTER `raw_mat_name`;
ALTER TABLE `Weight_Container` ADD `contractor_id` INT(11) NULL AFTER `raw_mat_name`;
ALTER TABLE `Weight_Container_Log` ADD `contractor_id` INT(11) NULL AFTER `raw_mat_name`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code, raw_mat_name, contractor_id, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, NEW.contractor_id, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.location_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW
INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code, raw_mat_name, contractor_id, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, NEW.contractor_id, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.location_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT` BEFORE UPDATE ON `Weight` FOR EACH ROW
BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Log table
    INSERT INTO Weight_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code,raw_mat_name, contractor_id, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name,
        NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.contractor_id, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.location_id,
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_WEIGHT_CONTAINER` BEFORE UPDATE ON `Weight_Container` FOR EACH ROW
BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Weight_Container_Log table
    INSERT INTO Weight_Container_Log (
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, raw_mat_code,raw_mat_name, contractor_id, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, is_printed, manual_weight, indicator_id, weighbridge_id, location_id, indicator_id_2, unit_price, sub_total, sst, total_price, status, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
        NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, 
        NEW.plant_code, NEW.plant_name,
        NEW.customer_code, NEW.customer_name, 
        NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, 
        NEW.product_description, NEW.raw_mat_code, NEW.raw_mat_name, 
        NEW.contractor_id, 
        NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, 
        NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, 
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
        NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, 
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.is_printed, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.location_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `Features` (`feature_code`, `feature_name`, `description`, `created_by`) SELECT 'transaction_dispatch', 'Dispatch', 'Show Dispatch transaction status on the weighing screen', 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Features` WHERE `feature_code` = 'transaction_dispatch');
INSERT INTO `Features` (`feature_code`, `feature_name`, `description`, `created_by`) SELECT 'transaction_receiving', 'Receiving', 'Show Receiving transaction status on the weighing screen', 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Features` WHERE `feature_code` = 'transaction_receiving');
INSERT INTO `Features` (`feature_code`, `feature_name`, `description`, `created_by`) SELECT 'transaction_internal_transfer', 'Internal Transfer', 'Show Internal Transfer transaction status on the weighing screen', 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Features` WHERE `feature_code` = 'transaction_internal_transfer');
INSERT INTO `Features` (`feature_code`, `feature_name`, `description`, `created_by`) SELECT 'transaction_miscellaneous', 'Miscellaneous', 'Show Miscellaneous transaction status on the weighing screen', 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Features` WHERE `feature_code` = 'transaction_miscellaneous');
INSERT INTO `Company_Features` (`company_id`, `feature_code`, `enabled`, `created_by`) SELECT 1, 'transaction_dispatch', 1, 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Company_Features` WHERE `company_id` = 1 AND `feature_code` = 'transaction_dispatch');
INSERT INTO `Company_Features` (`company_id`, `feature_code`, `enabled`, `created_by`) SELECT 1, 'transaction_receiving', 1, 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Company_Features` WHERE `company_id` = 1 AND `feature_code` = 'transaction_receiving');
INSERT INTO `Company_Features` (`company_id`, `feature_code`, `enabled`, `created_by`) SELECT 1, 'transaction_internal_transfer', 1, 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Company_Features` WHERE `company_id` = 1 AND `feature_code` = 'transaction_internal_transfer');
INSERT INTO `Company_Features` (`company_id`, `feature_code`, `enabled`, `created_by`) SELECT 1, 'transaction_miscellaneous', 1, 'SYSTEM' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `Company_Features` WHERE `company_id` = 1 AND `feature_code` = 'transaction_miscellaneous');

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'vehicle_not_registered_title_code', 'Vehicle Not Registered', '车辆未登记', 'Kenderaan Belum Didaftarkan', 'வாகனம் பதிவு செய்யப்படவில்லை' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'vehicle_not_registered_title_code');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'vehicle_not_registered_code', 'Vehicle {vehicle} is not registered yet.', '车辆 {vehicle} 尚未登记。', 'Kenderaan {vehicle} belum didaftarkan lagi.', 'வாகனம் {vehicle} இன்னும் பதிவு செய்யப்படவில்லை.' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'vehicle_not_registered_code');
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) SELECT 'go_to_vehicle_code', 'Go to Vehicle', '前往车辆', 'Pergi ke Kenderaan', 'வாகனத்திற்குச் செல்' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `message_resource` WHERE `message_key_code` = 'go_to_vehicle_code');
