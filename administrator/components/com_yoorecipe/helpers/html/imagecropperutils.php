<?php
	/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

abstract class JHtmlImageCropperUtils
{
	/**
	* getCroppedPicturePath
	*/
	public static function getCroppedPicturePath($picture_path) {
	
		$path_parts = pathinfo($picture_path);
		$cropped_picture_path = $path_parts['dirname'].'/cropped-'.$path_parts['basename'];
		
		$result = true;
		if (!JFile::exists($cropped_picture_path)) {
			$result = self::cropPicture($picture_path, $cropped_picture_path);
		}
		
		return ($result) ? $cropped_picture_path : $picture_path;
	}
		
	/**
	 * cropPicture
	 */
	 private static function cropPicture($picture_path, $destination) {
	
		$extension = Jfile::getExt($picture_path);
		
		$image;
		switch (strtolower($extension)) {
			case 'jpg':
			case 'jpeg':
				$image = imagecreatefromjpeg($picture_path);
			break;
			
			case 'png':
				$image = imagecreatefrompng($picture_path);
			break;
			
			case 'gif':
				$image = imagecreatefromgif($picture_path);
			break;
		}
		
		if (!isset($image)) {
			return false;
		}
		
		$params 		= JComponentHelper::getParams('com_yoorecipe');
		$thumb_width	= $params->get('thumbnail_width', 800);
		$thumb_height	= $params->get('thumbnail_height', 600);

		$width 	= imagesx($image);
		$height = imagesy($image);

		$original_aspect 	= $width / $height;
		$thumb_aspect 		= $thumb_width / $thumb_height;

		if ( $original_aspect >= $thumb_aspect )
		{
		   // If image is wider than thumbnail (in aspect ratio sense)
		   $new_height 	= $thumb_height;
		   $new_width 	= $width / ($height / $thumb_height);
		}
		else
		{
		   // If the thumbnail is wider than the image
		   $new_width = $thumb_width;
		   $new_height = $height / ($width / $thumb_width);
		}

		$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

		// Resize and crop
		imagecopyresampled($thumb,
						   $image,
						   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
						   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
						   0, 0,
						   $new_width, $new_height,
						   $width, $height);
		
		switch (strtolower($extension)) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($thumb, $destination, 80);
			break;
			
			case 'png':
				imagepng($thumb, $destination, 9);
			break;
			
			case 'gif':
				imagegif($thumb, $destination);
			break;
		}
	}
}