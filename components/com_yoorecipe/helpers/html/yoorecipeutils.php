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

abstract class JHtmlYooRecipeUtils
{

	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	public static function featured($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('disabled.png',	'articles.featured',	'COM_CONTENT_UNFEATURED',	'COM_CONTENT_TOGGLE_TO_FEATURE'),
			1	=> array('featured.png',	'articles.unfeatured',	'COM_CONTENT_FEATURED',		'COM_CONTENT_TOGGLE_TO_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html	= JHtml::_('image','admin/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'.$html.'</a>';
		}

		return $html;
	}

	 /**
	  * Generate Social Bookmarks icons
	  */
	public static function socialSharing($params, $item)
	{
		$document 		= JFactory::getDocument();
		$language 		= JFactory::getLanguage();

		$params			= JComponentHelper::getParams('com_yoorecipe');
		$fb_api_key		= $params->get('fb_api_key', '', 'STRING');

		$locale 		= str_replace("-","_", $language->getTag());
		$social_link 	= urlencode(JURI::getInstance());

		$html	= array();
		$html[] = '<table cellpadding="0" cellspacing="0">';
		$html[] = '<tr>';

		if ($params->get('show_facebook')) {

			$html[] = '<td style="vertical-align:bottom; white-space:nowrap;zoom:1;">';
			$html[] = '<div class="gig-btn-container gig-fb-container">';
			$html[] = '<div id="fb-root"></div>';
			$html[] = '<script>(function(d, s, id) {';
			$html[] = '  var js, fjs = d.getElementsByTagName(s)[0];';
			$html[] = '  if (d.getElementById(id)) return;';
			$html[] = '  js = d.createElement(s); js.id = id;';
			$html[] = '  js.src = "//connect.facebook.net/'.$locale.'/sdk.js#xfbml=1&version=v2.3&appId='.$fb_api_key.'";';
			$html[] = '  fjs.parentNode.insertBefore(js, fjs);';
			$html[] = "}(document, 'script', 'facebook-jssdk'));</script>";
			$html[] = '<div class="fb-share-button" data-href="'.JURI::getInstance().'" data-layout="box_count"></div>';
			$html[] = '</div>';
			$html[] = '</td>';
		}

		if ($params->get('show_googleplus1')) {

			$document->addScriptDeclaration("window.___gcfg = {lang: '".$language->getTag()."',};
(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();");
			$html[] = '<td style="vertical-align:bottom; white-space:nowrap;zoom:1;">';
			$html[] = '<div class="gig-btn-container">';
			$html[] = '<div class="g-plusone" data-size="tall"></div>';
			$html[] = '</div>';
			$html[] = '</td>';
		}

		if ($params->get('show_twitter')) {

			$html[] = '<td style="vertical-align:bottom; white-space:nowrap;zoom:1;">';
			$html[] = '<div class="gig-btn-container">';
			$html[] = '<a href="'.$social_link.'" class="twitter-share-button" data-via="twitterapi" data-lang="en" data-count="vertical">'.JText::_('COM_YOORECIPE_ADD_TWITTER').'</a>';
			$html[] = '<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
			$html[] = '</div>';
			$html[] = '</td>';
		}

		if ($params->get('show_pinterest')) {
			$html[] = '<td style="vertical-align:bottom; white-space:nowrap;zoom:1;">';
			$html[] = '<div class="gig-btn-container">';
			$html[] = '<a target="_blank" href="https://pinterest.com/pin/create/button/?url='.$social_link.'&media='.urlencode(JUri::base().$item->picture).'&description='.urlencode(strip_tags($item->description)).'" data-pin-do="buttonPin" data-pin-config="above"></a>';
			$html[] = '<script type="text/javascript" async src="https://assets.pinterest.com/js/pinit.js"></script>';
			$html[] = '</div>';
			$html[] = '</td>';
		}

		$html[] = '</tr>';
		$html[] = '</table>';

		return implode("\n", $html);
	}

	/**
	 * Returns the parameter value of a given $paramName.
	 * Priority 1: menu parameters, Fallback: global parameters, fallback: default value
	 */
	public static function getParamValue($menuParams, $globalParams, $paramName, $default)
	{
		if (isset($menuParams)) {

			$paramVal = $menuParams->get($paramName);
			if (isset($paramVal)) {
				return ($menuParams->get($paramName) == 'use_global') ? $globalParams->get($paramName) : $menuParams->get($paramName);
			} else {
				return $globalParams->get($paramName);
			}
		}
		else {
			return $globalParams->get($paramName, $default);
		}
	}

	/**
	 * Automatically numbers paragraph contained in recipe directions
	 */
	public static function formatParagraphs($recipeDirections)
	{
		$result = '<ol class="numbering">';
		$regex = '#<p(.*)(.*)\>#iU';

		$matches = array();
		while (preg_match( $regex, $recipeDirections, $matches )) {
			$tag = $matches[0];
			$replaceText = '<li class="numbering"><div>';
			$recipeDirections = str_replace( $tag, $replaceText, $recipeDirections);
		}

		$regex = '/<\/p>/';

		$matches = array();
		while (preg_match( $regex, $recipeDirections, $matches )) {
			$tag = $matches[0];
			$replaceText = '</div></li>';
			$recipeDirections = str_replace( $tag, $replaceText, $recipeDirections);
		}

		$result .= $recipeDirections.'</ol>';
		return $result;
	}

	/**
	* Generate a browsable category list
	*/
	public static function generateCategoriesList($categories) {

		$html = array();
		$html[] = '<div>'.JText::_('COM_YOORECIPE_NO_RECIPE_FOUND').'</div>';
		$html[] = '<div>'.JText::_('COM_YOORECIPE_BROWSE_CATEGORIES_LIST').'</div>';
		$html[] = '<select onchange="window.location.href = this.value">';

		foreach ($categories as $category) {
			$url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getcategoryroute', $category->slug) , false);
			$html[] = '<option value="'.$url.'">'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $category->level-1).htmlspecialchars($category->title).'</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}

	/**
		* Generate add recipe button
	 */
	public static function generateAddRecipeButton($first = false){

		$html = array();

		$user = JFactory::getUser();
		if ($user->guest != 1 && ($user->authorise('core.edit', 'com_yoorecipe') || ($user->authorise('core.edit.own', 'com_yoorecipe')))) {

			if ($first){
				$html[] = '<div>'.JText::_('COM_YOORECIPE_FIRST_ADD_RECIPE').'</div>';
			}
			$html[] = '<a class="btn" href="'.JRoute::_('index.php?option=com_yoorecipe&view=form&layout=edit').'">'.JText::_('COM_YOORECIPE_ADD').'</a>';
			$html[] = '<br/>';
		}
		return implode("\n", $html);
	}

	/**
	 * Generate a mosaic of all sub-categories for the current category
	 */
	public static function generateSubCategoriesMosaic($subcategories, $show_sub_categories_picture) {

		$html = array();
		$cnt = 0;
		$nb_cols = 3;
		/* XANDER removing row fluid from the view */
		//$html[] = '<div class="row-fluid">';
		foreach($subcategories as $category) {

			if ($cnt % $nb_cols == 0 && $cnt > 0) {
				//$html[] = '</div>';
				//$html[] = '<div class="row-fluid">';
			}

			$cat_url = JRoute::_('index.php?option=com_yoorecipe&view=categories&id='.$category->slug);

			$params = new JRegistry();
			$params->loadString($category->params);

			$col_index = $cnt % $nb_cols;
			$html[] = '<div class="item column-1 span'.(int)12/$nb_cols.'">';
			if ($show_sub_categories_picture) {
				$picture_path = $params->get('image', 'media/com_yoorecipe/images/no-image.jpg');
				$picture_path = JHtml::_('imageutils.getPicturePath', $picture_path);
				$html[] = '<a href="'.$cat_url.'"><img class="thumbnail" src="'.$picture_path.'" alt="'.$category->title.'"/></a>';
			}
			$html[] = '<div></div>';
			$html[] = '<div class="cat-title"><i class="cat-title-triangle"></i><a href="'.$cat_url.'">'.$category->title.'</a>&nbsp;('.$category->nb_recipes.')</div>';

			$html[] = '</div>';
			$cnt++;
		}
		//$html[] = '</div>';
		return implode("\n", $html);
	}

	/**
	 * generatePagination
	 */
	public static function generatePagination($pagination) {

		require_once JPATH_COMPONENT.'/helpers/html/yoorecipepagination.php';

		// Component Parameters
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$show_pages_counter	= $yooRecipeparams->get('show_pages_counter', 0);
		$show_limit_box		= $yooRecipeparams->get('show_limit_box', 1);

		$html = array();

		$html[] = '<div class="pagination">';
		$html[] = $pagination->getPagesLinks();
		if ($show_limit_box) {
			$html[] = '<span>'.JText::_('COM_YOORECIPE_NB_RECIPES_PER_PAGE').$pagination->getLimitBox().'</span>';
		}

		$html[] = '</div>';
		if ($show_pages_counter) {
			$html[] = '<div>'.$pagination->getPagesCounter().'</div>';
		}

		return implode("\n", $html);
	}

	/**
	 * generateManagementPanel
	 */
	public static function generateManagementPanel($recipe) {

		$html = array();

		if ($recipe->canEdit || $recipe->canDelete) {

			$document 	= JFactory::getDocument();
			// $url 		= JURI::root().'index.php?option=com_yoorecipe&task=deleteRecipe&format=raw&id='.$recipe->id;
			$editUrl 	= JRoute::_('index.php?option=com_yoorecipe&view=form&layout=edit&id='.$recipe->slug);

			$html[] = '<div id="yr_btns_'.$recipe->id.'">';
			if ($recipe->canEdit) {
				$html[] = '<button type="button" class="btn" onclick="window.location=\''.$editUrl.'\'">'.JText::_('COM_YOORECIPE_EDIT').'</button>';
			}
			if ($recipe->canDelete) {
				$html[] = '<button type="button" class="btn" id="btn_yr_del_'.$recipe->id.'">'.JText::_('COM_YOORECIPE_DELETE').'</button>';

				$script = array();
				$script[] = "window.addEvent('domready', function () {";
				$script[] = "$('btn_yr_del_".$recipe->id."').addEvent('click', function () { deleteRecipe(".$recipe->id."); });";
				$script[] = "});";
				$document = JFactory::getDocument();
				$document-> addScriptDeclaration(implode("\n", $script));
			}
			$html[] =  '</div>';
		}

		return implode("\n", $html);
	}

	/**
	 * Generate recipe reviews
	 */
	public static function generateReviews($ratings, $can_report_comments, $limit) {

		$html	= array();
		$user 	= JFactory::getUser();

		foreach ($ratings as $i => $rating) {

			if ($i == $limit) {
				break;
			}

			$cssClass = (!$rating->published || $rating->abuse) ? "greyedout" : "";
			$html[] = '<div class="span4 yoorecipe-review '.$cssClass.'" id="yoorecipe_comment_'. $rating->id.'">';

			$width = ($rating->note*69)/5;
			$html[] = '<div class="rec-detail-wrapper">';
			$html[] = '<div class="rating-stars stars69x13 fl-left">';
			$html[] = '<div style="width:'.$width.'px;" class="rating-stars-grad"></div>';
			$html[] = '<div class="rating-stars-img">';
			$html[] = '<span class="rating hide">'.$rating->note.'</span>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</div>';

			$html[] = JHtml::_('date', $rating->creation_date);
			$html[] = '<br/><small>';

			$text_comment = htmlspecialchars($rating->comment);
			if(strlen($text_comment) > 100) {
				$preview = substr(htmlspecialchars($rating->comment), 0, 100).'...';
				$html[] = '<span class="comment_'.$rating->id.'">'.$preview.'<br/>';
				$html[] = '<a href="#" onclick="seeFullReview('.$rating->id.'); return false;">'.JText::_('COM_YOORECIPE_SEE_FULL_REVIEW').'</a>';
				$html[] = '</span>';
				$html[] = '<span class="full_review_'.$rating->id.'" style="display:none">'.$text_comment.'</span>';
			} else {
				$html[] = '<span id="comment_'.$rating->id.'">'.$text_comment.'</span>';
			}
			$html[] = '</small>';

			// Link to author if possible
			$html[] = '<span> - ';
			if ($rating->user_id != null && $rating->user_id != 0) {
				$authorUrl = JRoute::_(JHtml::_('YooRecipeHelperRoute.getuserroute', $rating->user_id) , false);
				$html[] = '<a href="'.$authorUrl.'">'.$rating->author_name.'</a>';
			} else {
				$html[] = htmlspecialchars($rating->author);
			}
			$html[] = '</span>';

			if (self::canManageComments($user, $rating->user_id)) {

			    $html[] = '<br/>';
			    $html[] = '<div class="btn-group">';
				$html[] = '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">';
				$html[] = JText::_('COM_YOORECIPE_ACTION');
				$html[] = '<span class="caret"></span>';
				$html[] = '</a>';
				$html[] = '<ul class="dropdown-menu">';
				$html[] = '<li><a href="#" onclick="editComment('.$rating->id.');return false;">'.JText::_('COM_YOORECIPE_EDIT').'</a></li>';
				$html[] = '<li><a href="#" onclick="com_yoorecipe_deleteComment('.$rating->recipe_id.','.$rating->id .');return false;">'.JText::_('COM_YOORECIPE_DELETE').'</a></li>';
				if ($can_report_comments && $rating->published && !$rating->abuse) {
					$html[] = '<li><a href="#" onclick="com_yoorecipe_reportReview('.$rating->recipe_id.','.$rating->id .');return false;">'.JText::_('COM_YOORECIPE_REPORT_AS_OFFENSIVE').'</a></li>';
				}
				$html[] = '</ul>';
				$html[] = '</div>';
			}

			// Show under moderation if needed, show report comment otherwise
			if (!$rating->published || $rating->abuse) {
				$html[] = '<img src="media/com_yoorecipe/images/pending.png" alt="" title="'.JText::_('COM_YOORECIPE_PENDING_APPROVAL').'"/>';
			}

			$html[] = '</div>';
			if($i%3 == 2){
				$html[] = '</div><div class="row-fluid">';
			}

		} // End foreach ($ratings as $i => $rating) {

		return implode("\n", $html);
	}

	/**
	 * Returns authorization to manage comments
	 */
	public static function canManageComments($user, $authorId) {
		return $user->authorise('core.admin', 'com_yoorecipe') || ($user->authorise('recipe.comments.edit.own', 'com_yoorecipe') && $authorId == $user->id);
	}

	/**
	 * Returns authorization to report comments
	 */
	public static function canreportReviews($user) {
		return $user->authorise('recipe.comments.report', 'com_yoorecipe');
	}

	/**
	 * Method that generates a video player for the recipe
	 */
	public static function generateVideoPlayer($video_link) {

		$html = array();

		$media_url = self::getVideoPlatformAndId($video_link);
		$exp = explode(':_:', $media_url);
		$html[] = '<div class="yoorecipe-video">';
		switch ($exp[0]) {
			case 'youtube':
				$html[] = '<iframe width="420" height="315" src="//www.youtube.com/embed/'.$exp[1].'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>';
			break;
			case 'vimeo':
				$html[] = '<iframe width="420" height="315" src="http://player.vimeo.com/video/'.$exp[1].'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			break;
			case 'dailymotion':
				$html[] = '<iframe width="420" height="315" src="http://www.dailymotion.com/embed/video/'.$exp[1].'" frameborder="0" allowfullscreen></iframe>';
			break;

			default:
			break;
		}

		$html[] = '</div>';
		return implode("\n", $html);
	}

	/**
	* getVideoPlatformAndId
	*/
	public static function getVideoPlatformAndId($video_link) {

		$media_url = "";
		$matches = array();

		// Check if dailymotion
		preg_match('#<object[^>]+>.+?http://www.dailymotion.com/swf/video/([A-Za-z0-9]+).+?</object>#s', $video_link, $matches);
		if(!isset($matches[1])) {

			preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $video_link, $matches);
			if(!isset($matches[1])) {
				preg_match('#http://www.dailymotion.com/embed/video/([A-Za-z0-9]+)#s', $video_link, $matches);
				if(isset($matches[1]) && strlen($matches[1])){
					$media_url = 'dailymotion:_:'.$matches[1];
				}
			} else if(strlen($matches[1])){
				$media_url = 'dailymotion:_:'.$matches[1];
			}
		} else if(strlen($matches[1])){
			if(strlen($matches[1])){
				$media_url = 'dailymotion:_:'.$matches[1];
			}
		}

		// Check if YouTube
		if(preg_match('#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#', $video_link, $videoid)) {
			if(strlen($videoid[0])) { $media_url = 'youtube:_:'.$videoid[0]; }
		}

		// Check if VIMEO
		if(preg_match('#(https?://)?(www.)?(player.)?vimeo.com/([a-z]*/)*([0-9]{6,11})[?]?.*#', $video_link, $videoid)){
			if(strlen($videoid[5])) { $media_url = 'vimeo:_:'.$videoid[5]; }
		}

		return $media_url;
	}

	/**
	 * Generate cross categories, recipe tags, difficulty, cost, prep time, cook time, wait time
	 */
	public static function generateRecipeActions($recipe, $yooRecipeparams, $can_show_category_title, $can_show_difficulty, $can_show_cost, $can_show_preparation_time, $can_show_cook_time, $can_show_wait_time)
	{
		$user 		= JFactory::getUser();
		$use_tags 	= $yooRecipeparams->get('use_tags', 1);

		$html = array();
		$html[] = '<ul class="yoorecipe-infos">';
		$url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false);

		if ($can_show_category_title) {
			$html[] = '<li>'.JHtml::_('yoorecipeutils.generateCrossCategories', $recipe, $do_row_fluid = true).'</li>';
		}

		// Generate Recipe tags
		if ($use_tags && !empty($recipe->tags)) {

			$recipe->tagLayout = new JLayoutFile('joomla.content.tags');
			$html[] = '<li>'.$recipe->tagLayout->render($recipe->tags->itemTags).'</li>';
		}

		if ($can_show_difficulty) {

			$html[] = '<li><strong>'.JText::_('COM_YOORECIPE_RECIPES_DIFFICULTY').':&nbsp;</strong>';
			$html[] = '<span class="label label-warning">';
				switch($recipe->difficulty){
				case 1:
					$html[] = JText::_('COM_YOORECIPE_YOORECIPE_SUPER_EASY_LABEL');
					break;
				case 2:
					$html[] = JText::_('COM_YOORECIPE_YOORECIPE_EASY_LABEL');
					break;
				case 3:
					$html[] = JText::_('COM_YOORECIPE_YOORECIPE_MEDIUM_LABEL');
					break;
				case 4:
					$html[] = JText::_('COM_YOORECIPE_YOORECIPE_HARD_LABEL');
					break;
				}
			$html[] = '</span>';
			$html[] = '</li>';
		}

		if ($can_show_cost) {

			$html[] = '<li><strong>'.'  '.JText::_('COM_YOORECIPE_RECIPES_COST').':&nbsp;</strong>';
			$html[] = '<span class="label label-warning">';

			switch($recipe->cost){
			case 1:
				$html[] = JText::_('COM_YOORECIPE_YOORECIPE_CHEAP_LABEL');
				break;
			case 2:
				$html[] =  JText::_('COM_YOORECIPE_YOORECIPE_INTERMEDIATE_LABEL');
				break;
			case 3:
				$html[] =  JText::_('COM_YOORECIPE_YOORECIPE_EXPENSIVE_LABEL');
				break;
			}
			$html[] =  '</span>';
			$html[] = '</li>';
		}

		if ($can_show_preparation_time && $recipe->preparation_time != 0) {

			$html[] = '<li><strong>'.JText::_('COM_YOORECIPE_RECIPES_PREPARATION_TIME').':&nbsp;</strong>';
			$html[] = '<span>'.JHtml::_('datetimeutils.formattime', $recipe->preparation_time).'</span></li>';
}

		if ($can_show_cook_time && $recipe->cook_time != 0) {

			$html[] = '<li><strong>'.JText::_('COM_YOORECIPE_RECIPES_COOK_TIME').':&nbsp;</strong>';
			$html[] = '<span>'.JHtml::_('datetimeutils.formattime', $recipe->cook_time).'</span></li>';
		}

		if ($can_show_wait_time && $recipe->wait_time != 0) {

			$html[] = '<li><strong>'.JText::_('COM_YOORECIPE_RECIPES_WAIT_TIME').':&nbsp;</strong>';
			$html[] = '<span>'.JHtml::_('datetimeutils.formattime', $recipe->wait_time).'</span></li>';
		}
		$html[] = '</ul>';

		return implode("\n", $html);
	}

	/**
	 * Generate recipe average rating
	 */
	public static function generateRecipeRatings($recipe, $enable_reviews, $rating_style)
	{
		$html = array();

		$params 		= JComponentHelper::getParams('com_yoorecipe');
		$rating_origin 	= $params->get('rating_origin', 'yoorecipe');

		$html[] = '<div class="row-fluid">';
		$html[] = '<span style="display:none" itemprop="worstRating">0</span>';

		switch ($rating_origin) {

			case 'yoorecipe':
				if ($recipe->note == null) {
					$recipe->note = 0;
				}

				if ($rating_style == 'grade') {
					$html[] = '<span itemprop="ratingValue">'.$recipe->note.'</span>/5';
				}
				if ($rating_style == 'stars') {
					$width = ($recipe->note*113)/5;
					$html[] = '<div class="rec-detail-wrapper span4">';
					$html[] = '<div class="rating-stars stars113x20 fl-left">';
					$html[] = '<div style="width:'.$width.'px;" class="rating-stars-grad"></div>';
					$html[] = '<div class="rating-stars-img">';
					$html[] = '<span class="rating hide" itemprop="ratingValue">'.$recipe->note.'</span>';
					$html[] = '</div>';
					$html[] = '</div>';
					$html[] = '</div>';
				}

				if ($enable_reviews) {
					$url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false);
					$nb_reviews = count($recipe->ratings);
					$html[] = '<div class="span6">';
					$html[] = '<a href="'.$url.'#reviews'.'" rel="nofollow">';
					$html[] = '(<span class="count" itemprop="reviewCount">'.$nb_reviews.'</span>'.' '.JText::_('COM_YOORECIPE_REVIEWS').')';
					$html[] = '</a>';
					$html[] = '</div>';
				}
			break;

			case 'komento':

				$komentoModel 		= JModelLegacy::getInstance('komento', 'YooRecipeModel');
				$recipe->note 		= round($komentoModel->getRecipeNote($recipe->id), 1);
				$recipe->nb_reviews = $komentoModel->getNbReviewsByRecipeId($recipe->id);

				if ($rating_style == 'grade') {
					$html[] = '<span itemprop="ratingValue">'.$recipe->note.'</span>/5';
				}
				if ($rating_style == 'stars') {
					$width = ($recipe->note*113)/5;
					$html[] = '<div class="rec-detail-wrapper span4">';
					$html[] = '<div class="rating-stars stars113x20 fl-left">';
					$html[] = '<div style="width:'.$width.'px;" class="rating-stars-grad"></div>';
					$html[] = '<div class="rating-stars-img">';
					$html[] = '<span class="rating hide" itemprop="ratingValue">'.$recipe->note.'</span>';
					$html[] = '</div>';
					$html[] = '</div>';
					$html[] = '</div>';
				}

				$url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false);

				$html[] = '<div class="span6">';
				$html[] = '<a href="'.$url.'#comments'.'" rel="nofollow">';
				$html[] = '(<span class="count" itemprop="reviewCount">'.$recipe->nb_reviews.'</span>'.' '.JText::_('COM_YOORECIPE_REVIEWS').')';
				$html[] = '</a>';
				$html[] = '</div>';
			break;
		}

