<?php
/*------------------------------------------------------------------------
# com_campingmanager
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://extensions.yoorock.fr
# Technical Support:  Forum - http://extensions.yoorock.fr/
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
* CampingManager Form Field class for the CampingManager component
*/
class JFormFieldSeason extends JFormFieldList
{
	/**
	* The field type.
	*
	* @var string
	*/
	protected $type = 'season';

	/**
	* Method to get a list of options for a list input.
	*
	* @return array An array of JHtml options.
	*/
	protected function getOptions() 
	{
		$yooRecipeparams	= JComponentHelper::getParams('com_yoorecipe');
		$seasons_type		= $yooRecipeparams->get('seasons_type', 'months');
		
		if ($seasons_type == 'months' || $seasons_type == 'all') {
			$options[] = JHtml::_('select.option', 'JAN', JText::_('COM_YOORECIPE_JAN'));
			$options[] = JHtml::_('select.option', 'FEB', JText::_('COM_YOORECIPE_FEB'));
			$options[] = JHtml::_('select.option', 'MAR', JText::_('COM_YOORECIPE_MAR'));
			$options[] = JHtml::_('select.option', 'APR', JText::_('COM_YOORECIPE_APR'));
			$options[] = JHtml::_('select.option', 'MAY', JText::_('COM_YOORECIPE_MAY'));
			$options[] = JHtml::_('select.option', 'JUN', JText::_('COM_YOORECIPE_JUN'));
			$options[] = JHtml::_('select.option', 'JUL', JText::_('COM_YOORECIPE_JUL'));
			$options[] = JHtml::_('select.option', 'AUG', JText::_('COM_YOORECIPE_AUG'));
			$options[] = JHtml::_('select.option', 'SEP', JText::_('COM_YOORECIPE_SEP'));
			$options[] = JHtml::_('select.option', 'OCT', JText::_('COM_YOORECIPE_OCT'));
			$options[] = JHtml::_('select.option', 'NOV', JText::_('COM_YOORECIPE_NOV'));
			$options[] = JHtml::_('select.option', 'DEC', JText::_('COM_YOORECIPE_DEC'));
		}
		if ($seasons_type == 'seasons' || $seasons_type == 'all') {
			$options[] = JHtml::_('select.option', 'WINTER', JText::_('COM_YOORECIPE_WINTER'));
			$options[] = JHtml::_('select.option', 'AUTUMN', JText::_('COM_YOORECIPE_AUTUMN'));
			$options[] = JHtml::_('select.option', 'SPRING', JText::_('COM_YOORECIPE_SPRING'));
			$options[] = JHtml::_('select.option', 'SUMMER', JText::_('COM_YOORECIPE_SUMMER'));
			$options[] = JHtml::_('select.option', 'ALL', JText::_('COM_YOORECIPE_ALL'));
		}

		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}