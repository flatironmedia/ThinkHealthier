<?php

/**
 * @version     3.4.0
 * @package     com_content
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Xander <avrhovac@ogosense.com> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Delagate records.
 */
class ContentModelAZAlphabet extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(

			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */



	protected function populateState($ordering = null, $direction = null)
	{


		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				// Extra validations
				switch ($name)
				{
					case 'fullordering':
						$orderingParts = explode(' ', $value);

						if (count($orderingParts) >= 2)
						{
							// Latest part will be considered the direction
							$fullDirection = end($orderingParts);

							if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
							{
								$this->setState('list.direction', $fullDirection);
							}

							unset($orderingParts[count($orderingParts) - 1]);

							// The rest will be the ordering
							$fullOrdering = implode(' ', $orderingParts);

							if (in_array($fullOrdering, $this->filter_fields))
							{
								$this->setState('list.ordering', $fullOrdering);
							}
						}
						else
						{
							$this->setState('list.ordering', $ordering);
							$this->setState('list.direction', $direction);
						}
						break;

					case 'ordering':
						if (!in_array($value, $this->filter_fields))
						{
							$value = $ordering;
						}
						break;

					case 'direction':
						if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
						{
							$value = $direction;
						}
						break;

					case 'limit':
						$limit = $value;
						break;

					// Just to keep the default case
					default:
						$value = $value;
						break;
				}

				$this->setState('list.' . $name, $value);
			}
		}

		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		$ordering = $app->input->get('filter_order');
		if (!empty($ordering))
		{
			$list             = $app->getUserState($this->context . '.list');
			$list['ordering'] = $app->input->get('filter_order');
			$app->setUserState($this->context . '.list', $list);
		}

		$orderingDirection = $app->input->get('filter_order_Dir');
		if (!empty($orderingDirection))
		{
			$list              = $app->getUserState($this->context . '.list');
			$list['direction'] = $app->input->get('filter_order_Dir');
			$app->setUserState($this->context . '.list', $list);
		}

		$list = $app->getUserState($this->context . '.list');



		$this->setState('list.ordering', $list['ordering']);
		$this->setState('list.direction', $list['direction']);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	public function getListQuery() {
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);

        return $db->loadObjectList();
	}

	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_content.azalphabet', 'azalphabet', array('load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;
		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && !$this->isValidDate($value))
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}
		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_PRUEBA_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	public function getCat(){

		$jinput = JFactory::getApplication()->input;
		$cat = $jinput->get('cat', '', 'STRING');

		if($cat){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('id, title, alias')
				->from($db->quoteName('#__categories'))
				->where($db->quoteName('alias').' LIKE '.$db->quote($cat));
			$db->setQuery($query);
			$result = $db->loadObject();
			if($result->id){
				$query = $db->getQuery(true);

				$query->select('id, title, catid')
					->from($db->quoteName('#__content'))
					->where($db->quoteName('catid').' = '.$db->quote($result->id).' AND '.$db->quoteName('state').' = 1 AND '.$db->quoteName('featured').' = 1')
					->order('hits DESC');
				$db->setQuery($query, 0, 15);
				$result->content = $db->loadObjectList();
			}
		}
		return $result;
	}

	public function getContent($cat_id){

		$jinput = JFactory::getApplication()->input;
		$let = $jinput->get('let', '', 'STRING');

		if($cat_id && $let){
			$db = JFactory::getDbo();

			$query = $db->getQuery(true);

			$query->select('id, title, catid')
				->from($db->quoteName('#__content'))
				->where($db->quoteName('title').' LIKE '.$db->quote($let.'%').' AND '.$db->quoteName('state').' = 1'.' AND '.$db->quoteName('catid').' = '.$db->quote($cat_id))
				->order('title ASC');
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		else if($let){
			$db = JFactory::getDbo();

			$query = $db->getQuery(true);
			$temp_query = 'SELECT id FROM `#__categories` WHERE `title` LIKE '.$db->quote('Health A-Z');
			$query->select('id')
				->from($db->quoteName('#__categories'))
				->where($db->quoteName('parent_id').' = ('.$temp_query.') AND '.$db->quoteName('published').' = 1');
			$db->setQuery($query);
			$catIDs = $db->loadObjectList();

			$temp = array();
			foreach ($catIDs as $key => $value) {
				$temp[] = $value->id;
			}
			$catIDs = $temp;

			$query = $db->getQuery(true);

			$query->select('id, title, catid')
				->from($db->quoteName('#__content'))
				->where($db->quoteName('title').' LIKE '.$db->quote($let.'%').' AND '.$db->quoteName('state').' = 1'.' AND '.$db->quoteName('catid').' in ('.implode(',', $catIDs).')')
				->order('title ASC');
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}

		return $result;
	}

	/**
	 * Checks if a given date is valid and in an specified format (YYYY-MM-DD)
	 *
	 * @param string Contains the date to be checked
	 *
	 */
	private function isValidDate($date)
	{
		return preg_match("/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/", $date) && date_create($date);
	}
}
