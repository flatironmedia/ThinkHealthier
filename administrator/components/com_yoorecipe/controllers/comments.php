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
 * YooRecipes Comments Controller
 */
class YooRecipeControllerComments extends JControllerAdmin
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
		$this->registerTask('setToNonOffensive',	'setToOffensive');
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Comments', $prefix = 'YooRecipeModel', $config = array()) 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	/**
	 * Method to publish a list of recipes.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function publish()
	{
		$publish;
		if ($this->getTask() == 'publish') {
			$publish = 1;
		}
		else {
			$publish = 0;
		}
		
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid', array(), 'ARRAY');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yoorecipe/tables');
		
		$reviewTable = JTable::getInstance('Comment', 'YooRecipeTable');
		$reviewTable->publish($cid, $publish);

		// Update recipes global note
		$yoorecipeModel	 		= $this->getModel('yoorecipe');
		$commentModel	 		= $this->getModel('comment');
		
		foreach ($cid as $comment_id) {
		
			$recipe_id 	= $commentModel->getRecipeIdFromRatingId($comment_id);
			$recipe 	= $yoorecipeModel->getRecipeById($recipe_id, array('ratings' => 1));
			$yoorecipeModel->updateRecipeGlobalNote($recipe_id);
		}
		
		$this->setRedirect( 'index.php?option=com_yoorecipe&view=comments');
	}
	
	/**
	 * Method to publish a list of recipes.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function setToOffensive()
	{
		$offensive 	= ($this-> getTask () == 'setToOffensive') ? 1 : 0;
		$input 		= JFactory::getApplication()->input;
		$cid 		= $input->get('cid', array(), 'ARRAY');
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yoorecipe/tables');
		$reviewTable = JTable::getInstance('Comment', 'YooRecipeTable');
		$reviewTable->offensive($cid, $offensive);
		
		// Update recipes global note
		$yoorecipeModel	 = $this->getModel('yoorecipe');
		$commentModel	 = $this->getModel('comment');
		
		foreach ($cid as $comment_id) {
			$recipe_id 	= $commentModel->getRecipeIdFromRatingId($comment_id);
			$recipe 	= $yoorecipeModel->getRecipeById($recipe_id, array('ratings' => 1));
			$yoorecipeModel->updateRecipeGlobalNote($recipe_id);
		}

		$this->setRedirect( 'index.php?option=com_yoorecipe&view=comments');
	}
	
	/**
	* delete
	*/
	function delete() {
	
		$input 	= JFactory::getApplication()->input;
		$pks	= $input->get('cid', array(), 'ARRAY');
		
		// Get the YooRecipe model.
		$model = $this->getModel('Comments');
		
		// Update recipes global note
		$yoorecipeModel	 		= $this->getModel('yoorecipe');
		$commentModel	 		= $this->getModel('comment');
		
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) {
			$recipe_id 	= $commentModel->getRecipeIdFromRatingId($pk);
			$model->deleteCommentsById($pk);
			$yoorecipeModel->updateRecipeGlobalNote($recipe_id);
		}

		$this->setRedirect('index.php?option=com_yoorecipe&view=comments');
	}
}