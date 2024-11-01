<?php 
/*
Plugin Name: TheThe Marquee
Plugin URI: http://thethefly.com/wp-plugins/thethe-marquee/
Description: Add "Marquee" - a scrolling area of text - on your web page. TheThe Marquee WordPress plugin creates both the TheThe Marquee widget and [thethe-marquee] shortcode.

Version: 1.0.0
Author: TheThe Fly
Author URI: http://www.thethefly.com
*/
/**
 * @version 	$Id: marquee.php 917 2011-08-10 09:05:44Z xagero $
 */
/**
 * Init classes,func and libs
 */
include_once ABSPATH . WPINC . '/rss.php';
require_once ABSPATH . '/wp-admin/includes/plugin.php';
require_once realpath(dirname(__FILE__) . '/lib/lib.core.php');
TheTheFly_require(dirname(__FILE__) . '/lib', array('func.','lib.'));
TheTheFly_require(dirname(__FILE__) . '/lib', array('class.','widget.'));

/**
 * Current plugin config
 * @var array
 */
$Plugin_Config = array(
	'shortname' => 'marquee',
	'plugin-hook' => 'thethe-marquee/marquee.php',
	'options' => array(
		'default' => array(
			'limit' => 3,
			'categories' => array(),
			//'tags' => array(),
			'width' => 600,
			'height' => 20,
			'showtime' => 5,
			'stoptime' => 0,
			'move' => 'easeInOutQuint',
			'effect' => null, // null - no effect, 1 - left, 2 - right, 3 - up, 4 - down
			'date-format' => 'F j, Y g:i a',
		),
		'style' => array(
			'custom-css' => 'div.marquee {}' . chr(10) . 'div.marquee div.mul {}' . chr(10)
		)
	),
	'requirements' => array('wp' => '3.1')
) + array('meta' => get_plugin_data(realpath(__FILE__)) + array(
	'wp_plugin_dir' => dirname(__FILE__),
	'wp_plugin_dir_url' => plugin_dir_url(__FILE__)
)) + array(
	'clubpanel' => array(),
	'adminpanel' => array('sidebar.donate' => true)
);

/**
 * @var PluginMarquee
 */
$GLOBALS['PluginMarquee'] = new PluginMarquee();

/**
 * Configure
 */
$GLOBALS['PluginMarquee']->configure($Plugin_Config);

/**
 * Init
 */
TheTheFly_require(dirname(__FILE__),array('init.'));
$GLOBALS['PluginMarquee']->init();

/** @todo fixme */
if (!function_exists('TheThe_makeAdminPage')) {
	function TheThe_makeAdminPage() {
		$GLOBALS['PluginMarquee']->displayAboutClub();
	}
}