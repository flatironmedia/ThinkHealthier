<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * YooRecipe Model
 */
class YooRecipeModelSearch extends JModelList
{
	/**
	 * Items total
	 * @var integer
	 */
	var $_total 	= null;

	/**
	 * Pagination object
	 * @var object
	 */
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/** Remove pagination from search results */
	protected function populateState($ordering = null, $direction = null)
	{
		$app 				= JFactory::getApplication();
		$input 				= JFactory::getApplication()->input;
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		
		// Search parameters
		// Adjust the context to support modal layouts.
		$input 	= JFactory::getApplication()->input;
		if ($layout = $input->get('layout', '', 'STRING')) {
			$this->context .= '.'.$layout;
		}
		
		// Pagination stuff
		$limit 	= $app->getUserStateFromRequest('global.list.limit', 'limit', $yooRecipeparams->get('list_length', 10), 'int');
		$this->setState('list.limit', $limit);
		$this->setState('list.start', $input->get('limitstart', 0, 'INT'));
		
		// Menu stuff
		$menu 	= $app->getMenu();
		$active = $menu->getActive();
		if ($active) {
			$params = new JRegistry();
			$params->loadString($active->params);
			$this->setState('orderCol',$params->get('recipe_sort', 'title'));
		} else {
			$this->setState('orderCol', 'title');
		}

		// Some basic filters
		$this->setState('filter.access', true);
		$this->setState('filter.language', $app->getLanguageFilter());
		
		$searchPerformed = $this->getUserStateFromRequest($this->context.'.filter.searchPerformed', 'searchPerformed');
		$this->setState('searchPerformed', $searchPerformed);
		
		$searchword = $this->getUserStateFromRequest($this->context.'.filter.searchword', 'searchword');
		$this->setState('filter.searchword', $searchword);
		
		$this->setState('filter.withIngredients', array_filter($input->get('withIngredients', array(), 'ARRAY')));
		$this->setState('filter.withoutIngredients', array_filter($input->get('withoutIngredients', array(), 'ARRAY')));
		$this->setState('filter.searchCategories', array_filter($input->get('searchCategories', array(), 'ARRAY')));
		
		$search_cuisine = $this->getUserStateFromRequest($this->context.'.filter.search_cuisine', 'search_cuisine');
		$this->setState('filter.search_cuisine', $search_cuisine);
				
		$searchSeasons = $this->getUserStateFromRequest($this->context.'.filter.searchSeasons', 'searchSeasons');
		$this->setState('filter.searchSeasons', $searchSeasons);
		
		$search_author = $this->getUserStateFromRequest($this->context.'.filter.search_author', 'search_author');
		$this->setState('filter.search_author', $search_author);
		
		$search_max_prep_hours = $this->getUserStateFromRequest($this->context.'.filter.search_max_prep_hours', 'search_max_prep_hours');
		$this->setState('filter.search_max_prep_hours', $search_max_prep_hours);
		
		$search_max_prep_minutes = $this->getUserStateFromRequest($this->context.'.filter.search_max_prep_minutes', 'search_max_prep_minutes');
		$this->setState('filter.search_max_prep_minutes', $search_max_prep_minutes);
		
		$search_max_cook_hours = $this->getUserStateFromRequest($this->context.'.filter.search_max_cook_hours', 'search_max_cook_hours');
		$this->setState('filter.search_max_cook_hours', $search_max_cook_hours);
		
		$search_max_cook_minutes = $this->getUserStateFromRequest($this->context.'.filter.search_max_cook_minutes', 'search_max_cook_minutes');
		$this->setState('filter.search_max_cook_minutes', $search_max_cook_minutes);
		
		$search_min_rate = $this->getUserStateFromRequest($this->context.'.filter.search_min_rate', 'search_min_rate');
		$this->setState('filter.search_min_rate', $search_min_rate);
		
		$search_max_cost = $this->getUserStateFromRequest($this->context.'.filter.search_max_cost', 'search_max_cost');
		$this->setState('filter.search_max_cost', $search_max_cost);
		
		$search_operator_price = $this->getUserStateFromRequest($this->context.'.filter.search_operator_price', 'search_operator_price');
		$this->setState('filter.search_operator_price', $search_operator_price);
		
		$search_price = $this->getUserStateFromRequest($this->context.'.filter.search_price', 'search_price');
		$this->setState('filter.search_price', $search_price);
		
		$search_type_diet = $this->getUserStateFromRequest($this->context.'.filter.search_type_diet', 'search_type_diet');
		$this->setState('filter.search_type_diet', $search_type_diet);
		
		$search_type_veggie = $this->getUserStateFromRequest($this->context.'.filter.search_type_veggie', 'search_type_veggie');
		$this->setState('filter.search_type_veggie', $search_type_veggie);
		
		$search_type_glutenfree = $this->getUserStateFromRequest($this->context.'.filter.search_type_glutenfree', 'search_type_glutenfree');
		$this->setState('filter.search_type_glutenfree', $search_type_glutenfree);
		
		$search_type_lactosefree = $this->getUserStateFromRequest($this->context.'.filter.search_type_lactosefree', 'search_type_lactosefree');
		$this->setState('filter.search_type_lactosefree', $search_type_lactosefree);
		
		$search_kcal = $this->getUserStateFromRequest($this->context.'.filter.search_kcal', 'search_kcal');
		$this->setState('filter.search_kcal', $search_kcal);
		
		$search_carbs = $this->getUserStateFromRequest($this->context.'.filter.search_carbs', 'search_carbs');
		$this->setState('filter.search_carbs', $search_carbs);
		
		$search_fat = $this->getUserStateFromRequest($this->context.'.filter.search_fat', 'search_fat');
		$this->setState('filter.search_fat', $search_fat);
		
		$search_proteins = $this->getUserStateFromRequest($this->context.'.filter.search_proteins', 'search_proteins');
		$this->setState('filter.search_proteins', $search_proteins);
	}
	
