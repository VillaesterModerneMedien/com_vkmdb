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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;

/**
 * Contact model for the Joomla Vkmdb component.
 *
 * @since  1.0.0
 */
class ContactModel extends BaseDatabaseModel
{
	/**
	 * @var string item
	 */
	protected $_item = null;

	/**
	 * Gets a contact
	 *
	 * @param   integer  $pk  Id for the contact
	 *
	 * @return  mixed Object or null
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		$app = Factory::getApplication();
		$pk = $app->input->getInt('id');

		if ($this->_item === null)
		{
			$this->_item = [];
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db    = $this->getDatabase();
				$query = $db->getQuery(true);

				$query->select('*')
					->from($db->quoteName('#__vkmdb_contacts', 'a'))
					->where('a.id = ' . (int) $pk);

				$db->setQuery($query);
				$data = $db->loadObject();

				if (empty($data))
				{
					throw new \Exception(Text::_('COM_VKMDB_ERROR_CONTACT_NOT_FOUND'), 404);
				}

				$this->_item[$pk] = $data;
			}
			catch (\Exception $e) {
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

		$this->setState('contact.id', $app->input->getInt('id'));
		$this->setState('params', $app->getParams());
	}
}
