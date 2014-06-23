<?php
$installer = $this;
 
$installer->startSetup();
 
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('coinbench_crypto_transactions')};
CREATE TABLE {$this->getTable('coinbench_crypto_transactions')} (
  `transaction_id` int(11) NOT NULL auto_increment,
  `order_id` varchar(25) default NULL,
  `currency` varchar(3) default NULL,
  `value` decimal(12,8) NOT NULL,
  `address` varchar(25) default NULL,
  `status` int(11) default NULL,
  `created` timestamp NULL default CURRENT_TIMESTAMP,
  `completed` timestamp NULL default NULL,
  PRIMARY KEY  (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    ");
 
$installer->endSetup();
