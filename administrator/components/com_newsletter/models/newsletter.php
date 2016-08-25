<?php
/**
 * @version    CVS: 3.4.2
 * @package    Com_Newsletter
 * @author     Aleksandar Vrhovac <avrhovac@ogosense.com>
 * @copyright  2016 Aleksandar Vrhovac
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Newsletter model.
 *
 * @since  1.6
 */
class NewsletterModelNewsletter extends JModelAdmin
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_NEWSLETTER';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_newsletter.newsletter';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Newsletter', $prefix = 'NewsletterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_newsletter.newsletter', 'newsletter',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.newsletter.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		return $item;
	}

	/**
	 * Method to duplicate an Newsletter
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user = JFactory::getUser();

		// Access checks.
		if (!$user->authorise('core.create', 'com_newsletter'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				if (!$table->check())
				{
					throw new Exception($table->getError());
				}
				

				// Trigger the before save event.
				$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

				if (in_array(false, $result, true) || !$table->store())
				{
					throw new Exception($table->getError());
				}

				// Trigger the after save event.
				$dispatcher->trigger($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new Exception($table->getError());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  Table Object
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		//die('<pre>'.print_r($table, true).'</pre>');
		$articles = array( 
						$table->ftrdArticleID,
						$table->article1ID,
						$table->article2ID,
						$table->article3ID );


		//die('<pre>'.print_r($table, true).'</pre>');

		foreach ($articles as $key => $article) {
			if ($article == 0) continue;
			$db = JFactory::getDbo();
			$query = "INSERT INTO `#__newsletter_history` 
						(`id`, 
						`type`, 
						`article_recipe_id`, 
						`timestamp`, 
						`position`) 
					VALUES 
						(NULL, 
						1, 
						".$article.", 
						CURRENT_TIMESTAMP, 
						".$key.");";
			$db->setQuery( $query );
			$db->execute();
			//die('<pre>'.print_r($query, true).'</pre>');
		}

		if ($table->ftrdRecipeID != 0) {
			$db = JFactory::getDbo();
			$query = "INSERT INTO `#__newsletter_history` 
						(`id`, 
						`type`, 
						`article_recipe_id`, 
						`timestamp`, 
						`position`) 
					VALUES 
						(NULL, 
						2, 
						".$table->ftrdRecipeID.", 
						CURRENT_TIMESTAMP, 
						0);";
			$db->setQuery( $query );
			$db->execute();

		}

		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM DailyHealthyNews');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	public function getLatestDate(){

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('DATE_FORMAT(DATE_ADD(date, INTERVAL +1 DAY), \'%m/%d/%Y\')')
			->from($db->quoteName('DailyHealthyNews'))
			->order('date DESC');
		$db->setQuery($query, 0, 1);
		$result = $db->loadRow();

		$return = explode('/', $result[0]);
		$return = date('Y-m-d' ,strtotime($return[2].'-'.$return[0].'-'.$return[1]));

		if($return < date('Y-m-d'))
			$return = date('Y-m-d');
		else
			$return = $result[0];

		return $return;
	}

	public function getArticles($data){

		$return = array(
			'featured_article'	=> 0,
			'article2'			=> 0,
			'article3'			=> 0,
			'article4'			=> 0
		);

		if (empty($data->ftrdArticleID)) $data->ftrdArticleID = 0;
		if (empty($data->article1ID)) $data->article1ID = 0;
		if (empty($data->article2ID)) $data->article2ID = 0;
		if (empty($data->article3ID)) $data->article3ID = 0;

		$return = array(
			'featured_article'	=> $data->ftrdArticleID,
			'article2'			=> $data->article1ID,
			'article3'			=> $data->article2ID,
			'article4'			=> $data->article3ID
		);


		//echo('/models/newsletter.php<pre>'.print_r($data, true).'</pre>');

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('id ,title')
			->from($db->quoteName('#__content'))
			->where($db->quoteName('id').' IN ('.implode(',', $return).')');
		$db->setQuery($query);
		$result = $db->loadObjectList();

		$temp = array();
		foreach ($result as $key => $value) {
			$temp[$value->id] = $value->title;
		}

		foreach ($return as $key => &$value) {
			if(isset($temp[$value]))
				$value = $temp[$value];
			else
				$value = 'No Article Selected';
		}

		$query = $db->getQuery(true);

		if (empty($data->ftrdRecipeID)) $data->ftrdRecipeID = 0;

		$query->select('id, title')
			->from($db->quoteName('#__yoorecipe'))
			->where($db->quoteName('id').' = '.$data->ftrdRecipeID);
		$db->setQuery($query);
		$recipe = $db->loadObject();

		if($recipe->id == 0)
			$return['featured_recipe'] = 'No Recipe Selected';
		else
			$return['featured_recipe'] = $recipe->title;

		//die('<pre>'.print_r("OGO SENSE TEST 111", true).'</pre>');

		return $return;
	}
}
