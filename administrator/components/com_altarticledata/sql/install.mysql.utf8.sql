CREATE TABLE IF NOT EXISTS `#__altarticledata_data` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`article_id` INT(11)  NOT NULL ,
`article_title` VARCHAR(255)  NOT NULL ,
`headline` VARCHAR(255)  NOT NULL ,
`intro` TEXT NOT NULL ,
`custom1` VARCHAR(255)  NOT NULL ,
`custom2` VARCHAR(255)  NOT NULL ,
`custom3` TEXT NOT NULL ,
`custom4` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

