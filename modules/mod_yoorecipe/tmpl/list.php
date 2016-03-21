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
<ul class="unstyled">
<?php
$open_status = count($items) > 1 ? '' : 'in';
foreach ($items as $i => $item) {
	
	if ($show_recipes_picture) {
		// Take care of picture
		$picture_path = JHtml::_('imageutils.getPicturePath', $item->picture);
	}
?>
	<li>
		<h4>
			<a href="<?php echo JRoute::_(JHtml::_('yoorecipehelperroute.getreciperoute', $item->slug, $catid = 0, $menu_item_id)); ?>"><?php echo htmlspecialchars($item->title); ?></a>
		</h4>
		<?php  if ($show_recipes_picture) { ?>
			<a href="<?php echo JRoute::_(JHtml::_('yoorecipehelperroute.getreciperoute', $item->slug, $catid = 0, $menu_item_id)); ?>">
				<img class="thumbnail" src="<?php echo $picture_path; ?>" 
				title="<?php echo htmlspecialchars($item->title); ?>" 
				alt="<?php echo htmlspecialchars($item->title);  ?>"
				/>
			</a>
		<?php } ?>	
	</li>
<?php
} // End foreach ($items as $i => $item) {
?>
</ul>
</div>
