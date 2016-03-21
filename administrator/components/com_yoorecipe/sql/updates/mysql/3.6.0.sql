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

ALTER TABLE `#__yoorecipe`  CHANGE `note` `note` DOUBLE;
ALTER TABLE `#__yoorecipe_ingredients`  CHANGE `quantity` `quantity` DOUBLE;