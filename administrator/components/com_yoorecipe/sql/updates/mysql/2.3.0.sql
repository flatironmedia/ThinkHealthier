ALTER TABLE `#__yoorecipe` ADD `servings_type` VARCHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `preparation`;
update `#__yoorecipe` set servings_type = 'P';
 
ALTER TABLE `#__yoorecipe` DROP COLUMN `created_by_alias`;
ALTER TABLE `#__yoorecipe_ingredients` ADD `ordering` INT( 11 ) AFTER `recipe_id`;

 
