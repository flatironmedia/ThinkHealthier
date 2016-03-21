ALTER TABLE `#__yoorecipe_ingredients_groups` CHANGE `text` `label` VARCHAR(50) NOT NULL;
ALTER TABLE `#__yoorecipe_ingredients_groups` ADD `lang` VARCHAR(5) NOT NULL;
ALTER TABLE `#__yoorecipe_ingredients_groups` ADD `published` tinyint(1) NOT NULL;