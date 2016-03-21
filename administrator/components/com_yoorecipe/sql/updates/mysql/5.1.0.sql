CREATE TABLE IF NOT EXISTS `#__yoorecipe_cuisines` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`code` varchar(255) NOT NULL,
	`published` TINYINT(1) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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