	/**
	* getListQuery
	*/
	protected function getListQuery() 
	{
		return $this->getSearchQuery($phrase = 'all');
	}
	
	/**
	* getExactMatchItems
	*/
	public function getExactMatchItems() {
	
		$db 	= JFactory::getDBO();
		$query 	= $this->getSearchQuery('exact');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* getExactMatchItems
	*/
	public function getAnyMatchItems() {
	
		$db 	= JFactory::getDBO();
		$query 	= $this->getSearchQuery('any');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* getSearchQuery
	* $phrase can be all, exact, any
	*/
	private function getSearchQuery($phrase = 'all') {
	
		// Create a new query object.		
		$user	= JFactory::getUser();
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// Retrieve component parameters
		$params 		= JComponentHelper::getParams('com_yoorecipe');
		$use_stemming	= $params->get('use_stemming', 1);
		
		// Get request variables
		$searchword 		= $this->getState('filter.searchword');
		$withIngredients 	= $this->getState('filter.withIngredients');
		$withoutIngredients = $this->getState('filter.withoutIngredients');
		$searchCategories 	= $this->getState('filter.searchCategories');
		$withoutIngredient 	= $this->getState('filter.withoutIngredient');
		$searchSeasons	 	= $this->getState('filter.searchSeasons');
		
		$search_author				= $this->getState('filter.search_author');
		$search_max_prep_hours		= $this->getState('filter.search_max_prep_hours');
		$search_max_prep_minutes	= $this->getState('filter.search_max_prep_minutes');
		$search_max_cook_hours		= $this->getState('filter.search_max_cook_hours');
		$search_max_cook_minutes	= $this->getState('filter.search_max_cook_minutes');
		$search_min_rate			= $this->getState('filter.search_min_rate');
		$search_max_cost			= $this->getState('filter.search_max_cost');
		
		$search_operator_price		= $this->getState('filter.search_operator_price');
		$search_price				= $this->getState('filter.search_price');
		$search_type_diet			= $this->getState('filter.search_type_diet');
		$search_type_veggie			= $this->getState('filter.search_type_veggie');
		$search_type_glutenfree		= $this->getState('filter.search_type_glutenfree');
		$search_type_lactosefree	= $this->getState('filter.search_type_lactosefree');
		
		$search_kcal 		= $this->getState('filter.search_kcal');
		$search_carbs 		= $this->getState('filter.search_carbs');
		$search_fat 		= $this->getState('filter.search_fat');
		$search_proteins 	= $this->getState('filter.search_proteins');
		
		// Get recipe fields
		$query->select('SQL_CALC_FOUND_ROWS r.id, r.access, r.title, r.alias, r.description, r.created_by, r.preparation, r.notes, r.serving_type_id' .
				', r.nb_persons, r.difficulty, r.cost, r.sugar, r.carbs, r.fat, r.saturated_fat, r.cholesterol, r.proteins, r.fibers, r.salt' .
				', r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.publish_up' .
				', r.publish_down, r.preparation_time, r.cook_time, r.wait_time, r.picture, r.video, r.published' .
				', r.validated, r.featured, r.nb_views, r.note, r.price, r.language, r.cuisine');
		$query->select('CASE WHEN CHARACTER_LENGTH(r.alias) THEN CONCAT_WS(\':\', r.id, r.alias) ELSE r.id END as slug');
		$query->select('CASE WHEN CHARACTER_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug');
		$query->select('CASE WHEN fr.recipe_id = r.id THEN 1 ELSE 0 END as favourite');
		
		$query->from('#__yoorecipe as r');
		
		// Join over cross categories
		$query->join('INNER', '#__yoorecipe_categories as cc on cc.recipe_id = r.id');
		
		// Join over categories
		$query->join('INNER', '#__categories as c on c.id = cc.cat_id');
		
		// Join over favourites
		$query->join('LEFT', '#__yoorecipe_favourites AS fr ON fr.recipe_id = r.id AND fr.user_id = '.$db->quote($user->id));
		
		// Join over the users for the author.
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$showAuthorName 	= $yooRecipeparams->get('show_author_name', 'username');
		
		if ($showAuthorName == 'username') {
			$query->select('ua.username AS author_name');
		} else if ($showAuthorName == 'name') {
			$query->select('ua.name AS author_name');
		}
		$query->join('LEFT', '#__users ua ON ua.id = r.created_by');
		
		// Prepare where clause
		$whereClause = 'r.published = 1 AND r.validated = 1';
		
		// Filter by title
		if (!empty($searchword)) {

			// Filter by title
			$search_terms = array();
			switch ($phrase) {
				
				// search exact
				case 'exact':
					$search_terms[] = $searchword;
				break;
				
				case 'any':
					$search_terms = preg_split("/[\s,]+/", $searchword);
				break;
				
				case 'all':
					$search_terms[] = $searchword;
					$search_terms = array_merge($search_terms, preg_split("/[\s,]+/", $searchword));
				break;
				
			}
		
			$sub_clauses = array();
			foreach($search_terms as $search_term) {
			
				// Remove words smaller than 3 chars
				if (strlen($search_term) <= 2) {
					continue;
				}
				
				if ($use_stemming) {
					$search_term = $this->getStemmedWord($search_term);
				}
				
				$sub_clause = ' ( r.title like '.$db->quote('%'.$search_term.'%');
				
				if ($params->get('include_search_on_description', 1)) {
					$sub_clause .= ' OR r.description like '.$db->quote('%'.$search_term.'%');
				}
				
				if ($params->get('include_search_on_preparation', 1)) {
					$sub_clause .= ' OR r.preparation like '.$db->quote('%'.$search_term.'%');
				}
								
				$sub_clause .= ')';
				$sub_clauses[] = $sub_clause;
			}
			
			if (count($sub_clauses) > 0) {
				$whereClause .= ' AND ( ';
				$whereClause .= implode(' OR ', $sub_clauses);
				$whereClause .= ' ) ';
			}
		}
		
		// Filter by nutrition facts
		if (isset($search_type_diet) && $search_type_diet == 'on') {
			$query->where('r.diet = 1');
		}
		if (isset($search_type_veggie) && $search_type_veggie == 'on') {
			$query->where('r.veggie = 1');
		}
		if (isset($search_type_glutenfree) && $search_type_glutenfree == 'on') {
			$query->where('r.gluten_free = 1');
		}
		if (isset($search_type_lactosefree) && $search_type_lactosefree == 'on') {
			$query->where('r.lactose_free = 1');
		}
		
		if (!empty($search_kcal)) {
			$from_to = explode(" - ", $search_kcal);
			$query->where('(r.kcal BETWEEN '.$from_to[0].' AND '.$from_to[1].')');
		}
		
		if (!empty($search_carbs)) {
			$from_to = explode(" - ", $search_carbs);
			$query->where('(r.carbs BETWEEN '.$from_to[0].' AND '.$from_to[1].')');
		}
		
		if (!empty($search_fat)) {
			$from_to = explode(" - ", $search_fat);
			$query->where('(r.fat BETWEEN '.$from_to[0].' AND '.$from_to[1].')');
		}
		
		if (!empty($search_proteins)) {
			$from_to = explode(" - ", $search_proteins);
			$query->where('(r.proteins BETWEEN '.$from_to[0].' AND '.$from_to[1].')');
		}
		
		// Filter by ingredients
		if (is_array($withIngredients) && !empty($withIngredients)) {
			JArrayHelper::toString($withIngredients);
			foreach ($withIngredients as $key => $alias)
			{
				$withIngredients[$key] = $db->quote('%'.$alias.'%');
			}
			$withIngredients = implode(" OR i.description LIKE ", $withIngredients);
			$whereClause .= ' AND (i.description LIKE '. $withIngredients.')';
			
			// Join over ingredients
			$query->join('INNER', '#__yoorecipe_ingredients as i on i.recipe_id = r.id');
		}
		
		// Exclude some ingredients
		if (is_array($withoutIngredients) && !empty($withoutIngredients)) {
			JArrayHelper::toString($withoutIngredients);
			
			foreach ($withoutIngredients as $key => $alias)
			{
				$withoutIngredients[$key] = $db->quote('%'.$alias.'%');
			}
			$withoutIngredients = implode(" OR i2.description LIKE ", $withoutIngredients);
			
			$queryRecipesToExclude = 'select distinct r2.id as id from #__yoorecipe r2 inner join #__yoorecipe_ingredients i2 '.
				' on i2.recipe_id = r2.id where i2.description LIKE '.$withoutIngredients;
			
			$db->setQuery($queryRecipesToExclude);
			$recipesToExclude = $db->loadObjectList();
			
			if (count($recipesToExclude) > 0) {
				foreach ($recipesToExclude as $recipeToExclude ) {
					$recipeIdsToExclude[] = $recipeToExclude->id;
				}
				JArrayHelper::toInteger($recipeIdsToExclude);
				$recipeIdsToExclude = implode(', ', $recipeIdsToExclude);
				$whereClause .= ' AND r.id NOT IN ('.$recipeIdsToExclude.')';
			}
		}

		// Filter by category
		if ($searchCategories != '*') {
			if (is_numeric($searchCategories) && !empty($searchCategories)) {
				$whereClause .= ' AND cc.cat_id = '.(int) $searchCategories;
			}
			else if (is_array($searchCategories) && !empty($searchCategories) && !in_array('*', $searchCategories)) {
				
				JArrayHelper::toInteger($searchCategories);
				$searchCategories = implode(',', $searchCategories);
				$whereClause .= ' AND cc.cat_id IN ('.$searchCategories.')';
			}
		}
		
		// Filter by season
		if (isset($searchSeasons) && !empty($searchSeasons)) {
			
			$query->join('LEFT', '#__yoorecipe_seasons AS sea ON sea.recipe_id = r.id');
			$whereClause .= ' AND sea.month_id = '.$db->quote($searchSeasons).' ';
		}
		
		$query->where($whereClause);
		
		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
			
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('r.access IN ('.$groups.')');
			$query->where('c.access IN ('.$groups.')');
		//}
		
		// Filter by cuisine
		if ($this->getState('filter.search_cuisine')) {
			$query->where('r.cuisine = '.$db->quote($this->getState('filter.search_cuisine')));
		}
		
		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('r.language IN ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}
		
		// Filter by author
		if (isset($search_author) && !empty($search_author)) {
			$query->where('r.created_by = '.$search_author);
		}
		
		// Filter by preparation time
		if ( (isset($search_max_prep_hours) && !empty($search_max_prep_hours)) || (isset($search_max_prep_minutes) && !empty($search_max_prep_minutes)) ) {
			
			$maxPreparationTime = $search_max_prep_hours * 60 + $search_max_prep_minutes;
			if ($maxPreparationTime > 0) {
				$query->where('r.preparation_time < '.$maxPreparationTime);
			}
		}
		
		// Filter by cook time
		if ( (isset($search_max_cook_hours) && !empty($search_max_cook_hours)) || (isset($search_max_cook_minutes) && !empty($search_max_cook_minutes)) ) {
			
			$maxCookTime = $search_max_cook_hours * 60 + $search_max_cook_minutes;
			if ($maxCookTime > 0) {
				$query->where('r.cook_time < '.$maxCookTime);
			}
		}
		
		// Filter by rating
		if (isset($search_min_rate) && !empty($search_min_rate)) {
			if ($search_min_rate > 0) {
				$query->where('r.note >= '.$search_min_rate);
			}
		}
		
		// Filter by cost
		if (isset($search_max_cost) && !empty($search_max_cost)) {
			if ($search_max_cost > 0) {
				$query->where('r.cost <= '.$search_max_cost);
			}
		}
		
		// Filter by price
		if (isset($search_operator_price) && $search_operator_price != 999) { // if different from Any
			$priceVal = str_replace(",",".", $search_price);
			if (isset($search_price) && !empty($search_price) && is_numeric($priceVal)) {
				$operator = ($search_operator_price == 'lt') ? '<=' : '>';
				$query->where('r.price '.$operator.$priceVal);
			}
		}
		
		// Filter by start and end dates.
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSQL());

		$query->where('(r.publish_up = '.$nullDate.' OR r.publish_up <= '.$nowDate.')');
		$query->where('(r.publish_down = '.$nullDate.' OR r.publish_down >= '.$nowDate.')');
		
		// Prepare order by clause
		//$query->order('r.'.$this->getState('orderCol').' '.'asc');
		$query->group('r.id');
		
		return $query;
	}
	
	/**
	* getStemmedWord
	*/
	public function getStemmedWord($search_term) {
	
		// Get stemmer according to user lang
		$language 	= JFactory::getLanguage();
		$lang_tag 	= $language->getTag();
		$lang 		=  substr($lang_tag, 0,2);
		
		switch ($lang_tag) {
			case 'fr-FR':
			case 'fr-CA':
			case 'fr-CH':
			case 'fr-BE':
			case 'fr-LU':
				$stemmer = 'fr';
			break;
			
			case 'en-GB':
			case 'en-US':
			case 'en_HK':
			case 'en-IE':
			case 'en-IN':
			case 'en-NZ':
			case 'en-PH':
			case 'en-SG':
			case 'en-ZA':
			case 'en-ZW':
				$stemmer = 'porter_en';
			break;
			
			default:
				$stemmer = 'snowball';
			break;
		}
		
		$indexer_stemmer 	= FinderIndexerStemmer::getInstance($stemmer);
		return $indexer_stemmer->stem($search_term, $lang);
	}
	
	/**
	 * Get recipe authors
	 */
	public function getAuthors()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Get categories fields
		$query->select('distinct created_by, u.id');
		
		// Join over the users for the author.
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$showAuthorName 	= $yooRecipeparams->get('show_author_name', 'username');
		
		if ($showAuthorName == 'username') {
			$query->select('u.username AS author_name');
		} else if ($showAuthorName == 'name') {
			$query->select('u.name AS author_name');
		}
		
		$query->from('#__yoorecipe as r');
		$query->join('LEFT', '#__users as u on u.id = r.created_by');
	
		// Set where clause
		$query->where('r.published = 1 and r.validated = 1');
		if ($showAuthorName == 'username') {
			$query->order('u.username asc');
		} else if ($showAuthorName == 'name') {
			$query->order('u.name asc');
		}
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   11.1
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Create the pagination object.
		jimport('joomla.html.pagination');
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		$page = new JHtmlYooRecipePagination($this->getTotal(), $this->getStart(), $limit);

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}
}