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

require_once JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/imagecropperutils.php';

abstract class JHtmlImageUtils
{	
	/**
	* uploadRecipePicture
	* fieldName = the field name
	* @returns: false if error, filepath otherwise
	*/
	public static function uploadRecipePicture($fieldName) {
	
		// Handle uploaded file
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		 
		// Get User & Component parameters
		$input 		= JFactory::getApplication()->input;
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$created_by	= $input->get('created_by', 0, 'INT');
		$user		= ($created_by != 0) ? JFactory::getUser($created_by) : JFactory::getUser();
		
		if (!isset($_FILES[$fieldName])) {
			return '';
		}
		
		//any errors the server registered on uploading
		$fileError = $_FILES[$fieldName]['error'];
		if ($fileError > 0) {
			switch ($fileError)  {
				case 1:
				JError::raiseWarning( 100, JText::_('COM_YOORECIPE_ERROR_WARNFILETOOLARGE'));
				return '';
		 
				case 2:
				JError::raiseWarning( 100, JText::_('COM_YOORECIPE_ERROR_WARNFILETOOLARGE'));
				return '';
		 
				case 3:
				JError::raiseWarning( 100, JText::_('COM_YOORECIPE_ERROR_PARTIAL_UPLOAD'));
				return '';
		 
				case 4: 
				// JError::raiseWarning( 100, JText::_('COM_YOORECIPE_ERROR_FILE_NOT_FOUND'));
				return '';
			}
		}
		 
		//check for filesize
		$fileSize = $_FILES[$fieldName]['size'];
		$max_upload_size_bytes = $params->get('max_upload_size', 2000) * 1024;
		if($fileSize > $max_upload_size_bytes)	{
			JError::raiseWarning( 100, JText::_('COM_YOORECIPE_ERROR_WARNFILETOOLARGE'));
			return '';
		}
		 
		//check the file extension is ok
		$fileName = $_FILES[$fieldName]['name'];
		JFile::makeSafe($fileName);
		$uploadedFileNameParts = explode('.',$fileName);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);
		$validFileExts = explode(',', $params->get('authorized_extensions', 'jpg,png' ));
		 
		//assume the extension is false until we know its ok
		$extOk = false;
		 
		// Check file extension is ok
		foreach($validFileExts as $key => $value) {
			if( preg_match("/$value/i", $uploadedFileExtension ) ) {
				$extOk = true;
			}
		}
		 
		if ($extOk == false) {
			JError::raiseWarning(100, JText::sprintf( 'COM_YOORECIPE_BAD_FILE_EXTENSION', $params->get('authorized_extensions', 'jpg,png')));
			return '';
		}
		 
		// the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $_FILES[$fieldName]['tmp_name'];
		 
		// for security purposes, we will also do a getimagesize on the temp file to check the MIME type of the file, and whether it has a width and height
		$imageinfo = getimagesize($fileTemp);
		 
		// we are going to define what file extensions/MIMEs are ok
		$okMIMETypes = 'image/jpeg,image/pjpeg,image/jpg,image/tiff,image/bmp,image/png,image/x-png,image/gif';
		$validFileTypes = explode(",", $okMIMETypes);		
		 
