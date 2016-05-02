<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_related_items
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


function get_words($sentence, $count = 10) {
		preg_match("/([\w-.,?!;:\/]+\s+){15}/i", $sentence, $matches);
		return $matches[0];
}

?>
<ul class="relateditems<?php echo $moduleclass_sfx; ?>" type="none" >
<?php foreach ($list as $item) :	?>
<li>
	<a href="<?php echo $item->route; ?>">
		<div class="related-left-block">
			<img
				src="/slir/w125-h75/<?php echo htmlspecialchars($item->images->image_fulltext); ?>"
				alt="<?php echo htmlspecialchars($item->images->image_fulltext_alt); ?>"
			/>
		</div>
		<div class="related-right-block">
			<div class="related-article-title">
				<?php if ($showDate) echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')) . " - "; ?>
				<?php echo $item->title; ?>
			</div>
			<!-- <div class="related-article-text">
				<?php echo get_words(strip_tags($item->introtext)); ?>
			</div> -->
		</div>

	</a>
</li>
<?php endforeach; ?>
</ul>
