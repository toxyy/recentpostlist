<?php
/**
 *
 * Recent Post List
 *
 * @copyright (c) 2019, toxyy, https://github.com/toxyy
 * @license       GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
 * DO NOT CHANGE
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
	'ACP_RECENTPOSTS_TITLE'			=> 'Recent Post List',
	'RECENTPOSTLIST_NOTICE'			=> '<div class="phpinfo"><p>The settings for this extensions are in <strong>%1$s » %2$s » %3$s</strong>.</p></div>',
));

