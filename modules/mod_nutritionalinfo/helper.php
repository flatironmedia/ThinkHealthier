<?php

/**
* @version      1.0.0
* @package      com_delagate
* @copyright    Copyright (C) 2014. All rights reserved.
* @license      GNU General Public License version 2 or later; see LICENSE.txt
* @author       Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

class modNutritionalInfoHelper
{
    /**
     * Retrieves the hello message
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */
    public static function getNutritionalInfo(){

        $jinput = JFactory::getApplication()->input;
        $db = JFactory::getDbo();

        $recipe_id = $jinput->get('id', 0, 'INT');

        $query = $db->getQuery(true);

        $query->select('kcal Calories, carbs Carbohydrates, fat Fat, proteins Protein, saturated_fat Saturated_Fat, salt Sodium, fibers Fiber, sugar Sugar, cholesterol Cholesterol, nb_persons')
            ->from($db->quoteName('#__yoorecipe'))
            ->where($db->quoteName('id').' = '.$recipe_id);
        $db->setQuery($query);
        $temp = $db->loadObject();

        $result = array();

        foreach ($temp as $key => $value){
            $result[$key]['value'] = $value;
            switch ($key) {
                case 'Calories':
                    $result[$key]['measure'] = '';
                    break;
                case 'Carbohydrates':
                    $result[$key]['measure'] = 'g';
                    break;
                case 'Fat':
                    $result[$key]['measure'] = 'g';
                    break;
                case 'Protein':
                    $result[$key]['measure'] = 'g';
                    break;
                case 'Saturated_Fat':
                    $result[$key]['measure'] = 'g';
                    break;
                case 'Sodium':
                    $result[$key]['measure'] = 'g';
                    break;
                case 'Fiber':
                    $result[$key]['measure'] = 'g';
                    break;
                case 'Sugar':
                    $result[$key]['measure'] = 'g';
                    break;
                case 'Cholesterol':
                    $result[$key]['measure'] = 'g';
                    break;
                default:
                    $result[$key]['measure'] = '';
                    break;
            }
        }
        return $result;
    }
}