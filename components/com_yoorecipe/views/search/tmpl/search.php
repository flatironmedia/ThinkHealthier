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
// no direct access
defined('_JEXEC') or die;

// Get factories
$document 	= JFactory::getDocument();
$user 		= JFactory::getUser();
$lang 		= JFactory::getLanguage();

JHtml::_('jquery.ui', array('core'));
JHtml::_('behavior.framework', $type = 'more');
JHtml::_('bootstrap.framework');
JHtml::_('behavior.formvalidation');


$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

$params 				= JComponentHelper::getParams('com_yoorecipe');
$canSearchCategories	= $params->get('search_categories', 1);
$canSearchIngredients	= $params->get('search_ingredients', 1);
$canExcludeIngredients	= $params->get('exclude_ingredients', 1);

$show_search_prep_time	= $params->get('show_search_prep_time', 1);
$show_search_cook_time	= $params->get('show_search_cook_time', 1);
$search_seasons		 	= $params->get('search_seasons', 1);
$show_search_cuisine 	= $params->get('show_search_cuisine', 1);
$show_search_author 	= $params->get('show_search_author', 1);
$show_search_rated		= $params->get('show_search_rated', 1);
$show_search_cost		= $params->get('show_search_cost', 1);
$show_search_nutrition_facts	= $params->get('show_search_nutrition_facts', 0);
$use_nutrition_facts	= $params->get('use_nutrition_facts', 1);

$searchword_mandatory	= $params->get('searchword_mandatory', 1);
$category_mandatory		= $params->get('category_mandatory', 1);
$ingredients_mandatory	= $params->get('ingredients_mandatory', 0);

$currency				= $params->get('currency', '&euro;');

JHtml::_('script', JUri::root().'components/com_yoorecipe/assets/jquery-ui-1.11.4.custom/jquery-ui.min.js');
$document->addStyleSheet(JUri::root().'components/com_yoorecipe/assets/jquery-ui-1.11.4.custom/jquery-ui.min.css');
$document->addStyleSheet(JUri::root().'components/com_yoorecipe/assets/jquery-ui-1.11.4.custom/jquery-ui.theme.min.css');
$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/search.js');

