ALTER TABLE `#__yoorecipe_seasons` CHANGE `recipe_id` `recipe_id` INT( 11 ) NOT NULL;
ALTER TABLE `#__yoorecipe_seasons` DROP PRIMARY KEY;
ALTER TABLE `#__yoorecipe_seasons` ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY;

ALTER TABLE `#__yoorecipe_categories` CHANGE `recipe_id` `recipe_id` INT( 11 ) NOT NULL ;
ALTER TABLE `#__yoorecipe_categories` DROP INDEX `recipe_id`;
ALTER TABLE `#__yoorecipe_categories` ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ;

ALTER TABLE `#__yoorecipe_favourites` DROP INDEX `recipe_id`;
ALTER TABLE `#__yoorecipe_favourites` CHANGE `recipe_id` `recipe_id` INT( 11 ) NOT NULL ;
ALTER TABLE `#__yoorecipe_favourites` ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ;