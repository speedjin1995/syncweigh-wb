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

-- 11/01/2026 --
ALTER TABLE `message_resource` ADD COLUMN `ja` text AFTER `ne`;

UPDATE `message_resource` SET `ja`='新規追加' WHERE `id`=1;
UPDATE `message_resource` SET `ja`='メッセージコード' WHERE `id`=3;
UPDATE `message_resource` SET `ja`='編集' WHERE `id`=4;
UPDATE `message_resource` SET `ja`='削除' WHERE `id`=5;
UPDATE `message_resource` SET `ja`='配送' WHERE `id`=6;
UPDATE `message_resource` SET `ja`='受領' WHERE `id`=7;
UPDATE `message_resource` SET `ja`='内部振替' WHERE `id`=8;
UPDATE `message_resource` SET `ja`='その他' WHERE `id`=9;
UPDATE `message_resource` SET `ja`='コンテナ計量待ち' WHERE `id`=10;
UPDATE `message_resource` SET `ja`='トラック計量待ち' WHERE `id`=11;
UPDATE `message_resource` SET `ja`='顧客' WHERE `id`=12;
UPDATE `message_resource` SET `ja`='サプライヤー' WHERE `id`=13;
UPDATE `message_resource` SET `ja`='製品' WHERE `id`=14;
UPDATE `message_resource` SET `ja`='計量レポート' WHERE `id`=15;
UPDATE `message_resource` SET `ja`='スタッフ' WHERE `id`=16;
UPDATE `message_resource` SET `ja`='マスターデータ' WHERE `id`=17;
UPDATE `message_resource` SET `ja`='メッセージリソース' WHERE `id`=18;
UPDATE `message_resource` SET `ja`='設定' WHERE `id`=19;
UPDATE `message_resource` SET `ja`='会社概要' WHERE `id`=20;
UPDATE `message_resource` SET `ja`='プロフィール' WHERE `id`=21;
UPDATE `message_resource` SET `ja`='パスワード変更' WHERE `id`=22;
UPDATE `message_resource` SET `ja`='ログアウト' WHERE `id`=23;
UPDATE `message_resource` SET `ja`='車両' WHERE `id`=24;
UPDATE `message_resource` SET `ja`='運送業者' WHERE `id`=25;
UPDATE `message_resource` SET `ja`='工場' WHERE `id`=26;
UPDATE `message_resource` SET `ja`='開始日' WHERE `id`=27;
UPDATE `message_resource` SET `ja`='日次計量' WHERE `id`=28;
UPDATE `message_resource` SET `ja`='計量' WHERE `id`=29;
UPDATE `message_resource` SET `ja`='終了日' WHERE `id`=30;
UPDATE `message_resource` SET `ja`='検索' WHERE `id`=31;
UPDATE `message_resource` SET `ja`='目的地' WHERE `id`=32;
UPDATE `message_resource` SET `ja`='原材料' WHERE `id`=33;
UPDATE `message_resource` SET `ja`='レポート' WHERE `id`=34;
UPDATE `message_resource` SET `ja`='監査ログ' WHERE `id`=35;
UPDATE `message_resource` SET `ja`='ポート設定' WHERE `id`=36;
UPDATE `message_resource` SET `ja`='テンプレートダウンロード' WHERE `id`=37;
UPDATE `message_resource` SET `ja`='Excelアップロード' WHERE `id`=38;
UPDATE `message_resource` SET `ja`='過去の記録' WHERE `id`=39;
UPDATE `message_resource` SET `ja`='未読メッセージ' WHERE `id`=40;
UPDATE `message_resource` SET `ja`='新規' WHERE `id`=41;
UPDATE `message_resource` SET `ja`='名前' WHERE `id`=42;
UPDATE `message_resource` SET `ja`='ユーザー名' WHERE `id`=43;
UPDATE `message_resource` SET `ja`='パスワード' WHERE `id`=44;
UPDATE `message_resource` SET `ja`='新しいパスワード' WHERE `id`=45;
UPDATE `message_resource` SET `ja`='古いパスワード' WHERE `id`=46;
UPDATE `message_resource` SET `ja`='パスワード確認' WHERE `id`=47;
UPDATE `message_resource` SET `ja`='役割' WHERE `id`=48;
UPDATE `message_resource` SET `ja`='送信' WHERE `id`=49;
UPDATE `message_resource` SET `ja`='言語' WHERE `id`=50;
UPDATE `message_resource` SET `ja`='メール' WHERE `id`=51;
UPDATE `message_resource` SET `ja`='顧客コード' WHERE `id`=52;
UPDATE `message_resource` SET `ja`='登録番号' WHERE `id`=53;
UPDATE `message_resource` SET `ja`='新規登録番号' WHERE `id`=54;
UPDATE `message_resource` SET `ja`='顧客名' WHERE `id`=55;
UPDATE `message_resource` SET `ja`='住所' WHERE `id`=56;
UPDATE `message_resource` SET `ja`='電話' WHERE `id`=57;
UPDATE `message_resource` SET `ja`='FAX番号' WHERE `id`=58;
UPDATE `message_resource` SET `ja`='担当者' WHERE `id`=59;
UPDATE `message_resource` SET `ja`='IC' WHERE `id`=60;
UPDATE `message_resource` SET `ja`='TIN番号' WHERE `id`=61;
UPDATE `message_resource` SET `ja`='閉じる' WHERE `id`=62;
UPDATE `message_resource` SET `ja`='データプレビュー' WHERE `id`=64;
UPDATE `message_resource` SET `ja`='Excelエクスポート' WHERE `id`=65;
UPDATE `message_resource` SET `ja`='PDFエクスポート' WHERE `id`=66;
UPDATE `message_resource` SET `ja`='ステータス' WHERE `id`=67;
UPDATE `message_resource` SET `ja`='アクション' WHERE `id`=68;
UPDATE `message_resource` SET `ja`='伝票' WHERE `id`=70;
UPDATE `message_resource` SET `ja`='チケット番号' WHERE `id`=72;
UPDATE `message_resource` SET `ja`='日付' WHERE `id`=73;
UPDATE `message_resource` SET `ja`='納品書番号' WHERE `id`=74;
UPDATE `message_resource` SET `ja`='注文書番号' WHERE `id`=75;
UPDATE `message_resource` SET `ja`='コンテナ番号1' WHERE `id`=76;
UPDATE `message_resource` SET `ja`='コンテナ番号2' WHERE `id`=77;
UPDATE `message_resource` SET `ja`='封印番号1' WHERE `id`=78;
UPDATE `message_resource` SET `ja`='封印番号2' WHERE `id`=79;
UPDATE `message_resource` SET `ja`='車両番号' WHERE `id`=80;
UPDATE `message_resource` SET `ja`='製品説明' WHERE `id`=81;
UPDATE `message_resource` SET `ja`='日時' WHERE `id`=82;
UPDATE `message_resource` SET `ja`='重量' WHERE `id`=83;
UPDATE `message_resource` SET `ja`='kg' WHERE `id`=84;
UPDATE `message_resource` SET `ja`='入場' WHERE `id`=85;
UPDATE `message_resource` SET `ja`='出場' WHERE `id`=86;
UPDATE `message_resource` SET `ja`='減少' WHERE `id`=87;
UPDATE `message_resource` SET `ja`='正味' WHERE `id`=88;
UPDATE `message_resource` SET `ja`='備考' WHERE `id`=89;
UPDATE `message_resource` SET `ja`='1回目計量者' WHERE `id`=90;
UPDATE `message_resource` SET `ja`='2回目計量者' WHERE `id`=91;
UPDATE `message_resource` SET `ja`='管理者承認' WHERE `id`=92;
UPDATE `message_resource` SET `ja`='受領者' WHERE `id`=93;
UPDATE `message_resource` SET `ja`='出場日時' WHERE `id`=94;
UPDATE `message_resource` SET `ja`='入場日時' WHERE `id`=95;
UPDATE `message_resource` SET `ja`='総重量' WHERE `id`=96;
UPDATE `message_resource` SET `ja`='皮重量' WHERE `id`=97;
UPDATE `message_resource` SET `ja`='正味重量' WHERE `id`=98;
UPDATE `message_resource` SET `ja`='梱包' WHERE `id`=99;
UPDATE `message_resource` SET `ja`='番号' WHERE `id`=100;
UPDATE `message_resource` SET `ja`='取引状況' WHERE `id`=101;
UPDATE `message_resource` SET `ja`='計量タイプ' WHERE `id`=102;
UPDATE `message_resource` SET `ja`='取引ID' WHERE `id`=103;
UPDATE `message_resource` SET `ja`='コンテナ番号' WHERE `id`=104;
UPDATE `message_resource` SET `ja`='封印番号' WHERE `id`=105;
UPDATE `message_resource` SET `ja`='請求書/納品書/注文書番号' WHERE `id`=106;
UPDATE `message_resource` SET `ja`='保留中' WHERE `id`=107;
UPDATE `message_resource` SET `ja`='完了' WHERE `id`=108;
UPDATE `message_resource` SET `ja`='通常計量' WHERE `id`=109;
UPDATE `message_resource` SET `ja`='重量状況' WHERE `id`=110;
UPDATE `message_resource` SET `ja`='顧客/サプライヤー' WHERE `id`=111;
UPDATE `message_resource` SET `ja`='総入荷重量' WHERE `id`=112;
UPDATE `message_resource` SET `ja`='出荷皮重量' WHERE `id`=113;
UPDATE `message_resource` SET `ja`='入荷日' WHERE `id`=114;
UPDATE `message_resource` SET `ja`='出荷日' WHERE `id`=115;
UPDATE `message_resource` SET `ja`='記録検索' WHERE `id`=116;
UPDATE `message_resource` SET `ja`='空コンテナ保留記録' WHERE `id`=117;
UPDATE `message_resource` SET `ja`='指示重量' WHERE `id`=118;
UPDATE `message_resource` SET `ja`='最終重量' WHERE `id`=119;
UPDATE `message_resource` SET `ja`='注文重量' WHERE `id`=120;
UPDATE `message_resource` SET `ja`='サプライヤー重量' WHERE `id`=121;
UPDATE `message_resource` SET `ja`='重量タイプ' WHERE `id`=122;
UPDATE `message_resource` SET `ja`='重量差' WHERE `id`=123;
UPDATE `message_resource` SET `ja`='減重量' WHERE `id`=124;
UPDATE `message_resource` SET `ja`='取引日' WHERE `id`=125;
UPDATE `message_resource` SET `ja`='単価' WHERE `id`=126;
UPDATE `message_resource` SET `ja`='請求書番号' WHERE `id`=127;
UPDATE `message_resource` SET `ja`='SST' WHERE `id`=128;
UPDATE `message_resource` SET `ja`='配送番号' WHERE `id`=129;
UPDATE `message_resource` SET `ja`='小計' WHERE `id`=130;
UPDATE `message_resource` SET `ja`='手動重量' WHERE `id`=131;
UPDATE `message_resource` SET `ja`='はい' WHERE `id`=132;
UPDATE `message_resource` SET `ja`='いいえ' WHERE `id`=133;
UPDATE `message_resource` SET `ja`='合計金額' WHERE `id`=134;
UPDATE `message_resource` SET `ja`='製品コード' WHERE `id`=135;
UPDATE `message_resource` SET `ja`='原材料コード' WHERE `id`=136;
UPDATE `message_resource` SET `ja`='営業担当者' WHERE `id`=137;
UPDATE `message_resource` SET `ja`='新規空入口ビン' WHERE `id`=138;
UPDATE `message_resource` SET `ja`='サプライヤー名' WHERE `id`=139;
UPDATE `message_resource` SET `ja`='交換用コンテナ' WHERE `id`=140;
UPDATE `message_resource` SET `ja`='製品追加' WHERE `id`=141;
UPDATE `message_resource` SET `ja`='車両ナンバープレート' WHERE `id`=142;
UPDATE `message_resource` SET `ja`='ドラム数' WHERE `id`=143;
UPDATE `message_resource` SET `ja`='入荷' WHERE `id`=144;
UPDATE `message_resource` SET `ja`='出荷' WHERE `id`=145;
UPDATE `message_resource` SET `ja`='車両重量' WHERE `id`=146;
UPDATE `message_resource` SET `ja`='空コンテナ重量' WHERE `id`=147;
UPDATE `message_resource` SET `ja`='その他備考' WHERE `id`=148;
UPDATE `message_resource` SET `ja`='送信＆印刷' WHERE `id`=149;
UPDATE `message_resource` SET `ja`='目的地コード' WHERE `id`=150;
UPDATE `message_resource` SET `ja`='目的地名' WHERE `id`=151;
UPDATE `message_resource` SET `ja`='説明' WHERE `id`=152;
UPDATE `message_resource` SET `ja`='製品コード' WHERE `id`=153;
UPDATE `message_resource` SET `ja`='製品名' WHERE `id`=154;
UPDATE `message_resource` SET `ja`='製品価格' WHERE `id`=155;
UPDATE `message_resource` SET `ja`='バリアンスタイプ' WHERE `id`=156;
UPDATE `message_resource` SET `ja`='高' WHERE `id`=157;
UPDATE `message_resource` SET `ja`='低' WHERE `id`=158;
UPDATE `message_resource` SET `ja`='原材料追加' WHERE `id`=159;
UPDATE `message_resource` SET `ja`='原材料名' WHERE `id`=160;
UPDATE `message_resource` SET `ja`='原材料価格' WHERE `id`=161;
UPDATE `message_resource` SET `ja`='タイプ' WHERE `id`=162;
UPDATE `message_resource` SET `ja`='サプライヤーコード' WHERE `id`=163;
UPDATE `message_resource` SET `ja`='会社登録番号' WHERE `id`=164;
UPDATE `message_resource` SET `ja`='会社名' WHERE `id`=165;
UPDATE `message_resource` SET `ja`='運送業者コード' WHERE `id`=166;
UPDATE `message_resource` SET `ja`='工場名' WHERE `id`=167;
UPDATE `message_resource` SET `ja`='従業員コード' WHERE `id`=168;
UPDATE `message_resource` SET `ja`='ユーザー名' WHERE `id`=169;
UPDATE `message_resource` SET `ja`='ユーザー記録' WHERE `id`=170;
UPDATE `message_resource` SET `ja`='工場コード' WHERE `id`=171;
UPDATE `message_resource` SET `ja`='計量記録' WHERE `id`=172;
UPDATE `message_resource` SET `ja`='差異' WHERE `id`=173;

