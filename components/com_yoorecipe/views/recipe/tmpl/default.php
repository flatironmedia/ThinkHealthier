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

jimport('joomla.application.module.helper');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

// Get Factories
$lang 		= JFactory::getLanguage();
$document	= JFactory::getDocument();

JHtml::_('bootstrap.framework');

JHtml::addIncludePath(JPATH_COMPONENT.'/lib');

$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addStyleSheet('media/com_yoorecipe/styles/bluecurve/bluecurve.css');

$document->addScript('media/com_yoorecipe/js/range.js');
$document->addScript('media/com_yoorecipe/js/timer.js');
$document->addScript('media/com_yoorecipe/js/slider.js');
$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/recipe.js');

// Init variables
$input	 	= JFactory::getApplication()->input;
$user 		= JFactory::getUser();
$recipe 	= $this->recipe;
$is_printing = $input->get('print', 0, 'INT');

// Add scripts
$document->addScriptDeclaration("new Fx.SmoothScroll({duration: 200}, window);");

// Component Parameters
$yooRecipeparams 			= JComponentHelper::getParams('com_yoorecipe');
$can_show_price				= $yooRecipeparams->get('show_price', 0);
$currency					= $yooRecipeparams->get('currency', '&euro;');

// Menu Parameters also defined in Component Settings
$enable_reviews				= $yooRecipeparams->get('enable_reviews', 1);
$show_author				= $yooRecipeparams->get('show_author', 1);

$use_shoppinglist			= $yooRecipeparams->get('use_shoppinglist', 1);
$use_google_recipe			= $yooRecipeparams->get('use_google_recipe', 1);
$use_nutrition_facts		= $yooRecipeparams->get('use_nutrition_facts', 1);
$currency					= $yooRecipeparams->get('currency', '&euro;');
$use_tags					= $yooRecipeparams->get('use_tags', 1);
$show_seasons				= $yooRecipeparams->get('show_seasons', 1);
$use_similar_recipes		= $yooRecipeparams->get('use_similar_recipes', 1);

$can_show_category			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_category', 1);

$can_show_description		= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_description', 1);
$can_show_difficulty		= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_difficulty', 1);
$can_show_cost				= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_cost', 1);
$rating_style				= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'rating_style', 'stars');

$can_show_cook_time			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_cook_time', 1);
$can_show_wait_time			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_wait_time', 1);

$can_show_ratings			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_rating', 1);

