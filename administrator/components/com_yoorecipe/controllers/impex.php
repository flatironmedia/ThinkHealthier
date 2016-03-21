<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
jimport( 'joomla.filesystem.file' );
 
/**
 * YooRecipes Controller
 */
class YooRecipeControllerImpex extends JControllerAdmin
{

	protected $_task;
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	YooRecipeControllerImpex
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Impex', $prefix = 'YooRecipeModel', $config = array()) 
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
	
	
	/**
	 * Method to export recipes
	 *
	 * @return	void
	 * @since	1.6
	 */
	function export()
	{
		// Check for request forgeries and load helper
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		JHtml::_('exportuserdatautils.exportUserData2XML');
	}
	
	/**
	 * Method to import recipes
	 *
	 * @return	void
	 * @since	1.6
	 */
	function import()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get variables
		$app = JFactory::getApplication();
		$errors = array();
		
		/// Get the file path from POST
		$fieldName = 'import_file';
		
		$fileError = $_FILES[$fieldName]['error'];
		if ($fileError > 0) 
		{
			switch ($fileError)  {
				case 1: $errors[] = JText::_('COM_YOORECIPE_ERROR_WARNFILETOOLARGE');
				case 2: $errors[] = JText::_('COM_YOORECIPE_ERROR_WARNFILETOOLARGE');
				return;
		 
				case 3: $errors[] = JText::_('COM_YOORECIPE_ERROR_PARTIAL_UPLOAD');
				return;
		 
				case 4: $errors[] = JText::_('COM_YOORECIPE_ERROR_FILE_NOT_FOUND');
				return;
			}
		}
		
		// Check the file extension is ok
		$fileName = $_FILES[$fieldName]['name'];
		$extension = strtolower(JFile::getExt($fileName));
				
		// Assume the extension is false until we know its ok
		if ($extension != 'zip' && $extension != 'xml') {
			$errors[] = JText::sprintf( 'COM_YOORECIPE_BAD_FILE_EXTENSION', 'zip, xml');
		}

		if (count($errors) > 0) {
			JError::raiseError(500, implode('<br />', $errors));
		} else {
			
			// Get variable from POST
			$input		= JFactory::getApplication()->input;
			$mode		= $input->get('mode', '', 'STRING');
			
			JHtml::_('importuserdatautils.importUserDataFromFile', $_FILES[$fieldName], $mode);
		}
		
		$app->redirect('index.php?option=com_yoorecipe&view=impex');
	}
}