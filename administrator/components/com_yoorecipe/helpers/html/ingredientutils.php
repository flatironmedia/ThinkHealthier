<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

abstract class JHtmlIngredientUtils
{
	/**
	 * buildIngredientGroupsFromRequest
	 */
	public static function buildIngredientGroupsFromRequest($input, $recipe_id) {
	
		$ingredient_groups = array();
		
		$groups 			= $input->get('group', array(), 'ARRAY');
		$group_actions 		= $input->get('group_action', array(), 'ARRAY');
		$group_orderings	= $input->get('group_ordering', array(), 'ARRAY');
		$group_ids	 		= $input->get('group_id', array(), 'ARRAY');
		
		foreach ($groups as $i => $group) {
			
			$ingredient_group = new stdclass;
			$ingredient_group->id		 	= $group_ids[$i];
			$ingredient_group->recipe_id 	= $recipe_id;
			$ingredient_group->label 		= $group;
			$ingredient_group->action 		= $group_actions[$i];
			$ingredient_group->ordering		= $i;
			$ingredient_group->index		= $group_orderings[$i];
			
			$ingredient_groups[] = $ingredient_group;
		}
		
		return $ingredient_groups;
	}
	
	/**
	 * buildIngredientsFromRequest
	 */
	public static function buildIngredientsFromRequest($input, $recipe_id) {
	
		$ingredients = array();
		
		$ingredient_ids 		= $input->get('ingredient_id', array(), 'ARRAY');
		$quantities 			= $input->get('quantity', array(), 'ARRAY');
		$units					= $input->get('unit', array(), 'ARRAY');
		$descriptions 			= $input->get('description', array(), 'ARRAY');
		$group_index 			= $input->get('group_index', array(), 'ARRAY');
		$ingredient_actions 	= $input->get('ingredient_action', array(), 'ARRAY');
		/* Xander added id */
		$nutrition_ids			= $input->get('nutrition_ids', array(), 'ARRAY');
		$standard_ingredients	= $input->get('standard_ingredients', array(), 'ARRAY');

		foreach ($quantities as $i => $quantity) {
			
			$ingredient = new stdclass;
			$ingredient->id	 					= $ingredient_ids[$i];
			$ingredient->recipe_id 				= $recipe_id;
			$ingredient->quantity 				= self::fractionToDecimal($quantities[$i]);
			$ingredient->unit 					= $units[$i];
			$ingredient->description 			= $descriptions[$i];
			$ingredient->group_index 			= $group_index[$i];
			$ingredient->action		 			= $ingredient_actions[$i];
			/*Xander added ID */
			$ingredient->nutrition_id 			= $nutrition_ids[$i];
			$ingredient->standard_ingredient 	= $standard_ingredients[$i];
			$ingredient->ordering	 			= $i;
			
			$ingredients[] = $ingredient;
		}
		
		return $ingredients;
	}
	
	/**
	* fractionToDecimal
	*/
	private static function fractionToDecimal($quantity) {
	
		// Perform some checks
		$qty = str_replace(',', '.', $quantity);
		$result;
		if (strpos($quantity, '/') == false) {
			$result = $qty;
		} else {
			$fraction = array('whole' => 0);
			preg_match('/^((?P<whole>\d+)(?=\s))?(\s*)?(?P<numerator>\d+)\/(?P<denominator>\d+)$/', $qty, $fraction);
			if ($fraction['denominator'] != 0) {
				$result = $fraction['whole'] + $fraction['numerator']/$fraction['denominator'];
			} else {
				$result = 0;
			}
		}
		
		return $result;
	}
	
	/**
	* getJsonQuantities
	*/
	public static function getJsonQuantities($nb_persons, $recipe) {
	
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$max_servings		= $yooRecipeparams->get('max_servings', 10);
		$use_fractions		= $yooRecipeparams->get('use_fractions', 0);

		$quantities = array();
		if ($nb_persons == 0) {
			return $quantities;
		}
		foreach ($recipe->groups as $group) {

			foreach ($group->ingredients as $ingredient) {

				$ingr_quantities = array();
				for ($i = 1; $i <= $max_servings ; $i++) {
					$new_quantity = (double) ($ingredient->quantity * $i / $nb_persons);	
					if ($new_quantity > 5) {
						$ingr_quantities[$i] = round($new_quantity, 0);
					} else {
					
						if ($use_fractions) {
							$ingr_quantities[$i] = self::decimalToFraction(round($new_quantity, 2));
						} else {
							$ingr_quantities[$i] = round($new_quantity, 2);
						}
					}
				}
				$quantities[$ingredient->id] = $ingr_quantities;
			}
		}
		
		return $quantities;
	}
	
