CREATE TABLE IF NOT EXISTS `#__newsletter_history` ( 
	`id` INT NOT NULL AUTO_INCREMENT ,
	`type` INT(1) NOT NULL ,
	`article_recipe_id` INT NOT NULL ,
	`timestamp` TIMESTAMP NOT NULL ,
	`position` INT(1) NOT NULL ,
	PRIMARY KEY (`id`) 
) DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__newsletter_healthy_news` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`date` DATE NOT NULL DEFAULT '0000-00-00',
`subject` VARCHAR(255)  NOT NULL ,
`featured_article` INT(11)  NOT NULL ,
`featured_recipe` INT(11)  NOT NULL ,
`article2` INT(11)  NOT NULL ,
`article3` INT(11)  NOT NULL ,
`article4` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Newsletter','com_newsletter.newsletter','{"special":{"dbtable":"#__newsletter_healthy_news","key":"id","type":"Newsletter","prefix":"TH Daily Newsletter ManagerTable"}}', '{"formFile":"administrator\/components\/com_newsletter\/models\/forms\/newsletter.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_newsletter.newsletter')
) LIMIT 1;
