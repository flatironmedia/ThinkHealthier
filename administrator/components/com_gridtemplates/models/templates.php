<?php

/**
 * @version     1.0.0
 * @package     com_gridtemplates
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Gridtemplates records.
 */
class GridtemplatesModelTemplates extends JModelList {

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
                'state', 'a.`state`',
                'created_by', 'a.`created_by`',
                'category', 'a.`category`',
                'type0', 'a.`type0`',
                'type1', 'a.`type1`',
                'type2', 'a.`type2`',
                'type3', 'a.`type3`',
                'type4', 'a.`type4`',
                'type5', 'a.`type5`',
                'type6', 'a.`type6`',
                'type7', 'a.`type7`',
                'type8', 'a.`type8`',
                'type9', 'a.`type9`',
                'type10', 'a.`type10`',
                'type11', 'a.`type11`',
                'type12', 'a.`type12`',
                'type13', 'a.`type13`',
                'type14', 'a.`type14`',
                'type15', 'a.`type15`',
                'type16', 'a.`type16`',
                'type17', 'a.`type17`',
                'type18', 'a.`type18`',
                'type19', 'a.`type19`',

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

        

        // Load the parameters.
        $params = JComponentHelper::getParams('com_gridtemplates');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.category', 'asc');
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
        $query->from('`#__gridtemplates_templates` AS a');

        
		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

        // Join with categories
        $query->select('c.`title`');
        $query->join('LEFT', '#__categories AS c ON c.`id` = a.`category`');

        

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

			if (isset($oneItem->category)) {
				$values = explode(',', $oneItem->category);

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

			$oneItem->category = !empty($textValue) ? implode(', ', $textValue) : $oneItem->category;

			}
					$oneItem->type0 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE0_OPTION_' . strtoupper($oneItem->type0));
					$oneItem->type1 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE1_OPTION_' . strtoupper($oneItem->type1));
					$oneItem->type2 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE2_OPTION_' . strtoupper($oneItem->type2));
					$oneItem->type3 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE3_OPTION_' . strtoupper($oneItem->type3));
					$oneItem->type4 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE4_OPTION_' . strtoupper($oneItem->type4));
					$oneItem->type5 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE5_OPTION_' . strtoupper($oneItem->type5));
					$oneItem->type6 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE6_OPTION_' . strtoupper($oneItem->type6));
					$oneItem->type7 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE7_OPTION_' . strtoupper($oneItem->type7));
					$oneItem->type8 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE8_OPTION_' . strtoupper($oneItem->type8));
					$oneItem->type9 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE9_OPTION_' . strtoupper($oneItem->type9));
					$oneItem->type10 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE10_OPTION_' . strtoupper($oneItem->type10));
					$oneItem->type11 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE11_OPTION_' . strtoupper($oneItem->type11));
					$oneItem->type12 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE12_OPTION_' . strtoupper($oneItem->type12));
					$oneItem->type13 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE13_OPTION_' . strtoupper($oneItem->type13));
					$oneItem->type14 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE14_OPTION_' . strtoupper($oneItem->type14));
					$oneItem->type15 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE15_OPTION_' . strtoupper($oneItem->type15));
					$oneItem->type16 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE16_OPTION_' . strtoupper($oneItem->type16));
					$oneItem->type17 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE17_OPTION_' . strtoupper($oneItem->type17));
					$oneItem->type18 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE18_OPTION_' . strtoupper($oneItem->type18));
					$oneItem->type19 = JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE19_OPTION_' . strtoupper($oneItem->type19));
		}
        return $items;
    }

}
