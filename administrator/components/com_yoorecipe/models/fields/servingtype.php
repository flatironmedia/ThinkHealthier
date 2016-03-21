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
class JFormFieldServingType extends JFormFieldList
{
	/**
	* The field type.
	*
	* @var string
	*/
	protected $type = 'servingtype';

	/**
	* Method to get a list of options for a list input.
	*
	* @return array An array of JHtml options.
	*/
	protected function getOptions() 
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('id, code');
		$query->from('#__yoorecipe_serving_types');
		$query->where('published = 1');
		
		$db->setQuery((string) $query);
		$serving_types = $db->loadObjectList();
		
		$options = array();
		if ($serving_types)
		{
			foreach($serving_types as $serving_type) 
			{
				$options[] = JHtml::_('select.option', $serving_type->id, JText::_('COM_YOORECIPE_SERVING_TYPE_'.$serving_type->code));
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}