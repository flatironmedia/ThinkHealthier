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
 
// import Joomla controller library
jimport('joomla.application.component.controller');
jimport( 'joomla.filter.output' );

/**
 * YooRecipe Component Controller
 */
class YooRecipeController extends JControllerLegacy
{

public function __construct($config = array())
	{
		parent::__construct($config);
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
	 * Task that inserts a comment for a given recipe
	 */
	public function addRecipeReview()
	{
		$yooRecipeparams	= JComponentHelper::getParams('com_yoorecipe');
		$show_recaptch		= $yooRecipeparams->get('show_recaptch', 'std');
		$recaptcha_version	= $yooRecipeparams->get('recaptcha_version', 'std');

		$json = new stdclass;
		$json->status = false;
		
		$check_valid = false;
		if ($show_recaptch == 'recaptcha') {
		
			require_once JPATH_COMPONENT.'/lib/recaptchalib.php';
			$recaptcha_private_key = $yooRecipeparams->get('recaptcha_private_key');
			
			switch ($recaptcha_version) {
				case 'v1':
				default:
				
					$resp = recaptcha_check_answer($recaptcha_private_key, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

					if ($resp->is_valid) {
						$check_valid = true;
					}
				break;
				
				case 'v2':
			
					$captcha = false;
					if(isset($_POST['g-recaptcha-response'])){
						$captcha = $_POST['g-recaptcha-response'];
					}
					if(!$captcha){
						echo json_encode($json);
						return;
					}
					
					$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_private_key."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']));
					if($response->success == false) {
						echo json_encode($json);
						return;
					} else {
						$check_valid = true;
					}
				break;
			
			} // End switch ($recaptcha_version) {
			
		} // End if ($show_recaptch == 'recaptcha') {
		else {
			$check_valid = true;
		}
		
		if ($check_valid) {
			$rating = $this->persistReview();
			if ($rating !== false) {
				$json->status = true;
			}
		}
			
		echo json_encode($json);
	}
	
	/**
	* Write a review to database
	*/
	private function persistReview() {
	
		$yooRecipeparams					= JComponentHelper::getParams('com_yoorecipe');
		$send_email_on_review				= $yooRecipeparams->get('send_email_on_review', 0, 'INT');
		$send_email_on_review_to_author 	= $yooRecipeparams->get('send_email_on_review_to_author', 0, 'INT');
		$auto_publish_reviews				= $yooRecipeparams->get('auto_publish_reviews', 1, 'INT');
		$show_author_name					= $yooRecipeparams->get('show_author_name', 'username');
		
		// Get form parameters
		$input 	= JFactory::getApplication()->input;
		$recipe_id 	= $input->get('recipeId', 0, 'INT');
		$comment	= $input->get('comment', '', 'STRING');
		$email		= htmlspecialchars($input->get('email', '', 'STRING'));
		$author		= htmlspecialchars($input->get('author', '', 'STRING'));
		$note	 	= htmlspecialchars($input->get('rating', 0, 'INT'));
		$userId		= htmlspecialchars($input->get('userId', 0, 'INT'));
		
		// Build comment object
		$reviewObj = new stdClass;
		$reviewObj->recipe_id 	= $recipe_id;
		$reviewObj->note 		= $note;
		$reviewObj->author 		= $author;
		$reviewObj->user_id 	= $userId;
		$reviewObj->email 		= $email;
		$reviewObj->ip_address	= $_SERVER['REMOTE_ADDR'];
		$reviewObj->comment 	= $comment;
		$reviewObj->published 	= $auto_publish_reviews;
		$reviewObj->abuse	 	= 0;

		// Insert object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_reviews', $reviewObj, 'id');
		
		if ($result === false) {
			return false;
		}
		
		// Complete object with missing info
		$reviewObj->id 				= $db->insertid();
		$reviewObj->creation_date 	= JFactory::getDate()->toSQL();
		$user = JFactory::getUser($userId);
		switch ($show_author_name) {
			case 'username':
				$reviewObj->author_name = $user->username;
				break;
			case 'name':
				$reviewObj->author_name = $user->name;
				break;
		}
		
		// Get recipe
		$model 	= $this->getModel();
		$recipe = $model->getRecipeById($recipe_id, $config = array('ingredients' => 1, 'categories' => 1, 'seasons' => 1, 'ratings' => 1));
		
		// Notify creation to admin
		if ($send_email_on_review){
			JHtmlEmailUtils::sendMailToAdminOnSubmitComment($reviewObj, $recipe);
		}
		
		// Notify creation to recipe author
		if ($send_email_on_review_to_author){
			JHtmlEmailUtils::sendMailToAuthorOnSubmitComment($reviewObj, $recipe);
		}
		
		// Update recipe global note
		$result = $model->updateRecipeGlobalNote($recipe_id);
		if ($result === false) {
			return false;
		}
		
		JPluginHelper::importPlugin( 'yoorecipe' );
		$dispatcher = JDispatcher::getInstance();
		$results 	= $dispatcher->trigger( 'onReviewCreate', array('com_yoorecipe.'.$this->name, &$recipe, &$reviewObj) );
		
		return $reviewObj;
	}
	
	/**
	 * Task to delete a recipe
	 * Called using Ajax
	 */
	public function deleteRecipe() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="deleteRecipe.json"');
		
		// Init variables
		$user 		= JFactory::getUser();
		$result 	= new stdclass;
		$continue 	= true;
		
		// Get form parameters
		$input 		= JFactory::getApplication()->input;
		$recipe_id 	= $input->get('id', 0, 'INT');
		
		// Check user tries to delete its own recipe
		$model 		= $this->getModel();
		$userId 	= $model->getAuthorByRecipeId($recipe_id);
		
		// Check user is connected
		if ($user->guest) {
			$result->status = false;
			$result->msg = JText::_('COM_YOORECIPE_SESSION_TIMED_OUT', true);
			$continue = false;
		}
		
		// Check recipe is set
		if ($continue && !isset($recipe_id)) {
			$result->status = false;
			$result->msg = JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION', true);
			$continue = false;
		}
		
		// Check user is authorized to perform delete operations 
		$authorised = $user->authorise('core.admin', 'com_yoorecipe') || ($user->authorise('core.delete.own', 'com_yoorecipe') && $user->id == $userId) || $user->authorise('core.delete', 'com_yoorecipe');
		if ($continue && $authorised !== true) {
			$result->status = false;
			$result->msg = JText::_('JERROR_ALERTNOAUTHOR', true);
			$continue = false;
		}
		
		if ($continue) {
			$result->status = $model->deleteRecipeById($recipe_id);
		}
		
		echo json_encode($result);
	}
	
	/**
	 * Task to delete a recipe
	 */
	public function deleteComment() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="deleteComment.json"');
		
		// Get User & Component parameters
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$user 		= JFactory::getUser();
		$result 	= new stdclass;
		$result->status = false;
		$continue	= true;
		
		// Check user is connected
		if ($user->guest) {
			$result->msg = JText::_('COM_YOORECIPE_SESSION_TIMED_OUT', true);
			$continue = false;
		}
		
		if ($continue) {
		
			// Get form parameters
			$input 			= JFactory::getApplication()->input;
			$model 			= $this->getModel();
			$reviewModel	= JModelLegacy::getInstance('review', 'YooRecipeModel');
			
			$recipe_id	= $input->get('recipeId', 0, 'INT');
			$comment_id	= $input->get('commentId', 0, 'INT');

			// Check user is authorized to perform delete comment operations 
			$rating 	= $reviewModel->getReviewById($comment_id);
			$authorised = $user->authorise('core.admin', 'com_yoorecipe') || ($user->authorise('recipe.comments.edit.own', 'com_yoorecipe') && $rating->user_id == $user->id);
			
			if ($authorised != 1) {
				$result->msg = JText::_('JERROR_ALERTNOAUTHOR', true);
				$continue = false;
			}
			
			if ($continue && (!isset($recipe_id) || !isset($comment_id)) ) {
				$result->msg = JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION', true);
				$continue = false;
			}
			
			// All previous tests are ok, perform delete
			if ($continue) {
				
				$results = array();
				$results[] = $reviewModel->deleteReviewByRecipeIdAndReviewId($recipe_id, $comment_id);
				$results[] = $model->updateRecipeGlobalNote($recipe_id);
				if (!in_array(false, $results)) {
					$recipe = $model->getRecipeById($recipe_id, $config = array('ratings' => 1));
					$result->status = true;
					$result->html = JHtmlYooRecipeUtils::generateRecipeRatings($recipe, $params->get('enable_reviews', 1), $params->get('rating_style', 'stars'));
				}
				
				JPluginHelper::importPlugin( 'yoorecipe' );
				$dispatcher = JDispatcher::getInstance();
				$results 	= $dispatcher->trigger( 'onReviewDelete', array( 'com_yoorecipe.'.$this->name, &$comment_id) );
			}
		}
		
		echo json_encode($result);
	}
	
