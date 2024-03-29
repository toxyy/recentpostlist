<?php
/**
 *
 * Recent Post List
 *
 * @copyright (c) 2019, toxyy, https://github.com/toxyy
 * @license       GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace toxyy\recentpostlist\event;

/**
 * @ignore
 */

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class admin_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_board_config_edit_add' => 'acp_board_config_edit_add',
		);
	}

	protected $config;
	protected $request;

	public function __construct(\phpbb\config\config $config, \phpbb\request\request $request)
	{
		$this->config = $config;
		$this->request = $request;
	}

	public function acp_board_config_edit_add($event)
	{
		if ($event['mode'] == 'settings')
		{
			if ($this->request->is_set_post('submit'))
			{
				$this->config->set('recentpostlist_excluded_forums', json_encode($this->request->variable('recentpostlist_excluded_forums', array(0))));
			}

			$display_vars = $event['display_vars'];
			$submit_key = array_search('ACP_SUBMIT_CHANGES', $display_vars['vars']);
			$submit_legend_number = substr($submit_key, 6);
			$display_vars['vars']['legend' . $submit_legend_number] = 'RECENTPOSTLIST';
			$new_vars = array(
				'recentpostlist_excluded_forums'       => array('lang' => 'RECENTPOSTLIST_EXCLUDED_FORUMS', 'validate' => 'string', 'type' => 'custom', 'function' => __NAMESPACE__ . '\admin_listener::forum_select', 'explain' => true),
				'legend' . ($submit_legend_number + 1) => 'ACP_SUBMIT_CHANGES',
			);
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $new_vars, array('after' => $submit_key));
			$event['display_vars'] = $display_vars;
		}
	}

	static function forum_select($value, $key)
	{
		global $config, $db;
		$forum_excluded_array = json_decode($config['recentpostlist_excluded_forums'], true);
		$forum_options = '';

		$sql = 'SELECT forum_id, forum_name FROM ' . FORUMS_TABLE . ' WHERE forum_type = 1 ORDER BY forum_name';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$forum_options .= '<option value="' . $row['forum_id'] . '"' .
				((in_array($row['forum_id'], $forum_excluded_array)) ? ' selected="selected"' : '') . '>' . $row['forum_name'] . '</option>';
		}
		return '<select multiple style="width:140px;" size="10" name="recentpostlist_excluded_forums[]" id="recentpostlist_excluded_forums" title="Exclude forums">' . $forum_options . '</select>';
	}
}
