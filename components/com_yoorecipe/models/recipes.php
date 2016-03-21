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
 * YooRecipe Recipes Model
 */
class YooRecipeModelRecipes extends JModelList
{
	/**
	 * @var string msg
	 */
	protected $msg;
	
	/**
	 * Items total
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 * @var object
	 */
	var $_pagination = null;
	
	function __construct() {
		
		parent::__construct();
		
		$yooRecipeparams	= JComponentHelper::getParams('com_yoorecipe');
		
		$app	= JFactory::getApplication();
		$input 	= $app->input;
		$limit 	= $app->getUserStateFromRequest('global.list.limit', 'limit', $yooRecipeparams->get('list_length', 10), 'int');
		$this->setState('list.limit', $limit);
		$this->setState('list.start', $input->get('limitstart', 0, 'INT'));
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		// List state information
		$app	= JFactory::getApplication();
		$input 	= $app->input;
		$layout = $input->get('layout', 'allrecipes', 'STRING');
		$this->context .= '.'.$layout;
		
		$this->setState('layout', $layout);
		$this->setState('drn', 'asc');
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$category_id = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $category_id);
		
		$cuisine = $this->getUserStateFromRequest($this->context.'.filter.cuisine', 'filter_cuisine');
		$this->setState('filter.cuisine', $cuisine);
		
		$recipe_time = $this->getUserStateFromRequest($this->context.'.filter.recipe_time', 'filter_recipe_time');
		$this->setState('filter.recipe_time', $recipe_time);
		
		$order_col = $this->getUserStateFromRequest($this->context.'.filter.order_col', 'order_col', 'title');
		$this->setState('orderCol', $order_col);
	
		$this->setState('filter.access', true);
		$this->setState('filter.language', $app->getLanguageFilter());
	}
		
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery($categoryId = null, $orderBy = null)
	{
		// Create a new query object.		
		$input	= JFactory::getApplication()->input;
		$user	= JFactory::getUser();
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSQL());
		
		// Select some fields
		$query->select( 'SQL_CALC_FOUND_ROWS r.id, r.access, r.title, r.alias, r.description, r.notes, r.created_by, r.preparation, r.serving_type_id' .
				', r.nb_persons, r.difficulty, r.cost, r.sugar, r.carbs, r.fat, r.saturated_fat, r.cholesterol, r.proteins, r.fibers, r.salt' .
				', r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.publish_up' .
				', r.publish_down, r.preparation_time, r.cook_time, r.wait_time, r.picture, r.video, r.published, r.cuisine' .
				', r.validated, r.featured, r.nb_views, r.note, r.metadata, r.metakey' .
				', r.price, r.language');

		$query->select('CASE WHEN CHARACTER_LENGTH(r.alias) THEN CONCAT_WS(\':\', r.id, r.alias) ELSE r.id END as slug');
		$query->select('CASE WHEN CHARACTER_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug');
		$query->select('CASE WHEN fr.recipe_id = r.id THEN 1 ELSE 0 END as favourite');
		$query->select('CASE WHEN (r.publish_up = '.$nullDate.' OR r.publish_up <= '.$nowDate.') THEN 1 ELSE 0 END as published_up');
		$query->select('CASE WHEN (r.publish_down = '.$nullDate.' OR r.publish_down >= '.$nowDate.') THEN 0 ELSE 1 END as published_down');
		
		// From the recipe table
		$query->from('#__yoorecipe as r');
		
		// Join over reviews
		$query->select('count(rat.id) as nb_reviews');
		$query->join('LEFT', '#__yoorecipe_reviews rat on rat.recipe_id = r.id');
				
		// Join over Cross Categories
		$query->join('LEFT', '#__yoorecipe_categories cc on cc.recipe_id = r.id');

		// Join over Categories
		$query->join('LEFT', '#__categories c on c.id = cc.cat_id');
		
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
		
		// Filter by featured
		if ($featured = $this->getState('filter.featured')) {
			$query->where('r.featured = 1');
		}
		
		// Filter by favourites
		if ($favourites = $this->getState('filter.favourites')) {
			$query->where('fr.user_id = '.$db->quote($user->id));
		}
		
		// Filter by best rated
		if ($reviewed = $this->getState('filter.reviewed')) {
			$query->where('r.note != 0');
		}
		
		// Filter by author
		$created_by = $this->getState('filter.created_by');
		if (!empty($created_by)) {
			$query->where('r.created_by = '.$db->quote($created_by));
		}
		
		// Filter by cuisine
		$cuisine = $this->getState('filter.cuisine');
		if (!empty($cuisine)) {
			$query->where('r.cuisine = '.$db->quote($cuisine));
		}
				
		// Filter by recipe_time.
		if ($recipe_time = $this->getState('filter.recipe_time')) {
			$query->select('SUM(r.preparation_time + r.cook_time + r.wait_time) as recipe_time');
			$query->having('recipe_time < '.$db->quote($recipe_time));
		}
		
		// Filter by season
		$season_id = $this->getState('filter.season_id');
		if (!empty($season_id)) {
			$query->where('r.created_by = '.$db->quote($created_by));
			$query->join('LEFT', '#__yoorecipe_seasons AS s ON s.recipe_id = r.id');
			$query->where('s.month_id = '.$db->quote($season_id));
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if ($published != null) {
			$query->where('r.published = '.$db->quote($published));
		}
		
		// Filter by validated state
		$validated = $this->getState('filter.validated');
		if ($validated != null) {
			$query->where('r.validated = '.$db->quote($validated));
		}
		
		// Filter by category id
		if ($this->getState('filter.category_id')) {
			$query->where('cc.cat_id = '.$db->quote($this->getState('filter.category_id')));
		}
		
		if ($categoryId != null) {
			$query->where('cc.cat_id = '.$db->quote($categoryId));
		}
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('r.access IN ('.$groups.')');
		}
		
		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('r.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('r.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('r.title LIKE '.$search);
			}
		}

		// Filter by start and end dates.
		$query->where('(r.publish_up = '.$nullDate.' OR r.publish_up <= '.$nowDate.')');
		$query->where('(r.publish_down = '.$nullDate.' OR r.publish_down >= '.$nowDate.')');
		
		// Prepare order by clause
		if ($orderBy != null) {
			$orderByClause = 'r.'.$orderBy.' '.$asc.' ';
			$query->order($orderByClause);
		} else {
			$query->order($this->getState('orderCol').' '.$this->getState('drn'));
		}
		$query->group('r.id');
		
		return $query;
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