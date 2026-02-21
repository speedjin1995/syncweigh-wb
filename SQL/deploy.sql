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

DELIMITER ;

-- 28/09/2025 --
ALTER TABLE `Users` ADD `allow_manual` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `plant_id`;

ALTER TABLE `Users_Log` ADD `allow_manual` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `plant_id`;

ALTER TABLE `Users` ADD `allow_deduct` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `allow_manual`;

ALTER TABLE `Users_Log` ADD `allow_deduct` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `allow_manual`;

ALTER TABLE `Users` ADD `password2` TEXT NULL AFTER `token`, ADD `token2` TEXT NULL AFTER `password2`, ADD `password3` TEXT NULL AFTER `token2`, ADD `token3` TEXT NULL AFTER `password3`;

ALTER TABLE `Users_Log` ADD `password2` TEXT NULL AFTER `password`, ADD `password3` TEXT NULL AFTER `password2`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_USER` AFTER INSERT ON `Users` FOR EACH ROW 
INSERT INTO Users_Log (
    user_id, employee_code, username, name, useremail, password, password2, password3, plant_id, allow_manual, allow_deduct, `status`, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.password, NEW.password2, NEW.password3, NEW.plant_id, NEW.allow_manual, NEW.allow_deduct, NEW.status, 1, NEW.created_by, NEW.created_date
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
        user_id, employee_code, username, name, useremail, password, password2, password3, plant_id, allow_manual, allow_deduct, `status`, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.password, NEW.password2, NEW.password3, NEW.plant_id, NEW.allow_manual, NEW.allow_deduct, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES
('additional_passwords_code', 'Additional Passwords', 'é™„åŠ å¯†ç ', 'Kata Laluan Tambahan', 'à®•à¯‚à®Ÿà¯à®¤à®²à¯ à®•à®Ÿà®µà¯à®šà¯à®šà¯Šà®²à¯');

-- 09/10/2025 --
ALTER TABLE `Deduction` MODIFY `status` ENUM('Enable', 'Manual', 'Auto', 'Disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

UPDATE `Deduction` SET `status` = 'Manual' WHERE `status` = 'Enable';

ALTER TABLE `Deduction` MODIFY `status` ENUM('Manual', 'Auto', 'Disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Deduction` ADD `auto_data` LONGTEXT NULL AFTER `F12`;

