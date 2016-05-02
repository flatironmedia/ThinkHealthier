<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenurhealthazcategories
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

class ModDropMenuHealthAZCategories
{
    /**
     * Retrieves the hello message
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */
    public static function getCategories($params)
    {
         if ($params->menuid !== NULL)
        {
            $menuitem = $params->menuid;
        }
        else
        {
            $menuitem = 789;
        }
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('id, title, alias')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('parent_id').' = 109 AND '.$db->quoteName('published').' = 1');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        
        foreach ($result as &$value) {
            
            $value->slug    = $value->id . ':' . $value->alias;
            $value->link = JRoute::_('health-az/az-alphabet/?cat='.$value->alias);
        }
        
        return $result;
    }
}