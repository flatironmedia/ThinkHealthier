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
jimport( 'joomla.filter.output' );

// no direct access
defined('_JEXEC') or die ;


function YooRecipeBuildRoute( &$query )
{
	$segments 	= array();
	$params 	= JComponentHelper::getParams('com_yoorecipe');
	
	if(isset($query['controller'])) {
		unset($query['controller']);
	}

	if(isset($query['task'])) {
		unset($query['task']);
	}
	
	if(isset($query['view'])) {
	
		switch ($query['view']) {
			case 'categories':
				$segments[] = $params->get('seo_categories', 'categories');
			break;
			
			case 'form':
				$segments[] = $params->get('seo_edit', 'edit');
				unset($query['layout']);
			break;
			
			case 'meals':
				$segments[] = 'meals';
				if (isset($query['layout'])) {
					$segments[] = $query['layout'];
					unset($query['layout']);
				}
			break;
			
			case 'recipes':
				$segments[] = 'recipes';
				$layout = $query['layout'];
				switch ($layout) {
				
					case 'myrecipes';
						$segments[] = $query['layout'];
						unset($query['layout']);
					break;
					
					case 'chef';
						$segments[] = $query['layout'];
						unset($query['layout']);
					break;
				}
			break;
			
			case 'recipe':
				$segments[] = $params->get('seo_recipe', 'recipe');
			break;
			
			case 'search':
				$segments[] = 'search';
				if(isset($query['layout']))	{
					$segments[] = $query['layout'];
					// unset($query['layout']);
				}
			break;
			
			case 'shoppinglist':
				$segments[] = 'shoppinglist';
				if(isset($query['layout']))	{
					$segments[] = $query['layout'];
					unset($query['layout']);
				}
			break;
			
			default:
				$segments[] = $query['view'];
			break;
		}
		unset($query['view']);
	}
				
	if(isset($query['id']))	{
		$segments[] = $query['id'];
		unset($query['id']);
	}
	
	if(isset($query['month_id'])) {
		$segments[] = $query['month_id'].':'.JFilterOutput::stringURLSafe(JText::_('COM_YOORECIPE_'.$query['month_id']));
		unset($query['month_id']);
	}
	
	if(isset($query['cuisine'])) {
		$segments[] = $query['layout'];
		$segments[] = JFilterOutput::stringURLSafe($query['cuisine']);
		unset($query['cuisine']);
		unset($query['layout']);
	}
	
	return $segments;
}

function YooRecipeParseRoute($segments)
{
	$vars = array();
	
	$params = JComponentHelper::getParams('com_yoorecipe');
	switch ($segments[0]) {
		
		case $params->get('seo_categories', 'categories'):
			$vars['view'] = 'categories';
			$vars['id'] = isset($segments[1]) ? $segments[1] : 0;;
		break;
		
		case $params->get('seo_edit', 'edit'):
			$vars['view'] = 'form';
			$vars['layout'] = 'edit';
			$vars['id'] = isset($segments[1]) ? $segments[1] : 0;
		break;
		
		case 'landingpage':
			$vars['view'] = 'landingpage';
			if (isset($segments[1]) && $segments[1] == 'letters') {
				$vars['layout'] = 'letters';
				$vars['l'] = $segments[2];
			}
		break;
		
		case 'letters':
			$vars['view'] = 'landingpage';
			$vars['layout'] = 'letters';
			$vars['l'] = isset($segments[1]) ? $segments[1] : 0;
		break;
		
		case $params->get('seo_recipe', 'recipe'):
			$vars['view'] = 'recipe';
			$vars['id'] = isset($segments[1]) ? $segments[1] : 0;
		break;
		
		case 'recipes':
			$vars['view'] = 'recipes';
			$vars['layout'] = $segments[1];
			switch ($segments[1]) {
				case 'tags':
				$vars['value'] = $segments[2];
				break;
				
				case 'chef':
				$vars['id'] = $segments[2];
				break;
				
				case 'seasons':
				$vars['month_id'] = $segments[2];
				break;
				
				case 'cuisine':
				$vars['cuisine'] = $segments[2];
				break;
			}
		break;
		
		case 'search':
			$vars['view'] = 'search';
			$vars['layout'] = isset($segments[1]) ? $segments[1] : 'search';
		break;
		
		case 'shoppinglist':
			$vars['view'] = 'shoppinglist';
			
			if(isset($segments[2])) {
				$vars['layout'] = $segments[1];
				$vars['id'] = $segments[2];
			} else if(isset($segments[1])) {
				$vars['layout'] = 'default';
				$vars['id'] = $segments[1];
			}
		break;
		
		case 'shoppinglists':
			$vars['view'] = 'shoppinglists';
		break;
		
		case 'meals':
			$vars['view'] = 'meals';
			if (isset($segments[1])) {
				$vars['layout'] = $segments[1];
			}
		break;
		
		default:
			$vars['view'] = $segments[0];
		break;
		
	}
	return $vars;
}