ALTER TABLE `Deduction_Log` MODIFY `status` ENUM('Enable', 'Manual', 'Auto', 'Disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

UPDATE `Deduction_Log` SET `status` = 'Manual' WHERE `status` = 'Enable';

ALTER TABLE `Deduction_Log` MODIFY `status` ENUM('Manual', 'Auto', 'Disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `Deduction_Log` ADD `auto_data` LONGTEXT NULL AFTER `F12`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `after_deduction_update` BEFORE UPDATE ON `Deduction` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Deduction_Log table
    INSERT INTO Deduction_Log (
        deduction_id,
        F1, F2, F3, F4, F5, F6,
        F7, F8, F9, F10, F11, F12, auto_data,
        status, created_by, modified_by,
        action_id, action_by, event_date
    )
    VALUES (
        NEW.id,
        NEW.F1, NEW.F2, NEW.F3, NEW.F4, NEW.F5, NEW.F6,
        NEW.F7, NEW.F8, NEW.F9, NEW.F10, NEW.F11, NEW.F12, NEW.auto_data,
        NEW.status, NEW.created_by, NEW.modified_by,
        action_value, NEW.modified_by, NOW()
    );
END
$$
DELIMITER ;

CREATE TABLE `Customer_Deduction` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `F1` decimal(10,2) DEFAULT NULL,
  `F2` decimal(10,2) DEFAULT NULL,
  `F3` decimal(10,2) DEFAULT NULL,
  `F4` decimal(10,2) DEFAULT NULL,
  `F5` decimal(10,2) DEFAULT NULL,
  `F6` decimal(10,2) DEFAULT NULL,
  `F7` decimal(10,2) DEFAULT NULL,
  `F8` decimal(10,2) DEFAULT NULL,
  `F9` decimal(10,2) DEFAULT NULL,
  `F10` decimal(10,2) DEFAULT NULL,
  `F11` decimal(10,2) DEFAULT NULL,
  `F12` decimal(10,2) DEFAULT NULL,
  `auto_data` longtext DEFAULT NULL,
  `status` enum('Manual','Auto','Disable') NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Customer_Deduction` ADD PRIMARY KEY (`id`);

ALTER TABLE `Customer_Deduction` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Supplier_Deduction` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `F1` decimal(10,2) DEFAULT NULL,
  `F2` decimal(10,2) DEFAULT NULL,
  `F3` decimal(10,2) DEFAULT NULL,
  `F4` decimal(10,2) DEFAULT NULL,
  `F5` decimal(10,2) DEFAULT NULL,
  `F6` decimal(10,2) DEFAULT NULL,
  `F7` decimal(10,2) DEFAULT NULL,
  `F8` decimal(10,2) DEFAULT NULL,
  `F9` decimal(10,2) DEFAULT NULL,
  `F10` decimal(10,2) DEFAULT NULL,
  `F11` decimal(10,2) DEFAULT NULL,
  `F12` decimal(10,2) DEFAULT NULL,
  `auto_data` longtext DEFAULT NULL,
  `status` enum('Manual','Auto','Disable') NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Supplier_Deduction` ADD PRIMARY KEY (`id`);

ALTER TABLE `Supplier_Deduction` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 31/10/2025 --
ALTER TABLE `Deduction` CHANGE `status` `status` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Deduction_Log` CHANGE `status` `status` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Deduction` ADD `default_range_min` DECIMAL(10,2) NULL AFTER `auto_data`, ADD `default_range_max` DECIMAL(10,2) NULL AFTER `default_range_min`, ADD `default_range_weight` DECIMAL(10,2) NULL AFTER `default_range_max`;

ALTER TABLE `Deduction_Log` ADD `default_range_min` DECIMAL(10,2) NULL AFTER `auto_data`, ADD `default_range_max` DECIMAL(10,2) NULL AFTER `default_range_min`, ADD `default_range_weight` DECIMAL(10,2) NULL AFTER `default_range_max`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `after_deduction_update` BEFORE UPDATE ON `Deduction` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Deduction_Log table
    INSERT INTO Deduction_Log (
        deduction_id,
        F1, F2, F3, F4, F5, F6,
        F7, F8, F9, F10, F11, F12, auto_data,
        default_range_min, default_range_max, default_range_weight,
        status, created_by, modified_by,
        action_id, action_by, event_date
    )
    VALUES (
        NEW.id,
        NEW.F1, NEW.F2, NEW.F3, NEW.F4, NEW.F5, NEW.F6,
        NEW.F7, NEW.F8, NEW.F9, NEW.F10, NEW.F11, NEW.F12, NEW.auto_data,
        NEW.default_range_min, NEW.default_range_max, NEW.default_range_weight,
        NEW.status, NEW.created_by, NEW.modified_by,
        action_value, NEW.modified_by, NOW()
    );
END
$$
DELIMITER ;

-- 06/12/2025 --
ALTER TABLE `Deduction` ADD `customers` TEXT NULL AFTER `default_range_weight`, ADD `suppliers` TEXT NULL AFTER `customers`;

ALTER TABLE `Deduction_Log` ADD `customers` TEXT NULL AFTER `default_range_weight`, ADD `suppliers` TEXT NULL AFTER `customers`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `after_deduction_update` BEFORE UPDATE ON `Deduction` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Deduction_Log table
    INSERT INTO Deduction_Log (
        deduction_id,
        F1, F2, F3, F4, F5, F6,
        F7, F8, F9, F10, F11, F12, auto_data,
        default_range_min, default_range_max, default_range_weight,
        customers, suppliers,
        status, created_by, modified_by,
        action_id, action_by, event_date
    )
    VALUES (
        NEW.id,
        NEW.F1, NEW.F2, NEW.F3, NEW.F4, NEW.F5, NEW.F6,
        NEW.F7, NEW.F8, NEW.F9, NEW.F10, NEW.F11, NEW.F12, NEW.auto_data,
        NEW.default_range_min, NEW.default_range_max, NEW.default_range_weight,
        NEW.customers, NEW.suppliers,
        NEW.status, NEW.created_by, NEW.modified_by,
        action_value, NEW.modified_by, NOW()
    );
END
$$
DELIMITER ;

--11/12/2025 --
ALTER TABLE `Company` ADD `include_price` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `mobile_no`, ADD `include_container` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `include_price`;

ALTER TABLE `Company_Log` ADD `include_price` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `mobile_no`, ADD `include_container` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `include_price`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, new_reg_no, `name`, address_line_1, address_line_2, address_line_3, phone_no, fax_no, tin_no, mobile_no, include_price, include_container, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.tin_no, NEW.mobile_no, NEW.include_price, NEW.include_container, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Customer` ADD `mpob` TEXT NULL AFTER `tin_no`;
ALTER TABLE `Customer_Log` ADD `mpob` TEXT NULL AFTER `tin_no`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_CUSTOMER` AFTER INSERT ON `Customer` FOR EACH ROW 
INSERT INTO Customer_Log (
    customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, 1, NEW.created_by, NEW.created_date
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
        customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Supplier` ADD `mpob` TEXT NULL AFTER `tin_no`;
ALTER TABLE `Supplier_Log` ADD `mpob` TEXT NULL AFTER `tin_no`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SUPPLIER` AFTER INSERT ON `Supplier` FOR EACH ROW 
INSERT INTO Supplier_Log (
    supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, 1, NEW.created_by, NEW.created_date
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
        supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 20/12/2025 --
INSERT INTO `roles` (`id`, `role_code`, `role_name`, `deleted`) VALUES (NULL, 'AUTHORITY', 'AUTHORITY', '0');

-- 24/12/2025 --
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'sales_code', 'Sales', '销售', 'Jualan', 'விற்பனை');

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'purchase_code', 'Purchase', '购买', 'Pembelian', 'கொள்முதல்');

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'public_code', 'Public', '公众', 'Awam', 'பொது');

-- 07/01/2026 --
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'payment_voucher_code', 'Payment Voucher', '付款凭证', 'Baucar Pembayaran', 'கட்டண வவுச்சர்');

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'start_weighing_code', 'Start Weighing', '开始称重', 'Mula Menimbang', 'துவக்க எடை');

-- 14/01/2026 --
ALTER TABLE `Customer` ADD `payment_term` VARCHAR(10) NULL AFTER `mpob`;
ALTER TABLE `Customer_Log` ADD `payment_term` VARCHAR(10) NULL AFTER `mpob`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_CUSTOMER` AFTER INSERT ON `Customer` FOR EACH ROW 
INSERT INTO Customer_Log (
    customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, payment_term, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.payment_term, 1, NEW.created_by, NEW.created_date
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
        customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, payment_term, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.payment_term, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Supplier` ADD `payment_term` VARCHAR(10) NULL AFTER `mpob`;
ALTER TABLE `Supplier_Log` ADD `payment_term` VARCHAR(10) NULL AFTER `mpob`;
ALTER TABLE `Supplier` ADD `customer_id` INT(11) NULL AFTER `payment_term`;
ALTER TABLE `Supplier_Log` ADD `customer_id` INT(11) NULL AFTER `payment_term`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SUPPLIER` AFTER INSERT ON `Supplier` FOR EACH ROW 
INSERT INTO Supplier_Log (
    supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, payment_term, customer_id, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.payment_term, NEW.customer_id, 1, NEW.created_by, NEW.created_date
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
        supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, payment_term, customer_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.payment_term, NEW.customer_id, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'payment_term_code', 'Payment Term', '付款期限', 'Tempoh Pembayaran', 'கட்டண காலம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'term_code', 'Term', '期限', 'Tempoh', 'காலம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cash_code', 'Cash', '现金', 'Tunai', 'பணம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'supplier_code_code', 'Supplier Code', '供应商代码', 'Kod Pembekal', 'சப்ளையர் குறியீடு');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'accounting_code', 'Accounting', '账期', 'Tempoh Akaun', 'கணக்குக் காலம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'report_code', 'Report', '报告', 'Laporan', 'அறிக்கை');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'daily_cash_report_code', 'Daily Cash Report', '每日现金报告', 'Laporan Tunai Harian', 'தினசரி பண அறிக்கை');

ALTER TABLE `Weight` ADD `gross_deduction1` VARCHAR(100) NULL AFTER `gross_weight_by1`;
ALTER TABLE `Weight` ADD `tare_deduction1` VARCHAR(100) NULL AFTER `tare_weight_by1`;
ALTER TABLE `Weight` ADD `nett_deduction1` VARCHAR(100) NULL AFTER `nett_weight1`;
ALTER TABLE `Weight_Log` ADD `gross_weight_by1` VARCHAR(50) NULL AFTER `gross_weight1_date`;
ALTER TABLE `Weight_Log` ADD `tare_weight_by1` VARCHAR(50) NULL AFTER `tare_weight1_date`;
ALTER TABLE `Weight_Log` ADD `gross_deduction1` VARCHAR(100) NULL AFTER `gross_weight_by1`;
ALTER TABLE `Weight_Log` ADD `tare_deduction1` VARCHAR(100) NULL AFTER `tare_weight_by1`;
ALTER TABLE `Weight_Log` ADD `nett_deduction1` VARCHAR(100) NULL AFTER `nett_weight1`;


DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, 
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

ALTER TABLE `Weight_Container` ADD `gross_deduction1` VARCHAR(100) NULL AFTER `gross_weight_by1`;
ALTER TABLE `Weight_Container` ADD `tare_deduction1` VARCHAR(100) NULL AFTER `tare_weight_by1`;
ALTER TABLE `Weight_Container` ADD `nett_deduction1` VARCHAR(100) NULL AFTER `nett_weight1`;
ALTER TABLE `Weight_Container_Log` ADD `gross_weight_by1` VARCHAR(50) NULL AFTER `gross_weight1_date`;
ALTER TABLE `Weight_Container_Log` ADD `tare_weight_by1` VARCHAR(50) NULL AFTER `tare_weight1_date`;
ALTER TABLE `Weight_Container_Log` ADD `gross_deduction1` VARCHAR(100) NULL AFTER `gross_weight_by1`;
ALTER TABLE `Weight_Container_Log` ADD `tare_deduction1` VARCHAR(100) NULL AFTER `tare_weight_by1`;
ALTER TABLE `Weight_Container_Log` ADD `nett_deduction1` VARCHAR(100) NULL AFTER `nett_weight1`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, 
        NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container,
        NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, 
        NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

CREATE TABLE `Payment_Voucher` (
  `id` int(11) NOT NULL,
  `customer_supplier` varchar(100) NOT NULL,
  `voucher_date` datetime NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `unit_price` varchar(100) NOT NULL DEFAULT '0',
  `tax` varchar(3) NOT NULL DEFAULT '0',
  `total_nett_weight` varchar(100) DEFAULT NULL,
  `total_amount` varchar(100) DEFAULT NULL,
  `deduction_amount` varchar(100) DEFAULT NULL,
  `addition_amount` varchar(100) DEFAULT NULL,
  `final_amount` varchar(100) DEFAULT NULL,
  `deduction_details` text DEFAULT NULL,
  `addition_details` text DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` varchar(50) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Payment_Voucher` ADD PRIMARY KEY (`id`);

ALTER TABLE `Payment_Voucher`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Payment_Voucher_Log` (
  `id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL,
  `customer_supplier` varchar(100) NOT NULL,
  `voucher_date` datetime NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `unit_price` varchar(100) NOT NULL DEFAULT '0',
  `tax` varchar(3) NOT NULL DEFAULT '0',
  `total_nett_weight` varchar(100) DEFAULT NULL,
  `total_amount` varchar(100) DEFAULT NULL,
  `deduction_amount` varchar(100) DEFAULT NULL,
  `addition_amount` varchar(100) DEFAULT NULL,
  `final_amount` varchar(100) DEFAULT NULL,
  `deduction_details` text DEFAULT NULL,
  `addition_details` text DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Payment_Voucher_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Payment_Voucher_Log`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PAY` AFTER INSERT ON `Payment_Voucher` FOR EACH ROW INSERT INTO Payment_Voucher_Log (
    payment_voucher_id, customer_supplier, voucher_date, invoice_no, unit_price, tax, total_nett_weight, total_amount, deduction_amount, addition_amount, final_amount, deduction_details, addition_details, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.customer_supplier, NEW.voucher_date, NEW.invoice_no, NEW.unit_price, NEW.tax, NEW.total_nett_weight, NEW.total_amount, NEW.deduction_amount, NEW.addition_amount, NEW.final_amount, NEW.deduction_details, NEW.addition_details, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PAY` BEFORE UPDATE ON `Payment_Voucher` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Payment_Voucher_Log table
    INSERT INTO Payment_Voucher_Log (
        payment_voucher_id, customer_supplier, voucher_date, invoice_no, unit_price, tax, total_nett_weight, total_amount, deduction_amount, addition_amount, final_amount, deduction_details, addition_details, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.customer_supplier, NEW.voucher_date, NEW.invoice_no, NEW.unit_price, NEW.tax, NEW.total_nett_weight, NEW.total_amount, NEW.deduction_amount, NEW.addition_amount, NEW.final_amount, NEW.deduction_details, NEW.addition_details, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`) VALUES
('print_slip_code', 'Print Slip', '打印单据', 'Cetak Slip', 'ஸ்லிப் அச்சிடு'),
('print_with_letter_header_code', 'Print With Letter Header', '打印（含信头）', 'Cetak Dengan Kepala Surat', 'கடிதத் தலைப்புடன் அச்சிடு'),
('print_without_letter_header_code', 'Print Without Letter Header', '打印（不含信头）', 'Cetak Tanpa Kepala Surat', 'கடிதத் தலைப்பின்றி அச்சிடு');

-- 24/01/2026 --
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'mpob_code', 'MPOB', '马棕局', 'MPOB', 'எம்.பி.ஓ.பி');

ALTER TABLE `Payment_Voucher` CHANGE `invoice_no` `invoice_no` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `Payment_Voucher_Log` CHANGE `invoice_no` `invoice_no` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

-- 08/01/2026 --
ALTER TABLE `Company` ADD `package` TEXT NULL AFTER `mobile_no`;
ALTER TABLE `Company_Log` ADD `package` TEXT NULL AFTER `mobile_no`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, new_reg_no, `name`, address_line_1, address_line_2, address_line_3, phone_no, fax_no, tin_no, mobile_no, package, include_price, include_container, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.tin_no, NEW.mobile_no, NEW.package, NEW.include_price, NEW.include_container, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 09/02/2026 --
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cash_book_payment_code', 'Cash Book Payment', '现金簿付款', 'Pembayaran Buku Tunai', 'பணம் புத்தக கட்டணம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cash_book_code', 'Cash Book', '现金簿', 'Buku Tunai', 'பணப்பதிவு புத்தகம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'number_short_code', 'No.', '编号', 'No.', 'எண்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'amount_code', 'Amount', '金额', 'Jumlah', 'தொகை');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'total_code', 'Total', '总计', 'Jumlah Keseluruhan', 'மொத்தம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'total_deduction_code', 'Total Deduction', '总扣除', 'Jumlah Potongan', 'மொத்த கழிவு');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'total_addition_code', 'Total Addition', '总增加', 'Jumlah Penambahan', 'மொத்த சேர்க்கை');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cash_book_no_code', 'Cash Book No.', '现金簿编号', 'No. Buku Tunai', 'பணப்பதிவு புத்தகம் எண்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'delete_reason_code', 'Delete Reason', '删除原因', 'Sebab Padam', 'அழிக்கும் காரணம்');

