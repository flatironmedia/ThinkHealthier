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
 * YooRecipe Favourite Model
 */
class YooRecipeModelFavourite extends JModelAdmin
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
	public function getTable($type = 'Favourite', $prefix = 'YooRecipeTable', $config = array()) 
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
		$form = $this->loadForm('com_yoorecipe.favourite', 'favourite', array('control' => 'jform', 'load_data' => $loadData));
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
		return JFactory::getApplication()->getUserState('com_yoorecipe.edit.favourite.data', array());
	}	
	
	/**
	 * insertFavouritesObj
	 */
	public function insertFavouritesObj($favouritesObj) {
		
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_favourites', $favouritesObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	 * updateFavouritesObj
	 */
	public function updateFavouritesObj($favouritesObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe_favourites', $favouritesObj, 'id', true);
	}
	
	/**
	* addToFavourites
	*/
	public function addToFavourites($recipe_id, $user) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->insert('#__yoorecipe_favourites');
		$query->set('recipe_id = '.$db->quote($recipe_id));
		$query->set('user_id = '.$db->quote($user->id));
		
		$db->setQuery($query);
		return $db->execute();
	}
}