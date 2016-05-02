


<?php
/**
 * ------------------------------------------------------------------------
 * JA Nuevo template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
if(version_compare(JVERSION, '3.0', 'lt')){
	JHtml::_('behavior.tooltip');
}
JHtml::_('behavior.framework');

// Create a shortcut for params.
$params  = & $this->item->params;
if (empty($params)) $params = JComponentHelper::getParams('com_content');
//die('<pre>'.print_r($params, true).'</pre>');
$images  = json_decode($this->item->images);
$canEdit = $this->item->params->get('access-edit');
$info    = $this->item->params->get('info_block_position', 0);
$hasInfo = (($params->get('show_author') && !empty($this->item->author)) or
			($params->get('show_category')) or
			($params->get('show_create_date')) or
			$params->get('show_publish_date') or
			($params->get('show_parent_category')));
$hasCtrl = ($params->get('show_print_icon') ||
			$params->get('show_email_icon') ||
			$canEdit);
$loadParamsGridContents = NuevoHelper::loadParamsGridContents($this->item);
$grid_info = explode('x',$loadParamsGridContents['size']);
$grid = '';
$grid .= $grid_info[0] > 1?' item-w'.$grid_info[0]:'';
$grid .= $grid_info[1] > 1?' item-h'.$grid_info[1]:'';

if (! empty($images->image_intro)) {
            $images->image_fulltext         = $images->image_intro;
            $images->image_fulltext_alt     = $images->image_intro_alt;
            $images->image_fulltext_caption = $images->image_intro_caption;
            $images->float_fulltext         = $images->float_intro;
        }


$db = JFactory::getDBO();   
$query = 'SELECT * FROM `#__gridtemplates_templates` WHERE category='.$this->category->id;
$db->setQuery(true);
$db->setQuery($query);
$db->execute();
unset($query);
$gridTemplate = $db->loadObjectList();

global $articleCounter2;
global $articleCounter3;


if (empty($articleCounter2)) $articleCounter2 = 0;
if ($articleCounter2 > 19) $articleCounter2 = 0;


$blockType = "";
if (! empty($gridTemplate)) {
          $spec_type = 'type'.$articleCounter2;

          if ($gridTemplate[0]->$spec_type == 2) $blockType = 'grid-item--width2';
          echo '<div class="grid-item item inf-item '.$blockType.' '.$grid.'">';
        }
else echo '<div class="grid-item item inf-item '.$grid.'">';
$articleCounter2++;

$db    = JFactory::getDbo();
$query = "SELECT * FROM `#__altarticledata_data` 
  WHERE article_id=".$this->item->id;
$db->setQuery($query);
$altData = $db->loadObjectList();

unset($customIntro);
if (! empty($altData)) {
    if (! empty($altData[0]->headline)) $this->item->title = $altData[0]->headline;
    if (! empty($altData[0]->intro)) $customIntro = $altData[0]->intro;
}

unset($altData);



?>




<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
	<?php endif; ?>

	<!-- Article -->
	<article>
	<!-- <div class="mask"></div> -->
	<div class="item-image front">
		<?php if (isset($images->image_fulltext) and !empty($images->image_fulltext)) : ?>
				<?php $imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro; ?>
				<div class="pull-<?php echo htmlspecialchars($imgfloat); ?>">
					<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
					<img
						<?php if ($images->image_fulltext_caption):
							echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
						endif; ?>
						src="<?php echo htmlspecialchars($images->image_fulltext); ?>"
						alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/>
						</a>
						<div class="title-cat-wrap">
						<a class="category" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
								<span><?php echo $this->item->category_title; ?></span>
						</a>
						<br />
						<a class="title" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
								<span><?php echo $this->escape($this->item->title); ?></span>
						</a>
						</div>
				</div>
			<?php else:  ?>
				<?php if (isset($customIntro) and !empty($customIntro)) : ?>
                  <div class="text-only-box">
                      <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
                        <div class="text-only-box-title">
                          <?php echo $this->escape($this->item->title); ?>
                        </div>
                        <div class="text-only-box-intro">
                          <?php echo $customIntro;  ?>
                        </div>
                      </a>
                  </div>
                <?php else:  ?>
				    <div>
				    	<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
		                      <img src="<?php echo JURI::root(true);?>/images/default_image.jpg" alt="Think Healthier - Image coming soon"/>
				      	</a>
						<div class="title-cat-wrap">
						<a class="category" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
								<span><?php echo $this->item->category_title ?></span>
						</a>
						<br />
						<a class="title" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
								<span><?php echo $this->escape($this->item->title); ?></span>
						</a>
						</div>
					</div>
				<?php endif; ?>
    	<?php endif; ?>
	</div>

	</article>
	<!-- //Article -->

	<?php if ($this->item->state == 0) : ?>
</div>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?>

</div>


<script>
imagesLoaded( '#grid', function() {
	//alert("AAACC2");
	//masonry('layout');
	jQuery('#grid').masonry( 'layout' );
});
</script>




