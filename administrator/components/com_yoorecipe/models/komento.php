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
jimport('joomla.application.component.modeladmin');

/**
 * YooRecipeList Model
 */
class YooRecipeModelKomento extends JModelList
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

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', 1);
		$this->setState('filter.published', $published);
		
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
				'cid, id, component, comment, name, title, email, url, ip, created_by, created, modified_by, '.
				'modified, deleted_by, deleted, flag, published, publish_up, publish_down, '.
				'sticked, sent, parent_id, lft, rgt, depth, latitude, longitude, address, params, ratings'
			)
		);

		$query->from('#__komento_comments');
		
		// Filter by component
		$query->where('component = '.$db->quote('com_yoorecipe'));
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('published = '.(int) $published);
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
	
		return $query;
	}
	
	/**
	* getRecipeNote
	*/
	public function getRecipeNote($recipe_id) {
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('avg(ratings)/2 as ratings');
		
		$query->from('#__komento_comments');
		$query->where('component = '.$db->quote('com_yoorecipe'));
		$query->where('cid = '.$recipe_id);
		$query->where('published = 1');
		
		$db->setQuery((string) $query);
		return $db->loadResult();
	}
	
	/**
	* getNbReviewsByRecipeId
	*/
	public function getNbReviewsByRecipeId($recipe_id) {
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('count(id) as nb_reviews');
		
		$query->from('#__komento_comments');
		$query->where('component = '.$db->quote('com_yoorecipe'));
		$query->where('cid = '.$recipe_id);
		$query->where('published = 1');
		
		$db->setQuery((string) $query);
		return $db->loadResult();
	}
}