	/**
	 * Report review as abusive
	 */
	public function reportReview() {
		
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="reportReview.json"');
		
		// Get User & Component parameters
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$user 		= JFactory::getUser();
				
		$result = new stdclass;
		$result->status = false;
		
		// Get form parameters
		$input 			= JFactory::getApplication()->input;
		$reviewModel	= JModelLegacy::getInstance('review', 'YooRecipeModel');
		$recipe_id	= $input->get('recipeId', 0, 'INT');
		$comment_id	= $input->get('commentId', 0, 'INT');

		if (!isset($recipe_id) || !isset($comment_id) ) {
			$result->msg = JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION', true);
		} else {
			$result->status = $reviewModel->reportReview($recipe_id, $comment_id);
		}
		
		echo json_encode($result);
	}
	
	/**
	 * addToFavourites
	 */
	public function addToFavourites()
	{
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="addToFavourites.json"');
		
		// Get User & Component parameters
		$params = JComponentHelper::getParams('com_yoorecipe');
		$user 	= JFactory::getUser();

		// Get parameters
		$input 		= JFactory::getApplication()->input;
		$recipe_id	= $input->get('recipe_id', 0, 'INT');
		
		// Get variables
		$result = new stdclass;
		
		// Perform model operation
		$favouriteModel		= JModelLegacy::getInstance('favourite', 'YooRecipeModel');
		$result->status 	= $favouriteModel->addToFavourites($recipe_id, $user);
		
		// Refresh screen
		if ($result->status) {
			$recipe = new StdClass;
			$recipe->id 		= $recipe_id;
			$recipe->favourite 	= 1;
			$result->html = JHtmlYooRecipeIcon::favourites($recipe, $params);
		}

		echo json_encode($result);
	}
	
