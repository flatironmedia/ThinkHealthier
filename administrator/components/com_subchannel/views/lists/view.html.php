<?php

/**
 * @version     1.0.2
 * @package     com_subchannel
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Subchannel.
 */
class SubchannelViewLists extends JViewLegacy {

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

        SubchannelHelper::addSubmenu('lists');

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
        require_once JPATH_COMPONENT . '/helpers/subchannel.php';

        $state = $this->get('State');
        $canDo = SubchannelHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_SUBCHANNEL_TITLE_LISTS'), 'lists.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/list';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('list.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('list.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('lists.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('lists.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'lists.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('lists.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('lists.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'lists.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('lists.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        //Show delete 
        if (isset($this->items[0]->state)) {
            
                JToolBarHelper::deleteList('', 'lists.delete', 'Delete');
                JToolBarHelper::divider();
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_subchannel');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_subchannel&view=lists');

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
		'a.`category_id`' => JText::_('COM_SUBCHANNEL_LISTS_CATEGORY_ID'),
		'a.`id0`' => JText::_('COM_SUBCHANNEL_LISTS_ID0'),
		'a.`id1`' => JText::_('COM_SUBCHANNEL_LISTS_ID1'),
		'a.`id2`' => JText::_('COM_SUBCHANNEL_LISTS_ID2'),
		'a.`id3`' => JText::_('COM_SUBCHANNEL_LISTS_ID3'),
		'a.`id4`' => JText::_('COM_SUBCHANNEL_LISTS_ID4'),
		'a.`id5`' => JText::_('COM_SUBCHANNEL_LISTS_ID5'),
		'a.`id6`' => JText::_('COM_SUBCHANNEL_LISTS_ID6'),
		'a.`id7`' => JText::_('COM_SUBCHANNEL_LISTS_ID7'),
		'a.`id8`' => JText::_('COM_SUBCHANNEL_LISTS_ID8'),
		'a.`id9`' => JText::_('COM_SUBCHANNEL_LISTS_ID9'),
		'a.`id10`' => JText::_('COM_SUBCHANNEL_LISTS_ID10'),
		'a.`id11`' => JText::_('COM_SUBCHANNEL_LISTS_ID11'),
		'a.`id12`' => JText::_('COM_SUBCHANNEL_LISTS_ID12'),
		'a.`id13`' => JText::_('COM_SUBCHANNEL_LISTS_ID13'),
		'a.`id14`' => JText::_('COM_SUBCHANNEL_LISTS_ID14'),
		'a.`id15`' => JText::_('COM_SUBCHANNEL_LISTS_ID15'),
		'a.`id16`' => JText::_('COM_SUBCHANNEL_LISTS_ID16'),
		'a.`id17`' => JText::_('COM_SUBCHANNEL_LISTS_ID17'),
		'a.`id18`' => JText::_('COM_SUBCHANNEL_LISTS_ID18'),
		'a.`id19`' => JText::_('COM_SUBCHANNEL_LISTS_ID19'),
		);
	}

}
