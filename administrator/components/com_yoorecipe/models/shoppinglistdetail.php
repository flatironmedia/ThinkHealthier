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
class YooRecipeModelShoppingListDetail extends JModelAdmin
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
	public function getTable($type = 'ShoppingListDetail', $prefix = 'YooRecipeTable', $config = array()) 
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
		$form = $this->loadForm('com_yoorecipe.shoppinglistdetail', 'ShoppingListDetail', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.shoppinglistdetail.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
	
	/**
	* deleteShoppingListDetail
	*/
	public function deleteShoppingListDetail($id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->delete('#__yoorecipe_shoppinglist_details');
		$query->where('id = '.$db->quote($id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* updateShoppingListDetailStatus
	*/
	public function updateShoppingListDetailStatus($id, $status)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->update('#__yoorecipe_shoppinglist_details');

		$query->set('status = '.$db->quote($status));
		$query->where('id = '.$db->quote($id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* updateShoppingListDetail
	*/
	public function updateShoppingListDetail($id, $quantity, $description)
	{
		// Create a new query object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->update('#__yoorecipe_shoppinglist_details');
		$query->set('quantity = '.$db->quote($quantity));
		$query->set('description = '.$db->quote($description));
		$query->where('id = '.$db->quote($id));
		
		$db->setQuery((string) $query);
		return $db->execute();
	}
	
	/**
	 * updateShoppingListDetailObj
	 */
	public function updateShoppingListDetailObj($shoppingListDetailObj) {
		
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe_shoppinglist_details', $shoppingListDetailObj, 'id', true);
	}
	
	/**
	 * insertShoppingListDetailObj
	 */
	public function insertShoppingListDetailObj($shoppingListDetailObj) {
		
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_shoppinglist_details', $shoppingListDetailObj, 'id');
		
		if ($result === false) {
			return false;
		}
		
		return $db->insertid();
	}
}