	/**
	 * removeFromFavourites
	 */
	public function removeFromFavourites()
	{
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="removeFromFavourites.json"');
		
		// Get User & Component parameters
		$params = JComponentHelper::getParams('com_yoorecipe');
		$user 	= JFactory::getUser();

		// Get parameters
		$input 		= JFactory::getApplication()->input;
		$recipe_id	= $input->get('recipe_id', 0, 'INT');
		
		// Get variables
		$result = new stdclass;
		
		// Perform model operation
		$favouritesModel	= JModelLegacy::getInstance('favourites', 'YooRecipeModel');
		$result->status 	= $favouritesModel->deleteFromFavourites($recipe_id, $user);
		
		if ($result->status) {
			$recipe 			= new stdclass;
			$recipe->id 		= $recipe_id;
			$recipe->favourite 	= 0;
			$result->html = JHtmlYooRecipeIcon::favourites($recipe, $params);
		}
		
		echo json_encode($result);
	}
	
	/**
	* getReview
	*/
	public function getReview() {
		
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="getReview.json"');
		
		// Get parameters
		$input 		= JFactory::getApplication()->input;
		$rating_id 	= $input->get('rating_id', 0, 'INT');
		
		$reviewModel	= JModelLegacy::getInstance('review', 'YooRecipeModel');
		$review			= $reviewModel->getReviewById($rating_id);
		echo json_encode($review);
	}
	
	/**
	* updateReview
	*/
	public function updateReview() {
		
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="updateReview.json"');
		
		// Check user can edit rating
		$result = new stdclass;
		$result->status = false;
		
		// Get parameters
		$user 			= JFactory::getUser();
		$input 			= JFactory::getApplication()->input;
		$rating_id 		= $input->get('edit_rating_id', 0, 'INT');
		$model 			= $this->getModel();
		$reviewModel	= JModelLegacy::getInstance('review', 'YooRecipeModel');
		$rating			= $reviewModel->getReviewById($rating_id);
		$results	= array();
		
		$yooRecipeparams		= JComponentHelper::getParams('com_yoorecipe');
		$auto_publish_reviews	= $yooRecipeparams->get('auto_publish_reviews', 1, 'INT');
		
		$canManageComments = JHtmlYooRecipeUtils::canManageComments($user, $rating->user_id);
		if ($canManageComments) {
		
			$reviewObj 				= $reviewModel->getReviewById($rating_id);
			$reviewObj->comment 	= $input->get('comment', '', 'STRING');
			if (!$auto_publish_reviews) {
				$reviewObj->published = 0; 
			}
			
			$results[]= $reviewModel->updateReviewObj($reviewObj);
			if (!$auto_publish_reviews) {
				$results[] = $model->updateRecipeGlobalNote($reviewObj->recipe_id);
			}
		}
		
		if (!in_array(false, $results)) {
			$result->status = true;
			$result->published = $reviewObj->published;
		}
		
		echo json_encode($result);
	}
	
	/**
	* createShoppingList
	*/
	public function createShoppingList() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="createShoppingList.json"');
		