		// if the temp file does not have a width or a height, or it has a non ok MIME, return
		if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) ) {
			JError::raiseWarning( 100, JText::sprintf( 'COM_YOORECIPE_BAD_FILE_EXTENSION', $params->get('authorized_extensions', 'jpg,png,gif')));
			return '';
		}
		
		// lose any special characters in the filename
		$fileName = date('YmdHis').'-'.preg_replace("/[^A-Za-z0-9]/i", ".", $fileName);
		
		// Prepare image directory
		$image_folder = $params->get('image_folder', '/images/com_yoorecipe');
		$absolute_path = JPATH_ROOT.$image_folder;
		$relative_path = JPATH_BASE.$image_folder;

		$user_folder = $absolute_path.'/'.$user->id;

		if(!is_dir($absolute_path)){
			JFolder::create($absolute_path, 0755);
			$content = ".";
			JFile::write($absolute_path.'/index.html', $content, false);
		}
		if(!is_dir($user_folder)){
			$content = ".";
			JFolder::create($user_folder, 0755);
			JFile::write($user_folder.'/index.html',  $content, false);
		}

		$uploadPath = $user_folder.'/'.$fileName;
		$result = JFile::upload($fileTemp, $uploadPath);
		
		// Resize picture on the fly if needed
		$resize_on_the_fly = $params->get('resize_on_the_fly', 1);
		$max_resize_width = $params->get('max_resize_width', 800);
		
		if ($resize_on_the_fly && $imageinfo[0] > $max_resize_width) {
			
			$ratio 			= $max_resize_width / $imageinfo[0] * 100;
			$newwidth 		= $imageinfo[0] * $ratio / 100;
			$newheight 		= $imageinfo[1] * $ratio / 100;
			
			$thumb 	= imagecreatetruecolor($newwidth, $newheight);
			$source = '';
			switch (strtolower($uploadedFileExtension)) {
				case 'jpg':
				case 'jpeg':
					$source = imagecreatefromjpeg($uploadPath);
				break;
				
				case 'png':
					$source = imagecreatefrompng($uploadPath);
				break;
				
				case 'gif':
					$source = imagecreatefromgif($uploadPath);
				break;
			}

			if (!empty($source)) {
				// Resize
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $imageinfo[0], $imageinfo[1]);
				imagejpeg($thumb,$uploadPath,75);
				imagedestroy($source);
				imagedestroy($thumb);
			}
		}

		return ($result) ? substr($image_folder.'/'.$user->id.'/'.$fileName, 1, strlen($image_folder.'/'.$user->id.'/'.$fileName)) : '';
	}
	
	/**
	* getPicturePath
	*/
	public static function getPicturePath($recipe_picture) {

		$yooRecipeparams 		= JComponentHelper::getParams('com_yoorecipe');
		$use_watermark			= $yooRecipeparams->get('use_watermark', 1);
		
		if (!JFile::exists($recipe_picture)) {
			$params		 	= JComponentHelper::getParams('com_yoorecipe');
			$recipe_picture	= $params->get('default_item_picture', 'media/com_yoorecipe/images/img-not-available.png');
		}

		//--- Xander@OGOSense Recipe image batch modification ---//
		$path_parts = pathinfo($recipe_picture);
		
		if ($path_parts['dirname'] != 'images/com_yoorecipe/recipes') {
			$picture_path = JHtmlImageCropperUtils::getCroppedPicturePath($recipe_picture);
			
			if ($use_watermark && !empty($picture_path)) {
				$picture_path = self::watermarkImage($picture_path, JText::sprintf('COM_YOORECIPE_COPYRIGHTED_TEXT', JURI::base()));
			}
		}
		else{
			$picture_path = $recipe_picture;
		}
		
		return $picture_path;
	}

	/**
	 * Adds dynamicall a copyright label on picture
	 * sourceFile: image path
	 */
	private static function watermarkImage($source_file, $watermark_text) {
		
		if ((strpos($source_file, '.jpg') == FALSE && strpos($source_file, '.jpeg') == FALSE)) {
			return $source_file;
		}
	
		$matches = array();
		preg_match('/.*images\/com_yoorecipe\/(.*)\.(jpe?g)/i', $source_file, $matches);
		$watermarkedPicturePath = '';
		$inputFilePath = '';
		
		if (count($matches) > 0) {
			// Use recipe picture, while making sure no http:// inside picture path
			$watermarkedPicturePath = 'images/com_yoorecipe/'.$matches[1].'.protected.jpg';
			$inputFilePath = 'images/com_yoorecipe/'.$matches[1].'.'. $matches[2];
		}
		else {
			// Use default no image
			$watermarkedPicturePath = substr($source_file, 0, strpos($source_file, '.')).'.protected.jpg';
			$inputFilePath = $source_file;
		}
			
		if (!file_exists($watermarkedPicturePath) && file_exists($inputFilePath)) {
		
			list($width, $height) = getimagesize($inputFilePath);
			$image_p = imagecreatetruecolor($width, $height);
			$image = imagecreatefromjpeg($inputFilePath);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
			imagedestroy($image);
			$color = imagecolorallocate($image_p, 255 , 255, 255);
			$font = JPATH_ROOT.'/media/com_yoorecipe/fonts/arial.ttf';
			$font_size = 10;
			
			imagettftext($image_p, $font_size, 0, 10, $height-10, $color, $font, $watermark_text);
			imagejpeg ($image_p, $watermarkedPicturePath, 75);
			imagedestroy($image_p);
		}
		
		return $watermarkedPicturePath;
	}
	
	/**
	* createThumbnail
	*/
	private static function createThumbnail($file_path, $width, $height) {
	
		if (!JFile::exists($file_path)) {
			return;
		}
		
		$extension = JFile::getExt($file_path);
		$source;
		switch (strtolower($extension)) {

			case 'png':
				$source = imagecreatefrompng($file_path);
			break;
			
			case 'gif':
				$source = imagecreatefromgif($file_path);
			break;
			
			case 'jpg':
			case 'jpeg':
			default:
				$source = imagecreatefromjpeg($file_path);
			break;
		}
		
		if ($source === false) {
			return;
		}
		
		$imageinfo 	= getimagesize($file_path);
		$dest_path 	= JFile::stripExt($file_path).'-'.$width.'x'.$height.'.'.$extension;
		$thumb 		= imagecreatetruecolor($width, $height);
		imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $imageinfo[0], $imageinfo[1]);
		
		switch (strtolower($extension)) {
			case 'png':
				imagepng($thumb, $dest_path, 9);
			break;
			
			case 'gif':
				imagegif($thumb, $dest_path);
			break;
			
			case 'jpg':
			case 'jpeg':
			default:
				imagejpeg($thumb, $dest_path, 75);
			break;
		}
		imagedestroy($thumb);
	}
}