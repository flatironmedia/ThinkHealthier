<?php 

class ModFeaturedCategoriesHelper {

	public static function getFeaturedCategories($params) {
		//return $this;
		//echo('<pre>'.print_r($params->get('article_count'), true).'</pre>');
		$db = JFactory::getDbo();
		$query = 'SELECT id,title FROM `#__tags` WHERE id IN ('.implode(', ', $params->get('categories') ).')';
		$db->setQuery($query);
		$result = $db->loadObjectList();
		// die('<pre>'.print_r($result, true).'</pre>');

		return $result;
	}

}