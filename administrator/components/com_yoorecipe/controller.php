<?php
	/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
 /**
 * General Controller of YooRecipe component
 */
class YooRecipeController extends JControllerLegacy
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * display task
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) 
	{
		// set default view if not set
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->get('view', 'YooRecipes', 'STRING'));
 
		// call parent behavior
		parent::display($cachable, $urlparams);
	}
	
	/**
	* refreshRecipeNotes
	*/
	public function refreshRecipeNotes() {
	
		$model = $this->getModel('yoorecipe');
		$commentsModel = $this->getModel('comments');
		
		$recipes = $model->getItems();
		foreach ($recipes as $recipe) {
			$recipe->ratings = $commentsModel->getCommentsByRecipeId($recipe->id, $published = 1, $abuse = 0);
			$model->updateRecipeGlobalNote($recipe->id);
		}
		
		echo 'OK';
	}

	/**
     * Query nutritionix dabatase
     */
    public function getIngredientsNutritionix() {
        // Get search query
        $query     = $_POST['query'];
        $error_msg = "<strong>No results found</strong>";
        $data      = new stdClass();
        $offset    = $_POST['offset'];
        $limit     = $_POST['limit'];
        $res_num = $offset;

        if(isset($query) && trim($query) != '') {
            // Set APP ID and Key
            define('NUTRITIONIX_APP_ID', '57ae7b6b');
            define('NUTRITIONIX_APP_KEY', '0a5c74dcac1512b33113c21c24ae2725');

            $newData['item_name'] = $query;

            if($newData['item_name'] == '')
                echo $error_msg;
            else {
                require_once('nutritionix_api/nutritionix.v1.1.php');
                $nutritionix = new Nutritionix(NUTRITIONIX_APP_ID, NUTRITIONIX_APP_KEY);

                $newData['filters'] = json_decode('{"not":{"nf_calories":null,"nf_total_fat":null, "nf_saturated_fat":null, "nf_protein":null, "nf_total_carbohydrate":null, "nf_sugars":null, "nf_dietary_fiber":null, "nf_cholesterol":null, "nf_sodium":null}}', true);
                $newData['fields']  = array(
                    "item_name",
                    "nf_serving_weight_grams",
                    "nf_calories",
                    "nf_total_fat",
                    "nf_saturated_fat",
                    "nf_protein",
                    "nf_total_carbohydrate",
                    "nf_sugars",
                    "nf_dietary_fiber",
                    "nf_cholesterol",
                    "nf_sodium",
                    "nf_serving_size_unit",
                    "nf_serving_size_qty",
                    "brand_name"
                );

                $data = $nutritionix->search(
                    $newData['item_name'], // query
                    "", // brand name
                    $offset, // offset
                    $limit, // limit
                    NULL, // min_score
                    $newData['fields'], // fields
                    NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, // allergen_contains_milk...
                    array(), // sort
                    $newData['filters'], // filters
                    0 // result as json
                );
            }
        } else
            echo $error_msg;

        if($data) {
            $ing_id  = $_POST['ingredient_id'] ? $_POST['ingredient_id'] : 0;
            $html    = '';

            echo "<div style='width:100%;'>";
            if($data->total)
                echo '<div style="text-align: left; float:left;">Total results found: <strong>' . $data->total . '</strong></div>';

            if(!$ing_id) {
                if($data->total > 10 && ($data->total - ($offset + 10) > 0))
                    echo '<a class="btn" style="cursor: pointer; float: right; margin-bottom: 10px;" onclick="offsetSerach('.($offset + 10).', 10); return false;">Next 10 Items</a>';
                if($offset >= 10)
                    echo '<a class="btn" style="cursor: pointer; float: right; margin-bottom: 10px; margin-right: 10px;" onclick="offsetSerach('.($offset - 10).', 10); return false;">Previous 10 Items</a>';
            } else {
                if($data->total > 10 && ($data->total - ($offset + 10) > 0))
                    echo '<a class="btn" style="cursor: pointer; float: right; margin-bottom: 10px;" onclick="searchIngFunction('.$ing_id.', '.($offset + 10).', 10); return false;">Next 10 Items</a>';
                if($offset >= 10)
                    echo '<a class="btn" style="cursor: pointer; float: right; margin-bottom: 10px; margin-right: 10px;" onclick="searchIngFunction('.$ing_id.', '.($offset - 10).', 10); return false;">Previous 10 Items</a>';
            }
            echo "</div><div style='clear: both'></div>";

            echo '<table class="table table-hover">';
            echo '<thead>
                    <tr>
                        <td><strong>Name</strong></td>
                        <td><strong>Brand Name</strong></td>
                        <td><strong>Serving</strong></td>
                        <td><strong>Weight(g)</strong></td>
                        <td><strong>Calories</strong></td>
                        <!--<td><strong>Carbs(g)</strong></td>
                        <td><strong>Sodium(mg)</strong></td>
                        <td><strong>Fiber(g)</strong></td>
                        <td><strong>Protein(g)</strong></td>-->
                    </tr>
                </thead>';

            echo '<tbody>';
            $html .= '<tr class="result">';

            // Name
            $html .= '<td>';
            $html .= '<a style="cursor: pointer; display: block;" onclick="processLabelClick(this,' . $ing_id . '); return false;" data-nutid="_NUT_ID">';
            $html .= 'nameString</a>';
            $html .= '</td>';

            // Brand name
            $html .= '<td>';
            $html .= '_brandString';
            $html .= '</td>';

            // Servings
            $html .= '<td>';
            $html .= '_servingString';
            $html .= '</td>';

            // Grams
            $html .= '<td>';
            $html .= '_gramsString';
            $html .= '</td>';

            // Calories
            $html .= '<td>';
            $html .= '_caloriesString';
            $html .= '</td>';

            $html .= '</tr>';

            $session          = JFactory::getSession();
            $nutrition_values = array();

            foreach($data->hits as $hit) {
                preg_match("/" . $query . '/i', $hit->fields->item_name, $matches);
                $display_name = preg_replace("/" . $query . "/i", "<b class='highlight'>" . $matches[0] . "</b>", $hit->fields->item_name);

                // Set data to session
                $nutritions = array(
                    "item_name"               => $hit->fields->item_name/$hit->fields->nf_serving_size_qty,
                    "nf_serving_weight_grams" => $hit->fields->nf_serving_weight_grams/$hit->fields->nf_serving_size_qty,
                    "nf_calories"             => $hit->fields->nf_calories/$hit->fields->nf_serving_size_qty,
                    "nf_total_fat"            => $hit->fields->nf_total_fat/$hit->fields->nf_serving_size_qty,
                    "nf_saturated_fat"        => $hit->fields->nf_saturated_fat/$hit->fields->nf_serving_size_qty,
                    "nf_protein"              => $hit->fields->nf_protein/$hit->fields->nf_serving_size_qty,
                    "nf_total_carbohydrate"   => $hit->fields->nf_total_carbohydrate/$hit->fields->nf_serving_size_qty,
                    "nf_sugars"               => $hit->fields->nf_sugars/$hit->fields->nf_serving_size_qty,
                    "nf_dietary_fiber"        => $hit->fields->nf_dietary_fiber/$hit->fields->nf_serving_size_qty,
                    "nf_cholesterol"          => $hit->fields->nf_cholesterol/$hit->fields->nf_serving_size_qty,
                    "nf_sodium"               => $hit->fields->nf_sodium/$hit->fields->nf_serving_size_qty
                );

                $nutrition_values[$hit->_id] = $nutritions;

                // Insert Name
                $output = str_replace('nameString', $display_name, $html);

                // Add ID to link
                $output = str_replace('_NUT_ID', $hit->_id, $output);

                // Set serving string
                $output = str_replace("_servingString", $hit->fields->nf_serving_size_qty . " " . $hit->fields->nf_serving_size_unit, $output);

                // Set grams string
                $output = str_replace("_gramsString", $hit->fields->nf_serving_weight_grams, $output);

                // Set calories string
                $output = str_replace("_caloriesString", $hit->fields->nf_calories, $output);

                // Set brand name
                //$output = str_replace("_fromName", " From " . $hit->fields->brand_name, $output);
                $output = str_replace("_brandString", $hit->fields->brand_name, $output);

                // Output
                echo($output);
            }
            echo '</tbody>';
            echo '</table>';
            echo '<hr />';

            $session->set("nutrition_values", $nutrition_values);
        }
    }


    /**
     * Add an ingredient to a given recipe
     * returns the ingredient id
     */
    public function editIngredient() {
        // Get the document object.
        $document = JFactory::getDocument();

        // Set the MIME type for JSON output.
        $document->setMimeEncoding('application/json');

        // Change the suggested filename.
        JResponse::setHeader('Content-Disposition', 'attachment;filename="editIngredient.json"');

        // Retrieve parameters
        $input                  = JFactory::getApplication()->input;
        $id                     = $input->get('id', 0, 'INT');
        $real_ingredient        = $input->get('real_ingredient', '', 'STRING');
        $recipe_id              = $input->get('recipe_id', 0, 'INT');
        $quantity               = $input->get('quantity', '', 'STRING');
        $usda_ingredient_id     = $input->get('usda_ingredient_id', '', 'STRING');
        $unit                   = $input->get('unit', '', 'STRING');
        $description            = $input->get('description', '', 'STRING');
        $group_id               = $input->get('group_id', 0, 'INT');

        // Check quantity
        if($quantity == '' || $quantity == 0)
            $quantity = 1;

        // Perform some checks
        $qty = str_replace(',', '.', $quantity);

        if(strpos($quantity, '/') == false) {
            $qtyToNum = $qty;
        } else {
            $fraction = array('whole' => 0);
            preg_match('/^((?P<whole>\d+)(?=\s))?(\s*)?(?P<numerator>\d+)\/(?P<denominator>\d+)$/', $qty, $fraction);
            if($fraction['denominator'] != 0) {
                $qtyToNum = $fraction['whole'] + $fraction['numerator'] / $fraction['denominator'];
            } else {
                $qtyToNum = 0;
            }
        }

        // get ingredients model
        $model = $this->getModel('ingredients');

        $ingredient                       = new stdclass;
        $ingredient->recipe_id            = $recipe_id;
        $ingredient->quantity             = $qtyToNum;
        $ingredient->unit                 = $unit;
        $ingredient->description          = $description;
        $ingredient->group_id             = $group_id;
        $ingredient->ordering 			  = $model->getIngredientOrdering($ingredient->recipe_id);

        $result         = new stdclass;
        $result->status = false;

        $ingredient->id = $model->insertIngredientObj($ingredient);
        if($ingredient->id)
        	$result->status = true;

        $result->nutritions = $model->getNutritionsFromId($ingredient->id);

        if($result->status) {

            // Get models
            $yoorecipeModel   = JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
            $ingredientsModel = JModelLegacy::getInstance('ingredients', 'YooRecipeModel');
            $unitsModel       = JModelLegacy::getInstance('units', 'YooRecipeModel');
            $ingredient       = $ingredientsModel->getIngredientByIdAndRecipeId($ingredient->id, $ingredient->recipe_id);

            $version    = new JVersion;
            $joomla     = $version->getShortVersion();
            $is_joomla3 = version_compare($joomla, '3.0', '>=') ? true : false;

            // Get data
            $recipe           = $yoorecipeModel->getRecipeById($recipe_id);
            $groups           = $ingredientsModel->getListOfIngredientsGroups($recipe->language);
            $units            = $unitsModel->getAllPublishedUnitsByLocale($recipe->language);
            //$usda_ingredients = $yoorecipeModel->getUSDAIngredients();

            if($is_joomla3) {
                $result->html = JHtml::_('ingredientutils.generateIngredientHTML', $ingredient, $groups, $units);
            } else {
                $result->html = JHtml::_('ingredientutils.generateIngredientHTML_j25', $ingredient, $groups, $units);
            }
        }
        echo json_encode($result);
    }

    public function insertIngredient(){
        
        define('NUTRITIONIX_APP_ID', '57ae7b6b');
        define('NUTRITIONIX_APP_KEY', '0a5c74dcac1512b33113c21c24ae2725');
        require_once JPATH_ADMINISTRATOR . "/components/com_yoorecipe/nutritionix_api/nutritionix.v1.1.php";
        $nutritionix = new Nutritionix(NUTRITIONIX_APP_ID, NUTRITIONIX_APP_KEY);

        $jinput = JFactory::getApplication()->input;
        $ingredient_id = $jinput->get('ingredient_id', '', 'STRING');
        $recipe_id = $jinput->get('recipe_id', 0, 'INT');
        $quantity = $jinput->get('quantity', '1', 'STRING');

        $ingredient_info = $nutritionix->getItem($ingredient_id, "id", false, false);

        $result = new stdClass();
        $result->nutrition['serving_size'] = number_format(($ingredient_info['nf_serving_weight_grams']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['kcal'] = number_format(($ingredient_info['nf_calories']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['fat'] = number_format(($ingredient_info['nf_total_fat']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['saturated_fat'] = number_format(($ingredient_info['nf_saturated_fat']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['proteins'] = number_format(($ingredient_info['nf_protein']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['carbs'] = number_format(($ingredient_info['nf_total_carbohydrate']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['sugar'] = number_format(($ingredient_info['nf_sugars']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['fibers'] = number_format(($ingredient_info['nf_dietary_fiber']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['cholesterol'] = number_format(($ingredient_info['nf_cholesterol']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['salt'] = number_format(($ingredient_info['nf_sodium']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['kjoule'] = number_format((($ingredient_info['nf_calories']*4.185)/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');

        $model = $this->getModel('ingredients');
        
        $result->success = 'true';

        die(json_encode($result));  
    }


    public function deleteIngredient(){
        define('NUTRITIONIX_APP_ID', '57ae7b6b');
        define('NUTRITIONIX_APP_KEY', '0a5c74dcac1512b33113c21c24ae2725');
        require_once JPATH_ADMINISTRATOR . "/components/com_yoorecipe/nutritionix_api/nutritionix.v1.1.php";
        $nutritionix = new Nutritionix(NUTRITIONIX_APP_ID, NUTRITIONIX_APP_KEY);

        $jinput = JFactory::getApplication()->input;
        $quantity = $jinput->get('quantity', '1', 'STRING');
        $nutrition_id = $jinput->get('nutrition_id', '', 'STRING');

        $ingredient_info = $nutritionix->getItem($nutrition_id, "id", false, false);

        $result = new stdClass();
        $result->nutrition['serving_size'] = number_format(($ingredient_info['nf_serving_weight_grams']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['kcal'] = number_format(($ingredient_info['nf_calories']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['fat'] = number_format(($ingredient_info['nf_total_fat']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['saturated_fat'] = number_format(($ingredient_info['nf_saturated_fat']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['proteins'] = number_format(($ingredient_info['nf_protein']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['carbs'] = number_format(($ingredient_info['nf_total_carbohydrate']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['sugar'] = number_format(($ingredient_info['nf_sugars']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['fibers'] = number_format(($ingredient_info['nf_dietary_fiber']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['cholesterol'] = number_format(($ingredient_info['nf_cholesterol']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['salt'] = number_format(($ingredient_info['nf_sodium']/$ingredient_info['nf_serving_size_qty'])*$quantity, 2, '.', '');
        $result->nutrition['kjoule'] = number_format((($ingredient_info['nf_calories']*4.185)/$ingredient_info['nf_serving_size_qty'])*$quantity,2, '.', '');

        $result->success = 'true';

        die(json_encode($result)); 
    }
}