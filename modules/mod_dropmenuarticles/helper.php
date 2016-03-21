<?php 

class ModDropMenuArticlesHelper {

	public static function getArticles($params) {
		//return $this;
		//echo('<pre>'.print_r($params->get('article_count'), true).'</pre>');
		$db = JFactory::getDbo();
		$query = 'SELECT `#__categories`.`id` as category_id, `#__categories`.`title` as category_title, `#__categories`.`path`, `#__content`.* FROM `#__content` 
      			LEFT JOIN `#__categories` ON `#__categories`.`id`=`#__content`.`catid`
      			WHERE `path` like "'.$params->get('category').'%" AND `state`=1 ORDER BY RAND() LIMIT '.$params->get('article_count');
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

}