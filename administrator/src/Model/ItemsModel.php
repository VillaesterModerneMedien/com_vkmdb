<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VkmdbNamespace\Component\Vkmdb\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Table\Table;

/**
 * Methods supporting a list of items records.
 *
 * @since  1.0.0
 */
class ItemsModel extends ListModel
{	
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
                'published', 'a.published',
                'created', 'a.created',
                'created_by', 'a.created_by',
				'catid', 'a.catid', 'category_id', 'category_title',
				'level', 'c.level',
			);
		}

		parent::__construct($config);
	}
	

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Item', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}
	
	/**
	 * Returns an object list
	 *
	 * @param   string  $query       The query
	 * @param   int     $limitstart  Offset
	 * @param   int     $limit       The number of records
	 *
	 * @return  array
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$listOrder = $this->getState('list.ordering', 'a.id');
		$listDirn  = $this->getState('list.direction', 'asc');
		
		$query->order($this->_db->quoteName($listOrder) . ' ' . $this->_db->escape($listDirn));

		// Process pagination.
		$result = parent::_getList($query, $limitstart, $limit);
		return $result;
	}
	
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');
		$query->from($db->quoteName('#__vkmdb_items', 'a'));
        
		// Join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join(
				'LEFT',
				$db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid')
			);
  
		
        // Filter by published state
		$published = (string) $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('a.published') . ' = :published');
			$query->bind(':published', $published, ParameterType::INTEGER);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->quoteName('a.published') . ' = 0 OR ' . $db->quoteName('a.published') . ' = 1)');
		}
        
		// Filter by search in title or note or id:.
		$search = $this->getState('filter.search');
        
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$search = substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = :id');
				$query->bind(':id', $search, ParameterType::INTEGER);
			}
			else
			{
                $search = '%' . trim($search) . '%';
				$query->where('(' .
                        '(' . $db->quoteName('a.title') . ' LIKE :title)' . ' OR ' .
                        '(' . $db->quoteName('a.alias') . ' LIKE :alias)' . ' OR ' .
                        '(' . $db->quoteName('a.content') . ' LIKE :content)' .
                    ')'
                );
                $query->bind(':title', $search);
				$query->bind(':alias', $search);
                $query->bind(':content', $search);
			}
		}

		// Filter by categories and by level
		$categoryId = $this->getState('filter.category_id', array());
		$level = $this->getState('filter.level');

		if (!is_array($categoryId))
		{
			$categoryId = $categoryId ? array($categoryId) : array();
		}

		// Case: Using both categories filter and by level filter
		if (count($categoryId))
		{
			$categoryId = ArrayHelper::toInteger($categoryId);
			$categoryTable = Table::getInstance('Category', 'JTable');
			$subCatItemsWhere = array();

			// @todo: Convert to prepared statement
			foreach ($categoryId as $filter_catid)
			{
				$categoryTable->load($filter_catid);
				$subCatItemsWhere[] = '(' .
					($level ? 'c.level <= ' . ((int) $level + (int) $categoryTable->level - 1) . ' AND ' : '') .
					'c.lft >= ' . (int) $categoryTable->lft . ' AND ' .
					'c.rgt <= ' . (int) $categoryTable->rgt . ')';
			}

			$query->where('(' . implode(' OR ', $subCatItemsWhere) . ')');
		}

		// Case: Using only the by level filter
		elseif ($level)
		{
			$query->where($db->quoteName('c.level') . ' <= :level');
			$query->bind(':level', $level, ParameterType::INTEGER);
		}
        
		return $query;
	}

    /**
     * Get the filter form
     *
     * @param   array    $data      data
     * @param   boolean  $loadData  load current data
     *
     * @return  \Joomla\CMS\Form\Form|null  The Form object or null if the form can't be found
     *
     * @since   1.0.0
     */
    public function getFilterForm($data = array(), $loadData = true)
    {
        $form = parent::getFilterForm($data, $loadData);

        $params = ComponentHelper::getParams('com_vkmdb');

        if (!$params->get('workflow_enabled')) {
            $form->removeField('stage', 'filter');
        } else {
            $ordering = $form->getField('fullordering', 'list');

            $ordering->addOption('JSTAGE_ASC', ['value' => 'ws.title ASC']);
            $ordering->addOption('JSTAGE_DESC', ['value' => 'ws.title DESC']);
        }

        return $form;
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
	protected function populateState($ordering = 'a.id', $direction = 'asc')
	{
		// Load the filter state.
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		
		// List state information.
		parent::populateState($ordering, $direction);
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
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . serialize($this->getState('filter.category_id'));
		$id .= ':' . $this->getState('filter.level');

		return parent::getStoreId($id);
	}
    
}
