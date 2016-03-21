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
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the YooRecipe Component
 */
class YooRecipeViewCategories extends JViewLegacy
{

	public function display($tpl = null)
	{
		$input 		= JFactory::getApplication()->input;
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();
		$active 	= $menu->getActive();
		
		// Parameters
		$app       = JFactory::getApplication();
		$doc       = JFactory::getDocument();
		$params    = $app->getParams();
		$feedEmail = $app->get('feed_email', 'author');
		$siteEmail = $app->get('mailfrom');
		$doc->link = JRoute::_('index.php?option=com_yoorecipe&view=recipes&layout='.$layout);

		// Get some data from the model
		$app->input->set('limit', $app->get('feed_limit'));
		$categories = JCategories::getInstance('Content');
		$rows       = $this->get('Items');
		
		// Get the yoorecipe model
		$categoriesModel		= JModelLegacy::getInstance('categories','YooRecipeModel');
		$ingredientsGroupsModel	= JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');
		$ingredientsModel		= JModelLegacy::getInstance('ingredients','YooRecipeModel');
		
		foreach ($rows as $row)
		{
			$row->groups = $ingredientsGroupsModel->getIngredientsGroupsByRecipeId($row->id);
			foreach ($row->groups as $group) {
				$group->ingredients = $ingredientsModel->getIngredientsByGroupId($group->id);
			}
			
			$row->categories	= $categoriesModel->getRecipeCategories($row->id);
			
			// strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the article slug
			$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;

			// Url link to article
			$link = JRoute::_(JHTML::_('yoorecipehelperroute.getreciperoute', $row->slug, $row->catslug));

			$description	= $row->description;
			$author			= $row->author_name;

			// Load individual item creator class
			$item				= new JFeedItem;
			$item->title		= $title;
			$item->link			= $link;
			$item->date			= $row->creation_date;
			$item->category		= array();
			
			$categories_titles = array();
			foreach ($row->categories as $category) {
				$categories_titles[] = $category->title;
			}
			$item->category[]	= implode(", ", $categories_titles);

			$item->author = $author;

			if (true || $feedEmail == 'site')
			{
				$item->authorEmail = $siteEmail;
			}
			/*elseif ($feedEmail === 'author')
			{
				$item->authorEmail = $row->author_email;
			}*/

			// Add readmore link to description if introtext is shown, show_readmore is true and fulltext exists
			if (!$params->get('feed_summary', 0) && $params->get('feed_show_readmore', 0) && $row->description)
			{
				$description .= '<p class="feed-readmore"><a target="_blank" href ="' . $item->link . '">' . JText::_('COM_YOORECIPE_READMORE') . '</a></p>';
			}

			// Load item description and add div
			$item->description	= '<div class="feed-description">'.$description.'</div>';

			// Loads item info into rss array
			$doc->addItem($item);
		}
	}
}