$use_social_sharing					= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'use_social_sharing', 1);
$show_social_bookmarks_on_bottom	= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_on_bottom', 0);
?>
<div class="huge-ajax-loading"></div>
 <?php
 if (!isset($recipe) || !$this->canShow) {
	echo JHtml::_('yoorecipeutils.generateCategoriesList', $this->categories);
}
else {
	
	// Add FB opengraph tags
	$openGraphTags = array();
	$uri	= JURI::getInstance();
	$lang 	= JFactory::getLanguage();
	$config = JFactory::getConfig();
	$openGraphTags[] = '<meta property="og:url" content="'.$uri->toString().'"/>';
	$openGraphTags[] = '<meta property="og:image" content="'.JUri::base().$recipe->picture.'"/>';
	$openGraphTags[] = '<meta property="og:title" content="'.htmlspecialchars($recipe->title).'"/>';
	$openGraphTags[] = '<meta property="og:description" content="'.strip_tags($recipe->description).'"/>';
	$openGraphTags[] = '<meta property="og:type" content="recipebox:recipe"/>';
	$openGraphTags[] = '<meta property="og:locale" content="'.$lang->getTag().'"/>';
	$openGraphTags[] = '<meta property="og:site_name" content="'.$config->get('config.sitename').'"/>';

	$document->addCustomTag(implode("\n", $openGraphTags));
?>
<a id="top" name="top"></a>

<?php
if ($use_google_recipe) { 
	echo '<div itemscope itemtype="http://schema.org/Recipe">';
}
?>

<?php

if ($is_printing) {
	$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe-print.css', 'text/css', 'print');
	echo '<input type="button" class="btn" onclick="javascript:window.print()" value="'.JText::_('COM_YOORECIPE_PRINT').'"/>';
}
?>

<h1 itemprop="name">
<?php 
	echo htmlspecialchars($recipe->title); if($can_show_price==1 && $recipe->price!=null && $recipe->price > 0){ echo ' '.$recipe->price.$currency;}

	if ($recipe->featured) {
		echo '<img class="pull-right" src="media/com_yoorecipe/images/featured-item.png" alt="'.JText::_('JFEATURED').'">';
	}
?>
</h1>
<div class="yoorecipe-after-title">
<?php
	$modules = JModuleHelper::getModules('yoorecipe-after-title');
	foreach($modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
?>
</div>

<?php if ($can_show_category) { ?>
<div class="row-fluid">
<?php
	$cross_categories_layout = new JLayoutFile('cross_categories', $basePath = JPATH_ROOT .'/components/com_yoorecipe/layouts');
		
	$display_data = array();
	$display_data['recipe'] 		= $recipe;
	$display_data['do_row_fluid'] 	= true;
	echo $cross_categories_layout->render($display_data);
?>
</div>
<?php } ?>

<?php
if ($use_tags) { 
	$recipe->tagLayout = new JLayoutFile('joomla.content.tags');
	echo $recipe->tagLayout->render($recipe->tags->itemTags);
}
?>

<?php if ($show_seasons) { ?>
<div class="row-fluid">
<?php echo JHtml::_('yoorecipeutils.generateRecipeSeason', $recipe->seasons, $do_row_fluid = true); ?>
</div>
<?php } ?>

<?php
if ($show_author) {
	$authorUrl = JRoute::_(JHtml::_('YooRecipeHelperRoute.getuserroute', $recipe->created_by) , false);
	echo '<div>'.JText::_('COM_YOORECIPE_BY').' ';
	echo '<a href="'.$authorUrl.'">';
	echo '<span itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name" class="author">'.$recipe->author_name.'</span></span>';
	echo '</a>';
	echo '</div>';
}
?>

<?php
if ($can_show_description) { 
	echo '<div id="div-recipe-description">';
	echo '<div itemprop="description">'.$recipe->description.'</div>';
	echo '</div>';
}
?>


<div class="review" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
	<div id="div-recipe-rating">
	<?php
	if ($can_show_ratings){
		echo JHtml::_('yoorecipeutils.generateRecipeRatings', $recipe, $enable_reviews, $rating_style);
	}
	?>
	</div>
</div>

<div class="row-fluid recipe-information">
<?php 
	if ($can_show_difficulty) {
		echo '<div class="span2">';
		echo '<span class="label label-warning">';
		switch($recipe->difficulty){
		case 1:
			echo JText::_('COM_YOORECIPE_YOORECIPE_SUPER_EASY_LABEL');
			break;
		case 2:
			echo JText::_('COM_YOORECIPE_YOORECIPE_EASY_LABEL');
			break;
		case 3:
			echo JText::_('COM_YOORECIPE_YOORECIPE_MEDIUM_LABEL');
			break;
		case 4:
			echo JText::_('COM_YOORECIPE_YOORECIPE_HARD_LABEL');
			break;
		}
		echo '</span>';
		echo '</div>';
	} 

	if ($can_show_cost) {
		echo '<div class="span2">';
		echo '<span class="label label-warning">';
		
		switch($recipe->cost){
		case 1:
			echo JText::_('COM_YOORECIPE_YOORECIPE_CHEAP_LABEL');
			break;
		case 2:
			echo JText::_('COM_YOORECIPE_YOORECIPE_INTERMEDIATE_LABEL');
			break;
		case 3:
			echo JText::_('COM_YOORECIPE_YOORECIPE_EXPENSIVE_LABEL');
			break;
		}
		echo '</span>';
		echo '</div>';
	}
?>
</div>

<div class="row-fluid">
	<div class="span4">
	<?php 
		echo $this->loadTemplate('otherdetails'); 
		
		if($use_similar_recipes && count($recipe->similar_recipes) > 0 && !$is_printing){
			echo $this->loadTemplate('similar_recipes');
		}
	?>
	
		<div class="yoorecipe-after-details-left">
		<?php
			$modules = JModuleHelper::getModules('yoorecipe-after-details-left');
			foreach($modules as $module) {
				echo JModuleHelper::renderModule($module);
			}
		?>
		</div>
	</div>
	<div class="span7 offset1"><?php echo $this->loadTemplate('maindetails'); ?></div>
</div>

<?php
if($use_shoppinglist && !$user->guest){
	echo $this->loadTemplate('shoppinglist');
} 
?>

<?php
	if ($use_social_sharing && $show_social_bookmarks_on_bottom) {
		echo '<center>';
		echo JHtml::_('yoorecipeutils.socialSharing', $yooRecipeparams, $recipe);
		echo '</center>';
	}
?>

<time datetime="<?php echo JFactory::getDate($recipe->creation_date)->format('Y-m-d'); ?>" itemprop="datePublished"></time>


<?php
if ($use_nutrition_facts) {
	echo $this->loadTemplate('nutrition_facts');
}
?>

<?php
if ($enable_reviews && !$is_printing) {
	echo $this->loadTemplate('reviews');
}

if (!$is_printing) {
	echo $this->loadTemplate('comments');
}
?>

<?php if ($use_google_recipe) : ?> </div> <?php endif; ?>
<br/>
<div class="pull-right">
	<?php $url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false); ?>
	<a class="btn" href="<?php echo $url.'#top'; ?>"><?php echo JText::_('COM_YOORECIPE_GO_TOP'); ?></a>
</div>
<div class="clearfix"></div>

<?php
} // End if (isset($recipe) && $canShow) {
?>
