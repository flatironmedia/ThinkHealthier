<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_articles_category
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
if(isset($item_heading) || $item_heading=='') $item_heading = 4;
if (version_compare(JVERSION, '3.0', 'ge')){
JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
}
?>
<div class="category-module<?php echo $moduleclass_sfx; ?> category-blog">
<?php if ($grouped) : ?>
	<?php foreach ($list as $group_name => $group) : ?>
	<div>
		<h<?php echo $item_heading; ?>><?php echo $group_name; ?></h<?php echo $item_heading; ?>>
		<div>
			<?php foreach ($group as $item) : ?>
				<li>
					<h<?php echo $item_heading+1; ?>>
					   	<?php if ($params->get('link_titles') == 1) : ?>
						<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
						<?php echo $item->title; ?>
				        <?php if ($item->displayHits) :?>
							<span class="mod-articles-category-hits">
				            (<?php echo $item->displayHits; ?>)  </span>
				        <?php endif; ?></a>
				        <?php else :?>
				        <?php echo $item->title; ?>
				        	<?php if ($item->displayHits) :?>
							<span class="mod-articles-category-hits">
				            (<?php echo $item->displayHits; ?>)  </span>
				        <?php endif; ?></a>
				            <?php endif; ?>
			        </h<?php echo $item_heading+1; ?>>
					<?php if ($params->get('show_author')) :?>
						<span class="mod-articles-category-writtenby">
						<?php echo $item->displayAuthorName; ?>
						</span>
					<?php endif;?>
	
					<?php if ($item->displayCategoryTitle) :?>
						<span class="mod-articles-category-category">
						(<?php echo $item->displayCategoryTitle; ?>)
						</span>
					<?php endif; ?>
					<?php if ($item->displayDate) : ?>
						<span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
					<?php endif; ?>
					<?php if ($params->get('show_introtext')) :?>
				<p class="mod-articles-category-introtext">
				<?php echo $item->displayIntrotext; ?>
				</p>
			<?php endif; ?>
	
			<?php if ($params->get('show_readmore')) :?>
				<p class="mod-articles-category-readmore">
					<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
					<?php if ($item->params->get('access-view')== FALSE) :
							echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE');
						elseif ($readmore = $item->alternative_readmore) :
							echo $readmore;
							echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
							if ($params->get('show_readmore_title', 0) != 0) :
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif;
						elseif ($params->get('show_readmore_title', 0) == 0) :
							echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE');
						else :
	
							echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE');
							echo JHtml::_('string.truncate', ($item->title), $params->get('readmore_limit'));
						endif; ?>
		        </a>
				</p>
				<?php endif; ?>
		</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endforeach; ?>
<?php else : ?>
	<?php 
		$numberColumn = $params->get('article_column',3);
		$count = 1;
		
		foreach ($list as $item) :  ?>
	    <div class="category-module-item col-xs-12 col-sm-<?php echo 12/$numberColumn; ?> <?php if($count == $numberColumn): echo 'latest'; endif; ?>" >

				<div class="article-img">
					<?php  
					//Get images 
					$images = "";
					if (isset($item->images)) {
						$images = json_decode($item->images);
					}
					$imgexists = (isset($images->image_intro) and !empty($images->image_intro)) || (isset($images->image_fulltext) and !empty($images->image_fulltext));
					
					if ($imgexists) {			
					$images->image_intro = $images->image_intro?$images->image_intro:$images->image_fulltext;
					?>

						<a href="<?php echo $item->link; ?>">
						<img
							<?php if ($images->image_intro_caption):
								echo 'class="caption"'.' title="' .htmlspecialchars($images->image_intro_caption) .'"';
							endif; ?>
							src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>
						</a>
					<?php }else{ ?>
						<a href="<?php echo $item->link; ?>">
							<img src="<?php echo JURI::root(true);?>/images/joomlart/demo/default.jpg" alt="Default Image"/>
						</a>
					<?php } ?>	

				</div>
	   		<h<?php echo $item_heading; ?>>
	   		<?php if ($params->get('link_titles') == 1) : ?>
					<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
				<?php echo $item->title; ?>
					
        <?php if ($item->displayHits) :?>
			<span class="mod-articles-category-hits">
            (<?php echo $item->displayHits; ?>)  </span>
        <?php endif; ?></a>
        <?php else :?>
        <?php echo $item->title; ?>
        	<?php if ($item->displayHits) :?>
			<span class="mod-articles-category-hits">
            (<?php echo $item->displayHits; ?>)  </span>
        <?php endif; ?></a>
            <?php endif; ?>
        </h<?php echo $item_heading; ?>>
        
		
		<?php if ( $params->get('show_introtext') || $params->get('show_readmore') ) : ?>
			<div class="mod-articles-desc">
				
				<?php if ($params->get('show_introtext')) :?>
					<p class="mod-articles-category-introtext">
					<?php echo $item->displayIntrotext; ?>
					</p>
				<?php endif; ?>

				<?php if ($params->get('show_readmore')) :?>
						<p class="mod-articles-category-readmore">
							<a class="mod-articles-category-title btn btn-link <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
					        <?php if ($item->params->get('access-view')== FALSE) :
									echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE');
								elseif ($readmore = $item->alternative_readmore) :
									echo $readmore;
									echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
								elseif ($params->get('show_readmore_title', 0) == 0) :
									echo JText::sprintf('TPL_MOD_ARTICLES_CATEGORY_READ_MORE_TITLE');
								else :
									echo JText::_('TPL_MOD_ARTICLES_CATEGORY_READ_MORE');
									echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
								endif; ?>
				        </a>
						</p>
					<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<?php if ( $params->get('show_author') || $item->displayCategoryTitle || $item->displayDate ): ?>
		<div class="mod-articles-meta">
				<?php if ($item->displayDate) : ?>
					<span class="mod-articles-category-date">
						<i class="fa fa-calendar"></i><?php echo $item->displayDate; ?>
					</span>
				<?php endif; ?>
				
				<?php if ($params->get('show_author')) :?>
       		<span class="mod-articles-category-writtenby">
						<i class="fa fa-user"></i><?php echo $item->displayAuthorName; ?>
					</span>
				<?php endif;?>
				<?php if ($item->displayCategoryTitle) :?>
					<span class="mod-articles-category-category">
					(<?php echo $item->displayCategoryTitle; ?>)
					</span>
				<?php endif; ?>
        
		</div>
		<?php endif; ?>
		</div>
	<?php $count++; endforeach; ?>
<?php endif; ?>
</div>
