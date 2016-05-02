<?php

/**
* @version 		1.0.0
* @package 		com_content
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

$counter = 0;

// no direct access

defined('_JEXEC') or die;

?>
<div class="healthaz-wrapper">
	<div id="healthaz_label_heading" class="healthaz-label-heading">
		<div class="healthaz-text">
			When you're looking for a trusted source on all things, medical, think healthier. 
			Our team of experts share the most up-to-date information on symptyoms, causes, diagnosis, treatment options and more.
		</div>
		<div class="healthaz-adam-img">
			<img src="/images/powered-by-adam.png">
		</div>
	</div>
	<div id="healthaz_categories_all" class="healthaz-categories-all">
		<h4>Select Category</h4>

		<?php foreach ($this->content as $value) :
			$temp = substr_replace($value->path, '-az/az-alphabet/?cat=', strpos($value->path, '/') , 1);
			if($counter % 3 == 0) : ?>
				<!-- <div class="healthaz-categories-row"> -->
			<?php endif; ?>

				<div class="healthaz-category"> 
					<a href="<?php echo '/'.$temp; ?>">
						<img src="<?php echo $value->image; ?>" alt="<?php echo $value->image_alt; ?>">
						<div class="healthaz-category-title"><?php echo $value->title; ?></div>
					</a>
					<div class="healthaz-content-without-title">
						<div class="healthaz-article-most-viewed">Most Viewed</div>

					<?php foreach ($value->articles as $id => $article) : ?>
						<div class="healthaz-article-title">
							<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->id.':'.strtolower(str_replace(' ', '-', $article->title)), $article->catid, '')); ?>">
								<?php echo $article->title; ?>
							</a>
						</div>
					<?php endforeach; ?>
					</div>
				</div>

			<?php if($counter % 3 == 2) : ?>
				<!-- </div> -->
			<?php endif; 
			$counter++;
		endforeach; ?>
	</div>
</div>