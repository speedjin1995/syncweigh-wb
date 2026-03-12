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

ALTER TABLE `Weight` ADD `reject_weight` VARCHAR(100) NULL AFTER `final_weight`;
ALTER TABLE `Weight` ADD `grader_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight` ADD `grade_detail` TEXT NULL AFTER `grader_id`;
ALTER TABLE `Weight_Log` ADD `weight_id` INT(11) NOT NULL AFTER `id`;
ALTER TABLE `Weight_Log` ADD `reject_weight` VARCHAR(100) NULL AFTER `final_weight`;
ALTER TABLE `Weight_Log` ADD `grader_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight_Log` ADD `grade_detail` TEXT NULL AFTER `grader_id`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    weight_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        weight_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
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
        NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail,
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight_Container` ADD `reject_weight` VARCHAR(100) NULL AFTER `final_weight`;
ALTER TABLE `Weight_Container` ADD `grader_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight_Container` ADD `grade_detail` TEXT NULL AFTER `grader_id`;
ALTER TABLE `Weight_Container_Log` ADD `weight_container_id` INT(11) NOT NULL AFTER `id`;
ALTER TABLE `Weight_Container_Log` ADD `reject_weight` VARCHAR(100) NULL AFTER `final_weight`;
ALTER TABLE `Weight_Container_Log` ADD `grader_id` INT(11) NULL AFTER `weighbridge_id`;
ALTER TABLE `Weight_Container_Log` ADD `grade_detail` TEXT NULL AFTER `grader_id`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    weight_container_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        weight_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
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
        NEW.reduce_weight, NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail,
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.total_price, NEW.load_drum, 
        NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;


ALTER TABLE `Company` ADD `mpob_no` VARCHAR(100) NULL AFTER `package`, ADD `mpob_expiry_date` DATETIME NULL AFTER `mpob_no`, ADD `mspo_no` VARCHAR(100) NULL AFTER `mpob_expiry_date`, ADD `mspo_expiry_date` DATETIME NULL AFTER `mspo_no`;
ALTER TABLE `Company_Log` ADD `mpob_no` VARCHAR(100) NULL AFTER `package`, ADD `mpob_expiry_date` DATETIME NULL AFTER `mpob_no`, ADD `mspo_no` VARCHAR(100) NULL AFTER `mpob_expiry_date`, ADD `mspo_expiry_date` DATETIME NULL AFTER `mspo_no`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, new_reg_no, `name`, address_line_1, address_line_2, address_line_3, phone_no, fax_no, tin_no, mobile_no, package, mpob_no, mpob_expiry_date, mspo_no, mspo_expiry_date, include_price, include_container, include_display_setup, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.tin_no, NEW.mobile_no, NEW.package, NEW.mpob_no, NEW.mpob_expiry_date, NEW.mspo_no, NEW.mspo_expiry_date, NEW.include_price, NEW.include_container, NEW.include_display_setup, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 25/02/2026 --
ALTER TABLE `Company` ADD `include_grading` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `include_display_setup`;
ALTER TABLE `Company_Log` ADD `include_grading` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `include_display_setup`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, new_reg_no, `name`, address_line_1, address_line_2, address_line_3, phone_no, fax_no, tin_no, mobile_no, package, include_price, include_container, include_display_setup, include_grading, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.tin_no, NEW.mobile_no, NEW.package, NEW.include_price, NEW.include_container, NEW.include_display_setup, NEW.include_grading, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

-- 26/02/2026 --
ALTER TABLE `Vehicle` CHANGE `vehicle_weight` `vehicle_weight` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

-- 27/02/2026 --
ALTER TABLE `Users` ADD `basic_salary` VARCHAR(100) NULL DEFAULT '0' AFTER `plant_id`;
ALTER TABLE `Users_Log` ADD `basic_salary` VARCHAR(100) NULL DEFAULT '0' AFTER `plant_id`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_USER` AFTER INSERT ON `Users` FOR EACH ROW 
INSERT INTO Users_Log (
    user_id, employee_code, username, name, useremail, password, password2, password3, plant_id, allow_manual, allow_deduct, basic_salary, `status`, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.password, NEW.password2, NEW.password3, NEW.plant_id, NEW.allow_manual, NEW.allow_deduct, NEW.basic_salary, NEW.status, 1, NEW.created_by, NEW.created_date
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
        user_id, employee_code, username, name, useremail, password, password2, password3, plant_id, allow_manual, allow_deduct, basic_salary, `status`, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.password, NEW.password2, NEW.password3, NEW.plant_id, NEW.allow_manual, NEW.allow_deduct, NEW.basic_salary, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'payslip_code', 'Payslip', '工资单', 'Slip Gaji', 'சம்பளச்சீட்டு');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'pay_slip_no_code', 'Payslip No', '工资单号', 'Nombor Slip Gaji', 'சம்பளச்சீட்டு எண்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'employee_code', 'Employee', '员工', 'Pekerja', 'பணியாளர்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'payment_type_code', 'Payment Type', '付款类型', 'Jenis Pembayaran', 'கட்டண வகை');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'earnings_code', 'Earnings', '收入', 'Pendapatan', 'வருமானம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'deductions_code', 'Deductions', '扣除', 'Potongan', 'கழித்தல்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'net_pay_code', 'Net Pay', '净支付', 'Gaji Bersih', 'நிகர சம்பளம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'gross_pay_code', 'Gross Pay', '总支付', 'Gaji Kasar', 'மொத்த சம்பளம்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cheque_no_code', 'Cheque No', '支票号', 'No Cek', 'செக் எண்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'payslip_setting_code', 'Payslip Setting', '工资单设置', 'Tetapan Slip Gaji', 'சம்பளச்சீட்டு அமைப்பு');

