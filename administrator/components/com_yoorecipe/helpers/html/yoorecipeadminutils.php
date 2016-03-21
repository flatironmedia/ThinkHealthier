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

abstract class JHtmlYooRecipeAdminUtils
{
	
	
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	public static function featured($value = 0, $i, $canChange = true, $controller)
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('disabled.png',	$controller.'featured',	'COM_YOORECIPE_UNFEATURED',	'COM_YOORECIPE_TOGGLE_TO_FEATURE'),
			1	=> array('featured.png',	$controller.'unfeatured',	'COM_CONTENT_FEATURED',		'COM_YOORECIPE_TOGGLE_TO_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html	= JHtml::_('image','admin/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html.'</a>';
		}

		return $html;
	}
	
	/**
	 * @param	mixed $value	Either the scalar value, or an object (for backward compatibility, deprecated)
	 * @param	int $i
	 * @param	string $img1	Image for a positive or on value
	 * @param	string $img0	Image for the empty or off value
	 * @param	string $prefix	An optional prefix for the task
	 *
	 * @return	string
	 */
	public static function offensive($value, $i, $img1 = 'tick.png', $img0 = 'publish_x.png', $prefix='comments.')
	{
		if (is_object($value)) {
			$value = $value->published;
		}

		$img	= $value ? $img1 : $img0;
		$task	= $value ? 'setToNonOffensive' : 'setToOffensive';
		$alt	= $value ? JText::_('COM_YOORECIPE_OFFENSIVE') : JText::_('COM_YOORECIPE_NONOFFENSIVE');
		$action = $value ? JText::_('COM_YOORECIPE_SET_NONOFFENSIVE') : JText::_('COM_YOORECIPE_SET_OFFENSIVE');

		$href = '<a href="#" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">'.
		JHtml::_('image','admin/'.$img, $alt, NULL, true).'</a>'
		;

		return $href;
	}
		
	/**
	 * @param	mixed $value	Either the scalar value, or an object (for backward compatibility, deprecated)
	 * @param	int $i
	 * @param	string $img1	Image for a positive or on value
	 * @param	string $img0	Image for the empty or off value
	 * @param	string $prefix	An optional prefix for the task
	 *
	 * @return	string
	 */
	public static function validated($value, $i, $img1 = 'tick.png', $img0 = 'publish_x.png', $prefix='yoorecipes.')
	{
		if (is_object($value)) {
			$value = $value->validated;
		}

		$img	= $value ? $img1 : $img0;
		$task	= $value ? 'unvalidate' : 'validate';
		$alt	= $value ? JText::_('COM_YOORECIPE_VALIDATED_OPTION') : JText::_('COM_YOORECIPE_NOTVALIDATED_OPTION');
		$action = $value ? JText::_('COM_YOORECIPE_JLIB_HTML_UNVALIDATE_ITEM') : JText::_('COM_YOORECIPE_JLIB_HTML_VALIDATE_ITEM');

		$href = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''. $prefix.$task .'\')" title="'. $action .'">'.JHtml::_('image','admin/'.$img, $alt, NULL, true).'</a>';

		return $href;
	}
	
	/**
	* hasPicture
	*/
	public static function hasPicture($value, $i, $img1 = 'tick.png', $img0 = 'publish_x.png', $prefix='yoorecipes.')
	{
		if (is_object($value)) {
			$value = $value->validated;
		}

		$img	= $value=='' ? $img0 : $img1;
		$alt	= $value=='' ? JText::_('COM_YOORECIPE_NO_PICTURE') : JText::_('COM_YOORECIPE_HAS_PICTURE');
		
		$html[] = JHtml::_('image','admin/'.$img, $alt, NULL, true);
		
		$showAsTooltip = true;
		$options = array(
			'onShow' => 'jMediaRefreshPreviewTip',
		);
		$html[] = JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
		
		if ($value && file_exists(JPATH_ROOT.'/'.$value))
		{
			$src = JURI::root().$value;
		}
		else
		{
			$src = '';
		}

		$attr = array(
			'id' => $i.'_preview',
			'class' => 'media-preview',
			'style' => 'max-width:160px; max-height:100px;'
		);
		$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $attr);
		$previewImg = '<div id="'.$i.'_preview_img"'.($src ? '' : ' style="display:none"').'>'.$img.'</div>';
		$previewImgEmpty = '<div id="'.$i.'_preview_empty"'.($src ? ' style="display:none"' : '').'>'
			. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY').'</div>';

		$html[] = '<div class="media-preview fltlft">';
		$tooltip = $previewImgEmpty.$previewImg;
		$options = array(
			'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
			'text' => JText::_('JLIB_FORM_MEDIA_PREVIEW_TIP_TITLE'),
			'class' => 'hasTipPreview'
		);
		$html[] = JHtml::tooltip($tooltip, $options);
			
		$html[] = '</div>';
			
		return implode("\n", $html);
	}
}