CREATE TABLE `Cash_Book` (
  `id` int(11) NOT NULL,
  `cash_book_no` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `deduction_details` text DEFAULT NULL,
  `addition_details` text DEFAULT NULL,
  `total_deduction` varchar(100) DEFAULT NULL,
  `total_addition` varchar(100) DEFAULT NULL,
  `accum_deduction` text DEFAULT NULL,
  `accum_addition` text DEFAULT NULL,
  `accum_purchase` text DEFAULT NULL,
  `accum_sales` text DEFAULT NULL,
  `prev_values` text DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `delete_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Cash_Book` ADD PRIMARY KEY (`id`);

ALTER TABLE `Cash_Book` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Cash_Book_Log` (
  `id` int(11) NOT NULL,
  `cash_book_id` int(11) NOT NULL,
  `cash_book_no` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `deduction_details` text DEFAULT NULL,
  `addition_details` text DEFAULT NULL,
  `total_deduction` varchar(100) DEFAULT NULL,
  `total_addition` varchar(100) DEFAULT NULL,
  `accum_deduction` text DEFAULT NULL,
  `accum_addition` text DEFAULT NULL,
  `accum_purchase` text DEFAULT NULL,
  `accum_sales` text DEFAULT NULL,
  `prev_values` text DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Cash_Book_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Cash_Book_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_CASH_BOOK` AFTER INSERT ON `Cash_Book` FOR EACH ROW INSERT INTO Cash_Book_Log (
    cash_book_id, cash_book_no, date, deduction_details, addition_details, total_deduction, total_addition, accum_deduction, accum_addition, accum_purchase, accum_sales, prev_values, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.cash_book_no, NEW.date, NEW.deduction_details, NEW.addition_details, NEW.total_deduction, NEW.total_addition, NEW.accum_deduction, NEW.accum_addition, NEW.accum_purchase, NEW.accum_sales, NEW.prev_values,  1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_CASH_BOOK` BEFORE UPDATE ON `Cash_Book` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Cash_Book_Log table
    INSERT INTO Cash_Book_Log (
        cash_book_id, cash_book_no, date, deduction_details, addition_details, total_deduction, total_addition, accum_deduction, accum_addition, accum_purchase, accum_sales, prev_values, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.cash_book_no, NEW.date, NEW.deduction_details, NEW.addition_details, NEW.total_deduction, NEW.total_addition, NEW.accum_deduction, NEW.accum_addition, NEW.accum_purchase, NEW.accum_sales, NEW.prev_values, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('cash_book', 1);

-- 15/02/2026 --
ALTER TABLE `Customer` ADD `mspo_no` VARCHAR(10) NULL AFTER `mpob`;

ALTER TABLE `Customer_Log` ADD `mspo_no` VARCHAR(10) NULL AFTER `mpob`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_CUSTOMER` AFTER INSERT ON `Customer` FOR EACH ROW 
INSERT INTO Customer_Log (
    customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, 1, NEW.created_by, NEW.created_date
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
        customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Supplier` ADD `mspo_no` VARCHAR(10) NULL AFTER `mpob`;

ALTER TABLE `Supplier_Log` ADD `mspo_no` VARCHAR(10) NULL AFTER `mpob`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SUPPLIER` AFTER INSERT ON `Supplier` FOR EACH ROW 
INSERT INTO Supplier_Log (
    supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, customer_id, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, NEW.customer_id, 1, NEW.created_by, NEW.created_date
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
        supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, customer_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, NEW.customer_id, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'mspo_code', 'MSPO Code', 'MSPO代码', 'Kod MSPO', 'MSPO குறியீடு');

-- 20/02/2026 --
ALTER TABLE `Company` ADD `include_display_setup` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `include_container`;
ALTER TABLE `Company_Log` ADD `include_display_setup` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `include_container`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, new_reg_no, `name`, address_line_1, address_line_2, address_line_3, phone_no, fax_no, tin_no, mobile_no, package, include_price, include_container, include_display_setup, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.tin_no, NEW.mobile_no, NEW.package, NEW.include_price, NEW.include_container, NEW.include_display_setup, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 21/02/2026 --
ALTER TABLE `Payment_Voucher` ADD `voucher_no` VARCHAR(100) NOT NULL AFTER `customer_supplier`;
ALTER TABLE `Payment_Voucher_Log` ADD `voucher_no` VARCHAR(100) NOT NULL AFTER `customer_supplier`;
ALTER TABLE `Payment_Voucher` ADD `outstanding_amount` VARCHAR(100) NULL AFTER `final_amount`;
ALTER TABLE `Payment_Voucher_Log` ADD `outstanding_amount` VARCHAR(100) NULL AFTER `final_amount`;
ALTER TABLE `Payment_Voucher` ADD `outstanding_details` TEXT NULL AFTER `outstanding_amount`;
ALTER TABLE `Payment_Voucher_Log` ADD `outstanding_details` TEXT NULL AFTER `outstanding_amount`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PAY` AFTER INSERT ON `Payment_Voucher` FOR EACH ROW INSERT INTO Payment_Voucher_Log (
    payment_voucher_id, voucher_no, customer_supplier, voucher_date, invoice_no, unit_price, tax, total_nett_weight, total_amount, deduction_amount, addition_amount, final_amount, outstanding_amount, outstanding_details, deduction_details, addition_details, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.voucher_no, NEW.customer_supplier, NEW.voucher_date, NEW.invoice_no, NEW.unit_price, NEW.tax, NEW.total_nett_weight, NEW.total_amount, NEW.deduction_amount, NEW.addition_amount, NEW.final_amount, NEW.outstanding_amount, NEW.outstanding_details, NEW.deduction_details, NEW.addition_details, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PAY` BEFORE UPDATE ON `Payment_Voucher` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if deleted = 1, set action_id to 3, otherwise set to 2
    IF NEW.deleted = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Payment_Voucher_Log table
    INSERT INTO Payment_Voucher_Log (
        payment_voucher_id, voucher_no, customer_supplier, voucher_date, invoice_no, unit_price, tax, total_nett_weight, total_amount, deduction_amount, addition_amount, final_amount, outstanding_amount, outstanding_details, deduction_details, addition_details, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.voucher_no, NEW.customer_supplier, NEW.voucher_date, NEW.invoice_no, NEW.unit_price, NEW.tax, NEW.total_nett_weight, NEW.total_amount, NEW.deduction_amount, NEW.addition_amount, NEW.final_amount, NEW.outstanding_amount, NEW.outstanding_details, NEW.deduction_details, NEW.addition_details, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('payment_voucher', 1);
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'voucher_no_code', 'Voucher No', '凭证号', 'Nombor Baucar', 'வவுசர் எண்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'outstanding_amount_code', 'Outstanding Amount', '未结金额', 'Jumlah Tertunggak', 'மீதமுள்ள தொகை');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'grader_code', 'Grader', '分级员', 'Penilai', 'மதிப்பீட்டாளர்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'grader_name_code', 'Grader Name', '分级员姓名', 'Nama Penilai', 'மதிப்பீட்டாளர் பெயர்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'certificate_id_code', 'Certificate ID', '证书编号', 'ID Sijil', 'சான்றிதழ் ஐடி');

CREATE TABLE `Grader` (
  `id` int(11) NOT NULL,
  `grader_name` varchar(100) DEFAULT NULL,
  `cert_id` varchar(100) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Grader` ADD PRIMARY KEY (`id`);

ALTER TABLE `Grader` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Grader_Log` (
  `id` int(11) NOT NULL,
  `grader_id` int(11) NOT NULL,
  `grader_name` varchar(100) DEFAULT NULL,
  `cert_id` varchar(100) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Grader_Log` ADD PRIMARY KEY (`id`);
  
ALTER TABLE `Grader_Log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_GRADER` AFTER INSERT ON `Grader` FOR EACH ROW INSERT INTO Grader_Log (
    grader_id, grader_name, cert_id, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.grader_name, NEW.cert_id, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_GRADER` BEFORE UPDATE ON `Grader` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Grader_Log table
    INSERT INTO Grader_Log (
        grader_id, grader_name, cert_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.grader_name, NEW.cert_id, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;
