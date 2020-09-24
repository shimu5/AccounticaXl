ALTER TABLE `accounts`
MODIFY COLUMN `cur_id`  varchar(5) NOT NULL DEFAULT '0' AFTER `type`;

ALTER TABLE `ledgers`
MODIFY COLUMN `cur_id`  varchar(5) NOT NULL DEFAULT '0' AFTER `balance_after`;

ALTER TABLE `ledgers`
MODIFY COLUMN `deposit_cur_id`  varchar(5) NOT NULL DEFAULT '0' AFTER `deposit`;

ALTER TABLE `rates`
MODIFY COLUMN `cur_id`  varchar(5) NOT NULL DEFAULT '0' AFTER `base_cur_id`;

ALTER TABLE `rates`
MODIFY COLUMN `base_cur_id`  varchar(5) NOT NULL DEFAULT '0' AFTER `rate`;

ALTER TABLE `pending_ledgers`
MODIFY COLUMN `cur_id`  varchar(5) NOT NULL AFTER `balance_after`;

ALTER TABLE `curs`
ADD COLUMN `code`  varchar(5) NOT NULL AFTER `name`;
ALTER TABLE `curs`
MODIFY COLUMN `code`  varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `name`,
MODIFY COLUMN `sign`  varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `code`;

ALTER TABLE `sync_resellers` ADD `status` TINYINT( 3 ) NOT NULL DEFAULT '0' COMMENT '0=Synchronized; 1=Accepted';
ALTER TABLE `sync_gateways` ADD `status` TINYINT( 3 ) NOT NULL DEFAULT '0' COMMENT '0=Synchronized; 1=Accepted' AFTER `id`;

ALTER TABLE `pending_ledgers`
MODIFY COLUMN `deposit_cur_id`  varchar(5) NOT NULL DEFAULT '0' AFTER `deposit`;