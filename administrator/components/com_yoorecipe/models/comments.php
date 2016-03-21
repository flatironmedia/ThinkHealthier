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

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * YooRecipe Comments Model
 */
class YooRecipeModelComments extends JModelList
{
	
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'rat.id',
				'comment', 'rat.comment',
				'note', 'rat.note',
				'author', 'rat.author',
				'email', 'rat.email',
				'ip_address', 'rat.ip_address',
				'creation_date', 'rat.creation_date',
				'published', 'rat.published',
				'recipe_id', 'rat.recipe_id',
				'abuse', 'rat.abuse'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		$input 	= JFactory::getApplication()->input;
		if ($layout = $input->get('layout', '', 'STRING')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);
		
		$recipeId = $this->getUserStateFromRequest($this->context.'.filter.recipe_id', 'filter_recipe_id');
		$this->setState('filter.recipe_id', $recipeId);
		
		$offensive = $this->getUserStateFromRequest($this->context.'.filter.offensive', 'filter_offensive');
		$this->setState('filter.offensive', $offensive);
		
		// List state information.
		parent::populateState('r.title', 'asc');
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'rat.id, rat.recipe_id, rat.note, rat.author, rat.user_id, rat.email, rat.ip_address, rat.comment, rat.creation_date, rat.published, rat.abuse'
			)
		);
		
		// Join over recipes
		$query->select('r.title AS title');
		$query->join('LEFT', '#__yoorecipe AS r ON r.id = rat.recipe_id');
		
		// Join over the users for the author
		$query->select('ua.name AS author_name');
		$query->select('ua.email AS author_email');
		$query->join('LEFT', '#__users ua ON ua.id = rat.user_id');
		$query->from('#__yoorecipe_reviews as rat');
		
		// Join over CROSS categories
		$query->join('LEFT', '#__yoorecipe_categories cc on rat.recipe_id = cc.recipe_id');
		
		// Filter by search in comment.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->quote('%'.$db->escape($search, true).'%');
			$query->where('rat.comment LIKE '.$search);
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('rat.published = '.(int) $published);
		}
		
		// Filter by offensive state
		$offensive = $this->getState('filter.offensive');
		if (is_numeric($offensive)) {
			$query->where('rat.abuse = '.(int) $offensive);
		}
		
		// Filter by recipe id
		if ($recipeId = $this->getState('filter.recipe_id')) {
			$query->where('rat.recipe_id = '.(int) $recipeId);
		}
				
		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('cc.cat_id = '.(int) $categoryId);
		}
		else if (is_array($categoryId)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('cc.cat_id IN ('.$categoryId.')');
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		$query->group('rat.id');
	
		return $query;
	}
	
	/**
	* deleteCommentsByRecipeId
	*/
	public function deleteCommentsByRecipeId($recipe_id)
	{
		// Prepare query
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->from('#__yoorecipe_reviews');
		$query->delete();
		$query->where('recipe_id = '.(int)$recipe_id);
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* deleteCommentsById
	*/
	public function deleteCommentsById($id)
	{
		// Prepare query
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->delete();
		$query->from('#__yoorecipe_reviews');
		$query->where('id = '.(int)$id);
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* getCommentsByRecipeId
	*/
	public function getCommentsByRecipeId($recipe_id, $published = 1, $abuse = 0) {
	
		// Prepare query
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->from('#__yoorecipe_reviews');
		$query->select('id, recipe_id, note, author, user_id, email, ip_address, comment, creation_date, published, abuse');
		$query->where('recipe_id = '.(int)$recipe_id);
		$query->where('published = '.(int)$published);
		$query->where('abuse = '.(int)$abuse);
		
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
}