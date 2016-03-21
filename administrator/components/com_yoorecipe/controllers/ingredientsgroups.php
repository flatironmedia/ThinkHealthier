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
class YooRecipeControllerIngredientsGroups extends JControllerAdmin
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
		$this->registerTask('publish',	'unpublish');
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'IngredientsGroups', $prefix = 'YooRecipeModel', $config = array()) 
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
		$publish 	= ($this->getTask () == 'publish') ? 1 : 0;
		$input 		= JFactory::getApplication()->input;
		$cid 		= $input->get('cid', array(), 'ARRAY' );
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yoorecipe/tables');
		$reviewTable = JTable::getInstance('IngredientsGroup', 'YooRecipeTable');
		$reviewTable->publish($cid, $publish);

		$this->setRedirect('index.php?option=com_yoorecipe&view=ingredientsgroups');
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
			$model = $this->getModel('IngredientsGroups');
			
			// Publish the items.
			if (!$model->featured($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_yoorecipe&view=ingredientsgroups');
	}
}