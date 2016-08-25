<?php

/**
 * @version    CVS: 3.4.2
 * @package    Com_Newsletter
 * @author     Aleksandar Vrhovac <avrhovac@ogosense.com>
 * @copyright  2016 Aleksandar Vrhovac
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Newsletter.
 *
 * @since  1.6
 */
class NewsletterViewThdailynewslettermanager extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		NewsletterHelper::addSubmenu('thdailynewslettermanager');

		$this->addToolbar();

		//$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/newsletter.php';

		$state = $this->get('State');
		$canDo = NewsletterHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_NEWSLETTER_TITLE_THDAILYNEWSLETTERMANAGER'), 'envelope-opened.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/newsletter';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('newsletter.add', 'JTOOLBAR_NEW');
				//JToolbarHelper::custom('thdailynewslettermanager.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('newsletter.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('thdailynewslettermanager.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('thdailynewslettermanager.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'thdailynewslettermanager.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('thdailynewslettermanager.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('thdailynewslettermanager.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'thdailynewslettermanager.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('thdailynewslettermanager.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_newsletter');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_newsletter&view=thdailynewslettermanager');

		$this->extra_sidebar = '';
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`date`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_DATE'),
			'a.`subject`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_SUBJECT'),
			'a.`featured_article`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_FEATURED_ARTICLE'),
			'a.`featured_recipe`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_FEATURED_RECIPE'),
			'a.`article2`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_ARTICLE2'),
			'a.`article3`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_ARTICLE3'),
			'a.`article4`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_ARTICLE4'),
			'a.`newsletter`' => JText::_('COM_NEWSLETTER_THDAILYNEWSLETTERMANAGER_NEWSLETTER'),
		);
	}
}
