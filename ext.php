<?php
/**
 *
 * Recent Post List
 *
 * @copyright (c) 2019, toxyy, https://github.com/toxyy
 * @license       GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace toxyy\recentpostlist;

use phpbb\extension\base;

class ext extends base
{
	/**
	 * phpBB 3.2.x and PHP 7+
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');

		// check phpbb and phpb versions
		$is_enableable = (phpbb_version_compare($config['version'], '3.2', '>=') && version_compare(PHP_VERSION, '7', '>='));
		return $is_enableable;
	}

	function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				if (empty($old_state))
				{
					$this->container->get('user')->add_lang_ext('toxyy/recentpostlist', 'info_acp_recentpostlist');
					$this->container->get('template')
						->assign_var('L_EXTENSION_ENABLE_SUCCESS', $this->container->get('user')->lang['EXTENSION_ENABLE_SUCCESS'] .
							(isset($this->container->get('user')->lang['recentpostlist_NOTICE']) ?
								sprintf($this->container->get('user')->lang['recentpostlist_NOTICE'],
									$this->container->get('user')->lang['ACP_CAT_GENERAL'],
									$this->container->get('user')->lang['ACP_BOARD_SETTINGS'],
									$this->container->get('user')->lang['ACP_RECENTPOSTS_TITLE']) : ''))
					;
				}

				// Run parent enable step method
				return parent::enable_step($old_state);

			break;

			default:
				// Run parent enable step method
				return parent::enable_step($old_state);

			break;
		}
	}
}
