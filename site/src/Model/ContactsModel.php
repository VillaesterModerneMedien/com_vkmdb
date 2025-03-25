<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VkmdbNamespace\Component\Vkmdb\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

/**
 * Contacts model class.
 *
 * @since  1.0.0
 */
class ContactsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'id', 'a.id',
				'title', 'a.title',
                'catid', 'a.catid',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 */
	public function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();

		 // Prepare the data.
        foreach ($items as $item)
        {
            $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
        }

		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string    An SQL query
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		$user = Factory::getApplication()->getIdentity();
		$groups = $user->getAuthorisedViewLevels();

		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'))
			->from($db->quoteName('#__vkmdb_contacts', 'a'));
		$query->innerJoin($db->quoteName('#__categories', 'c') . ' ON c.id = a.catid')
			->whereIn($db->quoteName('c.access'), $groups);

		// Filter by category.
		if ($categoryId = $this->getState('category.id'))
		{
			$query->where($db->quoteName('a.catid') . ' = :catid');
			$query->bind(':catid', $categoryId, ParameterType::INTEGER);
		}

		$query->select('c.published as cat_published, c.published AS parents_published')
			->where('c.published = 1');
        
		// Filter by state/published
		$query->whereIn($db->quoteName('a.published'), [1,2]);

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.title')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_vkmdb');

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		$orderCol = $app->input->get('filter_order', 'title');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'title';
		}

		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');

		if (!in_array(strtoupper($listOrder), ['ASC', 'DESC', '']))
		{
			$listOrder = 'ASC';
		}

		$this->setState('list.direction', $listOrder);

		// Load the parameters.
		$this->setState('params', $params);
	}
}
