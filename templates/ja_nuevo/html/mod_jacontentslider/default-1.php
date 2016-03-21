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

// no direct access
defined('_JEXEC') or die('Restricted access');
if ($total):
    $mainframe = JFactory::getApplication();
    $tmplPath = 'templates/' . $mainframe->getTemplate() . '/';
    $tmplimages = $tmplPath . 'images/';
    $modPath = 'modules/mod_jacontentslider/assets/images/';
    //Images
    $image_path = $modPath;
    if (file_exists(JPATH_SITE . '/' . $tmplimages . 're-left.gif')) {
        $image_path = $tmplimages;
    }
    $image_path = str_replace('\\', '/', $image_path);

    $cateArr = array();
    foreach ($contents as $contn) {
        if (isset($contn->cateName) && !isset($cateArr[$contn->catid])) {
            $cateArr[$contn->catid] = $contn->cateName;
        }
    }
    if (!$showTab || count($cateArr) <= 1) {
        //if not display tabs
        //we must show all items of All Categories on one tab
        $firstCid = 0;
    } else {
        $firstCid = array_keys($cateArr);
        $firstCid = $firstCid[0];
    }
    ?>
<script type="text/javascript">
	//<!--[CDATA[
	function contentSliderInit_<?php echo $module->id;?> (cid) {
		cid = parseInt(cid);
		var containerID = 'ja-contentslider-<?php echo $module->id;?>';
		var container =  $(containerID),
			//containersize = document.id(containerID).getComputedSize().width,
			containersize = document.id('Mod<?php echo $module->id;?>').getComputedSize().width,
			windowsize = $(window).getSize();

		container.getElements('.jsslide').each(function(el){
			el.dispose();
		});

		if(cid == 0) {
			var elems = $('ja-contentslider-center-<?php echo $module->id;?>').getElements('div[class*=content_element]');
		}else{
			var elems = $('ja-contentslider-center-<?php echo $module->id;?>').getElements('div[class*=jaslide2_'+cid+']');
		}
		var total = elems.length;
	
		//Defined padding
		
		if (windowsize.x > 1200) {
			jpadding  = 0;
		} else if (windowsize.x >= 992) {
			jpadding  = 0;
		} else if (windowsize.x >= 768) {
			jpadding  = 0;
		} else if (windowsize.x >= 480) {
			jpadding  = 0;
		}else{
			jpadding  = 0;
		}
		
		if(total >0){
			if (windowsize.x > <?php echo $xwidth; ?>  * <?php echo  $numElem; ?> + jpadding) {
				var cw = <?php echo $xwidth; ?>;	
				var nel = <?php echo  $numElem; ?>;
			} else if(windowsize.x <= 480){
				var cw = containersize+jpadding;	
				var nel = 1;
			}else if(windowsize.x <= 980){
				var cw = (containersize+jpadding)/2;
				var nel = 2;
			}else if(windowsize.x <= 1200){
				var cw = (containersize+jpadding)/3;
				var nel = 3;
			}
			else{
				var cw = (containersize+jpadding)/<?php echo  $numElem; ?>;
				var nel = <?php echo  $numElem; ?>;
			}
		}
		
		var ch = (cw / <?php echo $xwidth; ?>) * <?php echo $xheight; ?>;
		
		var options={
			w: cw,
			h: ch,
			num_elem: nel,
			mode: '<?php  echo  $mode; ?>', //horizontal or vertical
			direction: '<?php echo $direction; ?>', //horizontal: left or right; vertical: up or down
			total: total,
			url: '<?php echo JURI::base(); ?>modules/mod_jacontentslider/mod_jacontentslider.php',
			wrapper:  container.getElement("div.ja-contentslider-center"),
			duration: <?php echo $animationtime; ?>,
			interval: <?php echo $delaytime; ?>,
			modid: <?php echo $module->id;?>,
			running: false,
			auto: <?php echo $auto;?>
		};

		var jscontentslider = new JS_ContentSlider( options );

		for(i=0;i<elems.length;i++){
			jscontentslider.update (elems[i].innerHTML, i);
		}
		jscontentslider.setPos(null);
		if(jscontentslider.options.auto){
			jscontentslider.nextRun();
		}

		<?php if( $params->get( 'showbutton' ) || ($params->get( 'showbutton' ) == '') ): ?>
		  <?php if($params->get( 'scroll_when', 'click' ) == 'click'): ?>
			<?php if ($mode == 'vertical'): ?>
			container.getElement(".ja-contentslide-up-img").onclick = function(){setDirection2<?php echo $module->id;?>('down', jscontentslider);};
			container.getElement(".ja-contentslide-down-img").onclick = function(){setDirection2<?php echo $module->id;?>('up', jscontentslider);};
			<?php else: ?>
			container.getElement(".ja-contentslide-left-img").onclick = function(){setDirection2<?php echo $module->id;?>('right', jscontentslider);};
			container.getElement(".ja-contentslide-right-img").onclick = function(){setDirection2<?php echo $module->id;?>('left', jscontentslider);};
			<?php endif; //vertical? ?>
		  <?php else: ?>
			<?php if ($mode == 'vertical'): ?>
			container.getElement(".ja-contentslide-up-img").onmouseover = function(){setDirection<?php echo $module->id;?>('down',0, jscontentslider);};
			container.getElement(".ja-contentslide-up-img").onmouseout = function(){setDirection<?php echo $module->id;?>('down',1, jscontentslider);};
			container.getElement(".ja-contentslide-down-img").onmouseover = function(){setDirection<?php echo $module->id;?>('up',0, jscontentslider);};
			container.getElement(".ja-contentslide-down-img").onmouseout = function(){setDirection<?php echo $module->id;?>('up',1, jscontentslider);};
			<?php else: ?>
			container.getElement(".ja-contentslide-left-img").onmouseover = function(){setDirection<?php echo $module->id;?>('right',0, jscontentslider);};
			container.getElement(".ja-contentslide-left-img").onmouseout = function(){setDirection<?php echo $module->id;?>('right',1, jscontentslider);};
			container.getElement(".ja-contentslide-right-img").onmouseover = function(){setDirection<?php echo $module->id;?>('left',0, jscontentslider);};
			container.getElement(".ja-contentslide-right-img").onmouseout = function(){setDirection<?php echo $module->id;?>('left',1, jscontentslider);};
			<?php endif; //vertical? ?>
		  <?php endif; //scroll event ?>
		<?php endif; //show control? ?>

		/**active tab**/
		if (container.getElement('.ja-button-control')) {
		container.getElement('.ja-button-control').getElements('a').each(function(el){
			var css = (el.getProperty('rel') == cid) ? 'active' : '';
			el.className = css;
		});
		}
	}
	window.addEvent( 'domready', function(){ contentSliderInit_<?php echo $module->id;?>(<?php echo $firstCid; ?>); } );
	window.addEvent('resize', function(){
		contentSliderInit_<?php echo $module->id;?>(<?php echo $firstCid; ?>); 
	});
	function setDirection<?php echo $module->id;?>(direction,ret, jscontentslider) {
		jscontentslider.options.direction = direction;
		
		if(ret){
			jscontentslider.options.auto = <?php echo $auto; ?>;
			jscontentslider.options.interval = <?php echo $delaytime; ?>;
			jscontentslider.options.direction = '<?php echo $direction; ?>';
		}
		else{
			jscontentslider.options.auto = 1;
			jscontentslider.options.interval = 100;
			jscontentslider.nextRun();
			jscontentslider.options.interval = <?php echo $delaytime; ?>;
		}
	}
	
	function setDirection2<?php echo $module->id;?>(direction, jscontentslider) {
		var oldDirection = jscontentslider.options.direction;
		
		jscontentslider.options.direction = direction;
		
		jscontentslider.options.interval = 100;
		jscontentslider.options.auto = 1;
		jscontentslider.nextRun();
		jscontentslider.options.auto = <?php echo $auto; ?>;
		jscontentslider.options.interval = <?php echo $delaytime; ?>;
		
		setTimeout(function(){
			jscontentslider.options.direction = oldDirection;
		}, 510);
	}
	//]]-->
