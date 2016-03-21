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

abstract class JHtmlEmailUtils
{
	/**
	 * Notify a user a recipe has been created
	 */
	public static function sendMailToUserOnCreate($recipe, $update = 0)
	{
		jimport('joomla.mail.helper');
		jimport( 'joomla.utilities.utility' );
		
		$app		= JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');

		$email		= $recipe->author_email;
		$subject	= JText::sprintf('COM_YOORECIPE_RECIPE_CREATION_SUBJECT', $recipe->title);

		// Check for a valid to address
		$error	= false;
		if (! $email  || ! JMailHelper::isEmailAddress($email))
		{
			$error	= JText::sprintf('COM_YOORECIPE_CREATION_EMAIL_NOT_SENT', $recipe->title);
			JError::raiseWarning(0, $error);
		}

		// Check 'From' address is valid
		if (! $mailfrom || ! JMailHelper::isEmailAddress($mailfrom))
		{
			$error	= JText::sprintf('COM_YOORECIPE_CREATION_EMAIL_NOT_SENT', $recipe->title);
			JError::raiseWarning(0, $error);
		}

		if ($error)
		{
			return;
		}

		// Build the message to send
		$body	= JText::sprintf('COM_YOORECIPE_RECIPE_CREATION_BODY', $recipe->author_name, $recipe->title, $FromName);

		// Clean the email data
		$subject 	= JMailHelper::cleanSubject($subject);
		$body	 	= JMailHelper::cleanBody($body);
		$fromname	= JMailHelper::cleanAddress($fromname);

		$mailer = JFactory::getMailer();
		$mailer->setSender($fromname);
		$mailer->addRecipient($email);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		
		// Send the email
		$send = $mailer->Send();
		if ( $send !== true ) {
			JError::raiseNotice(500, JText::sprintf('COM_YOORECIPE_CREATION_EMAIL_NOT_SENT', $recipe->title));
		}
	}
	
