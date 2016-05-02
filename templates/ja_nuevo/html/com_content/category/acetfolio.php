

<?php



defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(T3_PATH.'/html/com_content');
JHtml::addIncludePath(dirname(dirname(__FILE__)));
//JHtml::_('behavior.caption');
//register the helper class
JLoader::register('NuevoHelper', T3_TEMPLATE_PATH . '/templateHelper.php');
//template params
$tplparams = JFactory::getApplication()->getTemplate(true)->params;
//Load grid items
NuevoHelper::loadGridItems();
static $articleCounter2 = 0;
static $articleCounter3;
static $articleCounter4 = 0;
static $articleCounter = 0;
static $tempCounter=0;
static $gridTemplate;

if ( JURI::current() == JURI::base()) echo '
                <script type="text/javascript">
                      var isHome = true;
                </script>'; 

else echo '
                <script type="text/javascript">
                      var isHome = false;
                </script>'; 


// Getting grid templates created in custom component (defining small or big boxes)
$db = JFactory::getDBO();   
$query = 'SELECT * FROM `#__gridtemplates_templates` WHERE category='.$this->category->id;
$db->setQuery(true);
$db->setQuery($query);
$db->execute();
unset($query);
$gridTemplate = $db->loadObjectList();

// Getting parameters which define after how many articles ads appear
$query = 'SELECT `params` FROM `#__extensions` WHERE name="com_ads_frequency"';
$db->setQuery(true);
$db->setQuery($query);
$db->execute();
unset($query);
$ads = $db->loadObjectList();
$ads = json_decode($ads[0]->params);
$ad_param_x = $ads->ad_param_x;
$ad_param_y = $ads->ad_param_y;

$_SESSION['is_mobile'] = false;
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    $_SESSION['is_mobile'] = true;


?>

<div class="ja-masonry-wrap">
 <?php if ($this->params->get('show_page_heading', 1)) : ?>
  <div class="page-header clearfix">
    <h1 class="page-title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
  </div>
  <?php endif; ?>

  <?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
    <div class="page-subheader clearfix">
      <h2 class="page-subtitle"><?php echo $this->escape($this->params->get('page_subheading')); ?>
      <?php if ($this->params->get('show_category_title')) : ?>
      <small class="subheading-category"><?php echo $this->category->title;?></small>
      <?php endif; ?>
      </h2>
  </div>
  <?php endif; ?>

  <?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
  <div class="category-desc clearfix">
    <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
      <img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
    <?php endif; ?>
    <?php if ($this->params->get('show_description') && $this->category->description) : ?>
      <?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
    <?php endif; ?>
  </div>
  <?php endif; ?>
<!-- Load Modules with position "portfolio-menu" -->
  <?php if(NuevoHelper::loadmodules('portfolio-menu','T3xhtml')): ?>
      <div class="inset">
          <?php echo NuevoHelper::loadmodules('portfolio-menu','T3xhtml'); ?>
      </div>
  <?php endif;?>