</script>

<div id="ja-contentslider-<?php echo $module->id;?>" class="ja-contentslider-1 ja-contentslider<?php echo $params->get( 'moduleclass_sfx' );?> clearfix" >
  <!--toolbar-->
  <?php if( $params->get( 'showbutton' ) || ($params->get( 'showbutton' ) == '') ) : ?>
     
		<?php if ($mode == 'vertical'){ ?>
			<div class="ja-contentslider-right ja-contentslide-up-img" title="<?php echo JText::_('Next'); ?>">&nbsp;</div>
			<div class="ja-contentslider-left ja-contentslide-down-img" title="<?php echo JText::_('Previous'); ?>">&nbsp;</div>
		<?php } else {?>
			<div class="ja-contentslider-right ja-contentslide-right-img" title="<?php echo JText::_('Next'); ?>">&nbsp;</div>
			<div class="ja-contentslider-left ja-contentslide-left-img" title="<?php echo JText::_('Previous'); ?>">&nbsp;</div>
		<?php } ?>

  <?php endif; //show showbutton? ?>

  <!--items-->
  <div class="ja-contentslider-center-wrap clearfix">
    <div id="ja-contentslider-center-<?php echo $module->id;?>" class="ja-contentslider-center">
      <?php
	foreach( $contents  as $contn ) :
		$link = $contn->link;
		$image = $contn->image;
		$show_data = false;
		if (!empty($image)){
			$show_data = true;
		}
		if ($params->get( 'showintrotext' )){
			$show_data = true;
		}
		if ($params->get( 'showtitle' )){
			$show_data = true;
		}
		if ($show_data == true) :
	?>
      <div class="content_element jaslide2_<?php echo $contn->catid; ?>" style="display:none;">
        
        <?php if(  $params->get( 'showimages' ) && (strlen($image)>3) ) { ?>
        <div class="ja_slideimages tooltips clearfix">
          <div class="ja_slideimages_inner">
            <div class="content">
              <?php echo $image; ?>
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="ja_slideContent" onclick="location.href='<?php echo $link; ?>'" ><div class="box-align">
  
	        <?php if( $params->get( 'showtitle' ) ) { ?>
	        <div class="ja_slidetitle">
	          <?php  echo ($params->get( 'link_titles' ) ) ? '<a href="'.$link.'" title="">'.$contn->title.'</a>' : $contn->title;?>
	        </div>
	        <?php } ?>
	        
	        <?php if(  $params->get( 'showintrotext' ) ) { ?>
	        <div class="ja_slideintro"> <?php echo ( $params->get('numchar') ) ? $contn->introtext1 : $contn->introtext; ?> </div>
	        <?php } ?>
	        <?php if( $params->get('showreadmore') ){ ?>
	        <div class="ja-slidereadmore"> <a href="<?php echo $link;?>" class="readon"><?php echo JTEXT::_('READMORE');?></a> </div>
	        <?php } // endif;?>
	        <?php if (isset($contn->demoUrl) && $contn->demoUrl != '') { ?>
	        <div class="ja-slideDemo"> <a href="<?php echo $contn->demoUrl;?>" class="readon"><?php echo $contn->demoUrl;?></a> </div>
	        <?php } ?>
		      </div>
      </div></div>
      <?php
		endif;
	  endforeach; ?>
    </div>
  </div>
</div>
<?php endif; //not total ?>