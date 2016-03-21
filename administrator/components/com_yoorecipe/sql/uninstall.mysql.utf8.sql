 
 DROP TABLE IF EXISTS `#__yoorecipe`;
 DROP TABLE IF EXISTS `#__yoorecipe_categories`;
 DROP TABLE IF EXISTS `#__yoorecipe_favourites`;
 DROP TABLE IF EXISTS `#__yoorecipe_ingredients`;
 DROP TABLE IF EXISTS `#__yoorecipe_ingredients_groups`;
 DROP TABLE IF EXISTS `#__yoorecipe_meals`;
 DROP TABLE IF EXISTS `#__yoorecipe_mealplanners_queues`;
 DROP TABLE IF EXISTS `#__yoorecipe_reviews`;
 DROP TABLE IF EXISTS `#__yoorecipe_seasons`;
 DROP TABLE IF EXISTS `#__yoorecipe_shoppinglists`;
 DROP TABLE IF EXISTS `#__yoorecipe_shoppinglists_details`;

 DELETE FROM `#__categories` WHERE extension = 'com_yoorecipe';