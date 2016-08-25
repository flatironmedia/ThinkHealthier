<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenurecipecategories
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

class ModDropMenuRecipeCategories
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
            $menuitem = 639;
        }
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('DISTINCT b.id, b.title, b.alias')
            ->from($db->quoteName('#__yoorecipe_categories', 'a'))
            ->join('INNER', $db->quoteName('#__categories', 'b').' ON ('.$db->quoteName('a.cat_id').' = '.$db->quoteName('b.id').')')
            ->where($db->quoteName('published').' = 1');
        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach ($result as $key => &$value) {
            $value->link = JRoute::_(JHtml::_('yoorecipehelperroute.getcategoryroute', $value->id.':'.$value->alias.'&Itemid='.$menuitem));
        }
        
        return $result;
    }
}