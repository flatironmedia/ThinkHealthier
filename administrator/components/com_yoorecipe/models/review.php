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
class YooRecipeModelReview extends JModelAdmin
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
	public function getTable($type = 'Review', $prefix = 'YooRecipeTable', $config = array()) 
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
		$form = $this->loadForm('com_yoorecipe.review', 'review', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	/**
	* deleteReviewByRecipeIdAndReviewId
	*/
	public function deleteReviewByRecipeIdAndReviewId($recipe_id, $review_id = null) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->delete('#__yoorecipe_reviews');
		if ($review_id != null) {
			$query->where('id = '.$db->quote($review_id));
		}
		$query->where('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	* hasUserAlreadyCommentedRecipe
	*/
	public function hasUserAlreadyCommentedRecipe($recipe_id, $user_id) {
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('count(r.id)');

		// From the recipe rating table
		$query->from('#__yoorecipe_reviews r');
		//$query->where('(r.user_id = '.$db->quote($user_id). ' OR r.ip_address = '.$db->quote($_SERVER['REMOTE_ADDR']).')');
		if ($user_id != 0) {
			$query->where('r.user_id = '.$db->quote($user_id));
		}
		$query->where('r.recipe_id = '.$db->quote($recipe_id));

		$db->setQuery($query);
		
		return ($db->loadResult() > 0) ? true : false;
	}
	
	/**
	 * getReviewById
	 */
	public function getReviewById($rating_id) {

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('r.id, r.recipe_id, r.note, r.author, r.user_id, r.email, r.comment, r.creation_date, r.published, r.abuse');

		// From the recipe rating table
		$query->from('#__yoorecipe_reviews r');
		$query->where('r.id = '.$db->quote($rating_id));

		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	* reportReview
	*/
	public function reportReview($recipe_id, $review_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->update('#__yoorecipe_reviews');
		$query->set('abuse = 1');
		$query->where('id = '.$db->quote($review_id));
		$query->where('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * insertReviewObj
	 */
	public function insertReviewObj($reviewObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_reviews', $reviewObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	 * updateReviewObj
	*/
	public function updateReviewObj($reviewObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe_reviews', $reviewObj, 'id', true);
	}
}