if ($params->get('search_categories_display', 'dropdown') == 'flat') {

// This algorithm is based on the number of spans (indentations) to determine which inputs are children of the one clicked on
	$document->addScriptDeclaration("
//<![CDATA[
function com_yoorecipe_search_checkChildren(elt) {

	liElt = elt.getParent('li');
	nbChildrenOfClickedElt = liElt.getChildren().length;
	
	allNextLiElts = liElt.getAllNext();
	for (i = 0 ; i < allNextLiElts.length; i++) {
		
		childrenElts = allNextLiElts[i].getChildren();
		if (childrenElts.length > nbChildrenOfClickedElt) {
			childrenElts[0].checked = elt.checked;
		} else {
			break;
		}
	}
}
/* ]]> */");
}
?>
<input type="hidden" id="language_tag" value="<?php echo $lang->getTag(); ?>"/>

<div class="item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/Article">
	<div class="page-header">
	<?php if ($this->menuParams->get('show_page_heading', 1)) { ?>
		<h1> <?php echo $this->escape($this->menuParams->get('page_heading')); ?> </h1>
	<?php } else { ?>
		<h1><?php echo JText::_('COM_YOORECIPE_SEARCH_RECIPE'); ?></h1>	
	<?php } ?>
	</div>
	
	<div class="huge-ajax-loading"></div>
	<form class="form-horizontal" id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=search&layout=results', false); ?>" method="post">
		
		<a id="yoorecipe-search" name="yoorecipe-search"></a>
		<input type="hidden" name="searchPerformed" value="1"/>
		
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_YOORECIPE_SEARCH_BY_RECIPE'); ?>
			</div>
			<div class="controls">
				<input type="text" id="search-searchword" name="searchword" placeholder="<?php echo JText::_('COM_YOORECIPE_SEARCH_KEYWORD'); ?>" value="<?php echo $this->escape($this->state->get('filter.searchword')); ?>" class="<?php echo ($searchword_mandatory) ? 'required' : ''; ?> inputbox" />
			</div>
		</div>
		
		<div class="row-fluid">
	<?php
	if ($canSearchCategories || $show_search_cuisine) {

		echo '<div class="span6">';
		
		if ($canSearchCategories) {
		
			echo '<legend>'.JText::_('COM_YOORECIPE_SEARCH_BY_CATEGORY').'</legend>';
			if ($params->get('search_categories_display', 'dropdown') == 'flat')
			{
				echo '<ul class="unstyled">';
				foreach ($this->categories as $i => $category) : 
					echo '<li>';
					$chked = '';
					if ($i == 0 || in_array($category->id, $this->searchCategories)) {
						$chked = 'checked="checked"';
					}
			?>
					<input
						type="checkbox" name="searchCategories[]" <?php echo $chked; ?> 
						value="<?php echo $category->id; ?>" 
						id="com_yoorecipe_catid_<?php echo $category->id ?>" 
						onclick="com_yoorecipe_search_checkChildren(this);" 
						class="<?php echo ($i==0 && $category_mandatory) ? 'validate-one-required' : ''; ?>"
					/>
			<?php
					echo str_repeat('<span>&nbsp;&nbsp;&nbsp;</span>', $category->level-1).htmlspecialchars($category->title);
					echo '</li>';
				endforeach;
				echo '</ul>';
			}
			
			else if ($params->get('search_categories_display', 'dropdown') == 'dropdown')
			{
				echo '<select name="searchCategories[]" multiple="multiple" class="required">';
				echo '<option value="*" selected="selected">',JText::_('COM_YOORECIPE_ALL'),'</option>';
				foreach ($this->categories as $category) : 
					echo '<option';
					
					if (in_array($category->id, $this->searchCategories)) {
						echo ' selected="selected"';
					}
					
					echo ' value="',$category->id.'">',str_repeat('&nbsp;&nbsp;&nbsp;', $category->level-1),htmlspecialchars($category->title);
					echo '</option>';
				endforeach;
				echo '</select>';
			}
			
			echo '<br/>';
			
		} // End if ($canSearchCategories) {
		
		if ($show_search_cuisine) {
?>
			<legend><?php echo JText::_('COM_YOORECIPE_CUISINE'); ?></legend>
			<select name="search_cuisine">
				<?php echo JHtml::_('select.options', JHtml::_('optionsutils.cuisineOptions'), 'value', 'text', $this->state->get('filter.search_cuisine')); ?>
			</select>
<?php
		}
	
		echo '</div>';
		
	} // End if ($canSearchCategories || $show_search_cuisine) {

	if ($use_nutrition_facts && $show_search_nutrition_facts) {
?>
		<div class="span6">	
			<legend><?php echo JText::_('COM_YOORECIPE_YOORECIPE_NUTRITION_FACTS'); ?></legend>
			
			<div class="row-fluid">
				<div class="span6">
					<label class="checkbox">
					<input type="checkbox" name="search_type_diet" value="1" <?php echo $this->state->get('filter.search_type_diet') == 'on' ? 'checked="checked"' : ''; ?>/>
					<?php echo JText::_('COM_YOORECIPE_YOORECIPE_DIET_LABEL'); ?></label>
				</div>
				<div class="span6">
					<label class="checkbox">
					<input type="checkbox" name="search_type_veggie" value="1" <?php echo $this->state->get('filter.search_type_veggie') == 'on' ? 'checked="checked"' : ''; ?>/>
					<?php echo JText::_('COM_YOORECIPE_YOORECIPE_VEGGIE_LABEL'); ?></label>
				</div>
			</div>		
			<div class="row-fluid">
				<div class="span6">
					<label class="checkbox">
					<input type="checkbox" name="search_type_glutenfree" value="1" <?php echo $this->state->get('filter.search_type_glutenfree') == 'on' ? 'checked="checked"' : ''; ?>/>
					<?php echo JText::_('COM_YOORECIPE_YOORECIPE_GLUTEN_FREE_LABEL'); ?></label>
				</div>
				<div class="span6">
					<label class="checkbox">
					<input type="checkbox" name="search_type_lactosefree" value="1" <?php echo $this->state->get('filter.search_type_lactosefree') == 'on' ? 'checked="checked"' : ''; ?>/>
					<?php echo JText::_('COM_YOORECIPE_YOORECIPE_LACTOSE_FREE_LABEL'); ?></label>
				</div>
			</div>		
			
			<div class="row-fluid">
				<div class="span6">
					<div class="input-append">
						<input type="text" id="kcal" name="search_kcal" class="input-mini" readonly value="<?php echo $this->escape($this->state->get('filter.search_kcal')); ?>">
						<span class="add-on"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_KCAL_LABEL'); ?></span>
					</div>
				</div>
				<div class="span6">
					<div id="slider-kcal"></div>
				</div>
			</div>
			
			<div class="row-fluid">
				<div class="span6">
					<div class="input-append">
						<input type="text" id="carbs" name="search_carbs" class="input-mini" readonly value="<?php echo $this->escape($this->state->get('filter.search_carbs')); ?>">
						<span class="add-on"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_CARBS_LABEL'); ?></span>
					</div>
				</div>
				<div class="span6">
					<div id="slider-carbs"></div>
				</div>
			</div>
			
			<div class="row-fluid">
				<div class="span6">
					<div class="input-append">
						<input type="text" id="fat" name="search_fat" class="input-mini" readonly value="<?php echo $this->escape($this->state->get('filter.search_fat')); ?>">
						<span class="add-on"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_FAT_LABEL'); ?></span>
					</div>
				</div>
				<div class="span6">
					<div id="slider-fat"></div>
				</div>
			</div>
			
			<div class="row-fluid">
				<div class="span6">
					<div class="input-append">
						<input type="text" id="proteins" name="search_proteins" class="input-mini" readonly value="<?php echo $this->escape($this->state->get('filter.search_proteins')); ?>">
						<span class="add-on"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_PROTEINS_LABEL'); ?></span>
					</div>
				</div>
				<div class="span6">
					<div id="slider-proteins"></div>
				</div>
			</div>
		</div>
<?php
	}
?>
	</div>

	<div class="row-fluid">
	<?php
	if ($search_seasons) {
	?>
		<div class="span6">
			<legend><?php echo JText::_('COM_YOORECIPE_SEARCH_BY_SEASON'); ?></legend>
			<select name="searchSeasons">
				<option value=""><?php echo JText::_('COM_YOORECIPE_ANY'); ?></option>
				<option value="JAN"><?php echo JText::_('COM_YOORECIPE_JAN'); ?></option>
				<option value="FEB"><?php echo JText::_('COM_YOORECIPE_FEB'); ?></option>
				<option value="MAR"><?php echo JText::_('COM_YOORECIPE_MAR'); ?></option>
				<option value="APR"><?php echo JText::_('COM_YOORECIPE_APR'); ?></option>
				<option value="MAY"><?php echo JText::_('COM_YOORECIPE_MAY'); ?></option>
				<option value="JUN"><?php echo JText::_('COM_YOORECIPE_JUN'); ?></option>
				<option value="JUL"><?php echo JText::_('COM_YOORECIPE_JUL'); ?></option>
				<option value="AUG"><?php echo JText::_('COM_YOORECIPE_AUG'); ?></option>
				<option value="SEP"><?php echo JText::_('COM_YOORECIPE_SEP'); ?></option>
				<option value="OCT"><?php echo JText::_('COM_YOORECIPE_OCT'); ?></option>
				<option value="NOV"><?php echo JText::_('COM_YOORECIPE_NOV'); ?></option>
				<option value="DEC"><?php echo JText::_('COM_YOORECIPE_DEC'); ?></option>
			</select>
			<br/><br/>
		</div>
	<?php
	}

	if ($show_search_author) {
	?>
		<div class="span6">
			<legend><?php echo JText::_('COM_YOORECIPE_SEARCH_BY_AUTHOR'); ?></legend>
			<select name="search_author">
				<option value=""><?php echo JText::_('COM_YOORECIPE_ANY'); ?></option>
	<?php   foreach ($this->authors as $author) {
				echo '<option value="'.$author->id .'">'.$author->author_name.'</option>';
			} ?>
			</select>
			<br/><br/>
		</div>
	<?php 
	} ?>
	</div>

	<?php
	if ($show_search_prep_time || $show_search_cook_time || $show_search_rated || $show_search_cost) {
	?>
		<legend><?php echo JText::_('COM_YOORECIPE_SEARCH_BY_TIME'); ?></legend>	
		<div class="row-fluid">
	<?php 
		if ($show_search_prep_time){ 
	?>
			<div class="span6">
				<label for="search_max_prep_hours"><?php echo JText::_('COM_YOORECIPE_SEARCH_BY_PREP_TIME'); ?></label>
				<div class="input-append">
					<select name="search_max_prep_hours" class="input-mini">
						<?php for ($i = 0 ; $i < 24 ; $i++) { ?><option value="<?php echo $i; ?>" <?php if ($i==2) : echo ' selected="selected"'; endif; ?>><?php echo $i; ?></option><?php } ?>
					</select>
					<span class="add-on"><?php echo JText::_('COM_YOORECIPE_HOUR'); ?></span>
					<select name="search_max_prep_minutes" class="input-mini">
						<?php for ($i = 0 ; $i < 60 ; $i++) { ?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php } ?>
					</select>
					<span class="add-on"><?php echo JText::_('COM_YOORECIPE_MIN');  ?></span>
				</div>
				<br/><br/>
			</div>
	<?php } 
		if ($show_search_cook_time)	{
	?>
			<div class="span6">
				<label for="search_max_cook_hours"><?php echo JText::_('COM_YOORECIPE_SEARCH_BY_COOK_TIME'); ?></label>
				<div class="input-append">
					<select name="search_max_cook_hours" class="input-mini">
						<?php for ($i = 0 ; $i < 24 ; $i++) { ?><option value="<?php echo $i; ?>" <?php if ($i==1) : echo ' selected="selected"'; endif; ?>><?php echo $i; ?></option><?php } ?>
					</select>
					<span class="add-on"><?php echo JText::_('COM_YOORECIPE_HOUR'); ?></span>
					<select name="search_max_cook_minutes" class="input-mini">
						<?php for ($i = 0 ; $i < 60 ; $i = $i+5) { ?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php } ?>
					</select>
					<span class="add-on"><?php echo JText::_('COM_YOORECIPE_MIN');  ?></span>
				</div>
				<br/><br/>
			</div>
	<?php } ?>
		</div>

		<div class="row-fluid">
	<?php
		if ($show_search_rated){ 
	?>		
			<div class="span6">
				<label for="search_min_rate"><?php echo JText::_('COM_YOORECIPE_SEARCH_BY_RATING'); ?></label>
				<select name="search_min_rate">
					<option value="0" selected="selected"><?php echo JText::_('COM_YOORECIPE_ANY'); ?></option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
				</select>
				<br/><br/>
			</div>
	<?php }

		if ($show_search_cost)
		{  ?>
			<div class="span6">
				<label for="search_max_cost"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_MAX_COST'); ?></label>
				<select name="search_max_cost">
					<option value="999" selected="selected"><?php echo JText::_('COM_YOORECIPE_ANY'); ?></option>
					<option value="1"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_CHEAP_LABEL'); ?></option>
					<option value="2"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_INTERMEDIATE_LABEL'); ?></option>
					<option value="3"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_EXPENSIVE_LABEL'); ?></option>
				</select>
				<br/><br/>
			</div>
	<?php } ?>
		</div>
		
	<?php 
	} // End if ($show_search_prep_time || $show_search_cook_time || $show_search_rated || $show_search_cost) {

	if ($canSearchIngredients || $canExcludeIngredients) { ?>	
			<legend><?php echo JText::_('COM_YOORECIPE_SEARCH_BY_INGREDIENTS'); ?></legend>
		
			<span><?php echo JText::_('COM_YOORECIPE_SEARCH_WITH_INGREDIENTS'); ?></span>
			<div>
		<?php if ($canSearchIngredients) { ?>
				<input type="text" name="withIngredients[]" id="search-ingredient1" 
					size="30" maxlength="<?php echo $upper_limit; ?>" 
					value="<?php if (isset($this->withIngredients[0])) { echo $this->escape($this->withIngredients[0]); }?>" 
					class="inputbox <?php echo $ingredients_mandatory? 'validate-one-required' : ''; ?>" placeholder="<?php echo JText::_('COM_YOORECIPE_SEARCH_INGREDIENT1'); ?>"
				/>
			
				<input type="text" name="withIngredients[]" id="search-ingredient2"
					size="30" maxlength="<?php echo $upper_limit; ?>" 
					value="<?php if (isset($this->withIngredients[1])) { echo $this->escape($this->withIngredients[1]); }?>" 
					class="inputbox" placeholder="<?php echo JText::_('COM_YOORECIPE_SEARCH_INGREDIENT2'); ?>"
				/>
			
				<input type="text" name="withIngredients[]" id="search-ingredient3"
					size="30" maxlength="<?php echo $upper_limit; ?>" 
					value="<?php if (isset($this->withIngredients[2])) { echo $this->escape($this->withIngredients[2]); }?>"
					class="inputbox" placeholder="<?php echo JText::_('COM_YOORECIPE_SEARCH_INGREDIENT3'); ?>"
				/>
		<?php } ?>
		<?php if ($canExcludeIngredients) { ?>
				<br/>
				<label><?php echo JText::_('COM_YOORECIPE_SEARCH_WITHOUT_INGREDIENTS'); ?></label>
				<input type="text" name="withoutIngredients[]" id="search-ingredient4"
					size="30" maxlength="<?php echo $upper_limit; ?>" 
					value="<?php if (isset($this->withoutIngredients[0])) { echo $this->escape($this->withoutIngredient[0]); }?>"
					class="inputbox" placeholder="<?php echo JText::_('COM_YOORECIPE_SEARCH_INGREDIENT1'); ?>"
				/>
				
				<input type="text" name="withoutIngredients[]" id="search-ingredient5"
					size="30" maxlength="<?php echo $upper_limit; ?>" 
					value="<?php if (isset($this->withoutIngredients[1])) { echo $this->escape($this->withoutIngredient[1]); }?>"
					class="inputbox" placeholder="<?php echo JText::_('COM_YOORECIPE_SEARCH_INGREDIENT2'); ?>"
				/>
				
				<input type="text" name="withoutIngredients[]" id="search-ingredient6"
					size="30" maxlength="<?php echo $upper_limit; ?>" 
					value="<?php if (isset($this->withoutIngredients[2])) { echo $this->escape($this->withoutIngredient[2]); }?>"
					class="inputbox" placeholder="<?php echo JText::_('COM_YOORECIPE_SEARCH_INGREDIENT3'); ?>"
				/>
		<?php } ?>
			</div>
	<?php } ?>

		<input type="button" name="search" onclick="yoorecipeSearchFormValidator.validate();" class="btn btn-primary pull-right" value="<?php echo JText::_('COM_YOORECIPE_SEARCH');?>" />
		<input type="hidden" name="task" value="search" />
	</form>
	<br/>
	<?php if($this->error): ?>
	<div class="error">
		<?php echo $this->escape($this->error); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->searchPerformed && count($this->items)== 0) : ?>
	<div class="error">
		<?php echo JText::_('COM_YOORECIPE_SEARCH_NO_RESULTS_FOUND'); ?>
	</div>
	<?php endif; ?>
</div>