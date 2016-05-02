<?php
/*----------------------------------------------------------------------
# YooRock! YooRecipe Random Module 1.0.0
# ----------------------------------------------------------------------
# Copyright (C) 2011 YooRock.All Rights Reserved.
# Coded by: YooRock!
# License: GNU GPL v2
# Website: http://www.yoorecipe.com
------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); // no direct access

$lang		= JFactory::getLanguage();
$document 	= JFactory::getDocument();
$document->addStyleSheet('media/mod_yoorecipe/styles/mod_yoorecipe.css');

JHtml::_('bootstrap.framework');

$show_recipes_picture	= $params->get('show_recipes_picture', 0);
$moduleclass_sfx	= $params->get('moduleclass_sfx', '');
$menu_item_id		= $params->get('menu_item_id', '');
?>
<div class="<?php echo $moduleclass_sfx; ?>">
<?php
if (strlen($params->get('intro_text')) > 0) {
	echo '<div class="intro_text">',$params->get('intro_text'),'</div>';
}
?>

<?php
$open_status = count($items) > 1 ? '' : 'in';
foreach ($items as $i => $item) {

	if ($show_recipes_picture) {
		// Take care of picture
		$picture_path = JHtml::_('imageutils.getPicturePath', $item->picture);
	}

	// Format title tag
	$chunkedItemTitle;
	if (strlen($item->title) > $params->get('recipe_title_max_length', 20)) {
		$chunkedItemTitle = substr(htmlspecialchars($item->title), 0, $params->get('recipe_title_max_length', 20)).'...';
	}
	else {
		$chunkedItemTitle = htmlspecialchars($item->title);
	}
?>

	<div>
		<?php  if ($show_recipes_picture) { ?>
			<div class="feat-recipe-item">
			<a href="<?php echo JRoute::_(JHtml::_('yoorecipehelperroute.getreciperoute', $item->slug, $catid = 0, $menu_item_id)); ?>">
				<img class="thumbnail" src="<?php echo '/slir/w303-h202/'.$picture_path; ?>"
				title="<?php echo htmlspecialchars($item->title); ?>"
				alt="<?php echo htmlspecialchars($item->title);  ?>"
				/>
			</a>
			<div class="title-cat-wrap">
				<span class="category"><?php echo $item->cat_title; ?></span>
				<br/>
				<div class="title">
					<a class="title" href="<?php echo JRoute::_(JHtml::_('yoorecipehelperroute.getreciperoute', $item->slug, $catid = 0, $menu_item_id)); ?>"><?php echo htmlspecialchars($item->title); ?></a>
				</div>
			</div>
		</div>
		<?php  } ?>

<?php
	if ($params->get('show_rating', 1)) {
		if ($item->note != null)  {

			if ($params->get('rating_style', 'stars') == 'grade') {
				echo '<strong>'.JText::_('MOD_YOORECIPE_RECIPE_NOTE').': </strong><span> '.$item->note.'/5</span>';
			}
			else if ($params->get('rating_style', 'stars') == 'stars') {
				$width = ($item->note*113)/5;
				echo '<div class="rec-detail-wrapper span12">';
				echo '<div class="rating-stars stars113x20 fl-left">';
				echo '<div style="width:'.$width.'px;" class="rating-stars-grad"></div>';
				echo '<div class="rating-stars-img-mod"></div>';
				echo '</div>';
				echo '</div>';
			}
		}else {
			echo '<br/>';
		}
	}

	if ($params->get('show_difficulty', 1)) {

		echo '<span class="label label-warning">';
			switch($item->difficulty){
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
		echo '</span>&nbsp;';
	}

	if ($params->get('show_cost', 1)) {

		echo '<span class="label label-warning">';
		switch($item->cost){
		case 1:
			echo JText::_('COM_YOORECIPE_YOORECIPE_CHEAP_LABEL');
			break;
		case 2:
			echo  JText::_('COM_YOORECIPE_YOORECIPE_INTERMEDIATE_LABEL');
			break;
		case 3:
			echo  JText::_('COM_YOORECIPE_YOORECIPE_EXPENSIVE_LABEL');
			break;
		}
		echo '</span>';
	}

	if ($params->get('show_ingredients', 1) && count($item->groups) > 0) {
		echo '<br/><span class="ingredientsTitle">'.JText::_('MOD_YOORECIPE_RECIPES_INGREDIENTS').': </span><br/>';
		echo '<span class="ingredientsList">';
		$ingredients_list = array();
		foreach ($item->groups as $group) {
			foreach ($group->ingredients as $ingredient) {
				$ingredients_list[] = $ingredient->description;
			}
		}
		echo implode(", ", $ingredients_list);
		echo '</span>';
	}

	if ($params->get('show_description', 1)) {
		if ($item->description != '') :
			echo '<br/><span class="ingredientsTitle">'.JText::_('MOD_YOORECIPE_RECIPES_DESCRIPTION').': </span><br/>';
			echo '<div>'.$item->description.'</div>';
		endif;
	}

	if ($params->get('show_preparation_time', 1)) {
		echo '<br/><span class="preparation_time">'.JText::_('MOD_YOORECIPE_RECIPES_PREPARATION').': '.JHtml::_('datetimeutils.formattime', $item->preparation_time).'</span>';
	}
	if ($params->get('show_cook_time', 1)) {
		echo '<br/><span class="cook_time">'.JText::_('MOD_YOORECIPE_RECIPES_COOK_TIME').': '.JHtml::_('datetimeutils.formattime', $item->cook_time).'</span>';
	}
	if ($params->get('show_wait_time', 1)) {
		echo '<br/><span class="wait_time">'.JText::_('MOD_YOORECIPE_RECIPES_WAIT_TIME').': '.JHtml::_('datetimeutils.formattime', $item->wait_time).'</span>';
	}

	if ($params->get('show_readmore', 1)) {

		echo '<p class="mod_yoorecipe_readmore">';
		echo '<a href="',JRoute::_(JHtml::_('yoorecipehelperroute.getreciperoute', $item->slug, $catid = 0, $menu_item_id)),'">';
		echo JText::sprintf('MOD_YOORECIPE_READMORE_RECIPE', $item->title);
		echo '</a>';
		echo '</p>';
	}
?>
	</div>
<?php
} // End foreach ($items as $i => $item) {
?>
</div>
