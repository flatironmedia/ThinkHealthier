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

jimport('joomla.html.pagination');

class JHtmlYooRecipePagination extends JPagination
{
	/**
	 * Creates a dropdown box for selecting how many records to show per page.
	 * Override Joomla native pagination to make it possible to display more than one pagination per page
	 * @return  string  The HTML for the limit # input box.
	 *
	 * @since   11.1
	 */
	public function getLimitBox()
	{
		$app = JFactory::getApplication();
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$nbcols			 	= $yooRecipeparams->get('nb_cols', '4');
		
		// Initialise variables.
		$limits = array();
		
		switch ($nbcols) {

			case 1:
			default:
				// Make the option list.
				for ($i = 5; $i <= 30; $i += 5)
				{
					$limits[] = JHtml::_('select.option', $i);
				}
				$limits[] = JHtml::_('select.option', '50', JText::_('J50'));
				$limits[] = JHtml::_('select.option', '0', JText::_('JALL'));
			break;
			
			case 2:
				// Make the option list.
				for ($i = 2; $i <= 8; $i += 2)
				{
					$limits[] = JHtml::_('select.option', $i);
				}
				$limits[] = JHtml::_('select.option', 12);
				$limits[] = JHtml::_('select.option', 16);
				$limits[] = JHtml::_('select.option', 20);
				$limits[] = JHtml::_('select.option', 30);
				$limits[] = JHtml::_('select.option', '50', JText::_('J50'));
			break;
			
			case 3:
				$limits[] = JHtml::_('select.option', 6);
				$limits[] = JHtml::_('select.option', 9);
				$limits[] = JHtml::_('select.option', 12);
				$limits[] = JHtml::_('select.option', 15);
				$limits[] = JHtml::_('select.option', 18);
				$limits[] = JHtml::_('select.option', 21);
				 $limits[] = JHtml::_('select.option', '0', JText::_('JALL'));
			break;
			
			case 4:
				$limits[] = JHtml::_('select.option', 4);
				$limits[] = JHtml::_('select.option', 8);
				$limits[] = JHtml::_('select.option', 12);
				$limits[] = JHtml::_('select.option', 16);
				$limits[] = JHtml::_('select.option', 20);
				 $limits[] = JHtml::_('select.option', '0', JText::_('JALL'));
			break;
		}

		$selected = 	$this->limit;

		// Build the select list.

		$html = JHtml::_(
			'select.genericlist',
			$limits,
			$this->prefix.'limit',
			'class="inputbox input-small yoorecipe-limitbox" size="1" onchange="updateLimitBox(this);Joomla.submitform();"',
			'value',
			'text',
			$selected
		);

		return $html;
	}
	
	/**
	 * Create the html for a list footer
	 *
	 * @param   array  $list  Pagination list data structure.
	 *
	 * @return  string  HTML for a list start, previous, next,end
	 *
	 * @since   11.1
	 */
	protected function _list_render($list)
	{
		// Reverse output rendering for right-to-left display.
		$html = '<ul>';
		$html .= '<li>'.$list['start']['data'].'</li>';
		$html .= '<li>'.$list['previous']['data'].'</li>';
		foreach ($list['pages'] as $page)
		{
			$html .= '<li>'.$page['data'].'</li>';
		}
		$html .= '<li>'.$list['next']['data'].'</li>';
		$html .= '<li>'.$list['end']['data'].'</li>';
		$html .= '</ul>';

		return $html;
	}
	
	/**
	 * Method to create an active pagination link to the item
	 *
	 * @param   JPaginationObject  &$item  The object with which to make an active link.
	 *
	 * @return   string  HTML link
	 *
	 * @since    11.1
	 */
	protected function _item_active(JPaginationObject $item)
	{
		$app = JFactory::getApplication();
		if ($app->isAdmin())
		{
			if ($item->base > 0)
			{
				return "<a title=\"".$item->text."\" onclick=\"document.adminForm.".$this->prefix."limitstart.value=".$item->base
					. "; Joomla.submitform();return false;\">".$item->text."</a>";
			}
			else
			{
				return "<a title=\"".$item->text."\" onclick=\"document.adminForm.".$this->prefix
					. "limitstart.value=0; Joomla.submitform();return false;\">".$item->text."</a>";
			}
		}
		else
		{
			return "<a title=\"".$item->text."\" href=\"".$item->link."\">".$item->text."</a>";
		}
	}

	/**
	 * Method to create an inactive pagination string
	 *
	 * @param   object  &$item  The item to be processed
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _item_inactive(JPaginationObject $item)
	{
		return "<span>".$item->text."</span>";
	}
}