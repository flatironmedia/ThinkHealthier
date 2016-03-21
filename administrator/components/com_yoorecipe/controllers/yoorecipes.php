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
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * YooRecipes Controller
 */
class YooRecipeControllerYooRecipes extends JControllerAdmin
{

	protected $_task;
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	ContentControllerArticles
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unvalidate',	'validate');
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'YooRecipe', $prefix = 'YooRecipeModel', $config = array()) 
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
	
	/**
	 * Method to publish a list of recipes.
	 *
	 * @return	void
	 * @since	1.0
	 */
	function publish()
	{
		$publish = ($this-> getTask () == 'publish') ? 1 : 0;
		$input 	= JFactory::getApplication()->input;
		$cid 	= $input->get('cid', array(), 'ARRAY');
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yoorecipe/tables');
		$reviewTable =JTable::getInstance('Yoorecipe', 'YooRecipeTable');
		$reviewTable->publish($cid, $publish);

		$this->setRedirect( 'index.php?option=com_yoorecipe');
	}
	
	/**
	 * Method to validate/unvalidate a list of recipes.
	 *
	 * @return	void
	 * @since	2.1.0
	 */
	function validate()
	{
		$validate 	= ($this-> getTask () == 'validate') ?  1 : 0;
		$input 		= JFactory::getApplication()->input;
		$cid 		= $input->get('cid', array(), 'ARRAY');
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yoorecipe/tables');
		$reviewTable = JTable::getInstance('Yoorecipe', 'YooRecipeTable');
		$reviewTable->validate($cid, $validate);

		$this->setRedirect('index.php?option=com_yoorecipe');
	}
	
	/**
	 * Method to toggle the featured setting of a list of recipes.
	 *
	 * @return	void
	 * @since	1.6
	 */
	function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$input = JFactory::getApplication()->input;
		$ids	= $input->get('cid', array(), 'ARRAY');
		$value	= 1;

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel('YooRecipes');
			
			// Publish the items.
			if (!$model->featured($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_yoorecipe&view=yoorecipes');
	}
	
	/**
	 * Method to toggle the featured setting of a list of recipes.
	 *
	 * @return	void
	 * @since	1.6
	 */
	function unfeatured() {
		
		$input 	= JFactory::getApplication()->input;
		$ids	= $input->get('cid', array(), 'ARRAY');
		$value	= 0;

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel('YooRecipes');
			
			// Publish the items.
			if (!$model->featured($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_yoorecipe&view=yoorecipes');
	}
	
	/**
	* delete
	*/
	function delete() {
	
		// Init variables
		$user 	= JFactory::getUser();
		$input 	= JFactory::getApplication()->input;
		$pks	= $input->get('cid', array(), 'ARRAY');
		
		// Check user is authorized to perform delete operations 
		$authorised = $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.delete', 'com_yoorecipe');
		if ($authorised !== true) {
			JError::raiseWarning(500, JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION'));
			return;
		}
		
		// Get model
		$result = array();
		$model = $this->getModel('YooRecipe');
		foreach ($pks as $i => $pk) {
			$result[] = $model->deleteRecipeById($pk);
		}

		$this->setRedirect('index.php?option=com_yoorecipe&view=yoorecipes');
	}
}