	/**
	* migrateIngredients
	*/
	public static function migrateIngredients() {
	
		$results = array();

		// Create a new query object
		$db = JFactory::getDBO();
		$db->transactionStart();
		$query = $db->getQuery(true);
		
		// Migrate units
		$query_parts = array();
		$query_parts[] = 'update `#__yoorecipe_ingredients` i';
		$query_parts[] = 'join `#__yoorecipe` r on r.id = i.recipe_id';
		$query_parts[] = 'join `#__yoorecipe_units` u on u.lang = r.language and i.unit = u.code';
		$query_parts[] = 'set i.unit = u.label';
		$query_parts[] = 'where i.migrated = 0';
		
		$db->setQuery(implode(" ", $query_parts));
		$results[] = $db->execute();
		
		// Migrate ingredients groups
		$query = $db->getQuery(true);
		$query->select('distinct i.group_id, i.recipe_id, g.label');
		$query->from('#__yoorecipe_ingredients i');
		$query->join('LEFT', '#__yoorecipe_ingredients_groups g on g.id = i.group_id');
		$query->where('migrated = 0');
		$query->order('recipe_id asc');
		$db->setQuery((string) $query);
		
		$ingredients_groups = $db->loadObjectList();

		foreach ($ingredients_groups as $ingredients_group) {
			
			$new_ingredient_group = new stdclass;
			$new_ingredient_group->recipe_id 	= $ingredients_group->recipe_id;
			$new_ingredient_group->label		= $ingredients_group->label;
			
			$db->insertObject('#__yoorecipe_ingredients_groups', $new_ingredient_group, 'id');
			$new_ingredient_group->id = $db->insertid();
			
			$update_query = 'update #__yoorecipe_ingredients set group_id = '.$new_ingredient_group->id.', migrated = 1 where recipe_id = '.$ingredients_group->recipe_id.' and group_id = '.$ingredients_group->group_id;
			$db->setQuery($update_query);
			$results[] = $db->execute();
		}
		
		// Delete old units
		$db->setQuery('delete from `#__yoorecipe_units` where to_delete = 1');
		$results[] = $db->execute();
		
		if (in_array(false, $results)) {
			$db->transactionRollback();
		} else {
			$db->transactionCommit();
		}
		
	}
	
	
	/**
	* Turns a decimal value into a fraction
	*/
	private static function getFraction($value) {
		
		if ($value == 0) return '';
		if ($value == 0.13) return '1/8';
		if ($value == 0.2) return '1/5';
		if ($value == 0.25) return '1/4';
		if ($value == 0.33) return '1/3';
		if ($value == 0.5) return '1/2';
		if ($value == 0.67) return '2/3';
		if ($value == 0.75) return '3/4';
		
		if ($value < 0.13) return '1/10';
		if ($value > 0.13 && $value <= 0.18) return '1/8';
		if ($value > 0.18 && $value <= 0.22) return '1/5';
		if ($value > 0.22 && $value <= 0.28) return '1/4';
		if ($value > 0.28 && $value <= 0.33) return '1/3';
		if ($value > 0.33 && $value <= 0.5) return '1/2';
		if ($value > 0.5 && $value < 0.67) return '2/3';
		if ($value > 0.67) return '3/4';
		return $value;
	}
	
	/**
	 * Turn a decimal value into a fraction
	 */
	public static function decimalToFraction($value) {
		$elts = explode('.', $value);
		if (count($elts) > 1) {
			$fraction;
			if ($elts[0] == '0') {
				$fraction = self::getFraction($value);
			} else {
				$decimal = (double) ($elts[1] / pow(10, strlen($elts[1])));
				$fraction = $elts[0].' '.self::getFraction($decimal);
			}
			return $fraction;
		}
		else {
			return $value;
		}
	}
}