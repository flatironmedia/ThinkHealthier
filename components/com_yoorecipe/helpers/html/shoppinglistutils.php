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

abstract class JHtmlShoppingListUtils
{
	/**
	 * generateShoppingListHTML
	 */
	public static function generateShoppingListHTML($item) {

		$html = array();
		
		$html[] = '<tr id="shoppinglist_'.$item->id.'">';
		$html[] = '<td>';
		$html[] = '<div id="sl_title_'.$item->id.'"><a href="'.JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistroute',$item->id), true).'">'.$item->title.'</a></div>';
		$html[] = '<input style="display:none" type="text" id="title_'.$item->id.'" onblur="updateShoppingListTitle('.$item->id.');"/>';
		$html[] = '</td>';
		
		$creation_date = JHtml::_('date', $item->creation_date);
		
		$html[] = '<td>'.$creation_date.'</td>';
		$html[] = '<td class="btn-group">';
		$html[] = '<button type="button" class="btn btn-small" onclick="editShoppingListTitle('.$item->id.');"><i class="icon-edit" ></i></button>';
		$html[] = '<button type="button" class="btn btn-small" onclick="deleteShoppingList('.$item->id.');"><i class="icon-trash"></i></button>';
		$html[] = '</td>';
		$html[] = '</tr>';
		
		return implode("\n",$html);
	}
	
	/**
	* generateShoppingListDetailHTML
	*/
	public static function generateShoppingListDetailHTML($item, $use_fractions) {
	
		$rounded_quantity = round($item->quantity, 2);
		$quantity = ($use_fractions) ? JHtml::_('ingredientutils.decimalToFraction', $rounded_quantity) : $rounded_quantity;
		$quantity_string = ($quantity == 0) ? '' : $quantity.' ';
		
		$html = array();
			
		$html[] = '<li id="ingr_'.$item->id.'">';
		$html[] = '<div class="alert alert-info">';
		$html[] = '<div class="pull-right btn-group">';
		$html[] = '<button type="button" class="btn" onclick="loadUpdateShoppingListDetailModal('.$item->id.');"><i class="icon-edit" ></i></button>';
		$html[] = '<button type="button" class="btn" onclick="deleteShoppingListDetail('.$item->id.');"><i class="icon-trash"></i></button>';
		$html[] = '<input type="hidden" id="sld_quantity_'.$item->id.'" value="'.$quantity.'"/>';
		$html[] = '<input type="hidden" id="sld_description_'.$item->id.'" value="'.$item->description.'"/>';
		$html[] = '</div>';
		$html[] = '<p id="sld_label_'.$item->id.'">'.$quantity_string.$item->description.'</p>';
		$html[] = '</div>';
		$html[] = '</li>';
		
		return implode("\n",$html);
	}
	
	/**
	* generateShoppingListDetailForEditHTML
	*/
	public static function generateShoppingListDetailForEditHTML($item, $use_fractions) {
	
		$rounded_quantity = round($item->quantity, 2);
		$quantity = ($use_fractions) ? JHtml::_('ingredientutils.decimalToFraction', $rounded_quantity) : $rounded_quantity;
		$quantity_string = ($quantity == 0) ? '' : $quantity.' ';
		
		$html = array();
			
		$html[] = '<li id="ingr_'.$item->id.'">';
		$html[] = '<div class="alert alert-info">';
		$html[] = '<div class="pull-right btn-group">';
		$html[] = '<button type="button" class="btn" onclick="loadUpdateShoppingListDetailModal('.$item->id.');"><i class="icon-edit" ></i></button>';
		$html[] = '<button type="button" class="btn" onclick="deleteShoppingListDetail('.$item->id.');"><i class="icon-trash"></i></button>';
		$html[] = '<input type="hidden" id="sld_quantity_'.$item->id.'" value="'.$quantity.'"/>';
		$html[] = '<input type="hidden" id="sld_description_'.$item->id.'" value="'.$item->description.'"/>';
		$html[] = '</div>';
		$html[] = '<p id="sld_label_'.$item->id.'">'.$quantity_string.$item->description.'</p>';
		$html[] = '</div>';
		$html[] = '</li>';
		
		return implode("\n",$html);
	}

	/**
	 * generateShoppingListInRecipeHTML
	 */
	public static function generateShoppingListInRecipeHTML($item) {

		$html = array();
		
		$html[] = '<div class="alert alert-info">';
		$html[] = '<p>'.$item->title.'<button type="button" class="btn pull-right" onclick="addRecipeToShoppingList('.$item->id.');">'.JText::_('COM_YOORECIPE_CHOOSE').'</button></p>';
		$html[] = '</div>';
		
		return implode("\n",$html);
	}
	
	/**
	* addRecipeToShoppingList
	*/
	public static function addRecipeToShoppingList($recipe, $shoppinglist, $nb_persons) {
	
		$user = JFactory::getUser();
		
		// Quality Checks
		$recipe->nb_persons = ($recipe->nb_persons == 0) ? 1 : $recipe->nb_persons;
		$nb_persons = ($nb_persons == 0) ? $recipe->nb_persons : $nb_persons;
		
		// Build hashmap
		$hashmap = array();
		foreach($shoppinglist->details as $detail){
			$hashmap[$detail->description] = $detail;
		}
		
		// Loop over ingredients
		foreach($recipe->groups as $group){
		
			foreach($group->ingredients as $ingredient){
				
				$key = JText::_($ingredient->unit).' '.$ingredient->description;
				if (isset($hashmap[$key])) {
					
					$shoppinglist_detail = $hashmap[$key];
					$shoppinglist_detail->quantity += $ingredient->quantity * $nb_persons / $recipe->nb_persons;
					
					$hashmap[$key] = $shoppinglist_detail;
					
				} else {
				
					$shoppinglist_detail = new stdclass;
					
					$shoppinglist_detail->id = 0;
					$shoppinglist_detail->sl_id = $shoppinglist->id;
					$shoppinglist_detail->quantity = $ingredient->quantity * $nb_persons / $recipe->nb_persons;
					$shoppinglist_detail->description = $ingredient->unit.' '.$ingredient->description;
					$shoppinglist_detail->status = 0;
					$shoppinglist_detail->infos = null;
					$shoppinglist_detail->user_id = $user->id;
					
					$hashmap[$key] = $shoppinglist_detail;
				}
			}
		}
		
		// Return shopping list details
		return $hashmap;
	}
	
	/**
	* updateShoppingListInfos
	*/
	public static function updateShoppingListInfos($shoppinglist, $recipe) {
		
		$infos = (array) json_decode($shoppinglist->infos, true);
		$info = new stdclass;
		$info->recipe_id 	= $recipe->id;
		$info->title		= $recipe->title;
		$info->link			= JHtmlYooRecipeHelperRoute::getRecipeRoute($recipe->id.':'.$recipe->alias);
		
		if ($infos == '') {
			$infos = array();
		}
		
		$infos[$recipe->id] = $info;
		return json_encode($infos);
	}
}