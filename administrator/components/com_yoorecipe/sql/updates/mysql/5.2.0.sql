alter table `#__yoorecipe_ingredients_groups` add column `recipe_id` int(11) not null after `id`;
alter table `#__yoorecipe_ingredients_groups` add column `ordering` INT(11) DEFAULT NULL after `label`;
alter table `#__yoorecipe` drop column `is_draft`;
alter table `#__yoorecipe_ingredients_groups` drop column `lang`;
alter table `#__yoorecipe_ingredients_groups` drop column `published`;
alter table `#__yoorecipe_ingredients_groups` drop column `featured`;
alter table `#__yoorecipe_serving_types` drop column `lang`;
alter table `#__yoorecipe_serving_types` drop column `label`;

alter table `#__yoorecipe_ingredients` add column `migrated` tinyint(1);
alter table `#__yoorecipe_units` add column `to_delete` tinyint(1);
alter table `#__yoorecipe_ingredients_groups` add column `to_delete` tinyint(1);

update `#__yoorecipe_ingredients_groups` set `to_delete` = 1;
update `#__yoorecipe_units` set `to_delete` = 1;
update `#__yoorecipe_ingredients` set `migrated` = 0;