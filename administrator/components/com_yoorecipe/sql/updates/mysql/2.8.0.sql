ALTER TABLE `#__yoorecipe` ADD `carbs` INT( 11 ) NULL AFTER `cost` ;
ALTER TABLE `#__yoorecipe` ADD `fat` INT( 11 ) NULL AFTER `carbs` ;
ALTER TABLE `#__yoorecipe` ADD `saturated_fat` INT( 11 ) NULL AFTER `fat` ;
ALTER TABLE `#__yoorecipe` ADD `proteins` INT( 11 ) NULL AFTER `saturated_fat` ;
ALTER TABLE `#__yoorecipe` ADD `fibers` INT( 11 ) NULL AFTER `proteins` ;
ALTER TABLE `#__yoorecipe` ADD `salt` INT( 11 ) NULL AFTER `fibers` ;
ALTER TABLE `#__yoorecipe` ADD `kcal` INT( 11 ) NULL AFTER `salt` ;
ALTER TABLE `#__yoorecipe` ADD `kjoule` INT( 11 ) NULL AFTER `kcal` ;
ALTER TABLE `#__yoorecipe` ADD `diet` BOOL NULL AFTER `kjoule` ;
ALTER TABLE `#__yoorecipe` ADD `veggie` BOOL NULL AFTER `diet` ;
ALTER TABLE `#__yoorecipe` ADD `gluten_free` BOOL NULL AFTER `veggie` ;
ALTER TABLE `#__yoorecipe` ADD `lactose_free` BOOL NULL AFTER `gluten_free` ;

ALTER TABLE `#__yoorecipe_ingredients` ADD `price` VARCHAR( 10 ) NULL AFTER `description` ;

CREATE TABLE IF NOT EXISTS `#__yoorecipe_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `lang` VARCHAR(5) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `label` VARCHAR(50) NOT NULL,
  `ordering` INT(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL,
  `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;