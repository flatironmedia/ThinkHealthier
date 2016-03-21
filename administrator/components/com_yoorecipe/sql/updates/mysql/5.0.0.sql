CREATE TABLE IF NOT EXISTS `#__yoorecipe_serving_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `lang` VARCHAR(5) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `label` VARCHAR(50) NOT NULL,
  `ordering` INT(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL,
  `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

update `#__yoorecipe` set video = CONCAT('http://www.youtube.com/v/', video) where video != '';
update `#__menu` set link = 'index.php?option=com_yoorecipe&view=meals' where link = 'index.php?option=com_yoorecipe&view=mealplanner';

ALTER TABLE  `#__yoorecipe_seasons` CHANGE  `month_id`  `month_id` ENUM(  'JAN',  'FEB',  'MAR',  'APR',  'MAY',  'JUN',  'JUL',  'AUG',  'SEP',  'OCT',  'NOV',  'DEC', 'SPRING',  'WINTER',  'AUTUMN',  'SUMMER',  'ALL' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'ALL';

alter table `#__yoorecipe` add column `is_draft` TINYINT( 1 ) NOT NULL DEFAULT 1 after `validated`;
alter table `#__yoorecipe` add column `use_slider` TINYINT( 1 ) NOT NULL DEFAULT 1 after `is_draft`;
alter table `#__yoorecipe` add column `serving_type_id` INT( 11 ) NOT NULL after `servings_type`;
alter table `#__yoorecipe` add column `cholesterol` DOUBLE NULL DEFAULT NULL after `saturated_fat`;
alter table `#__yoorecipe` add column `cuisine` VARCHAR(45) DEFAULT 'LATIN' after `price`;
ALTER TABLE `#__yoorecipe` CHANGE  `note`  `note` DOUBLE NULL DEFAULT  '0';
update `#__yoorecipe` set is_draft = 0;

rename table `#__yoorecipe_rating` to `#__yoorecipe_reviews`;
rename table `#__yoorecipe_mealplanners` to `#__yoorecipe_meals`;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('YooRecipe', 'com_yoorecipe.recipe', '{"special":{"dbtable":"#__yoorecipe","key":"id","type":"YooRecipe","prefix":"YooRecipeTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}', '', '{\n  "common": {\n    "core_content_item_id": "id",\n    "core_title": "title",\n    "core_state": "published",\n    "core_alias": "alias",\n    "core_created_time": "creation_date",\n    "core_modified_time": "null",\n    "core_body": "description",\n    "core_hits": "nb_views",\n    "core_publish_up": "publish_up",\n    "core_publish_down": "publish_down",\n    "core_access": "access",\n    "core_params": "null",\n    "core_featured": "featured",\n    "core_metadata": "metadata",\n    "core_language": "language",\n    "core_images": "picture",\n    "core_urls": "null",\n    "core_version": "null",\n    "core_ordering": "null",\n    "core_metakey": "metakey",\n    "core_metadesc": "metadesc",\n    "core_catid": "null",\n    "core_xreference": "null",\n    "asset_id": "null"\n  },\n  "special": {\n  }\n}', 'JHtmlYooRecipeHelperRoute::getRecipeRoute', NULL),
('YooRecipe Category', 'com_yoorecipe.category', '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"nb_views","core_publish_up":"publish_up","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special": {"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}', '', '{"formFile":"administrator/components/com_categories/models/forms/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}');