		// Retrieve parameters
		$input			= JFactory::getApplication()->input;
		$title 			= $input->get('title', '', 'STRING');
		$fromRecipe		= $input->get('fromRecipe', 0, 'INT');
		
		$creation_date  = JFactory::getDate()->toSQL();
		$user			= JFactory::getUser();
		
		$result = new stdclass;
		$result->status = false;
			if (!$user->guest) {
				
				$model 				= JModelLegacy::getInstance('shoppinglist', 'YooRecipeModel');
				$shoppinglist_id 	= $model->createShoppingList($title, $creation_date, $user->id);
				if ($shoppinglist_id === false) {
					$result->status = false;
				} else {
					$result->status = true;
					
					$item = new stdclass;
					$item->id = $shoppinglist_id;
					$item->title = $title;
					$item->creation_date = $creation_date;
					if($fromRecipe == 0)
					{
						$result->html = JHtml::_('shoppinglistutils.generateShoppingListHTML', $item);
					}else{
						$result->html = JHtml::_('shoppinglistutils.generateShoppingListInRecipeHTML', $item);
					}
				}
			} 
		
		echo json_encode($result);
	}
	
	/**
	* createShoppingList
	*/
	public function createShoppingListDetail() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="createShoppingListDetail.json"');
		
		// Get variables
		$input			= JFactory::getApplication()->input;
		$sl_id	 		= $input->get('sl_id', 0, 'INT');
		$quantity 		= $input->get('quantity', '', 'STRING');
		$description	= $input->get('description', '', 'STRING');
		$user_id		= JFactory::getUser()->id;
		
		// Check sl_id belongs to user_id
		$shoppingListModel 			= JModelLegacy::getInstance('shoppinglist', 'YooRecipeModel');
		$shoppingListDetailModel	= JModelLegacy::getInstance('shoppinglistdetail', 'YooRecipeModel');
		$check_ok = $shoppingListModel->doesShoppingListIdExistForUser($sl_id, $user_id);
		
		$result = new stdclass;
		$result->status = false;
		
		if ($check_ok) {
		
			// Turn fraction into decimal if needed
			$qty = str_replace(',', '.', $quantity);
			$qtyToNum;
			if (strpos($quantity, '/') == false) {
				$qtyToNum = $qty;
			} else {
				$fraction = array('whole' => 0);
				preg_match('/^((?P<whole>\d+)(?=\s))?(\s*)?(?P<numerator>\d+)\/(?P<denominator>\d+)$/', $qty, $fraction);
				if ($fraction['denominator'] != 0) {
					$qtyToNum = $fraction['whole'] + $fraction['numerator']/$fraction['denominator'];
				} else {
					$qtyToNum = 0;
				}
			}
			
			$shoppinglist_detail = new stdclass;
			$shoppinglist_detail->sl_id = $sl_id;
			$shoppinglist_detail->quantity = $qtyToNum;
			$shoppinglist_detail->description = $description;
			$shoppinglist_detail->user_id = $user_id;
			
			$shoppinglist_detail_id = $shoppingListDetailModel->insertShoppingListDetailObj($shoppinglist_detail);
			
			$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
			$use_fractions		= $yooRecipeparams->get('use_fractions', 0);

			if ($shoppinglist_detail_id !== false) {
				$result->status = true;
				
				$shoppinglist_detail->id = $shoppinglist_detail_id;
				$result->html = JHtml::_('shoppinglistutils.generateShoppingListDetailHTML', $shoppinglist_detail, $use_fractions);
			}
		}
		
		echo json_encode($result);
	}
	
	/**
	* deleteShoppingList
	*/
	public function deleteShoppingList() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="deleteShoppingList.json"');
		
		// Retrieve parameters
		$input	= JFactory::getApplication()->input;
		$id 	= $input->get('id', 0, 'INT');
		
		$result = new stdclass;
		$model = JModelLegacy::getInstance('shoppinglist', 'YooRecipeModel');
		$result->status = $model->deleteShoppingListById($id);
		
		echo json_encode($result);
	}
	
	/**
	* deleteShoppingListDetail
	*/
	public function deleteShoppingListDetail() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="deleteShoppingListDetail.json"');
				
		// Retrieve parameters
		$input	= JFactory::getApplication()->input;
		$id 	= $input->get('id', 0, 'INT');
		
		$result = new stdclass;
		$shoppingListDetailModel 	= JModelLegacy::getInstance('shoppinglistdetail', 'YooRecipeModel');
		$result->status 			= $shoppingListDetailModel->deleteShoppingListDetail($id);
		
		echo json_encode($result);
	}	
	
	/**
	* updateShoppingListTitle
	*/
	public function updateShoppingListTitle(){
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="updateShoppingListTitle.json"');
				
		$result = new stdclass;
		$result->status = false;
		
		// Retrieve parameters
		$input			= JFactory::getApplication()->input;
		$user			= JFactory::getUser();
		$id 			= $input->get('id', 0, 'INT');
		$title 			= $input->get('title', '', 'STRING');
		
		$model = JModelLegacy::getInstance('shoppinglist', 'YooRecipeModel');
		$status = $model->updateShoppingListTitle($id, $title, $user->id);
		
		if($status){
			$result->status = true;
			$result->html = '<a href="'.JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistroute',$id), true).'">'.$title.'</a>';
		}
		
		echo json_encode($result);
	}

	/**
	* updateShoppingListDetail
	*/
	public function updateShoppingListDetail() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="updateShoppingListDetail.json"');
				
		// Retrieve parameters
		$input			= JFactory::getApplication()->input;
		$id 			= $input->get('id', 0, 'INT');
		$quantity 		= $input->get('quantity', '', 'STRING');
		$description	= $input->get('description', '', 'STRING');
		
		// Turn fraction into decimal if needed
		$qty = str_replace(',', '.', $quantity);
		$qtyToNum;
		if (strpos($quantity, '/') == false) {
			$qtyToNum = $qty;
		} else {
			$fraction = array('whole' => 0);
			preg_match('/^((?P<whole>\d+)(?=\s))?(\s*)?(?P<numerator>\d+)\/(?P<denominator>\d+)$/', $qty, $fraction);
			if ($fraction['denominator'] != 0) {
				$qtyToNum = $fraction['whole'] + $fraction['numerator']/$fraction['denominator'];
			} else {
				$qtyToNum = 0;
			}
		}
		
		$result = new stdclass;
		$shoppingListDetailModel 	= JModelLegacy::getInstance('shoppinglistdetail', 'YooRecipeModel');
		$result->status 			= $shoppingListDetailModel->updateShoppingListDetail($id, $qtyToNum, $description);
		
		echo json_encode($result);
	}
	
	/**
	* updateShoppingListDetailStatus
	*/
	public function updateShoppingListDetailStatus() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="updateShoppingListDetailStatus.json"');
		
		// Retrieve parameters
		$input	= JFactory::getApplication()->input;
		$id 	= $input->get('id', 0, 'INT');
		$status = $input->get('status', '', 'STRING');
		$status = ($status == 'true') ? true : false;
		
		$result = new stdclass;
		$shoppingListDetailModel 	= JModelLegacy::getInstance('shoppinglistdetail', 'YooRecipeModel');
		$result->status 			= $shoppingListDetailModel->updateShoppingListDetailStatus($id, $status);
		
		echo json_encode($result);
	}	
	
	/**
	* addRecipeToShoppingList
	*/
	public function addRecipeToShoppingList() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="addRecipeToShoppingList.json"');
		
		// Get variables
		$input		= JFactory::getApplication()->input;
		$sl_id 		= $input->get('sl_id', 0, 'INT');
		// $item_id	= $input->get('Itemid', 0, 'INT');
		$recipe_id 	= $input->get('recipe_id', 0, 'INT');
		$nb_persons	= $input->get('nb_persons', 0, 'INT');
		
		// Prepare output
		$results = array();
		$result = new stdclass;
		$result->status = false;
		
		// Get models
		$modelShoppingList			= JModelLegacy::getInstance('shoppinglist', 'YooRecipeModel');
		$modelShoppingListDetails	= JModelLegacy::getInstance('shoppinglistdetails', 'YooRecipeModel');
		$modelRecipe				= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		
		// Get data
		$shoppinglist	= $modelShoppingList->getShoppingListById($sl_id, $get_details = true);
		$recipe 		= $modelRecipe->getRecipeById($recipe_id, $config = array('ingredients' => 1));
		 
		// Do the tough work
		$shoppinglist_details = JHtmlShoppingListUtils::addRecipeToShoppingList($recipe, $shoppinglist, $nb_persons);
		$results[] = $modelShoppingListDetails->insertUpdateShoppingListDetails($shoppinglist_details);
		
		$shoppinglist->infos = JHtmlShoppingListUtils::updateShoppingListInfos($shoppinglist, $recipe);
		$results[] = $modelShoppingList->updateShoppingListObj($shoppinglist);
		
		if (!in_array(false, $results)) {
			$result->status = true;
			$result->href 	= JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistroute',$sl_id), false);
		}
		
		echo json_encode($result);
	}
	
	/**
	* addRecipeToQueue
	*/
	public function addRecipeToQueue() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="addRecipeToQueue.json"');
		
		// Get variables
		$user_id		= JFactory::getUser()->id;
		$input		= JFactory::getApplication()->input;
		$recipe_id 	= $input->get('recipe_id', 0, 'INT');
		
		// Get models
		$modelMealPlannerQueue	= JModelLegacy::getInstance('mealplannerqueue', 'YooRecipeModel');
		
		// Get data
		$result = new stdclass;
		$result->status	= $modelMealPlannerQueue->createMealPlannerQueue($user_id, $recipe_id);
		
		$result->html = '<span class="label label-success" onclick="removeRecipeFromQueue('.$recipe_id.');">'.JText::_('COM_YOORECIPE_ACTION_DEQUEUE').'</span>';
		$result->html2 = '<a href="#" onclick="removeRecipeFromQueue('.$recipe_id.');" ><i class="icon-minus"></i> '.JText::_('COM_YOORECIPE_RECIPE_BOX').'</a>';
		echo json_encode($result);
	}
	
	/**
	* removeRecipeFromQueue
	*/
	public function removeRecipeFromQueue() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="removeRecipeFromQueue.json"');
		
		// Get variables
		$user_id	= JFactory::getUser()->id;
		$input		= JFactory::getApplication()->input;
		$recipe_id 	= $input->get('recipe_id', 0, 'INT');
		
		// Get models
		$modelMealPlannerQueue	= JModelLegacy::getInstance('mealplannerqueue', 'YooRecipeModel');
		
		// Get data
		$result = new stdclass;
		$result->status	= $modelMealPlannerQueue->deleteMealPlannerQueueByRecipeIdAndUserId($recipe_id, $user_id);
		
		$result->html = '<span class="label label-info" onclick="addRecipeToQueue('.$recipe_id.');">'.JText::_('COM_YOORECIPE_ACTION_QUEUE').'</span>';
		$result->html2 = '<a href="#" onclick="addRecipeToQueue('.$recipe_id.');"><i class="icon-plus"></i> '.JText::_('COM_YOORECIPE_RECIPE_BOX').'</a>';;
		echo json_encode($result);
	}
	
	/**
	* insertMeal
	*/
	public function insertMeal() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="insertMeal.json"');
		
		// Prepare output
		$result = new stdclass;
		$result->status	= false;
		
		// Exit if session lost
		$user = JFactory::getUser();
		if ($user->guest) {
			echo json_encode($result);
			return;
		}
		
		// Get variables
		$input			= JFactory::getApplication()->input;
		$recipe_id 		= $input->get('recipe_id', 0, 'INT');
		$date 			= $input->get('date', '', 'STRING');
		$nb_servings 	= $input->get('nb_servings', 0, 'INT');
		
		$date_obj = JFactory::getDate($date);
		// $dow = strtolower($date_obj->format('D'));
		
		// Get models
		$modelMeal		= JModelLegacy::getInstance('meal', 'YooRecipeModel');
		$modelYooRecipe	= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		
		//TODO tester le cas ou la recette a ete mise en queue mais effacee depuis...
		$recipe 			= $modelYooRecipe->getRecipeById($recipe_id);
		$meal_id 	= $modelMeal->insertMeal($user->id, $date_obj->format('Y-m-d'), $nb_servings, $recipe_id);
		
		if ($meal_id !== false) {
			$result->status = true;
			$result->html = JHtml::_('mealsutils.generateMealEntryHTML', $meal_id, $recipe->id, $recipe->title, $recipe->alias, $recipe->servings_type_code, $nb_servings , $date, $recipe->picture);
		}
		echo json_encode($result);
	}
	
	/**
	* updateMealDate
	*/
	public function updateMealDate() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="updateMealDate.json"');
		
		// Prepare output
		$result = new stdclass;
		$result->status = false;
		
		// Exit if session lost
		$user = JFactory::getUser();
		if ($user->guest) {
			echo json_encode($result);
			return;
		}
		
		// Get variables
		$input			= JFactory::getApplication()->input;
		$meal_id	= $input->get('meal_id', 0, 'INT');
		$recipe_id 		= $input->get('recipe_id', 0, 'INT');
		$nb_servings	= $input->get('nb_servings', 0, 'INT');
		$meal_date 		= $input->get('date', '', 'STRING');
		
		$date_obj = JFactory::getDate($meal_date);
		// $dow = strtolower($date_obj->format('D'));
		
		// Get models
		$modelMeal		= JModelLegacy::getInstance('meal', 'YooRecipeModel');
		$modelYooRecipe	= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		
		// Get data
		$result->status	= $modelMeal->updateMealDate($meal_id, $recipe_id, $user->id, $meal_date);
		if ($result->status) {
			$recipe = $modelYooRecipe->getRecipeById($recipe_id);
			$result->html = JHtml::_('mealsutils.generateMealEntryHTML', $meal_id, $recipe_id, $recipe->title, $recipe->alias, $recipe->servings_type_code, $nb_servings, $meal_date, $recipe->picture);
		}
		
		echo json_encode($result);
	}
	
	/**
	* updateMealServings
	*/
	public function updateMealServings() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="updateMealServings.json"');
		
		// Prepare output
		$result = new stdclass;
		$result->status = false;
		
		// Exit if session lost
		$user = JFactory::getUser();
		if ($user->guest) {
			echo json_encode($result);
			return;
		}
		
		// Get variables
		$input			= JFactory::getApplication()->input;
		$meal_id	= $input->get('meal_id', 0, 'INT');
		$nb_servings	= $input->get('nb_servings', 0, 'INT');
		
		// Get models
		$modelMeal		= JModelLegacy::getInstance('meal', 'YooRecipeModel');
		$modelYooRecipe	= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		
		// Get data
		$result->status	= $modelMeal->updateMealServings($meal_id, $user->id, $nb_servings);
		if ($result->status) {
			$result->nb_servings = $nb_servings;
		}
		echo json_encode($result);
	}
	
	/**
	* deleteMeal
	*/
	public function deleteMeal() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="deleteMeal.json"');
		
		// Prepare output
		$result = new stdclass;
		$result->status = false;
		
		// Exit if session lost
		$user = JFactory::getUser();
		if ($user->guest) {
			echo json_encode($result);
			return;
		}
		
		// Get variables
		$input			= JFactory::getApplication()->input;
		$meal_id	= $input->get('id', 0, 'INT');
		
		// Get models
		$modelMeal		= JModelLegacy::getInstance('meal', 'YooRecipeModel');
		
		// Do tough work
		$result->status	= $modelMeal->deleteMeal($meal_id, $user->id);
		echo json_encode($result);
	}
	
	/**
	* loadMealPlanDay
	*/
	public function loadMealPlanDay() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="loadMealPlanDay.json"');
		
		// Prepare output
		$result = new stdclass;
		$result->status = false;
		
		// Exit if session lost
		$user = JFactory::getUser();
		if ($user->guest) {
			echo json_encode($result);
			return;
		}
		
		// Get variables
		$input			= JFactory::getApplication()->input;
		$mode			= $input->get('mode', 'next', 'STRING');
		$start_date		= $input->get('start_date', '', 'STRING');
		$end_date		= $input->get('end_date', '', 'STRING');
		
		$start_date_obj	= JFactory::getDate($start_date);
		$end_date_obj	= JFactory::getDate($end_date);
		
		$date_00h00m00s;
		$date_23h59m59s;
		if ($mode == 'next') {
			
			$start_date_obj->add(new DateInterval('P1D')); // +1 day
			$end_date_obj->add(new DateInterval('P1D')); // +1 day
			$date_00h00m00s = JHtmlDateTimeUtils::getDate00h00m00s($end_date_obj);
			$date_23h59m59s = JHtmlDateTimeUtils::getDate23h59m59s($end_date_obj);

		} else if ($mode == 'prev') {
			
			$start_date_obj->sub(new DateInterval('P1D')); // -1 day
			$end_date_obj->sub(new DateInterval('P1D')); // -1 day
			$date_00h00m00s = JHtmlDateTimeUtils::getDate00h00m00s($start_date_obj);
			$date_23h59m59s = JHtmlDateTimeUtils::getDate23h59m59s($start_date_obj);
		}

		// Get models
		$mealsModel			= JModelLegacy::getInstance('meals', 'YooRecipeModel');
		
		// Do tough work
		$meals				= $mealsModel->getMealsByUserIdAndPeriod($user->id, $date_00h00m00s, $date_23h59m59s, $get_details = true);
		$days_of_week		= JHtml::_('mealsutils.buildMealsObject', $date_00h00m00s, $nb_days = 1, $meals);
		
		$html = array();
		foreach ($days_of_week as $date => $day_of_week) {
			$html[] = '<div id="meal_'.$date.'" style="display:none">';
			$html[] = '<h1>'.JText::_($day_of_week->label).'</h1>';
			$html[] = '<div id="meal_ctnr_'.$date.'">';
			foreach ($day_of_week->meals as $meal) {
				$html[] = JHtml::_('mealsutils.generateMealEntryHTML', $meal->meal_id, $meal->recipe_id, $meal->title, $meal->alias, $meal->servings_type_code, $meal->nb_servings, $date, $meal->picture);
			}
			$html[] = '</div>';
			$html[] = '<div class="droppable"></div>';
			$html[] = '</div>';
		}
		
		$result->status			= true;
		$result->html 			= implode("\n", $html);
		$result->new_start_date = $start_date_obj->format('Y-m-d');
		$result->new_end_date 	= $end_date_obj->format('Y-m-d');
		$result->old_start_date = $start_date;
		$result->old_end_date 	= $end_date;
		
		echo json_encode($result);
	}
	
	/**
	* printMeals
	*/
	public function printMeals() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="printMeals.json"');
		
		// Get variables
		$user				= JFactory::getUser();
		$input				= JFactory::getApplication()->input;
		$print_start_date	= $input->get('print_start_date', '', 'STRING');
		$print_end_date		= $input->get('print_end_date', '', 'STRING');

		// Prepare output
		$result = new stdclass;
		$result->status = false;
		$results = array();
		
		// Get models
		$modelShoppingList			= JModelLegacy::getInstance('shoppinglist', 'YooRecipeModel');
		$modelShoppingLists			= JModelLegacy::getInstance('shoppinglists', 'YooRecipeModel');
		$modelShoppinglistdetail	= JModelLegacy::getInstance('shoppinglistdetail', 'YooRecipeModel');
		$yoorecipeModel				= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$mealsModel					= JModelLegacy::getInstance('meals', 'YooRecipeModel');
		
		// Create shopping list
		$shopping_list = new stdclass;
		$shopping_list->id = 0;
		
		// Create shoppinglist
		$shopping_list->title 			= JText::sprintf('COM_YOORECIPE_SHOPPING_LIST_FROM_TO', $print_start_date, $print_end_date);
		$shopping_list->creation_date 	= JFactory::getDate()->toSql();
		$shopping_list->user_id			= $user->id;
		
		// Delete existing similar shopping lists if any
		 $results[] = $modelShoppingList->deleteShoppingListByTitle($shopping_list->title, $user->id, $delete_children = true);
		 $shopping_list->id 				= $modelShoppingList->insertShoppingListObj($shopping_list);
		
		if ($shopping_list->id === false) {
			$results[] = false;
		}
		else {
		 
			$shopping_list->details = array();
			$start_date_obj = JFactory::getDate($print_start_date);
			$end_date_obj 	= JFactory::getDate($print_end_date);
			
			// Get data
			$meals = $mealsModel->getMealsByUserIdAndPeriod($user->id, JHtmlDateTimeUtils::getDate00h00m00s($start_date_obj), JHtmlDateTimeUtils::getDate23h59m59s($end_date_obj), $get_details = true);
			foreach ($meals as $meal) {
				$recipe = $yoorecipeModel->getRecipeById($meal->recipe_id, array('ingredients' => 1));
				$shopping_list->details = JHtmlShoppingListUtils::addRecipeToShoppingList($recipe, $shopping_list, $meal->nb_servings);
			}
			
			// Persist shopping list details
			foreach ($shopping_list->details as $detail) {
				$results[] = $modelShoppinglistdetail->insertShoppingListDetailObj($detail);
			}
		}
		
		
		if (!in_array(false, $results)) {
			$result->status = true;
			$result->url 	= JRoute::_('index.php?option=com_yoorecipe&view=meals&layout=printout&print_start_date='.$print_start_date.
				'&print_end_date='.$print_end_date.
				'&shoppinglist_id='.$shopping_list->id.
				'&tmpl=component',
			false);
		}
		
		echo json_encode($result);
	}
	
	/**
	* searchQueuedRecipes
	*/
	public function searchQueuedRecipes() {
	
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="searchQueuedRecipes.json"');
		
		// Prepare output
		$result = new stdclass;
		$result->status = false;
		
		// Exit if session lost
		$user = JFactory::getUser();
		if ($user->guest) {
			echo json_encode($result);
			return;
		}
		
		// Get variables
		$input			= JFactory::getApplication()->input;
		$search_word	= $input->get('search_word', '', 'STRING');

		// Get models
		$modelMealPlannerQueue	= JModelLegacy::getInstance('mealplannerqueue', 'YooRecipeModel');
		
		// Get data
		$queued_recipes = $modelMealPlannerQueue->getQueuedRecipesBySearchWord($search_word, $user->id);
		if ($queued_recipes !== false) {
		
			$result->status = true;
			$result->nb_results = count($queued_recipes);
			$result->html = JHtml::_('mealsutils.generateRecipeQueueItems', $queued_recipes);
		}
		echo json_encode($result);
	}
}