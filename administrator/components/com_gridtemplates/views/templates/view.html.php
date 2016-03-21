<?php

/**
 * @version     1.0.0
 * @package     com_gridtemplates
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Gridtemplates.
 */
class GridtemplatesViewTemplates extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        GridtemplatesHelper::addSubmenu('templates');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/gridtemplates.php';

        $state = $this->get('State');
        $canDo = GridtemplatesHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_GRIDTEMPLATES_TITLE_TEMPLATES'), 'templates.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/template';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('template.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('template.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('templates.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('templates.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'templates.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('templates.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('templates.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'templates.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('templates.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if (isset($this->items[0]->state)) {
            JToolBarHelper::deleteList('', 'templates.delete', 'JTOOLBAR_DELETE');
            JToolBarHelper::divider();
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_gridtemplates');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_gridtemplates&view=templates');

        $this->extra_sidebar = '';
        
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);

    }

	protected function getSortFields()
	{
		return array(
		'a.`id`' => JText::_('JGRID_HEADING_ID'),
		'a.`state`' => JText::_('JSTATUS'),
		'a.`category`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_CATEGORY'),
		'a.`type0`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE0'),
		'a.`type1`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE1'),
		'a.`type2`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE2'),
		'a.`type3`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE3'),
		'a.`type4`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE4'),
		'a.`type5`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE5'),
		'a.`type6`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE6'),
		'a.`type7`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE7'),
		'a.`type8`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE8'),
		'a.`type9`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE9'),
		'a.`type10`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE10'),
		'a.`type11`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE11'),
		'a.`type12`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE12'),
		'a.`type13`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE13'),
		'a.`type14`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE14'),
		'a.`type15`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE15'),
		'a.`type16`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE16'),
		'a.`type17`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE17'),
		'a.`type18`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE18'),
		'a.`type19`' => JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE19'),
		);
	}

}