CREATE TABLE `Payslip` (
  `id` int(11) NOT NULL,
  `payslip_no` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `cashbook_id` int(11) DEFAULT NULL,
  `earnings_detail` text NOT NULL,
  `gross_pay` varchar(100) NOT NULL,
  `deductions_detail` text NOT NULL,
  `total_deductions` varchar(100) NOT NULL,
  `net_pay` varchar(100) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` varchar(50) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Payslip` ADD PRIMARY KEY (`id`);

ALTER TABLE `Payslip` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `Payslip_Log` (
  `id` int(11) NOT NULL,
  `payslip_id` int(11) NOT NULL,
  `payslip_no` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `cashbook_id` int(11) DEFAULT NULL,
  `earnings_detail` text NOT NULL,
  `gross_pay` varchar(100) NOT NULL,
  `deductions_detail` text NOT NULL,
  `total_deductions` varchar(100) NOT NULL,
  `net_pay` varchar(100) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `event_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Payslip_Log` ADD PRIMARY KEY (`id`);

ALTER TABLE `Payslip_Log`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PAYSLIP` AFTER INSERT ON `Payslip` FOR EACH ROW INSERT INTO Payslip_Log (
    payslip_id, payslip_no, user_id, date, payment_type, cheque_no, cashbook_id, earnings_detail, gross_pay, deductions_detail, total_deductions, net_pay, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.payslip_no, NEW.user_id, NEW.date, NEW.payment_type, NEW.cheque_no, NEW.cashbook_id, NEW.earnings_detail, NEW.gross_pay, NEW.deductions_detail, NEW.total_deductions, NEW.net_pay, 1, NEW.created_by, NEW.created_date
)
$$
DELIMITER ;
DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_PAYSLIP` BEFORE UPDATE ON `Payslip` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Check if status = 1, set action_id to 3, otherwise set to 2
    IF NEW.status = 1 THEN
        SET action_value = 3;
    ELSE
        SET action_value = 2;
    END IF;

    -- Insert into Payslip_Log table
    INSERT INTO Payslip_Log (
        payslip_id, payslip_no, user_id, date, payment_type, cheque_no, cashbook_id, earnings_detail, gross_pay, deductions_detail, total_deductions, net_pay, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.payslip_no, NEW.user_id, NEW.date, NEW.payment_type, NEW.cheque_no, NEW.cashbook_id, NEW.earnings_detail, NEW.gross_pay, NEW.deductions_detail, NEW.total_deductions, NEW.net_pay, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('payslip', 1);

ALTER TABLE `Users` ADD `nric` VARCHAR(20) NULL AFTER `useremail`;
ALTER TABLE `Users_Log` ADD `nric` VARCHAR(20) NULL AFTER `useremail`;
ALTER TABLE `Users` ADD `position` VARCHAR(100) NULL AFTER `nric`;
ALTER TABLE `Users_Log` ADD `position` VARCHAR(100) NULL AFTER `nric`;
ALTER TABLE `Users` ADD `department` VARCHAR(100) NULL AFTER `position`;
ALTER TABLE `Users` ADD `is_resident` VARCHAR(1) NOT NULL DEFAULT 'Y' AFTER `nric`;
ALTER TABLE `Users_Log` ADD `is_resident` VARCHAR(1) NOT NULL DEFAULT 'Y' AFTER `nric`;
ALTER TABLE `Users` ADD `pcb_category` INT(1) NOT NULL DEFAULT '1' AFTER `basic_salary`;
ALTER TABLE `Users_Log` ADD `pcb_category` INT(1) NOT NULL DEFAULT '1' AFTER `basic_salary`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_USER` AFTER INSERT ON `Users` FOR EACH ROW 
INSERT INTO Users_Log (
    user_id, employee_code, username, name, useremail, nric, is_resident, position, user_department, password, password2, password3, plant_id, allow_manual, allow_deduct, basic_salary, pcb_category, `status`, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.nric, NEW.is_resident, NEW.position, NEW.department, NEW.password, NEW.password2, NEW.password3, NEW.plant_id, NEW.allow_manual, NEW.allow_deduct, NEW.basic_salary, NEW.pcb_category, NEW.status, 1, NEW.created_by, NEW.created_date
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
        user_id, employee_code, username, name, useremail, nric, is_resident, position, user_department, password, password2, password3, plant_id, allow_manual, allow_deduct, basic_salary, pcb_category, `status`, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.employee_code, NEW.username, NEW.name, NEW.useremail, NEW.nric, NEW.is_resident, NEW.position, NEW.department, NEW.password, NEW.password2, NEW.password3, NEW.plant_id, NEW.allow_manual, NEW.allow_deduct, NEW.basic_salary, NEW.pcb_category, NEW.status, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Company` ADD `epf` VARCHAR(10) NULL AFTER `include_grading`;
ALTER TABLE `Company_Log` ADD `epf` VARCHAR(10) NULL AFTER `include_grading`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_UPD_COMPANY` BEFORE UPDATE ON `Company` FOR EACH ROW BEGIN
    DECLARE action_value INT;

    -- Always set action_id = 2 for update
    SET action_value = 2;

    -- Insert into Company_Log table
    INSERT INTO Company_Log (
        company_id, company_code, company_reg_no, new_reg_no, `name`, address_line_1, address_line_2, address_line_3, phone_no, fax_no, tin_no, mobile_no, package, include_price, include_container, include_display_setup, include_grading, epf, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.company_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.tin_no, NEW.mobile_no, NEW.package, NEW.include_price, NEW.include_container, NEW.include_display_setup, NEW.include_grading, NEW.epf, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('socso', '[
  {"no":"1","min":"0","max":"30","employer":"0.40","employee":"0.10"},
  {"no":"2","min":"31","max":"50","employer":"0.70","employee":"0.20"},
  {"no":"3","min":"51","max":"70","employer":"1.10","employee":"0.30"},
  {"no":"4","min":"71","max":"100","employer":"1.50","employee":"0.40"},
  {"no":"5","min":"101","max":"140","employer":"2.10","employee":"0.60"},
  {"no":"6","min":"141","max":"200","employer":"2.95","employee":"0.85"},
  {"no":"7","min":"201","max":"300","employer":"4.35","employee":"1.25"},
  {"no":"8","min":"301","max":"400","employer":"6.15","employee":"1.75"},
  {"no":"9","min":"401","max":"500","employer":"7.85","employee":"2.25"},
  {"no":"10","min":"501","max":"600","employer":"9.65","employee":"2.75"},
  {"no":"11","min":"601","max":"700","employer":"11.35","employee":"3.25"},
  {"no":"12","min":"701","max":"800","employer":"13.15","employee":"3.75"},
  {"no":"13","min":"801","max":"900","employer":"14.85","employee":"4.25"},
  {"no":"14","min":"901","max":"1000","employer":"16.65","employee":"4.75"},
  {"no":"15","min":"1001","max":"1100","employer":"18.35","employee":"5.25"},
  {"no":"16","min":"1101","max":"1200","employer":"20.15","employee":"5.75"},
  {"no":"17","min":"1201","max":"1300","employer":"21.85","employee":"6.25"},
  {"no":"18","min":"1301","max":"1400","employer":"23.65","employee":"6.75"},
  {"no":"19","min":"1401","max":"1500","employer":"25.35","employee":"7.25"},
  {"no":"20","min":"1501","max":"1600","employer":"27.15","employee":"7.75"},
  {"no":"21","min":"1601","max":"1700","employer":"28.85","employee":"8.25"},
  {"no":"22","min":"1701","max":"1800","employer":"30.65","employee":"8.75"},
  {"no":"23","min":"1801","max":"1900","employer":"32.35","employee":"9.25"},
  {"no":"24","min":"1901","max":"2000","employer":"34.15","employee":"9.75"},
  {"no":"25","min":"2001","max":"2100","employer":"35.85","employee":"10.25"},
  {"no":"26","min":"2101","max":"2200","employer":"37.65","employee":"10.75"},
  {"no":"27","min":"2201","max":"2300","employer":"39.35","employee":"11.25"},
  {"no":"28","min":"2301","max":"2400","employer":"41.15","employee":"11.75"},
  {"no":"29","min":"2401","max":"2500","employer":"42.85","employee":"12.25"},
  {"no":"30","min":"2501","max":"2600","employer":"44.65","employee":"12.75"},
  {"no":"31","min":"2601","max":"2700","employer":"46.35","employee":"13.25"},
  {"no":"32","min":"2701","max":"2800","employer":"48.15","employee":"13.75"},
  {"no":"33","min":"2801","max":"2900","employer":"49.85","employee":"14.25"},
  {"no":"34","min":"2901","max":"3000","employer":"51.65","employee":"14.75"},
  {"no":"35","min":"3001","max":"3100","employer":"53.35","employee":"15.25"},
  {"no":"36","min":"3101","max":"3200","employer":"55.15","employee":"15.75"},
  {"no":"37","min":"3201","max":"3300","employer":"56.85","employee":"16.25"},
  {"no":"38","min":"3301","max":"3400","employer":"58.65","employee":"16.75"},
  {"no":"39","min":"3401","max":"3500","employer":"60.35","employee":"17.25"},
  {"no":"40","min":"3501","max":"3600","employer":"62.15","employee":"17.75"},
  {"no":"41","min":"3601","max":"3700","employer":"63.85","employee":"18.25"},
  {"no":"42","min":"3701","max":"3800","employer":"65.65","employee":"18.75"},
  {"no":"43","min":"3801","max":"3900","employer":"67.35","employee":"19.25"},
  {"no":"44","min":"3901","max":"4000","employer":"69.15","employee":"19.75"},
  {"no":"45","min":"4001","max":"4100","employer":"70.85","employee":"20.25"},
  {"no":"46","min":"4101","max":"4200","employer":"72.65","employee":"20.75"},
  {"no":"47","min":"4201","max":"4300","employer":"74.35","employee":"21.25"},
  {"no":"48","min":"4301","max":"4400","employer":"76.15","employee":"21.75"},
  {"no":"49","min":"4401","max":"4500","employer":"77.85","employee":"22.25"},
  {"no":"50","min":"4501","max":"4600","employer":"79.65","employee":"22.75"},
  {"no":"51","min":"4601","max":"4700","employer":"81.35","employee":"23.25"},
  {"no":"52","min":"4701","max":"4800","employer":"83.15","employee":"23.75"},
  {"no":"53","min":"4801","max":"4900","employer":"84.85","employee":"24.25"},
  {"no":"54","min":"4901","max":"5000","employer":"86.65","employee":"24.75"},
  {"no":"55","min":"5001","max":"5100","employer":"88.35","employee":"25.25"},
  {"no":"56","min":"5101","max":"5200","employer":"90.15","employee":"25.75"},
  {"no":"57","min":"5201","max":"5300","employer":"91.85","employee":"26.25"},
  {"no":"58","min":"5301","max":"5400","employer":"93.65","employee":"26.75"},
  {"no":"59","min":"5401","max":"5500","employer":"95.35","employee":"27.25"},
  {"no":"60","min":"5501","max":"5600","employer":"97.15","employee":"27.75"},
  {"no":"61","min":"5601","max":"5700","employer":"98.85","employee":"28.25"},
  {"no":"62","min":"5701","max":"5800","employer":"100.65","employee":"28.75"},
  {"no":"63","min":"5801","max":"5900","employer":"102.35","employee":"29.25"},
  {"no":"64","min":"5901","max":"6000","employer":"104.15","employee":"29.75"},
  {"no":"65","min":"6001","max":"","employer":"104.15","employee":"29.75"}
]');

INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('eis', '[
  {"no":"1","min":"0","max":"30","employer":"0.05","employee":"0.05"},
  {"no":"2","min":"31","max":"50","employer":"0.10","employee":"0.10"},
  {"no":"3","min":"51","max":"70","employer":"0.15","employee":"0.15"},
  {"no":"4","min":"71","max":"100","employer":"0.20","employee":"0.20"},
  {"no":"5","min":"101","max":"140","employer":"0.25","employee":"0.25"},
  {"no":"6","min":"141","max":"200","employer":"0.35","employee":"0.35"},
  {"no":"7","min":"201","max":"300","employer":"0.50","employee":"0.50"},
  {"no":"8","min":"301","max":"400","employer":"0.70","employee":"0.70"},
  {"no":"9","min":"401","max":"500","employer":"0.90","employee":"0.90"},
  {"no":"10","min":"501","max":"600","employer":"1.10","employee":"1.10"},
  {"no":"11","min":"601","max":"700","employer":"1.30","employee":"1.30"},
  {"no":"12","min":"701","max":"800","employer":"1.50","employee":"1.50"},
  {"no":"13","min":"801","max":"900","employer":"1.70","employee":"1.70"},
  {"no":"14","min":"901","max":"1000","employer":"1.90","employee":"1.90"},
  {"no":"15","min":"1001","max":"1100","employer":"2.10","employee":"2.10"},
  {"no":"16","min":"1101","max":"1200","employer":"2.30","employee":"2.30"},
  {"no":"17","min":"1201","max":"1300","employer":"2.50","employee":"2.50"},
  {"no":"18","min":"1301","max":"1400","employer":"2.70","employee":"2.70"},
  {"no":"19","min":"1401","max":"1500","employer":"2.90","employee":"2.90"},
  {"no":"20","min":"1501","max":"1600","employer":"3.10","employee":"3.10"},
  {"no":"21","min":"1601","max":"1700","employer":"3.30","employee":"3.30"},
  {"no":"22","min":"1701","max":"1800","employer":"3.50","employee":"3.50"},
  {"no":"23","min":"1801","max":"1900","employer":"3.70","employee":"3.70"},
  {"no":"24","min":"1901","max":"2000","employer":"3.90","employee":"3.90"},
  {"no":"25","min":"2001","max":"2100","employer":"4.10","employee":"4.10"},
  {"no":"26","min":"2101","max":"2200","employer":"4.30","employee":"4.30"},
  {"no":"27","min":"2201","max":"2300","employer":"4.50","employee":"4.50"},
  {"no":"28","min":"2301","max":"2400","employer":"4.70","employee":"4.70"},
  {"no":"29","min":"2401","max":"2500","employer":"4.90","employee":"4.90"},
  {"no":"30","min":"2501","max":"2600","employer":"5.10","employee":"5.10"},
  {"no":"31","min":"2601","max":"2700","employer":"5.30","employee":"5.30"},
  {"no":"32","min":"2701","max":"2800","employer":"5.50","employee":"5.50"},
  {"no":"33","min":"2801","max":"2900","employer":"5.70","employee":"5.70"},
  {"no":"34","min":"2901","max":"3000","employer":"5.90","employee":"5.90"},
  {"no":"35","min":"3001","max":"3100","employer":"6.10","employee":"6.10"},
  {"no":"36","min":"3101","max":"3200","employer":"6.30","employee":"6.30"},
  {"no":"37","min":"3201","max":"3300","employer":"6.50","employee":"6.50"},
  {"no":"38","min":"3301","max":"3400","employer":"6.70","employee":"6.70"},
  {"no":"39","min":"3401","max":"3500","employer":"6.90","employee":"6.90"},
  {"no":"40","min":"3501","max":"3600","employer":"7.10","employee":"7.10"},
  {"no":"41","min":"3601","max":"3700","employer":"7.30","employee":"7.30"},
  {"no":"42","min":"3701","max":"3800","employer":"7.50","employee":"7.50"},
  {"no":"43","min":"3801","max":"3900","employer":"7.70","employee":"7.70"},
  {"no":"44","min":"3901","max":"4000","employer":"7.90","employee":"7.90"},
  {"no":"45","min":"4001","max":"4100","employer":"8.10","employee":"8.10"},
  {"no":"46","min":"4101","max":"4200","employer":"8.30","employee":"8.30"},
  {"no":"47","min":"4201","max":"4300","employer":"8.50","employee":"8.50"},
  {"no":"48","min":"4301","max":"4400","employer":"8.70","employee":"8.70"},
  {"no":"49","min":"4401","max":"4500","employer":"8.90","employee":"8.90"},
  {"no":"50","min":"4501","max":"4600","employer":"9.10","employee":"9.10"},
  {"no":"51","min":"4601","max":"4700","employer":"9.30","employee":"9.30"},
  {"no":"52","min":"4701","max":"4800","employer":"9.50","employee":"9.50"},
  {"no":"53","min":"4801","max":"4900","employer":"9.70","employee":"9.70"},
  {"no":"54","min":"4901","max":"5000","employer":"9.90","employee":"9.90"},
  {"no":"55","min":"5001","max":"5100","employer":"10.10","employee":"10.10"},
  {"no":"56","min":"5101","max":"5200","employer":"10.30","employee":"10.30"},
  {"no":"57","min":"5201","max":"5300","employer":"10.50","employee":"10.50"},
  {"no":"58","min":"5301","max":"5400","employer":"10.70","employee":"10.70"},
  {"no":"59","min":"5401","max":"5500","employer":"10.90","employee":"10.90"},
  {"no":"60","min":"5501","max":"5600","employer":"11.10","employee":"11.10"},
  {"no":"61","min":"5601","max":"5700","employer":"11.30","employee":"11.30"},
  {"no":"62","min":"5701","max":"5800","employer":"11.50","employee":"11.50"},
  {"no":"63","min":"5801","max":"5900","employer":"11.70","employee":"11.70"},
  {"no":"64","min":"5901","max":"6000","employer":"11.90","employee":"11.90"},
  {"no":"65","min":"6001","max":"","employer":"11.90","employee":"11.90"}
]');

INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('tax', '[
  {"no":"1","min":"5001","max":"20000","m":"5000","r":"1","b1":"-400","b2":"-800"},
  {"no":"2","min":"20001","max":"35000","m":"20000","r":"3","b1":"-250","b2":"-650"},
  {"no":"3","min":"35001","max":"50000","m":"35000","r":"6","b1":"600","b2":"600"},
  {"no":"4","min":"50001","max":"70000","m":"50000","r":"11","b1":"1500","b2":"1500"},
  {"no":"5","min":"70001","max":"100000","m":"70000","r":"19","b1":"3700","b2":"3700"},
  {"no":"6","min":"100001","max":"400000","m":"100000","r":"25","b1":"9400","b2":"9400"},
  {"no":"7","min":"400001","max":"600000","m":"400000","r":"26","b1":"84400","b2":"84400"},
  {"no":"8","min":"600001","max":"2000000","m":"600000","r":"28","b1":"136400","b2":"136400"},
  {"no":"9","min":"2000001","max":"","m":"2000000","r":"30","b1":"528400","b2":"528400"}
]');

INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('ind_relief', 9000); -- Individual relief
INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('non_res_epf', 30); -- Non-resident EPF contribution rate (30% of gross salary)
INSERT INTO `miscellaneous` (`name`, `value`) VALUES ('ind_epf_relief', 4000); -- EPF relief for individual (capped at RM4000 per year)

-- 10/03/2026 --
ALTER TABLE `Product` ADD `is_default` INT(1) NOT NULL DEFAULT '0' AFTER `low`;
ALTER TABLE `Product_Log` ADD `is_default` INT(1) NOT NULL DEFAULT '0' AFTER `low`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_PRODUCT` AFTER INSERT ON `Product` FOR EACH ROW 
INSERT INTO Product_Log (
    product_id, product_code, name, price, description, variance, high, low, is_default, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.product_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, NEW.is_default, 1, NEW.created_by, NEW.created_date
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
    product_id, product_code, name, price, description, variance, high, low, is_default, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.product_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, NEW.is_default, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Raw_Mat` ADD `is_default` INT(1) NOT NULL DEFAULT '0' AFTER `low`;
ALTER TABLE `Raw_Mat_Log` ADD `is_default` INT(1) NOT NULL DEFAULT '0' AFTER `low`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_RAW_MAT` AFTER INSERT ON `Raw_Mat` FOR EACH ROW 
INSERT INTO Raw_Mat_Log (
    raw_mat_id, raw_mat_code, name, price, description, variance, high, low, is_default, type, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.raw_mat_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, NEW.is_default, NEW.type, 1, NEW.created_by, NEW.created_date
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
        raw_mat_id, raw_mat_code, name, price, description, variance, high, low, is_default, type, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.raw_mat_code, NEW.name, NEW.price, NEW.description, NEW.variance, NEW.high, NEW.low, NEW.is_default, NEW.type, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'voucher_date_code', 'Voucher Date', '凭证日期', 'Tarikh Baucar', 'வவுச்சர் தேதி');

ALTER TABLE `Customer` ADD `payment_by` VARCHAR(50) NULL AFTER `payment_term`, ADD `harvesting_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `payment_by`, ADD `transport_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `harvesting_price`;
ALTER TABLE `Customer_Log` ADD `payment_by` VARCHAR(50) NULL AFTER `payment_term`, ADD `harvesting_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `payment_by`, ADD `transport_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `harvesting_price`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_CUSTOMER` AFTER INSERT ON `Customer` FOR EACH ROW 
INSERT INTO Customer_Log (
    customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, payment_by, harvesting_price, transport_price, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, NEW.payment_by, NEW.harvesting_price, NEW.transport_price, 1, NEW.created_by, NEW.created_date
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
        customer_id, customer_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, payment_by, harvesting_price, transport_price, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.customer_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.address_line_4, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, NEW.payment_by, NEW.harvesting_price, NEW.transport_price, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Supplier` ADD `payment_by` VARCHAR(50) NULL AFTER `payment_term`, ADD `harvesting_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `payment_by`, ADD `transport_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `harvesting_price`;
ALTER TABLE `Supplier_Log` ADD `payment_by` VARCHAR(50) NULL AFTER `payment_term`, ADD `harvesting_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `payment_by`, ADD `transport_price` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `harvesting_price`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_SUPPLIER` AFTER INSERT ON `Supplier` FOR EACH ROW 
INSERT INTO Supplier_Log (
    supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, payment_by, harvesting_price, transport_price, customer_id, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, NEW.payment_by, NEW.harvesting_price, NEW.transport_price, NEW.customer_id, 1, NEW.created_by, NEW.created_date
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
        supplier_id, supplier_code, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, contact_name, ic_no, tin_no, mpob, mspo_no, payment_term, payment_by, harvesting_price, transport_price, customer_id, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.supplier_code, NEW.company_reg_no, NEW.new_reg_no, NEW.name, NEW.address_line_1, NEW.address_line_2, NEW.address_line_3, NEW.phone_no, NEW.fax_no, NEW.contact_name, NEW.ic_no, NEW.tin_no, NEW.mpob, NEW.mspo_no, NEW.payment_term, NEW.payment_by, NEW.harvesting_price, NEW.transport_price, NEW.customer_id, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'payment_by_code', 'Payment By', '付款方式', 'Bayaran Oleh', 'பணம் செலுத்துபவர்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cheque_code', 'Cheque', '支票', 'Cek', 'செக்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'harvesting_price_code', 'Harvesting Price', '收割价格', 'Harga Penuaian', 'அறுவடை விலை');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'transport_price_code', 'Transport Price', '运输价格', 'Harga Pengangkutan', 'போக்குவரத்து விலை');

ALTER TABLE `Weight` ADD `harvesting_price` VARCHAR(10) NULL AFTER `sst`, ADD `transport_price` VARCHAR(10) NULL AFTER `harvesting_price`;
ALTER TABLE `Weight_Log` ADD `harvesting_price` VARCHAR(10) NULL AFTER `sst`, ADD `transport_price` VARCHAR(10) NULL AFTER `harvesting_price`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT` AFTER INSERT ON `Weight` FOR EACH ROW 
INSERT INTO Weight_Log (
    weight_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, harvesting_price, transport_price, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.harvesting_price, NEW.transport_price, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        weight_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, harvesting_price, transport_price, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
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
        NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail,
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.harvesting_price, NEW.transport_price, NEW.total_price, 
        NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

ALTER TABLE `Weight_Container` ADD `harvesting_price` VARCHAR(10) NULL AFTER `sst`, ADD `transport_price` VARCHAR(10) NULL AFTER `harvesting_price`;
ALTER TABLE `Weight_Container_Log` ADD `harvesting_price` VARCHAR(10) NULL AFTER `sst`, ADD `transport_price` VARCHAR(10) NULL AFTER `harvesting_price`;

DELIMITER $$
CREATE OR REPLACE TRIGGER `TRG_INS_WEIGHT_CONTAINER` AFTER INSERT ON `Weight_Container` FOR EACH ROW INSERT INTO Weight_Container_Log (
    weight_container_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, harvesting_price, transport_price, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
) 
VALUES (
    NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, NEW.lorry_plate_no1, NEW.lorry_plate_no2, NEW.supplier_weight, NEW.order_weight, NEW.plant_code, NEW.plant_name, NEW.site_code, NEW.site_name, NEW.agent_code, NEW.agent_name, NEW.customer_code, NEW.customer_name, NEW.supplier_code, NEW.supplier_name, NEW.product_code, NEW.product_name, NEW.product_description, NEW.ex_del, NEW.raw_mat_code, NEW.raw_mat_name, NEW.container_no, NEW.invoice_no, NEW.purchase_order, NEW.delivery_no, NEW.transporter_code, NEW.transporter, NEW.destination_code, NEW.destination, NEW.remarks, NEW.gross_weight1, NEW.gross_weight1_date, NEW.gross_weight_by1, NEW.gross_deduction1, NEW.tare_weight1, NEW.tare_weight1_date, NEW.tare_weight_by1, NEW.tare_deduction1, NEW.nett_weight1, NEW.nett_deduction1, NEW.lorry_no2_weight, NEW.empty_container2_weight, NEW.replacement_container, NEW.gross_weight2, NEW.gross_weight2_date, NEW.tare_weight2, NEW.tare_weight2_date, NEW.nett_weight2, NEW.reduce_weight, NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail, NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.harvesting_price, NEW.transport_price, NEW.total_price, NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, 1, NEW.created_by, NEW.created_date
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
        weight_id, transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, plant_code, plant_name, site_code, site_name, agent_code, agent_name, customer_code, customer_name, supplier_code, supplier_name, product_code, product_name, product_description, ex_del, raw_mat_code,raw_mat_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, gross_deduction1, tare_weight1, tare_weight1_date, tare_weight_by1, tare_deduction1, nett_weight1, nett_deduction1, lorry_no2_weight, empty_container2_weight, replacement_container, gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, reject_weight, weight_different, is_complete, is_cancel, is_approved, manual_weight, indicator_id, weighbridge_id, grader_id, grade_detail, indicator_id_2, unit_price, sub_total, sst, harvesting_price, transport_price, total_price, load_drum, no_of_drum, status, approved_by, approved_reason, action_id, action_by, event_date
    ) 
    VALUES (
        NEW.id, NEW.transaction_id, NEW.transaction_status, NEW.weight_type, NEW.transaction_date, 
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
        NEW.reduce_weight, NEW.final_weight, NEW.reject_weight, NEW.weight_different, NEW.is_complete, NEW.is_cancel, 
        NEW.is_approved, NEW.manual_weight, NEW.indicator_id, NEW.weighbridge_id, NEW.grader_id, NEW.grade_detail,
        NEW.indicator_id_2, NEW.unit_price, NEW.sub_total, NEW.sst, NEW.harvesting_price, NEW.transport_price, NEW.total_price, 
        NEW.load_drum, NEW.no_of_drum, NEW.status, NEW.approved_by, NEW.approved_reason, action_value, NEW.modified_by, NEW.modified_date
    );
END
$$
DELIMITER ;

INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cash_book_receiving_code', 'Cash Book Receiving', '现金簿收款', 'Penerimaan Buku Tunai', 'பணம் புத்தக பெறுதல்');
INSERT INTO `message_resource` (`id`, `message_key_code`, `en`, `zh`, `my`, `ne`) VALUES (NULL, 'cash_book_summary_code', 'Cash Book Summary', '现金簿摘要', 'Ringkasan Buku Tunai', 'பணம் புத்தக சுருக்கம்');
