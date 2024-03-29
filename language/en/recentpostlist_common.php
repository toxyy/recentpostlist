<?php
/**
 *
 * Recent Post List
 *
 * @copyright (c) 2019, toxyy, https://github.com/toxyy
 * @license       GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'RECENTPOSTLIST'							=> 'Recent Post List',
	'RECENTPOSTLIST_EXPLAIN'					=> 'Scroll down for more topics',
	'RECENTPOSTLIST_BY'							=> '&nbsp;&#45;&nbsp;By&nbsp;',
	'RECENTPOSTLIST_LIMIT'						=> 'Posts to display',
	'RECENTPOSTLIST_LIMIT_EXPLAIN'				=> 'Set the posts block per each row (4 default)',

	'RECENTPOSTLIST_TIME'						=> 'Topic Fetch Limit',
	'RECENTPOSTLIST_TIME_EXPLAIN'				=> 'Set the amount in days for how far back to fetch topics from the database. 0 disables the limit.',

	'RECENTPOSTLIST_TYPE'						=> 'Sort order',
	'RECENTPOSTLIST_TYPE_EXPLAIN'				=> 'Display first or last post from recent topics',

	'RECENTPOSTLIST_EXCLUDED_FORUMS'			=> 'Exclude forums from the Recent Post List',
	'RECENTPOSTLIST_EXCLUDED_FORUMS_EXPLAIN'	=> 'Hold shift or control for selecting more forums',

	'RECENTPOSTLIST_READ_MORE'					=> 'READ MORE',
	'RECENTPOSTLIST_BACK_TO_START'				=> 'BACK TO START',
	'RECENTPOSTLIST_VIEW_MORE_TOPICS'			=> 'VIEW MORE TOPICS',
));
