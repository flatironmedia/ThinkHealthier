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

// No direct access
defined('_JEXEC') or die;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * YooRecipe Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class YooRecipeModelShoppingList extends JModelAdmin
{
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'ShoppingList', $prefix = 'YooRecipeTable', $config = array()) 
	{
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
		$form = $this->loadForm('com_yoorecipe.shoppinglist', 'ShoppingList', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
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
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.yoorecipe_shoppinglist.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
	
	/**
	* getShoppingListById
	*/
	public function getShoppingListById($id, $get_details = true)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('id, title, creation_date, infos, user_id');
		$query->from('#__yoorecipe_shoppinglists as sl');
		
		$query->where('id = '.$db->quote($id));
		$db->setQuery((string)$query);
		$shopping_list = $db->loadObject();
		
		if ($shopping_list && $get_details) {
		
			$shoppingListDetailsModel	= JModelLegacy::getInstance('shoppinglistdetails','YooRecipeModel');
			$shopping_list->details 	= $shoppingListDetailsModel->getShoppingListDetails($id, $shopping_list->user_id);
		}
		
		return $shopping_list;
	}
	
	/**
	* deleteShoppingListById
	*/
	public function deleteShoppingListById($id, $delete_children = true)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->delete('#__yoorecipe_shoppinglists');
		$query->where('id = '.$db->quote($id));
		
		$db->setQuery((string)$query);
		
		$results = array();
		$results[] = $db->execute();
		
		if (!in_array(false, $results) && $delete_children) {
		
			$shoppingListDetailsModel	= JModelLegacy::getInstance('shoppinglistdetails','YooRecipeModel');
			$results[] 					= $shoppingListDetailsModel->deleteShoppingListDetailsBySLId($id);
		}
		
		return !in_array(false, $results);
	}
	
	/**
	* deleteShoppingListByTitle
	*/
	public function deleteShoppingListByTitle($title, $user_id, $delete_children = true)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$results = array();
		
		if ($delete_children) {
			
			$shoppingListDetailsModel	= JModelLegacy::getInstance('shoppinglistdetails','YooRecipeModel');
			$results[] 					= $shoppingListDetailsModel->deleteShoppingListDetailsBySlTitle($title, $user_id);
		}
		
		if (!in_array(false, $results)) {
			$query->delete('#__yoorecipe_shoppinglists');
			$query->where('title = '.$db->quote($title));
			$query->where('user_id = '.$db->quote($user_id));
			
			$db->setQuery((string)$query);
			$results[] = $db->execute();
		}
		
		return !in_array(false, $results);
	}
	
	/**
	* updateShoppingListTitle
	*/
	public function updateShoppingListTitle($id, $title, $user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->update('#__yoorecipe_shoppinglists');

		$query->set('title = '.$db->quote($title));
		$query->where('id = '.$db->quote($id));
		$query->where('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* createShoppingList
	*/
	public function createShoppingList($title, $creation_date, $user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->insert('#__yoorecipe_shoppinglists');
		
		$query->set('title = '.$db->quote($title));
		$query->set('creation_date = '.$db->quote($creation_date));
		$query->set('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		$result = $db->execute();
		if ($result !== false) {
			return $db->insertid();
		} else {
			return false;
		}
	}
	
	/**
	* doesShoppingListIdExistForUser
	*/
	public function doesShoppingListIdExistForUser($shoppinglist_id, $user_id) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('count(id)');
		$query->from('#__yoorecipe_shoppinglists');
		$query->where('id = '.$db->quote($shoppinglist_id));
		$query->where('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string) $query);
		$count = $db->loadResult();
		
		return ($count == 1);
	}
	
	/**
	* insertShoppingListObj
	*/
	public function insertShoppingListObj($shoppingListObj) {
	
		// Prepare query
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_shoppinglists', $shoppingListObj, 'id');
		
		if ($result === false) {
			return false;
		}
		
		return $db->insertid();
	}	
	
	/**
	 * updateShoppingListObj
	 */
	public function updateShoppingListObj($shoppingListObj) {
		
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe_shoppinglists', $shoppingListObj, 'id', true);
	}
}