	/**
	 * Notify admin on recipe update
	 */
	public static function sendRecipeUpdateNotificationToAdmin($recipe_id) {
	
		$app	= JFactory::getApplication();
		$mailer = JFactory::getMailer();
		$params	= JComponentHelper::getParams('com_yoorecipe');
		
		// Get recipe from model
		$model 	= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		$recipe = $model->getRecipeById($recipe_id, $config = array('ingredients' => 1, 'categories' => 1, 'seasons' => 1, 'ratings' => 1));
		
		$admin_email = $params->get('admin_email', $app->getCfg('mailfrom'));
		$sender = array( 
			$admin_email,
			$app->getCfg('fromname')
		);
		
		// Build message body
		$body = array();
		$body[] = '<h2>'. JText::sprintf('COM_YOORECIPE_RECIPE_AWAITS_MODERATION', $recipe->title ).'</h2>';
		$body[] = '| <a href="'. JUri::root().'administrator/index.php?option=com_yoorecipe&task=yoorecipes.validate&cid[]='.$recipe->id.'">'.JText::_('COM_YOORECIPE_VALIDATE').'</a> | ';
		$body[] = '<a href="'. JUri::root().'administrator/index.php?option=com_yoorecipe&task=yoorecipes.publish&cid[]='.$recipe->id.'">'.JText::_('COM_YOORECIPE_PUBLISH').'</a> |<br/>';
		$body[] = '<div><ul>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_TITLE').':</strong>'.$recipe->title.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_DESCRIPTION_LABEL').':</strong>'.$recipe->description.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_PREPARATION_LABEL').':</strong>'.$recipe->preparation.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_NB_PERSONS_LABEL').':</strong>'.$recipe->nb_persons.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_DIFFICULTY_LABEL'). ':</strong>'.$recipe->difficulty.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_RECIPES_COST').':</strong>'.$recipe->cost.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_PREPARATION_TIME_LABEL') .':</strong>'.$recipe->preparation_time.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_COOK_TIME_LABEL').':</strong>'.$recipe->cook_time.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_WAIT_TIME_LABEL').':</strong>'.$recipe->wait_time.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_YOORECIPE_INGREDIENTS').':</strong>';
	
		foreach ($recipe->groups as $group) {
			$body[] = '<h4>'.$group->label.'</h4>';
			foreach ($group->ingredients as $ingredient) {
				$body[] = round($ingredient->quantity, 2).' '.$ingredient->unit.' '.htmlspecialchars($ingredient->description).', ';
			}
		}
		
		$body[] = '</li><li><strong>'.Jtext::_('COM_YOORECIPE_YOORECIPE_INGREDIENTS').':</strong><br/><img src="'.JURI::base().$recipe->picture.'" alt="'.$recipe->alias.'"/></ul></div><br/>';
		$body[] = '| <a href="'.JUri::root().'administrator/index.php?option=com_yoorecipe&task=yoorecipes.validate&cid[]='.$recipe->id.'">'.JText::_('COM_YOORECIPE_VALIDATE').'</a> | ';
		$body[] = '<a href="'.JUri::root().'administrator/index.php?option=com_yoorecipe&task=yoorecipes.publish&cid[]='.$recipe->id.'">'.JText::_('COM_YOORECIPE_PUBLISH'). '</a> |';
		
		// Prepare mailer
		$mailer->setSender($sender);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setSubject(JText::_('COM_YOORECIPE_NEW_RECIPE_TO_VALIDATE').': '.$recipe->title);
		$mailer->setBody(implode("\n", $body));
		
		// Add recipient
		$recipient = $app->getCfg('mailfrom');
		$mailer->addRecipient($recipient);
		
		// Send
		$send = $mailer->Send();
		if ( $send !== true ) {
			JError::raiseNotice(500, JText::sprintf('COM_YOORECIPE_CREATION_EMAIL_NOT_SENT', $recipe->title));
		}
	}
	
	/**
	 * Send an email to the admin when a user submit a comment
	 */
	public static function sendMailToAdminOnSubmitComment($review, $recipe)
	{
		jimport('joomla.mail.helper');
		jimport('joomla.utilities.utility');
		
		$app			= JFactory::getApplication();
		$params			= JComponentHelper::getParams('com_yoorecipe');
		$admin_email	= $params->get('admin_email', $app->getCfg('mailfrom'));
		$fromname		= $app->getCfg('fromname');

		$mailer 	= JFactory::getMailer();
		$email		= $admin_email;
		$sender		= $fromname;
		$from		= $admin_email;

		// Check for a valid to address
		$error	= false;
		if (! $email  || ! JMailHelper::isEmailAddress($email))
		{
			$error	= JText::_('COM_YOORECIPE_MODERATOR_EMAIL_NOT_SENT');
			JError::raiseWarning(0, $error);
		}

		// Check for a valid from address
		if (! $from || ! JMailHelper::isEmailAddress($from))
		{
			$error	= JText::_('COM_YOORECIPE_MODERATOR_EMAIL_NOT_SENT');
			JError::raiseWarning(0, $error);
		}

		if ($error)
		{
			return;
		}
		
		// Build message body
		$body = array();
		$body[] = JText::_('COM_YOORECIPE_NEW_REVIEW')." ";
		if ($params->get('auto_publish_reviews', 1 )){
			$body[] = JText::_('COM_YOORECIPE_NEW_REVIEW_PUBLISHED').' ';
		}
		else {
			$body[] = JText::_('COM_YOORECIPE_NEW_REVIEW_TO_MODERATE').' ';
		}
		
		$url = Juri::root().JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->id.':'.$recipe->alias) , false).'#reviews';
		$body[] = '<a href="'. $url .'">'.JText::sprintf($recipe->title). '</a>';
		
		if (!($params->get('auto_publish_reviews', 1 ))){
			$body[] = '<br/><a href="'. JUri::root().'administrator/index.php?option=com_yoorecipe&task=comments.publish&cid[]='.$review->id.'">'.JText::_('COM_YOORECIPE_PUBLISH'). '</a>';
		}
		$body[] = '<div><ul>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_REVIEW_AUTHOR').':</strong>&nbsp;'.$review->author.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_REVIEW_NOTE').':</strong>&nbsp;'.$review->note.'/5</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_REVIEW_EMAIL').':</strong>&nbsp;'.$review->email.'</li>';
		$body[] = '<li><strong>'.JText::_('COM_YOORECIPE_REVIEW_COMMENT').':</strong>&nbsp;'.$review->comment.'</li>';
		$body[] = '</ul></div>';
		if (!($params->get('auto_publish_reviews', 1 ))){
			$body[] = '<a href="'. JUri::root().'administrator/index.php?option=com_yoorecipe&task=comments.publish&cid[]='.$review->id.'">'.JText::_('COM_YOORECIPE_PUBLISH'). '</a>';
		}
		
		// Clean the email data
		$subject = JMailHelper::cleanSubject(JText::_('COM_YOORECIPE_RECIPE_VALIDATION_REVIEW_SUBJECT'));
		$body	 = JMailHelper::cleanBody(implode("\n", $body));
		$sender	 = JMailHelper::cleanAddress($sender);
		
		// Prepare mailer
		$mailer->setSender($sender);
		$mailer->addRecipient($email);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setSubject(JText::_('COM_YOORECIPE_RECIPE_VALIDATION_REVIEW_SUBJECT'));
		$mailer->setBody($body);
		
		$send = $mailer->Send();
		if ( $send !== true ) // Send the email
		{
			JError::raiseNotice(500, JText::_('COM_YOORECIPE_MODERATOR_EMAIL_NOT_SENT'));
		}
		return;
	}
	
	/**
	 * Send an email to the author of the recipe when a user comment it.
	 */
	public static function sendMailToAuthorOnSubmitComment($review, $recipe)
	{
		jimport('joomla.mail.helper');
		jimport('joomla.utilities.utility');
		
		$app				= JFactory::getApplication();
		$yooRecipeparams	= JComponentHelper::getParams('com_yoorecipe');
		$mailfrom			= $app->getCfg('mailfrom');
		$fromname			= $app->getCfg('fromname');

		$recipeAuthor = new JUser($recipe->created_by);
		$mailer 	= JFactory::getMailer();
		$email		= $recipeAuthor->email;
		$sender		= $fromname;
		$from		= $mailfrom;

		// Check for a valid to address
		$error	= false;
		if (! $email  || ! JMailHelper::isEmailAddress($email))
		{
			$error	= JText::_('COM_YOORECIPE_MODERATOR_EMAIL_NOT_SENT');
			JError::raiseWarning(0, $error);
		}

		// Check for a valid from address
		if (! $from || ! JMailHelper::isEmailAddress($from))
		{
			$error	= JText::_('COM_YOORECIPE_MODERATOR_EMAIL_NOT_SENT');
			JError::raiseWarning(0, $error);
		}

		if ($error)
		{
			return;
		}
		
		// Build message body
		$url = Juri::root().JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->id.':'.$recipe->alias) , false).'#reviews';
		$body = array();
		$body[] = JText::_('COM_YOORECIPE_NEW_REVIEW_TO_AUTHOR').' <a href="'. $url .'">'.JText::sprintf($recipe->title). '</a><br/>';
		if (!$yooRecipeparams->get('auto_publish_reviews', 1)){
			$body[] = JText::_('COM_YOORECIPE_NEW_REVIEW_UNDER_MODERATION');
		}
		
		// Clean the email data
		$subject = JMailHelper::cleanSubject(JText::_('COM_YOORECIPE_NEW_REVIEW_UNDER_MODERATION'));
		$body	 = JMailHelper::cleanBody(implode("\n", $body));
		$sender	 = JMailHelper::cleanAddress($sender);
		
		// Prepare mailer
		$mailer->setSender($sender);
		$mailer->addRecipient($email);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setSubject(JText::_('COM_YOORECIPE_RECIPE_NOTIFICATION_REVIEW_TO_AUTHOR'));
		$mailer->setBody($body);
		
		// Send the email
		$send = $mailer->Send();
		if ( $send !== true ) // Send the email
		{
			JError::raiseNotice(500, JText:: _ ('COM_YOORECIPE_MODERATOR_EMAIL_NOT_SENT'));
		}
		return;
	}
	
	/**
	* sendMailToUserOnValidation
	*/
	public static function sendMailToUserOnValidation($recipe)
	{
		jimport('joomla.mail.helper');
		jimport('joomla.utilities.utility');
		
		$app		= JFactory::getApplication();
		$SiteName	= $app->getCfg('sitename');
		$MailFrom	= $app->getCfg('mailfrom');
		$FromName	= $app->getCfg('fromname');

		$email		= $recipe->author_email;
		$sender		= $FromName;
		$from		= $MailFrom;
		$subject	= JText::sprintf('COM_YOORECIPE_RECIPE_VALIDATION_SUBJECT', $recipe->title);

		// Check for a valid to address
		$error	= false;
		if (! $email  || ! JMailHelper::isEmailAddress($email))
		{
			$error	= JText::sprintf('COM_YOORECIPE_VALIDATION_EMAIL_NOT_SENT', $recipe->title);
			JError::raiseWarning(0, $error);
		}

		// Check for a valid from address
		if (! $from || ! JMailHelper::isEmailAddress($from))
		{
			$error	= JText::sprintf('COM_YOORECIPE_VALIDATION_EMAIL_NOT_SENT', $recipe->title);
			JError::raiseWarning(0, $error);
		}

		if ($error)
		{
			return;
		}

		// Build the message to send
		$body	= JText::sprintf('COM_YOORECIPE_RECIPE_VALIDATION_BODY', $recipe->author_name, $recipe->title, $FromName);

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);
		$sender	 = JMailHelper::cleanAddress($sender);

		$mailer 	= JFactory::getMailer();
		$mailer->setSender($sender);
		$mailer->addRecipient($email);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		
		// Send the email
		$send = $mailer->Send();
		if ( $send !== true )
		{
			JError::raiseNotice(500, JText:: _ ('COM_YOORECIPE_VALIDATION_EMAIL_NOT_SENT'));
		}
	}
}