<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
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
class YooRecipeModelIngredientsGroups extends JModelList
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
				'id', 'ig.id',
				'lang', 'ig.lang',
				'label', 'ig.label',
				'published', 'ig.published'
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
		if ($layout = $input->get('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		
		$language 	= JFactory::getLanguage();
		$tag 	= $language->getTag();
		$lang 	= $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', $tag);
		$this->setState('filter.language', $lang);
		
		// List state information.
		parent::populateState('ig.label', 'asc');
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
				'ig.id, ig.label, ig.lang, ig.published, ig.featured'
			)
		);

		$query->from('#__yoorecipe_ingredients_groups as ig');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('ig.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('ig.label LIKE '.$search);
			}
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('ig.published = '.(int) $published);
		}
		
		// Filter by language
		$lang = $this->getState('filter.language');
		if ($lang != '') {
			$query->where('ig.lang = '.$db->quote($lang));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'label');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
	
		return $query;
	}
	
	/**
	 * Method to toggle the featured setting of ingredient groups.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks)) {
			$this->setError(JText::_('COM_YOORECIPE_NO_ITEM_SELECTED'));
			return false;
		}

		try {
		
			$db = JFactory::getDBO();

			$db->setQuery('UPDATE #__yoorecipe_ingredients_groups AS ig SET ig.featured = '.(int) $value.' WHERE ig.id IN ('.implode(',', $pks).')');
			if (!$db->execute()) {
				throw new Exception($db->getErrorMsg());
			}

		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		return true;
	}
	
	/**
	 * getAllRecipeIngredientsGroups
	 */
	public function getAllRecipeIngredientsGroups() {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe ingredients groups table
		$query->select('*');
		$query->from('#__yoorecipe_ingredients_groups');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* deleteIngredientGroupsByRecipeId
	*/
	public function deleteIngredientGroupsByRecipeId($recipe_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Delete ingredient groups
		$query->delete('#__yoorecipe_ingredients_groups');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * getIngredientsGroupsByRecipeId
	 */
	public function getIngredientsGroupsByRecipeId($recipe_id) {
	
		// Prepare query
		$db 	= JFactory::getDBO();	
		$query	= $db->getQuery(true);
		
		$query->select('id, label');
		$query->from('#__yoorecipe_ingredients_groups');
		$query->where('recipe_id = '.$recipe_id);
		$query->order('ordering asc');
		
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	/**
	 * getAllIngredientsGroups
	 */
	public function getAllIngredientsGroups($language) {
	
		// Prepare query
		$db 	= JFactory::getDBO();	
		$query	= $db->getQuery(true);
		$query->clear();

		$query->select('id, lang, label, published, featured');
		$query->from('#__yoorecipe_ingredients_groups');
		
		// Filter by known languages
		$query->where('published = 1');
		
		// Filter by known languages
		if ($language == null || $language == '*') {
			$langs = JLanguage::getKnownLanguages();
			$lang_array = array();
			foreach ($langs as $key => $language) {
				$lang_array[] = $db->quote($key);
			}
			$query->where('lang in ('.implode(',', $lang_array).','.$db->quote('*').')');
		} else {
			$query->where('lang = '.$db->quote($language));
		}
		
		$query->order('featured desc, label asc');

		$db->setQuery((string)$query);
		$groups = $db->loadObjectList();
		if (!$groups) {
			$this->setError($db->getErrorMsg());
			echo $db->getErrorMsg();
			return false;
		}
		
		return $groups;
	}

	/**
	* truncateYoorecipeIngredientsGroups
	*/
	public function truncateYoorecipeIngredientsGroups() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_ingredients_groups`;";
		$db->setQuery($query);
		return $db->execute();
	}
}