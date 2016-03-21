<?php
/**
 * ------------------------------------------------------------------------
 * JA System Lazyload Plugin for J25 & J3x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Joomla! P3P Header Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.p3p
 */

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgSystemJALazyload extends JPlugin
{

	private $enable = true;

	function onBeforeCompileHead()
	{
		$app = JFactory::getApplication('site');
		if ($app->isAdmin() || JFactory::getDocument()->getType() != 'html'){
			$this->enable = false;
			return;
		}

		//INCLUDING ASSET
		require_once(dirname(__FILE__).'/assets/behavior.php');
		//JHtml::_('behavior.framework', true);
		JHtml::_('JABehavior.jquery');
	}
	
	/**
	 * if enable lazy load, replace images with blank image and activate lazy load script
	 *
	 * @return unknown
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication('site');
		// don't use in admin
		if (!$this->enable){
			return;
		}

		if(!function_exists('imagecreate')) {
			JError::raiseWarning(400, JText::_('JA Lazyload plugin required GD2 libary is installed and enabled.'));
			return false;
		}
		
		$minWidth = $this->params->get('lazyload_minwidth', 100);
		$sharelimit = $this->params->get('sharelimit', 5);
		$exclude = $this->params->get('exclude',"");
		if($exclude !== ""){
			 $exclude = New JRegistry($exclude);
             $comname = JRequest::getCmd('option');
             $menu= JFactory::getApplication()->getMenu();
             $active = $menu->getActive();

             if(in_array($comname,explode(",", $exclude->get("component"))) || in_array($active->id,explode(",",$exclude->get("menus")))){
             	return false;
             }
		}
		$body = JResponse::getBody();
		$regex = '#<img[^>]+>#i';
		preg_match_all($regex, $body, $matches);

		$search = array();
		$replace = array();
		$shares = array();
		$shareCount = 0;
		
		$fullPath = JURI::base();
		$basePath = JURI::base(true);
		
		foreach ($matches[0] as $image) {
			$regex = '#(src|class)\s*=\s*"([^"]*)"#i';
			$src = '';
			$cls = 'lazyload';
			if (preg_match_all ($regex, $image, $match)) {
				if (strtolower($match[1][0]) == 'src') {
					$src = $match[2][0];
					if (isset($match[0][1])) $cls = $match[2][1].' '.$cls;
				} else {
					$src = $match[2][1];
					$cls = $match[2][0].' '.$cls;
				}
			}
			$orgSrc = $src;
			
			if(!preg_match('/^\w+\:\/\//i', $src)) {
				if(!empty($basePath)) {
					$src = str_replace($basePath, '', $src);
				}
				$src = preg_replace('#^[/\\\\]+#', '', $src);
				$src = JURI::base().$src;
			}
			
			if(strpos($src, JURI::base()) !== 0) {
				continue;//that is external image => do not support now
			}
			$img_path = JPATH_ROOT.'/'.substr($src, strlen(JURI::base()));
			
			if(!JFile::exists($img_path)) {
				continue;
			}
			
			$img_size = getimagesize ($img_path);
			// ignore for small image
			if ($img_size [0] < $minWidth) {
				continue;
			}
			
			//allow max $sharelimit items
			if($shareCount < $sharelimit){
				$shares[] = $fullPath . $orgSrc;
				$shareCount++;
			}

			$img_data = $this->createBlankImage ($img_size [0], $img_size [1]);
			$search[] = $image;
			$r = array();
			$r[0] = 'class="'.$cls.'" src="'.$img_data.'" longdesc="'.$src.'"';
			if (isset($match[0][1])) {
				$r[1] = '';
			}
			$replace[] = str_replace ($match[0], $r, $image);
		}
		$body = str_replace ($search, $replace, $body);
		
		//lazyload setting
		$failure_limit = (int) $this->params->get('failure_limit', 10);
		$threshold = (int) $this->params->get('threshold', 0);
		$load_invisible = (int) $this->params->get('load_invisible', 1);
		$display_effect = $this->params->get('display_effect', 'show');
		
		$skip_invisible = $load_invisible ? 'false' : 'true';
		// put js & call script
		$scripts = '<script type="text/javascript" src="'.JURI::base(true).'/plugins/system/jalazyload/assets/lazyload/jquery.lazyload.min.js'.'"></script>'."\n";
		$scripts .= '<script type="text/javascript">
			function lazyloadinit() {
				jQuery("img.lazyload").lazyload({
					failure_limit : '.$failure_limit.',
					threshold : '.$threshold.',
					effect : "'.$display_effect.'",
					skip_invisible : '.$skip_invisible.',
					load: function(){
						jQuery(this).removeClass("lazyload"); 
					},
					appear: function(){
						jQuery(this).attr("data-original", jQuery(this).attr("longdesc") || "").removeAttr("longdesc");
					}
				});
			}; 
			jQuery(document).ready(function(){
				lazyloadinit();
			});
			</script>'."\n";
		$body = str_replace ('</body>', $scripts."\n".'</body>', $body);
		
		if($shareCount > 0){
			$sharesHtml = '';
			foreach($shares as $shareImg){
				$sharesHtml .= '<meta property="og:image" content="' . $shareImg . '" />' . "\n";
			}
			$body = str_replace ('</head>', $sharesHtml . '</head>', $body);
		}
		
		JResponse::setBody($body);
	}

	function createBlankImage ($width, $height) {
		static $images = array();
		$fileName = $width .'x'.$height.'.png';
		
		if(isset($images[$fileName])) {
			return $images[$fileName];
		} else {
			
			$folder = JPATH_CACHE . '/jalazyload/';
			$file = $folder . $fileName;
			
			if(!JFolder::exists($folder)) {
				JFolder::create($folder);
			}
			
			if(JFile::exists($file)) {
				//$img_src = JFile::read($file);
				$img_src = JURI::base().'cache/jalazyload/'.$fileName;
			} else {
				
				$img = imagecreate ($width, $height);
				// Make the background transparent
				$background = imagecolorallocate($img, 0, 0, 0);
				imagecolortransparent($img, $background);
				//border
				/*$border = imagecolorallocate($img, 170, 170, 170);
				imagerectangle($img, 0, 0, $width-1, $height-1, $border);*/
				/*$border = imagecolorallocate($img, 102, 102, 102);
				imagerectangle($img, 1, 1, $width-2, $height-2, $border);*/
				ob_start();
				imagepng($img);
				$img_data = ob_get_contents();
				ob_end_clean ();
				imagedestroy($img);
				
				if(JFolder::exists($folder)) {
					if(!JFile::write($file, $img_data)) {
						$img_src = 'data:image/png;base64,'.base64_encode($img_data);
					} else {
						$img_src = JURI::base().'cache/jalazyload/'.$fileName;
					}
				}
			}
			$images[$fileName] = $img_src;
			return $img_src;
		}
	}
}
