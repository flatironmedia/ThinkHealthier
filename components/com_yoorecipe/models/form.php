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

// Base this model on the backend version.
// require_once JPATH_ADMINISTRATOR.'/components/com_yoorecipe/models/yoorecipe.php';

/**
 * YooRecipe Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
// class YooRecipeModelForm extends YooRecipeModelYooRecipe
class YooRecipeModelForm extends JModelAdmin
{

	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_yoorecipe.recipe';
	
	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'YooRecipe', $prefix = 'YooRecipeTable', $config = array()) 
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
		$form = $this->loadForm('com_yoorecipe.yoorecipe', 'form', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.yoorecipe.data', array());
		if (empty($data)) 
		{
			$seasonsModel		= JModelLegacy::getInstance('seasons','YooRecipeModel');
			$crossCategoryModel	= JModelLegacy::getInstance('crossCategory','YooRecipeModel');
			
			$data = $this->getItem();
			$data->category_id 	= $crossCategoryModel->getRecipeCategoriesIds($data->id);
			$data->seasons 		= $seasonsModel->getRecipeSeasonsIds($data->id);
		}
		return $data;
	}
	
	/**
	 * Method to get an item.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    Category data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($result = parent::getItem($pk))
		{
			if (!empty($result->id))
			{
				$result->tags = new JHelperTags;
				$result->tags->getTagIds($result->id, 'com_yoorecipe.recipe');
				// $result->metadata['tags'] = $result->tags;
			}			
		}

		return $result;
	}
}