		$html[] = '</div>';
		return implode("\n", $html);
	}

	/**
	* Generate ingredients list
	*/
	public static function generateIngredientsList($recipe) {

		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$use_fractions		= $yooRecipeparams->get('use_fractions', 0);

		$html = array();
		$html[] = '<p>';
		$html[] = '<span class="span-recipe-label">'.JText::_('COM_YOORECIPE_RECIPES_INGREDIENTS').':</span><br/>';
		$html[] = '<span class="span-recipe-ingredients">';

		$ingredientsList = array();

		foreach ($recipe->groups as $group) {

			foreach ($group->ingredients as $ingredient) {

				if ($ingredient->quantity == 0) {
					$ingredientsList[] =  $ingredient->unit.' '.$ingredient->description;
				} else {
					$quantity = ($use_fractions) ? JHtmlIngredientUtils::decimalToFraction(round($ingredient->quantity, 2)) : round($ingredient->quantity, 2);
					$ingredientsList[] =  $quantity.' '.$ingredient->unit.' '.$ingredient->description;
				}
			}
		}

		$html[] = implode(', ', $ingredientsList);

		$html[] = '</span>';
		$html[] = '</p>';

		return implode("\n", $html);
	}

	/**
	* generateRecipeSeason
	*/
	public static function generateRecipeSeason($slugs, $do_row_fluid = true) {

		// Add HTML
		$html = array();

		if (isset($slugs) && !empty($slugs)) {

			if ($do_row_fluid) {
				$html[] = '<div class="row-fluid">';
				$html[] = '<div class="span2">';
			}
			$html[] = '<strong>'.JText::_('COM_YOORECIPE_SEASONS').':&nbsp;</strong>';
			if ($do_row_fluid) {
				$html[] = '</div>';
				$html[] = '<div>';
			}

			$seasons = array();
			foreach ($slugs as $slug) {
				$chunks = preg_split('/:/',$slug);
				$season_url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getSeasonRoute', $slug) , false);
				$season_label = htmlspecialchars(JText::_('COM_YOORECIPE_'.$chunks[0]));
				$seasons[] = '<a href="'.$season_url.'" title="'.$season_label.'">'.$season_label.'</a>';
			}

			$html[] = implode(', ', $seasons);
			if ($do_row_fluid) {
				$html[] = '</div>';
				$html[] = '</div>';
			}
		}

		return implode("\n", $html);
	}

	/**
	* htmlCut
	*/
	public static function htmlCut($text, $max_length) {

		$tags   = array();
		$result = "";

		$is_open   = false;
		$grab_open = false;
		$is_close  = false;
		$in_double_quotes = false;
		$in_single_quotes = false;
		$tag = "";

		$i = 0;
		$stripped = 0;

		$stripped_text = strip_tags($text);

		while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
		{
			$symbol  = $text{$i};
			$result .= $symbol;

			switch ($symbol)
			{
			   case '<':
					$is_open   = true;
					$grab_open = true;
					break;

			   case '"':
				   if ($in_double_quotes)
					   $in_double_quotes = false;
				   else
					   $in_double_quotes = true;

				break;

				case "'":
				  if ($in_single_quotes)
					  $in_single_quotes = false;
				  else
					  $in_single_quotes = true;

				break;

				case '/':
					if ($is_open && !$in_double_quotes && !$in_single_quotes)
					{
						$is_close  = true;
						$is_open   = false;
						$grab_open = false;
					}

					break;

				case ' ':
					if ($is_open)
						$grab_open = false;
					else
						$stripped++;

					break;

				case '>':
					if ($is_open)
					{
						$is_open   = false;
						$grab_open = false;
						array_push($tags, $tag);
						$tag = "";
					}
					else if ($is_close)
					{
						$is_close = false;
						array_pop($tags);
						$tag = "";
					}

					break;

				default:
					if ($grab_open || $is_close)
						$tag .= $symbol;

					if (!$is_open && !$is_close)
						$stripped++;
			}

			$i++;
		}

		while ($tags)
			$result .= "</".array_pop($tags).">";

		return $result;
	}
}