<!-- End load -->
<div class="masonry-grid <?php echo $this->pageclass_sfx;?>" id="grid" >



  <?php if ($this->params->get('show_tags', 1) && !empty($this->category->tags->itemTags)) :
        $this->category->tagLayout = new JLayoutFile('joomla.content.tags');
        echo $this->category->tagLayout->render($this->category->tags->itemTags);
    endif; ?>

  <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) :
        if ($this->params->get('show_no_articles', 1)) : ?>
      <p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
    <?php endif; ?>
  <?php endif; ?>


  <?php
    $db = JFactory::getDBO();   

    // Getting list of featured articles (first 20 articles in the grid) selected in custom component for each category 
    $query = 'SELECT * FROM `#__subchannel_featured` WHERE category_id='.$this->category->id;
    $db->setQuery(true);
    $db->setQuery($query);
    $db->execute();
    unset($query);

    $selectedArticles = $db->loadObjectList();

    $idList = "";
    for ($i=0; $i < 20; $i++) { 
      $spec_id = 'id'.$i;
      if (! empty($selectedArticles[0]->$spec_id)) $idList .= $selectedArticles[0]->$spec_id.",";
    }
    $idList = rtrim($idList, ',');

    // if list is not empty retrive editorialy selected articles
    if ($idList!="") $query = 'SELECT `#__categories`.`id` as category_id, `#__categories`.`title` as category_title, `#__categories`.`path`, `#__content`.* FROM `#__content` 
      LEFT JOIN `#__categories` ON `#__categories`.`id`=`#__content`.`catid`
      WHERE `path` like "'.$this->category->path.'%" AND `#__content`.`id` IN ('.$idList.') AND `state`=1 ORDER BY RAND() LIMIT 20';
    // else retrieve 20 random featured articles 
    else $query = 'SELECT `#__categories`.`id` as category_id, `#__categories`.`title` as category_title, `#__categories`.`path`, `#__content`.* FROM `#__content` 
      LEFT JOIN `#__categories` ON `#__categories`.`id`=`#__content`.`catid`
      WHERE `path` like "'.$this->category->path.'%" AND `state`=1 AND `featured`=1 ORDER BY RAND() LIMIT 20';
    $db->setQuery(true);
    $db->setQuery($query);
    $db->execute();
    unset($query);

    $featuredArticles = $db->loadObjectList();

    // display featured articles
    foreach ($featuredArticles as &$item) {
        $this->item = &$item;

        $db    = JFactory::getDbo();
        // Get alternative headline and intro text
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



        // Create a shortcut for params.
        $params  = & $this->item->params;
        if (empty($params)) $params = JComponentHelper::getParams('com_content');

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

        $blockType = "";

        if (! empty($images->image_intro)) {
            $images->image_fulltext         = $images->image_intro;
            $images->image_fulltext_alt     = $images->image_intro_alt;
            $images->image_fulltext_caption = $images->image_intro_caption;
            $images->float_fulltext         = $images->float_intro;
        }

        $SLIR = "slir/w300-h200/";

        // setting size of the each box based on the defined template if any
        if (! empty($gridTemplate)) {
          $spec_type = 'type'.$articleCounter;

          if ($gridTemplate[0]->$spec_type == 2) {
            $blockType = 'grid-item--width2';
            $SLIR = "slir/w621-h414/";
          }
          echo '<div class="grid-item item '.$blockType.' '.$grid.'">';
          $articleCounter++;
        }
        else echo '<div class="grid-item item '.$grid.'">';
        if ($articleCounter > 19) $articleCounter = 0;


        if ($_SESSION['is_mobile']) $SLIR = "";

        if($_SERVER['REMOTE_ADDR']=='46.239.14.157')
            {
                //die('<pre>'.print_r($_SERVER,true).'</pre>');

            }
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
                  <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
                  <img
                    <?php if ($images->image_fulltext_caption):
                      echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
                    endif; ?>
                    src="<?php echo htmlspecialchars($SLIR.$images->image_fulltext); ?>"
                    alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/>
                    </a>
                    <div class="title-cat-wrap">
                    <a class="category" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
                        <span><?php echo $this->item->category_title ?></span> 
                    </a>
                    <br />
                    <a class="title" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
                        <span><?php 
                          echo $this->escape($this->item->title); 

                        ?></span>
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
                    <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid)); ?>">
                      
                        <img src="<?php echo JURI::root(true);?><?php echo $SLIR;?>/images/default_image.jpg" alt="Think Healthier - Image coming soon"/>
                        
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

        <?php //echo $this->item->event->afterDisplayContent; ?>

        </div>


        <?php
        $_SESSION['item_counter']++;


          // dispaly ads in the grid after X articles
          if ( JURI::current() != JURI::base() ) 
            if ($_SESSION['item_counter']%$ad_param_x == 0 ) 
              echo '
                <div class="grid-item item inf-item">
                  <div id="adslot'.$_SESSION['item_counter'].'" style="width:300px;height:250px;margin:0;padding:0"></div>
                </div>
                <script type="text/javascript">
                      var OX_ads = OX_ads || [];
                      OX_ads.push({
                         "slot_id":"adslot'.$_SESSION['item_counter'].'",
                         "auid":"538320228",
                         "vars":{"pos":"infinite","cat":"your-health"}
                      });
                </script>';

      }
  ?>



  <?php $leadingcount = 0; ?>
  <?php if (!empty($this->lead_items)) : ?>
    <?php shuffle($this->lead_items); ?>
    <?php foreach ($this->lead_items as &$item) : ?>
      <?php
                $this->item = &$item;
        echo $this->loadTemplate('item');
      ?>
    <?php $leadingcount++; ?>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php
    $introcount = (count($this->intro_items));
    $counter = 0;
  ?>

  <?php if (!empty($this->intro_items)) : ?>
  <?php shuffle($this->intro_items); ?>
  <?php foreach ($this->intro_items as $key => &$item) : ?>
    <?php $rowcount = ((int) $counter % (int) $this->columns) + 1; ?>
          <?php
          $this->item = &$item;
          echo $this->loadTemplate('item');
          $_SESSION['item_counter']++;
          // dispaly ads in the grid after X articles
          if ( JURI::current() != JURI::base() ) 
            if ($_SESSION['item_counter']%$ad_param_x == 0 ) 

              echo '
                <div class="grid-item item inf-item">
                  <div id="adslot'.$_SESSION['item_counter'].'" style="width:300px;height:250px;margin:0;padding:0"></div>
                </div>
                <script type="text/javascript">
                  var OX_ads = OX_ads || [];
                  OX_ads.push({
                     "slot_id":"adslot'.$_SESSION['item_counter'].'",
                     "auid":"538320228",
                     "vars":{"pos":"infinite","cat":"your-health"}
                  });
                </script>';
 
        ?>
        <?php $counter++; ?>

  <?php endforeach; ?>
  <?php endif; ?>


    <?php if ($this->params->get('show_pagination') == 3 && $this->pagination->get('pages.total') > 1) : ?>
        <nav id="page-nav" class="pagination">
            <?php
            $urlparams = '';
            if (!empty($this->pagination->_additionalUrlParams)){
                foreach ($this->pagination->_additionalUrlParams as $key => $value) {
                    $urlparams .= '&' . $key . '=' . $value;
                }
            }

            $next = $this->pagination->limitstart + $this->pagination->limit;
            $nextlink = JRoute::_($urlparams . '&' . $this->pagination->prefix . 'limitstart=' . $next);
            ?>
            <a id="page-next-link" href="<?php echo $nextlink ?>" data-limit="<?php echo $this->pagination->limit; ?>" data-start="<?php echo $this->pagination->limitstart ?>" data-page-total="<?php echo ceil($this->pagination->total / $this->pagination->limit);?>" data-total="<?php echo $this->pagination->total;?>"></a>
        </nav>
    <?php endif; ?>
