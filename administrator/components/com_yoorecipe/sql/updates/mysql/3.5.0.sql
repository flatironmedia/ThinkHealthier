ALTER TABLE `#__yoorecipe` ADD COLUMN `checked_out` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__yoorecipe` ADD COLUMN `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__yoorecipe_ingredients` DROP `price`;
update `#__yoorecipe_ingredients_groups` set published = 1 where lang = '';
update `#__yoorecipe_ingredients_groups` set lang = '*' where lang = '';