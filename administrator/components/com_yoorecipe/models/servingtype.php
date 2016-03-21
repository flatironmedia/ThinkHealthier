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
class YooRecipeModelServingType extends JModelAdmin
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
	public function getTable($type = 'ServingType', $prefix = 'YooRecipeTable', $config = array()) 
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
		$form = $this->loadForm('com_yoorecipe.servingtype', 'servingtype', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.serving_type.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}
	
	/**
	* deleteServingType
	*/
	public function deleteServingType($unitId) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Delete ingredient unit
		$query->delete('#__yoorecipe_serving_types');
		$query->where('id = '.$db->quote($unitId));
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	* isServingTypeStillInUse
	*/
	public function isServingTypeStillInUse($unitPk) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('count(unit)');
		$query->from('#__yoorecipe_ingredients i');
		$query->join('LEFT', '#__yoorecipe_serving_types u on u.code = i.unit');
		$query->where('u.id = '.$db->quote($unitPk));
		
		$db->setQuery((string)$query);
		return $db->loadResult();
	}
	
	/**
	 * insertServingTypeObj
	 */
	public function insertServingTypeObj($servingTypeObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_serving_types', $servingTypeObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	 * updateServingTypeObj
	 */
	public function updateServingTypeObj($servingTypeObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe_serving_types', $servingTypeObj, 'id', true);
	}
}