</div>
<?php
//Override pagination
if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1) || $this->params->def('show_pagination', 2) == 3) : ?>
    <?php if ($this->params->def('show_pagination', 2) == 3) : ?>
  <?php if($this->pagination->get('pages.total') > 1) :?>
        <div id="infinity-next" class="btn btn-primary hidden"><?php echo JText::_('TPL_INFINITY_NEXT')?></div>
    <?php else:?>
    <div id="infinity-next" class="btn btn-primary disabled"><?php echo JText::_('TPL_JSLANG_FINISHEDMSG');?></div>
<?php endif;?>
  <?php else : ?>
        <div class="pagination">
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="counter pull-right">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php  endif; ?>
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
</div>


<script>
// defining masonry grid options
jQuery(document).ready(function() {
  jQuery('#grid').masonry({
   columnWidth: 321,
   isFitWidth: true,
   itemSelector: '.item'
  }).imagesLoaded(function() {
   jQuery('#grid').masonry('layout');
  });
});

</script>


<script>
// setting infinite scroll
jQuery(document).ready(function(){

 var $container = jQuery('#grid');
 $container.masonry( 'layout' );

 $container.infinitescroll({
      //prefill: true,
      navSelector  : '#page-nav',    // selector for the paged navigation
      nextSelector : '#page-nav a',  // selector for the NEXT link (to page 2)
      itemSelector : '.inf-item',     // selector for all items you'll retrieve
      loading: {
          finishedMsg: 'No more pages to load.',
          img: 'images/infinite-load.GIF'
        }
      },
      // trigger Masonry as a callback
      function( newElements ) {

        if (! isHome) 
          for (var i = 0; i < 20; i++) {
            insertAd(<?php echo $ad_param_x.','.$ad_param_y; ?>);
          };
        // hide new items while they are loading
        var $newElems = jQuery( newElements ).css({ opacity: 0 });
        // ensure that images load before adding to masonry layout
        $newElems.imagesLoaded(function(){
          $container.masonry( 'layout' );
          // show elems now they're ready
          $newElems.animate({ opacity: 1 });
          $container.masonry( 'appended', $newElems, true );

        });
      }
    );
});
</script>


<script>
// load new content every 2 seconds untill the window is filled
setInterval(function(){
  var $container = jQuery('#grid');
  if(jQuery(window).height() + 100 >= jQuery(document).height()){
    $container.infinitescroll('retrieve');
  };
},2000);
</script>

