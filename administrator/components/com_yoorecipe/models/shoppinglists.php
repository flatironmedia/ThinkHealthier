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
class YooRecipeModelShoppingLists extends JModelList
{
	/**
	 * @var string msg
	 */
	protected $msg;
	
	var $_total 		= null;
	var $_pagination 	= null;
 	var $_type 			= null;
	
	function __construct() {
		parent::__construct();
	}
  
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC') {}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$input	= JFactory::getApplication()->input;
		$user	= JFactory::getUser();
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSQL());
		
		// Select some fields
		$query->select('id, title, creation_date, infos, user_id');

		// From the recipe table
		$query->from('#__yoorecipe_shoppinglists as sl');
		return $query;
	}
	
	/**
	* getShoppingListsByUserId
	*/
	public function getShoppingListsByUserId($user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('id, title, creation_date, infos, user_id');
		$query->from('#__yoorecipe_shoppinglists as sl');
		
		$query->where('user_id = '.$db->quote($user_id));
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	/**
	 * getAllRecipeShoppinglists
	 */
	public function	getAllRecipeShoppinglists() {	
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe shoppinglists table
		$query->select('*');
		$query->from('#__yoorecipe_shoppinglists');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* truncateYoorecipeShoppingLists
	*/
	public function truncateYoorecipeShoppingLists() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_shoppinglists`;";
		$db->setQuery($query);
		return $db->execute();
	}
}