<?php
$installer = $this;
 
$installer->startSetup();
 
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('coinbench_crypto_transactions')};
CREATE TABLE {$this->getTable('coinbench_crypto_transactions')} (
  `transaction_id` int(11) NOT NULL auto_increment,
  `order_id` varchar(25) default NULL,
  `address` varchar(50) default NULL,
  `status` int(11) default NULL,
  `message` varchar(150) DEFAULT NULL,
  `created` timestamp NULL default CURRENT_TIMESTAMP,
  `completed` timestamp NULL default NULL,
  PRIMARY KEY  (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
ALTER TABLE `{$this->getTable('sales/quote_payment')}` ADD `crypto_currency` VARCHAR(20) NOT NULL;
ALTER TABLE `{$this->getTable('sales/quote_payment')}` ADD `crypto_amount` DECIMAL(12,8) NOT NULL;
ALTER TABLE `{$this->getTable('sales/order_payment')}` ADD `crypto_currency` VARCHAR(20) NOT NULL;
ALTER TABLE `{$this->getTable('sales/order_payment')}` ADD `crypto_amount` DECIMAL(12,8) NOT NULL;

    ");
 
$installer->endSetup();
