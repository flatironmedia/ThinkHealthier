<?php

/**
 * @version    CVS: 3.4.2
 * @package    Com_Newsletter
 * @author     Aleksandar Vrhovac <avrhovac@ogosense.com>
 * @copyright  2016 Aleksandar Vrhovac
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Newsletter records.
 *
 * @since  1.6
 */
class NewsletterModelThdailynewslettermanager extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'date', 'a.`date`',
				'subject', 'a.`subject`',
				'featured_article', 'a.`featured_article`',
				'featured_recipe', 'a.`featured_recipe`',
				'article2', 'a.`article2`',
				'article3', 'a.`article3`',
				'article4', 'a.`article4`',
				'newsletter', 'a.`newsletter`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_newsletter');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.date', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`DailyHealthyNews` AS a');


		/*// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');*/

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.`date` LIKE ' . $search . ' )');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		if($items)
			$items = $this->processItems($items);

		return $items;
	}

	public function processItems($data){
		$articleIDs = array();
		$recipeIDs = array();
		foreach ($data as $item){

			$articleIDs[] = $item->ftrdArticleID;
			$articleIDs[] = $item->article1ID;
			$articleIDs[] = $item->article2ID;
			$articleIDs[] = $item->article3ID;
			
			$recipeIDs[] = $item->ftrdRecipeID;
		}
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);

		$query->select('id, title')
			->from($db->quoteName('#__content'))
			->where($db->quoteName('id').' IN ('.implode(',', $articleIDs).')');
		$db->setQuery($query);
		//die('<pre>'.print_r($db, true).'</pre>');
		$articles = $db->loadObjectList();
		
		$query = $db->getQuery(true);

		$query->select('id, title')
			->from($db->quoteName('#__yoorecipe'))
			->where($db->quoteName('id').' IN ('.implode(',', $recipeIDs).')');
		$db->setQuery($query);
		$recipes = $db->loadObjectList();

		$article_array = array();
		foreach ($articles as $value) {
			$article_array[$value->id] = $value->title;
		}

		$recipe_array = array();
		foreach ($recipes as $value) {
			$recipe_array[$value->id] = $value->title;
		}
		foreach ($data as &$item) {
			$item->featured_article = '<b>ID'.$item->ftrdArticleID.':</b> '.$article_array[$item->ftrdArticleID];
			$item->article2 = '<b>ID'.$item->article1ID.':</b> '.$article_array[$item->article1ID];
			$item->article3 = '<b>ID'.$item->article2ID.':</b> '.$article_array[$item->article2ID];
			$item->article4 = '<b>ID'.$item->article3ID.':</b> '.$article_array[$item->article3ID];
			$item->featured_recipe = '<b>ID'.$item->ftrdRecipeID.':</b> '.$recipe_array[$item->ftrdRecipeID];
		}

		return $data;
	}
}
