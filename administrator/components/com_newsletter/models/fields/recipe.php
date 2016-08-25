<?php
/**
 * @version 3.0 2013-03-01
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2013 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRecipe extends JFormFieldList
{
    protected $type = 'Recipe';

    public function getOptions($useauth = false)
    {
        $options = array();

        $db         = JFactory::getDbo();

        // Filter by start and end dates.
        $query = $db->getQuery(true);
        $query->select('id AS value, title AS text')
            ->from($db->quoteName('#__yoorecipe'))
            ->where($db->quoteName('published').' = 1');

        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        // Merge any additional options in the XML definition.
        if(isset($this->element))
        {
            $options = array_merge(parent::getOptions(), $options);
            array_unshift($options, JHtml::_('select.option', '', JText::_('Select a Recipe')));
        }

        return $options;
    }
}