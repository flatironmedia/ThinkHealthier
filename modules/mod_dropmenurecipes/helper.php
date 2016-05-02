<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenurecipes
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

class ModDropMenuRecipesHelper
{
    /**
     * Retrieves the three random featured recipes
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */
    public static function getFeaturedRecipes($params)
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

        $query->select('id, title, alias, description, picture')
            ->from($db->quoteName('#__yoorecipe'))
            ->where($db->quoteName('featured'). ' = 1')
            ->order('rand()');
        $db->setQuery($query, 0, 3);
        $result = $db->loadObjectList();
        foreach ($result as $key => &$value) {
            if(!$value->picture){
                if(JFile::exists('/images/com_yoorecipe/recipes/recipe'.$value->id.'.jpg'))
                    $value->picture = '/images/com_yoorecipe/recipes/recipe'.$value->id.'.jpg';
                else
                    $value->picture ='/media/com_yoorecipe/images/cropped-img-not-available.png';
            }
            //$value->link = '/healthy-recipes/recipe/'.$value->id.'-'.$value->alias;
            $value->link = JRoute::_(JHtml::_('yoorecipehelperroute.getreciperoute', $value->id.':'.$value->alias, $catid = 0, $menuitem));
        }

        return $result;
    }
}