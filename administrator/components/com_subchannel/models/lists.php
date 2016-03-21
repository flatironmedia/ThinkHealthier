<?php

/**
 * @version     1.0.2
 * @package     com_subchannel
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Subchannel records.
 */
class SubchannelModelLists extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.`id`',
                'ordering', 'a.`ordering`',
                'state', 'a.`state`',
                'created_by', 'a.`created_by`',
                'category_id', 'a.`category_id`',
                'id0', 'a.`id0`',
                'id1', 'a.`id1`',
                'id2', 'a.`id2`',
                'id3', 'a.`id3`',
                'id4', 'a.`id4`',
                'id5', 'a.`id5`',
                'id6', 'a.`id6`',
                'id7', 'a.`id7`',
                'id8', 'a.`id8`',
                'id9', 'a.`id9`',
                'id10', 'a.`id10`',
                'id11', 'a.`id11`',
                'id12', 'a.`id12`',
                'id13', 'a.`id13`',
                'id14', 'a.`id14`',
                'id15', 'a.`id15`',
                'id16', 'a.`id16`',
                'id17', 'a.`id17`',
                'id18', 'a.`id18`',
                'id19', 'a.`id19`',

            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        
		//Filtering category_id
		$this->setState('filter.category_id', $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '', 'string'));


        // Load the parameters.
        $params = JComponentHelper::getParams('com_subchannel');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.category_id', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'DISTINCT a.*'
                )
        );
        $query->from('`#__subchannel_featured` AS a');

        
		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");
		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');
        // Join with categories
        $query->select('c.`title`');
        $query->join('LEFT', '#__categories AS c ON c.`id` = a.`category_id`');

        

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( c.`title` LIKE '.$search.' )');
            }
        }

        

		//Filtering category_id


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
		foreach ($items as $oneItem) {

			if (isset($oneItem->category_id)) {
				$values = explode(',', $oneItem->category_id);

				$textValue = array();
				foreach ($values as $value){
					if(!empty($value)){
						$db = JFactory::getDbo();
						$query = "SELECT id,title FROM `#__categories` HAVING id LIKE '" . $value . "'";
						$db->setQuery($query);
						$results = $db->loadObject();
						if ($results) {
							$textValue[] = $results->title;
						}
					}
				}

			$oneItem->category_id = !empty($textValue) ? implode(', ', $textValue) : $oneItem->category_id;

			}
					/*$oneItem->id0 = JText::_('COM_SUBCHANNEL_LISTS_ID0_OPTION_' . strtoupper($oneItem->id0));
					$oneItem->id1 = JText::_('COM_SUBCHANNEL_LISTS_ID1_OPTION_' . strtoupper($oneItem->id1));
					$oneItem->id2 = JText::_('COM_SUBCHANNEL_LISTS_ID2_OPTION_' . strtoupper($oneItem->id2));
					$oneItem->id3 = JText::_('COM_SUBCHANNEL_LISTS_ID3_OPTION_' . strtoupper($oneItem->id3));
					$oneItem->id4 = JText::_('COM_SUBCHANNEL_LISTS_ID4_OPTION_' . strtoupper($oneItem->id4));
					$oneItem->id5 = JText::_('COM_SUBCHANNEL_LISTS_ID5_OPTION_' . strtoupper($oneItem->id5));
					$oneItem->id6 = JText::_('COM_SUBCHANNEL_LISTS_ID6_OPTION_' . strtoupper($oneItem->id6));
					$oneItem->id7 = JText::_('COM_SUBCHANNEL_LISTS_ID7_OPTION_' . strtoupper($oneItem->id7));
					$oneItem->id8 = JText::_('COM_SUBCHANNEL_LISTS_ID8_OPTION_' . strtoupper($oneItem->id8));
					$oneItem->id9 = JText::_('COM_SUBCHANNEL_LISTS_ID9_OPTION_' . strtoupper($oneItem->id9));
					$oneItem->id10 = JText::_('COM_SUBCHANNEL_LISTS_ID10_OPTION_' . strtoupper($oneItem->id10));
					$oneItem->id11 = JText::_('COM_SUBCHANNEL_LISTS_ID11_OPTION_' . strtoupper($oneItem->id11));
					$oneItem->id12 = JText::_('COM_SUBCHANNEL_LISTS_ID12_OPTION_' . strtoupper($oneItem->id12));
					$oneItem->id13 = JText::_('COM_SUBCHANNEL_LISTS_ID13_OPTION_' . strtoupper($oneItem->id13));
					$oneItem->id14 = JText::_('COM_SUBCHANNEL_LISTS_ID14_OPTION_' . strtoupper($oneItem->id14));
					$oneItem->id15 = JText::_('COM_SUBCHANNEL_LISTS_ID15_OPTION_' . strtoupper($oneItem->id15));
					$oneItem->id16 = JText::_('COM_SUBCHANNEL_LISTS_ID16_OPTION_' . strtoupper($oneItem->id16));
					$oneItem->id17 = JText::_('COM_SUBCHANNEL_LISTS_ID17_OPTION_' . strtoupper($oneItem->id17));
					$oneItem->id18 = JText::_('COM_SUBCHANNEL_LISTS_ID18_OPTION_' . strtoupper($oneItem->id18));
					$oneItem->id19 = JText::_('COM_SUBCHANNEL_LISTS_ID19_OPTION_' . strtoupper($oneItem->id19));*/
		}
        return $items;
    }

}
