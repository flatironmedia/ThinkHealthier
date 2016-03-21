CREATE TABLE IF NOT EXISTS `#__yoorecipe` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`asset_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.' ,
`access` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '1' ,
`category_id` INT( 11 ) NOT NULL,
`created_by` INT( 10 ) NOT NULL,
`user_id` INT( 11 ) NOT NULL,
`title` VARCHAR( 255 ) NOT NULL ,
`alias` VARCHAR( 255 ) NOT NULL ,
`description` VARCHAR( 5120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`preparation` LONGTEXT NOT NULL ,
`notes` LONGTEXT NOT NULL ,
`servings_type` VARCHAR( 1 ) NOT NULL ,
`serving_type_id` INT( 11 ) NOT NULL, 
`nb_persons` TINYINT NOT NULL ,
`difficulty` TINYINT NOT NULL ,
`cost` TINYINT NOT NULL ,
`sugar` DOUBLE NULL DEFAULT NULL,
`carbs` DOUBLE NULL DEFAULT NULL,
`fat` DOUBLE NULL DEFAULT NULL,
`saturated_fat` DOUBLE NULL DEFAULT NULL,
`cholesterol` DOUBLE NULL DEFAULT NULL,
`proteins` DOUBLE NULL DEFAULT NULL,
`fibers` DOUBLE NULL DEFAULT NULL,
`salt` DOUBLE NULL DEFAULT NULL,
`kcal` int(11) DEFAULT NULL,
`kjoule` int(11) DEFAULT NULL,
`diet` tinyint(1) DEFAULT NULL,
`veggie` tinyint(1) DEFAULT NULL,
`gluten_free` tinyint(1) DEFAULT NULL,
`lactose_free` tinyint(1) DEFAULT NULL,
`seasons` VARCHAR(50) DEFAULT NULL,
`creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`checked_out` int(11) unsigned NOT NULL DEFAULT '0',
`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`preparation_time` INT(4) NOT NULL ,
`cook_time` INT( 4 ) NOT NULL,
`wait_time` INT( 4 ) NOT NULL,
`featured` TINYINT( 1 ) NOT NULL,
`picture` VARCHAR( 255 ) NOT NULL ,
`video` VARCHAR( 255 ) NOT NULL ,
`serving_size` VARCHAR( 255 ) NULL ,
`published` BOOL NOT NULL ,
`validated` BOOL NOT NULL ,
`use_slider` TINYINT( 1 ) NOT NULL DEFAULT 1,
`nb_views` INT NOT NULL,
`note` DOUBLE NULL DEFAULT  '0',
`metakey` TEXT NULL,
`metadata` TEXT NULL,
`price` DOUBLE NULL,
`cuisine` VARCHAR(45) DEFAULT 'LATIN',
`language` CHAR(7) NOT NULL DEFAULT '*'
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_reviews` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`recipe_id` INT( 11 ) NOT NULL ,
`note` TINYINT NOT NULL ,
`author` VARCHAR( 255 ) NOT NULL ,
`user_id` INT( 11 ) NULL ,
`email` VARCHAR( 255 ) NOT NULL ,
`ip_address` VARCHAR( 50 ) NOT NULL default 'unknown',
`comment` LONGTEXT NOT NULL ,
`published` BOOL NOT NULL ,
`abuse` BOOL NOT NULL , 
`creation_date` TIMESTAMP NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_serving_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL,
  `ordering` INT(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL,
  `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_ingredients` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`recipe_id` INT( 11 ) NOT NULL ,
`group_id` INT( 11 ) NOT NULL ,
`ordering` INT( 11 ) ,
`quantity` DOUBLE NOT NULL ,
`unit` VARCHAR( 255 ) NOT NULL ,
`description` VARCHAR( 255 ) NOT NULL,
`migrated` tinyint(1)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_categories` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`recipe_id` int(11) NOT NULL,
	`cat_id` INT( 11 ) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_favourites` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`recipe_id` INT( 11 ) NOT NULL ,
	`user_id` INT( 11 ) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_ingredients_groups` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`recipe_id` int(11) NOT NULL,
	`label` VARCHAR(50) NOT NULL,
    `ordering` INT(11) DEFAULT NULL,
	`to_delete` tinyint(1),
	PRIMARY KEY (`id`)
)  ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_seasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `month_id` enum('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC','SPRING','WINTER','AUTUMN','SUMMER','ALL') NOT NULL DEFAULT  'ALL',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `#__yoorecipe` ADD INDEX ( `published`,`validated`);
ALTER TABLE `#__yoorecipe_categories` ADD INDEX ( `recipe_id`, `cat_id` );
ALTER TABLE `#__yoorecipe_favourites` ADD INDEX ( `recipe_id`, `user_id` );

CREATE TABLE IF NOT EXISTS `#__yoorecipe_shoppinglists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL, 
  `infos` TEXT NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_shoppinglist_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sl_id` int(11) NOT NULL,
  `quantity` DOUBLE NOT NULL ,
  `description` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `infos` text NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_meals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meal_date` datetime NOT NULL,
  `nb_servings` int(4) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_mealplanners_queues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_cuisines` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`code` varchar(255) NOT NULL,
	`published` TINYINT(1) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('YooRecipe', 'com_yoorecipe.recipe', '{"special":{"dbtable":"#__yoorecipe","key":"id","type":"YooRecipe","prefix":"YooRecipeTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}', '', '{\n  "common": {\n    "core_content_item_id": "id",\n    "core_title": "title",\n    "core_state": "published",\n    "core_alias": "alias",\n    "core_created_time": "creation_date",\n    "core_modified_time": "null",\n    "core_body": "description",\n    "core_hits": "nb_views",\n    "core_publish_up": "publish_up",\n    "core_publish_down": "publish_down",\n    "core_access": "access",\n    "core_params": "null",\n    "core_featured": "featured",\n    "core_metadata": "metadata",\n    "core_language": "language",\n    "core_images": "picture",\n    "core_urls": "null",\n    "core_version": "null",\n    "core_ordering": "null",\n    "core_metakey": "metakey",\n    "core_metadesc": "metadesc",\n    "core_catid": "null",\n    "core_xreference": "null",\n    "asset_id": "null"\n  },\n  "special": {\n  }\n}', 'JHtmlYooRecipeHelperRoute::getRecipeRoute', NULL),
('YooRecipe Category', 'com_yoorecipe.category', '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"nb_views","core_publish_up":"publish_up","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special": {"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}', '', '{"formFile":"administrator/components/com_categories/models/forms/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}');

INSERT INTO `#__yoorecipe_serving_types` (`code`, `published`, `ordering`, `creation_date`) values ('PERSONS', 1, 1, NOW());
INSERT INTO `#__yoorecipe_serving_types` (`code`, `published`, `ordering`, `creation_date`) values ('BATCHES', 1, 2, NOW());
INSERT INTO `#__yoorecipe_serving_types` (`code`, `published`, `ordering`, `creation_date`) values ('SERVINGS', 1, 3, NOW());
INSERT INTO `#__yoorecipe_serving_types` (`code`, `published`, `ordering`, `creation_date`) values ('DOZENS', 1, 4, NOW());

INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('AMERICAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('MEXICAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('ASIAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('KOREAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('FRENCH', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('TEX_MEX', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('INDIAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('HUNGARIAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('ITALIAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('MORROCAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('MEDITERANEAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('IRISH', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('THAI', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('CHINESE', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('CARIBBEAN', 1);
INSERT INTO `#__yoorecipe_cuisines` (`code`, `published`) values ('LATIN', 1);