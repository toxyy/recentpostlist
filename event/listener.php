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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	protected $config;
	protected $template;
	protected $phpbb_container;
	protected $phpbb_root_path;
	protected $php_ext;

	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, Container $phpbb_container, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->template = $template;
		$this->phpbb_container = $phpbb_container;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'	=> 'load_language_on_setup',
			'core.page_header'	=> 'page_header',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'toxyy/recentpostlist',
			'lang_set' => 'recentpostlist_common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function page_header($event)
	{
		$display = $this->phpbb_container->get('toxyy.recentpostlist.main');
		$post_list = $display->handle('index.html');
		foreach ($post_list as $key => $value)
		{
			$this->template->assign_block_vars('rpcats', array(
				'FORUM_NAME'	=> $key == 0 ? "Most Recent" : $post_list[$key][0]['forum_name'],
			));
			foreach ($post_list[$key] as &$row)
			{
				strip_bbcode($row['post_text']);
				$row['post_text'] = (version_compare($this->config['version'], '3.2.*', '<')) ? censor_text($row['post_text']) : htmlentities(censor_text($row['post_text']));
				$row['post_text'] = (utf8_strlen($row['post_text']) > 50) ? utf8_substr($row['post_text'], 0, 50) . '&#91;&hellip;&#93;' : $row['post_text'];

				$this->template->assign_block_vars('rpcats.recentpostlist', array(
					'U_TOPIC'					=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}?f={$row['forum_id']}&amp;t={$row['topic_id']}"),
					'U_FORUM'					=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}?f={$row['forum_id']}"),
					'U_LAST_POST'				=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}?p={$row['topic_last_post_id']}") . "#p{$row['topic_last_post_id']}",
					'TOPIC_AUTHOR'				=> ($row["topic_{$this->config['recentpostlist_type']}_poster_colour"]) ?
						("<a style=\"font-weight:700; color:#{$row["topic_{$this->config['recentpostlist_type']}_poster_colour"]}\"
							href=\"" . append_sid("{$this->root_path}memberlist.{$this->php_ext}?mode=viewprofile&u={$row['user_id']}") . "\">{$row['username']}</a>") :
						$row['username'],
					'TOPIC_TITLE'				=> censor_text($row['topic_title']),
					'FORUM_NAME'				=> $row['forum_name'],
					'FP_EXCERPT'				=> $row['post_text'],
				));
			}
		}
	}
}
