<?php
/**
 * ------------------------------------------------------------------------
 * JA System Social Feed Plugin for Joomla 2.5 & 3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__).'/base.php');

class jaSocialFeedTableK2 extends jaSocialFeedTableBase {
	
	public function __construct($plgParams) {
		if(!$this->isAvailable()) {
			return false;
		}
		
		parent::__construct($plgParams);
		
		$db = JFactory::getDbo();
		$this->table = new TableK2Item($db);
	}
	
	public function isAvailable() {
		$table = JPATH_ADMINISTRATOR.'/components/com_k2/tables/table.php';
		$tableK2 = JPATH_ADMINISTRATOR.'/components/com_k2/tables/k2item.php';
		if(!defined('K2_JVERSION')) {
			define('K2_JVERSION', '16');
		}
		if(!JFile::exists($table) && !JFile::exists($tableK2)) {
			//raise warning?
			return false;
		}

		//required for new version
		if(JFile::exists($table)){
			require_once($table);
		}

		if(!JFile::exists($tableK2)) {
			//raise warning?
			return false;
		}
		require_once($tableK2);
		return true;
	}
	
	public function existsPost($alias) {
		$db = JFactory::getDbo();
		$query = 'SELECT id FROM #__k2_items WHERE `alias` = '.$db->quote($alias);

		$db->setQuery($query);
		$id = $db->loadResult();
		if(!$id) $id = 0;
		return intval($id);
	}
	
	public function store($source, $post, $id = 0) {
		if(empty($post['source_alias']) || (!$id && $this->existsPost($post['source_alias']))) {
			return false;
		}
		$item = array();
		//general property
		$item['metakey'] = '';
		$item['metadesc'] = '';
		$item['published'] = '1';
		$item['access'] = '1';
		$item['language'] = '*';
		$item['featured'] = '0';
		$item['metadesc'] = '';
		$item['metakey'] = '';
		$item['meta'] = array('robots' => '', 'author' => '');
		
		//////
		$item['id'] = $id;
		$item['created_by'] = $post['created_by'];
		$item['created_by_alias'] = html_entity_decode($post['source_author'], ENT_COMPAT, 'UTF-8');
		$item['catid'] = $post['catid'];
		$item['alias'] = $post['source_alias'];
		$item['title'] = html_entity_decode($post['source_title'], ENT_COMPAT, 'UTF-8');
		$item['created'] = $this->dateToDb($post['source_published_date']);
		$item['publish_up'] = date('Y-m-d H:i:s', time()-86400);
		$item['publish_down'] = '';
		$item['video'] = isset($post['source_video']) ? $post['source_video'] : '';

		if(isset($post['source_images']) && is_object($post['source_images'])) {
			//image caption
			$img = $post['source_images'];
			$item['image_caption'] = htmlspecialchars($img->image_intro_caption, ENT_QUOTES, 'UTF-8');
		}

		$source_content = preg_replace('@<img @', '<img class="'.$source.'-img ja-social-img" ', $post['source_content']);
		if(isset($post['intro_text']) && !empty($post['intro_text'])) {
			$item['introtext'] = html_entity_decode($post['intro_text'], ENT_COMPAT, 'UTF-8');
			$item['fulltext'] = html_entity_decode($source_content, ENT_COMPAT, 'UTF-8');
		} else {
			$item['introtext'] = html_entity_decode($source_content, ENT_COMPAT, 'UTF-8');
			$item['fulltext'] = '';
		}

		//save article
		$this->table->load($id);
		$this->table->bind($item);
		if($this->table->check()) {
			$result = $this->table->store();
			if($result) {
				if(isset($post['source_images']) && is_object($post['source_images'])) {
					require_once JPATH_ADMINISTRATOR . '/components/com_k2/lib/class.upload.php';
					$img = $post['source_images'];

					// process to import image to k2 items.
					$image = JPATH_SITE.'/'.JPath::clean($img->image_intro);
					$handle = new upload($image);
					$handle->allowed = array('image/*');
					if ($handle->uploaded)
					{
						//Original image
						$savepath = JPATH_SITE.'/media/k2/items/src';
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->file_auto_rename = false;
						$handle->file_overwrite = true;
						$handle->file_new_name_body = md5("Image".$this->table->id);
						$handle->Process($savepath);

						$filename = $handle->file_dst_name_body;
						$savepath = JPATH_SITE.'/media/k2/items/cache';

						//XLarge image
						$handle->image_resize = true;
						$handle->image_ratio_y = true;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->file_auto_rename = false;
						$handle->file_overwrite = true;
						$handle->file_new_name_body = $filename.'_XL';
						$handle->image_x = 800;
						$handle->Process($savepath);

						//Large image
						$handle->image_resize = true;
						$handle->image_ratio_y = true;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->file_auto_rename = false;
						$handle->file_overwrite = true;
						$handle->file_new_name_body = $filename.'_L';
						$handle->image_x = 600;
						$handle->Process($savepath);

						//Medium image
						$handle->image_resize = true;
						$handle->image_ratio_y = true;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->file_auto_rename = false;
						$handle->file_overwrite = true;
						$handle->file_new_name_body = $filename.'_M';
						$handle->image_x = 400;
						$handle->Process($savepath);

						//Small image
						$handle->image_resize = true;
						$handle->image_ratio_y = true;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->file_auto_rename = false;
						$handle->file_overwrite = true;
						$handle->file_new_name_body = $filename.'_S';
						$handle->image_x = 200;
						$handle->Process($savepath);

						//XSmall image
						$handle->image_resize = true;
						$handle->image_ratio_y = true;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->file_auto_rename = false;
						$handle->file_overwrite = true;
						$handle->file_new_name_body = $filename.'_XS';
						$handle->image_x = 100;
						$handle->Process($savepath);

						//Generic image
						$handle->image_resize = true;
						$handle->image_ratio_y = true;
						$handle->image_convert = 'jpg';
						$handle->jpeg_quality = 100;
						$handle->file_auto_rename = false;
						$handle->file_overwrite = true;
						$handle->file_new_name_body = $filename.'_Generic';
						$handle->image_x = 300;
						$handle->Process($savepath);

						$handle->Clean(); // remove the source file.
					}

				}
			}
			return $result;
		}
		return false;
	}
}