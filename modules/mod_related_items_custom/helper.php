<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_related_items
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
 * Helper for mod_related_items
 *
 * @package     Joomla.Site
 * @subpackage  mod_related_items
 * @since       1.5
 */
abstract class ModRelatedItemsCustomHelper
{
	/**
	 * Get a list of related articles
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return array
	 */
	

	
	public static function getList(&$params)
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$date = JFactory::getDate();
		$maximum = (int) $params->get('maximum', 5);

		$option = $app->input->get('option');
		$view = $app->input->get('view');

		$category_id = $app->input->get('catid');

		$temp = $app->input->getString('id');
		$temp = explode(':', $temp);
		$id = $temp[0];

		$nullDate = $db->getNullDate();
		$now = $date->toSql();
		$related = array();
		$query = $db->getQuery(true);

		if ($option == 'com_content' && $view == 'article' && $id)
		{
			// Select related articles based on tags
			$query2 = 'SELECT tag_id FROM `#__contentitem_tag_map` 
						WHERE content_item_id='.(int) $id;
			$db->setQuery(true);
			$db->setQuery($query2);
			$db->execute();
			unset($query2);
			$tags = $db->loadObjectList();

			if (! empty($tags)) {

				foreach ($tags as $key => &$tag) {
					$tag = $tag->tag_id;
				}

				$tags = implode(",", $tags);

			
				$query2 = 'SELECT content_item_id FROM `#__contentitem_tag_map` 
							WHERE tag_id IN ('.$tags.') ORDER BY RAND()';
				$db->setQuery(true);
				$db->setQuery($query2);
				$db->execute();
				unset($query2);
				$relatedArticles = $db->loadObjectList();

				foreach ($relatedArticles as $key => &$ra) {
					$ra = $ra->content_item_id;
				}

				$relatedArticles = implode(",", $relatedArticles);
			}

			

			// Select the meta keywords from the item
/*			$query->select('metakey')
				->from('#__content')
				->where('id = ' . (int) $id);
			$db->setQuery($query);

			try
			{
				$metakey = trim($db->loadResult());
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');

				return;
			}

			// Explode the meta keys on a comma
			$keys = explode(',', $metakey);
			$likes = array();

			// Assemble any non-blank word(s)
			foreach ($keys as $key)
			{
				$key = trim($key);

				if ($key)
				{
					$likes[] = $db->escape($key);
				}
			}*/

			//if (count($likes))
			if (! empty($relatedArticles)) 
			{
				// Select other items based on the metakey field 'like' the keys found
				$query->clear()
					->select('a.id')
					->select('a.title')
					->select('a.images')
					->select('a.introtext')
					->select('DATE(a.created) as created')
					->select('a.catid')
					->select('a.language')
					->select('cc.access AS cat_access')
					->select('cc.published AS cat_state');

				// Sqlsrv changes
				$case_when = ' CASE WHEN ';
				$case_when .= $query->charLength('a.alias', '!=', '0');
				$case_when .= ' THEN ';
				$a_id = $query->castAsChar('a.id');
				$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
				$case_when .= ' ELSE ';
				$case_when .= $a_id . ' END as slug';
				$query->select($case_when);

				$case_when = ' CASE WHEN ';
				$case_when .= $query->charLength('cc.alias', '!=', '0');
				$case_when .= ' THEN ';
				$c_id = $query->castAsChar('cc.id');
				$case_when .= $query->concatenate(array($c_id, 'cc.alias'), ':');
				$case_when .= ' ELSE ';
				$case_when .= $c_id . ' END as catslug';
				$query->select($case_when)
					->from('#__content AS a')
					->join('LEFT', '#__content_frontpage AS f ON f.content_id = a.id')
					->join('LEFT', '#__categories AS cc ON cc.id = a.catid')
					->where('a.id != ' . (int) $id)
					->where('a.state = 1')
					->where('a.catid = '.$category_id)
					->where('a.access IN (' . $groups . ')');

				$wheres = array();

				/*foreach ($likes as $keyword)
				{
					$wheres[] = 'a.metakey LIKE ' . $db->quote('%' . $keyword . '%');
				}*/

				if (! empty($relatedArticles)) 
					$query->where('a.id IN ('.$relatedArticles.')');

				$query
					//->where('(' . implode(' OR ', $wheres) . ')')
					->where('(a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ')')
					->where('(a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ') ORDER BY a.id DESC');

				// Filter by language
				if (JLanguageMultilang::isEnabled())
				{
					$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}

				$db->setQuery($query, 0, $maximum);
				//die('<pre>'.print_r($query, true).'</pre>');
				try
				{
					$temp = $db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');

					return;
				}

				if (count($temp))
				{
					foreach ($temp as $row)
					{
						if ($row->cat_state == 1)
						{
							$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid, $row->language));
							$row->images = json_decode($row->images);
							if (! empty($row->images->image_intro)) {
					            $row->images->image_fulltext         = $row->images->image_intro;
					            $row->images->image_fulltext_alt     = $row->images->image_intro_alt;
					            $row->images->image_fulltext_caption = $row->images->image_intro_caption;
					            $row->images->float_fulltext         = $row->images->float_intro;
					        }
					        unset($row->images->image_intro);
					        unset($row->images->image_intro_alt);
					        unset($row->images->image_intro_caption);
					        unset($row->images->float_intro);

					        if (empty($row->images->image_fulltext)) {
					        	$row->images->image_fulltext = "/images/default_image.jpg";
					        	$row->images->image_fulltext_alt = "Think Healthier - Image coming soon";
					        }


							$related[] = $row;
						}
					}
				}

				unset ($temp);
			}
		}

		return $related;
	}
}
