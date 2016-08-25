<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenuhealthaz
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

class ModDropMenuHealthAZHelper
{
    /**
     * Retrieves the three random featured recipes
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */
    public static function getHealthAZCategories($params)
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

        $query->select('id, title, alias, description, params')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('id').' in (207, 225, 227)');
        $db->setQuery($query, 0, 3);
        $result = $db->loadObjectList();

        foreach ($result as $key => &$value) {
            $params = json_decode($value->params);
            $value->picture = $params->image;

            if(!$value->picture)
                $value->picture = '/images/default_image.jpg';

           
            $value->link = JRoute::_('health-az/az-alphabet/?cat='.$value->alias);

            unset($value->params);
        }
        
        return $result;
    }
}