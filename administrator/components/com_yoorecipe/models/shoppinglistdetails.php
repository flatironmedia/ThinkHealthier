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
jimport('joomla.application.component.modellist');

/**
 * YooRecipe Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class YooRecipeModelShoppingListDetails extends JModelList
{
	
	/**
	* getShoppingListDetails
	*/
	public function getShoppingListDetails($shopping_list_id, $user_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('id, sl_id, quantity, description, status, infos, user_id');
		$query->from('#__yoorecipe_shoppinglist_details');
		
		$query->where('sl_id = '.$db->quote($shopping_list_id));
		$query->where('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	/**
	* deleteShoppingListDetails
	*/
	public function deleteShoppingListDetailsBySLId($sl_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->delete('#__yoorecipe_shoppinglist_details');
		$query->where('sl_id = '.$db->quote($sl_id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* deleteShoppingListDetailsBySlTitle
	*/
	public function deleteShoppingListDetailsBySlTitle($title, $user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->delete('#__yoorecipe_shoppinglist_details');
		$query->where('sl_id in (select id from #__yoorecipe_shoppinglists where title = '.$db->quote($title).' and user_id = '.$db->quote($user_id).')');
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* insertUpdateShoppingListDetails
	*/
	public function insertUpdateShoppingListDetails($shoppinglist_details)
	{
	
		$results = array();
		
		$modelShoppingListDetail	= JModelLegacy::getInstance('shoppinglistdetail', 'YooRecipeModel');
		foreach($shoppinglist_details as $shoppinglist_detail)
		{
			if($shoppinglist_detail->id == 0) {
				$results[] = $modelShoppingListDetail->insertShoppingListDetailObj($shoppinglist_detail);
			} else{
				$results[] = $modelShoppingListDetail->updateShoppingListDetailObj($shoppinglist_detail);
			}
		}
		
		return !in_array(false, $results);
	}
	
	/**
	 * getAllRecipeShoppinglistsDetails
	 */
	public function	getAllRecipeShoppingListsDetails() {	
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe shoppinglists_details table
		$query->select('*');
		$query->from('#__yoorecipe_shoppinglist_details');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* truncateYoorecipeShoppingListDetails
	*/
	public function truncateYoorecipeShoppingListDetails() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_shoppinglist_details`;";
		$db->setQuery($query);
		return $db->execute();
	}
}