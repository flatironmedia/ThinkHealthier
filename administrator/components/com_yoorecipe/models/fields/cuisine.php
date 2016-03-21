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
class JFormFieldCuisine extends JFormFieldList
{
	/**
	* The field type.
	*
	* @var string
	*/
	protected $type = 'cuisine';

	/**
	* Method to get a list of options for a list input.
	*
	* @return array An array of JHtml options.
	*/
	protected function getOptions() 
	{
		$options = array();
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('code');
		$query->from('#__yoorecipe_cuisines');
		$query->where('published = 1');
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach ($items as &$item) {
			$options[] = JHtml::_('select.option', $item->code, JText::_('COM_YOORECIPE_CUISINE_'.$item->code));
		}
			
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}