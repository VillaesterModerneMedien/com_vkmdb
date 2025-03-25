<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VkmdbNamespace\Component\Vkmdb\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;

/**
 * Vkmdb Component Category Tree
 *
 * @since  1.0.0
 */
class Category extends Categories
{
	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 *
	 * @since   1.0.0
	 */
	public function __construct($options = [])
	{
		$options['table']      = '#__vkmdb_contacts';
		$options['extension']  = 'com_vkmdb';
		$options['statefield'] = 'published';

		parent::__construct($options);
	}
}
