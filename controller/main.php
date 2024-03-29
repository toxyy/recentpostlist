<?php
/**
 *
 * Recent Post List
 *
 * @copyright (c) 2019, toxyy, https://github.com/toxyy
 * @license       GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace toxyy\recentpostlist\controller;

use Symfony\Component\DependencyInjection\Container;

class main
{
	protected $cache;
	protected $db;
	protected $auth;
	protected $config;

	public function __construct(\phpbb\cache\service $cache, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\config\config $config)
	{
		$this->cache = $cache;
		$this->db = $db;
		$this->auth = $auth;
		$this->config = $config;
	}

	public function handle($route = 'index.html')
	{
		$pid = ($this->config['recentpostlist_type'] == 'last') ? 't.topic_last_poster_id' : 't.topic_poster';

		$forum_excluded_array = json_decode($this->config['recentpostlist_excluded_forums'], true);
		$forums = array_keys($this->auth->acl_getf('!f_read', true));
		$forums = (!empty($forum_excluded_array)) ? array_unique(array_merge($forum_excluded_array, $forums)) : $forums;
		$sql_where = (!empty($forums)) ? $this->db->sql_in_set('t.forum_id', $forums, true) . ' AND p.topic_id = t.topic_id ' : 'p.topic_id = t.topic_id ';

		$sql_ary = [
			'SELECT'			=> 'u.username, u.user_id, u.user_rank, p.post_id, p.post_text, t.topic_id, t.forum_id, t.topic_title,
									t.topic_last_post_time, t.topic_first_poster_colour, t.topic_last_post_id, t.topic_last_poster_name,
									t.topic_last_poster_colour',
			'FROM'				=> [
				TOPICS_TABLE	=> 't',
				POSTS_TABLE		=> 'p',
				USERS_TABLE		=> 'u',
			],
			'WHERE'				=> "$sql_where
                AND p.post_visibility = 1
                AND u.user_id = $pid
                AND p.post_id = t.topic_{$this->config['recentpostlist_type']}_post_id",
			'ORDER_BY'			=> 't.topic_last_post_time DESC',
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, $this->config['recentpostlist_limit'], 0);
		$rows = $this->db->sql_fetchrowset($result);

		$cache_id = "_toxyy_recenposts_time" . max(array_column($rows, 'topic_last_post_time'));
		$cache_list = $this->cache->get($cache_id);

		$recent_posts[0] = $rows;
		if($cache_list === false)
		{
			$sql = "SELECT f.forum_id, f.forum_name, f.forum_posts_approved
					FROM " . FORUMS_TABLE . " AS f
					ORDER BY f.forum_posts_approved DESC";

			$result = $this->db->sql_query($sql);
			$forum_ids = $this->db->sql_fetchrowset($result);

			$count = 1;
			foreach($forum_ids as $key => $forum_ary)
			{
				$forum_ids[$forum_ary['forum_id']] = $forum_ids[$key];
				$forum_id = $forum_ary['forum_id'];

				$sql_ary['WHERE'] = "$sql_where
					AND t.forum_id = $forum_id
					AND p.post_visibility = 1
					AND u.user_id = $pid
					AND p.post_id = t.topic_{$this->config['recentpostlist_type']}_post_id";

				$sql = $this->db->sql_build_query('SELECT', $sql_ary);
				$result = $this->db->sql_query_limit($sql, $this->config['recentpostlist_limit'], 0);
				$rows = $this->db->sql_fetchrowset($result);
				foreach($rows as &$row)
					$row['forum_name'] = $forum_ary['forum_name'];

				$recent_posts[$forum_id] = $rows;
				$this->db->sql_freeresult($result);
				if($count++ === 10) break;
			}
			foreach($recent_posts[0] as &$row)
				$row['forum_name'] = $forum_ids[$row['forum_id']]['forum_name'];

			$this->cache->put($cache_id, $recent_posts, 604800);
		}
		else
		{
			$recent_posts = $cache_list;
		}

		if ($route == 'index.html')
		{
			return $recent_posts;
		}
		else
		{
			echo 'redacted';
			$this->db->sql_freeresult($result);
			exit();
		}
	}
}
