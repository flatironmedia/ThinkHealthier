DROP TABLE IF EXISTS `#__newsletter_healthy_news`;
DROP TABLE IF EXISTS `#__newsletter_history`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_newsletter.%');