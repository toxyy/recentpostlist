<?php
/**
 *
 * Recent Post List
 *
 * @copyright (c) 2019, toxyy, https://github.com/toxyy
 * @license       GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace toxyy\recentpostlist\migrations\v31x;

class m1_initial_config extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	/**
	 * Add or update data in the database
	 *
	 * @return array Array of table data
	 * @access public
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('recentpostlist_limit', 3)),
			array('config.add', array('recentpostlist_type', 'last')),
			array('config.add', array('recentpostlist_excluded_forums', '[]')),
		);
	}
}