-- 13/01/2026 --
ALTER TABLE `Weight` ADD `reduce_weight_type` VARCHAR(10) NULL AFTER `nett_weight2`, ADD `reduce_weight_input` VARCHAR(100) NULL AFTER `reduce_weight_type`;

ALTER TABLE `Weight_Log` ADD `reduce_weight_type` VARCHAR(10) NULL AFTER `nett_weight2`, ADD `reduce_weight_input` VARCHAR(100) NULL AFTER `reduce_weight_type`;

ALTER TABLE `Weight_Container` ADD `reduce_weight_type` VARCHAR(10) NULL AFTER `nett_weight2`, ADD `reduce_weight_input` VARCHAR(100) NULL AFTER `reduce_weight_type`;

ALTER TABLE `Weight_Container_Log` ADD `reduce_weight_type` VARCHAR(10) NULL AFTER `nett_weight2`, ADD `reduce_weight_input` VARCHAR(100) NULL AFTER `reduce_weight_type`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight_type, reduce_weight_input, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight_type, NEW.reduce_weight_input, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight_type, reduce_weight_input, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight_type, NEW.reduce_weight_input, NEW.reduce_weight,
        NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight_type, reduce_weight_input, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.tare_weight1, NEW.tare_weight1_date, NEW.nett_weight1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight_type, NEW.reduce_weight_input, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight_type, reduce_weight_input, reduce_weight, final_weight, weight_different, weight_different_perc, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
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
        NEW.reduce_weight_type, NEW.reduce_weight_input, NEW.reduce_weight, NEW.final_weight, NEW.weight_different, NEW.weight_different_perc, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, 
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`, `ja`) VALUES
('print_slip_code', 'Print Slip', '打印单据', 'Cetak Slip', 'ஸ்லிப் அச்சிடு', '伝票印刷');
('print_with_letter_header_code', 'Print With Letter Header', '打印（含信头）', 'Cetak Dengan Kepala Surat', 'கடிதத் தலைப்புடன் அச்சிடு', 'レターヘッダー付き印刷'),
('print_without_letter_header_code', 'Print Without Letter Header', '打印（不含信头）', 'Cetak Tanpa Kepala Surat', 'கடிதத் தலைப்பின்றி அச்சிடு', 'レターヘッダーなし印刷');

-- 16/01/2026 --
INSERT INTO `message_resource` (`message_key_code`, `en`, `zh`, `my`, `ne`, `ja`) VALUES
('supply_weight_code', 'Supply Weight', '供应重量', 'Berat Bekalan', 'விநியோக எடை', '供給重量');
