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
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * YooRecipe Model
 */
class YooRecipeModelCrossCategory extends JModelAdmin
{

	private $checked_out;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'CrossCategory', $prefix = 'YooRecipeTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_yoorecipe.crosscategory', 'yoorecipe', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	 /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string       Script files
	 */
	public function getScript() {
		return 'administrator/components/com_yoorecipe/models/forms/crosscategory.js';
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.crosscategory.data', array());
		return $data;
	}
	
	/**
	 * getRecipeCategoriesIds
	 */
	public function getRecipeCategoriesIds($recipe_id) {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe table
		$query->from('#__yoorecipe_categories');
		$query->select('cat_id');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->loadColumn();
	}
	/**
	 * getRecipeCategories
	 */
	/*public function getRecipeCategories($recipe_id) {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe categories table
		$query->select('c.id, c.asset_id, c.parent_id, c.lft, c.rgt, c.level, c.path, c.extension, c.title, c.alias, c.note, c.description, c.published, c.access, c.checked_out, c.checked_out_time, c.access, c.params, c.metadesc, c.metakey, c.metadata , c.created_user_id, c.created_time, c.modified_user_id, c.modified_time, c.hits, c.language');
		$query->from('#__categories c');
		
		// Join over categories
		$query->join('LEFT', '#__yoorecipe_categories cc on cc.cat_id = c.id');
		
		// Where clause
		$query->where('cc.recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}*/
	
	/**
	 * getAllRecipeCategories
	 */
	public function getAllRecipeCategories() {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe categories table
		$query->select('*');
		$query->from('#__yoorecipe_categories');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* saveRecipeCategories
	*/
	public function saveRecipeCategories($category_ids, $recipe_id) {
	
		// Get variables
		$recipe_id	= $recipe_id;
		$result		= array();
		
		// Get query
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);

		// Remove categories affectation
		$query->delete('#__yoorecipe_categories');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$db->setQuery($query);
		$result[] = $db->execute();
			
		// Insert cross categories
		foreach($category_ids as $category_id) {
			$result[] = $this->insertCrossCategory($recipe_id, $category_id);
		}
		
		return !in_array(false, $result);
	}
	
	/**
	* insertCrossCategory
	*/
	public function insertCrossCategory($recipe_id, $cat_id) {
	
		// Prepare query
		$db = JFactory::getDBO();	
		$query = $db->getQuery(true);
		
		$query->clear();
		$query->insert('#__yoorecipe_categories');
		$query->set('recipe_id = '.$db->quote($recipe_id));
		$query->set('cat_id = '.$db->quote($cat_id));
				
		$db->setQuery((string)$query);
		if (!$db->execute()) {
			$this->setError($db->getErrorMsg());
			echo $db->getErrorMsg();
			return false;
		} else {
			return true;
		}
	}
	
	/**
	* deleteCrossCategoriesByRecipeId
	*/
	public function deleteCrossCategoriesByRecipeId($recipe_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Delete cross categories
		$query->delete('#__yoorecipe_categories');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * insertCrossCategoryObj
	 */
	public function insertCrossCategoryObj($crossCategoryObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_categories', $crossCategoryObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	 * updateCrossCategoryObj
	 */
	public function updateCrossCategoryObj($crossCategoryObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe_categories', $crossCategoryObj, 'id', true);
	}
}