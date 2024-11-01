<?php
/** @version 	$Id: lib.plugin.php 919 2011-08-10 09:44:56Z xagero $ */
if (!class_exists('PluginAbstract')) : 
class PluginAbstract
{
	protected $_config;
	public $viewIndexAll;
	public $viewIndex;
	public $pluginHook = null;
	
	public function __construct()
	{
		$this->_config = array();
	} // end func __construct
	
	public function configure($config)
	{
		$this->_config = $config;
	} // end func confugure
	
	// }}}
	// {{{ init
	
	/**
	 * init
	 */
	public function init()
	{
		$hook = $this->_config['plugin-hook'];
		register_activation_hook($hook, array($this,'_hook_activate'));
		register_deactivation_hook($hook, array($this,'_hook_deactivate'));
		register_uninstall_hook($hook, array($this,'_hook_uninstall'));
		
		add_filter( 'plugin_action_links', array($this,'_hook_plugin_action_links'),10,2);
		add_filter( 'query_vars', array($this,'_hook_query_vars'));
		add_filter( 'rewrite_rules_array', array($this,'_hook_rewrite_rules_array'));
		add_filter( 'init', array($this,'_hook_init'));
		add_filter( 'widgets_init', array($this,'_hook_widgets_init'));
		add_filter( 'save_post', array($this,'_hook_save_post'));
		add_filter( 'contextual_help', array($this,'_hook_contextual_help'),10,3);
		add_filter( 'wp_head', array($this, '_hook_wp_head'));
		add_filter( 'wp_footer', array($this, '_hook_wp_footer'));
		
		if (is_admin()) {
			add_filter('add_meta_boxes', array($this,'_hook_add_meta_boxes'));
			add_filter('admin_init', array($this,'_hook_admin_init'));
			add_filter('admin_menu', array($this,'_hook_admin_menu'));
		}
	}

	// }}}
	// {{{ manage_options

	/**
	 * display
	 */
	public function display()
	{
		if (!is_admin()) return false;
		if (!current_user_can('manage_options')) {
			wp_die('You do not have sufficient permissions to access this page.');
		}
		$view = (isset($_REQUEST['view']) ? $_REQUEST['view'] : 'default');
		$view = str_replace(' ','',ucwords(str_replace('-',' ',$view)));
		$methodName = '_'.$view.'View';
		if (method_exists($this,$methodName)) {
			return call_user_method($methodName,$this);
		} else {
			return $this->_defaultView();
		}
	} // end func display
	
	/**
	 * Func displayAboutClub
	 */
	public function displayAboutClub()
	{
		include_once ($this->_config['meta']['wp_plugin_dir'] . '/inc/view-about-us.php');
	} // end func displayAboutClub
	
	// }}}
	// {{{

	/**
	 * _displayRSS
	 * 
	 * @param string $url
	 * @param int $num_items
	 */
	protected function _displayRSS( $url, $num_items = -1 )
	{
		$rss = fetch_rss( $url );
		print '<h4>From the News:</h4>' . chr(13) . chr(10);
		if ( $rss ) {
			echo '<ul>';
			if ( $num_items !== -1 ) {
				$rss->items = array_slice( $rss->items, 0, $num_items );
			}
			if ($rss->items){
				foreach ( (array) $rss->items as $item ) {
					$date = new DateTime($item['pubdate']);
					printf(
						'<li><div class="date">%4$s</div><div class="thethefly-news-item">%2$s</div></li>',
						esc_url( $item['link'] ),
						//esc_attr( strip_tags( $item['description'] ) ),
						( $item['description']),
						esc_html( $item['title'] ),
						$date->format('D, d M Y')
					);
				}
			} else {				
				echo '<li>Unfortunately the news channel is temporarily closed</li>';
			}
			echo '</ul>';
		} else {
			_e( 'An error has occurred, which probably means the feed is down. Try again later.' );
		}
	} // end func _displayRSS
	
	// }}}
	// {{{ _hook_admin_menu
	
	/**
	 * _hook_admin_menu
	 */
	public function _hook_admin_menu()
	{
		global $menu;

		$flag['makebox'] = true;
		if (is_array($menu)) foreach ($menu as $e) {
			if (isset($e[0]) && (in_array($e[0], array('TheThe Fly','TheTheFly')))) {
				$flag['makebox'] = false;
				break;
			}
		}
		
		if ($flag['makebox']) {
			$icon_url = $title = $this->_config['meta']['wp_plugin_dir_url'] . 'style/admin/images/favicon.ico';
			add_menu_page('TheThe Fly', 'TheThe Fly', 'edit_theme_options', 'thethefly', 'TheThe_makeAdminPage', $icon_url, 63);
			$hook = add_submenu_page('thethefly', 'TheThe Fly: About the Club', 'About the Club', 'manage_options', 'thethefly', 'TheThe_makeAdminPage'); 
			add_filter( 'admin_print_styles-' . $hook, array($this,'_hook_admin_print_styles')); 
		}
		
		$title = $this->_config['meta']['Name'];
		$title = trim(str_replace('TheThe', null, $title));
		$shortname = $this->_config['shortname'];
		$this->pluginHook = add_submenu_page('thethefly', $title,$title,'manage_options',$shortname,array($this,'display'));
		add_filter( 'admin_print_styles-' . $this->pluginHook , array($this,'_hook_admin_print_styles')); 
	} // end func _hook_admin_menu
	
	// }}}
	// {{{ _hook_admin_print_styles
	
	/**
	 * _hook_admin_print_styles
	 */
	public function _hook_admin_print_styles()
	{
		wp_admin_css( 'nav-menu' );
		$interface_css = $this->_config['meta']['wp_plugin_dir_url'] . '/style/admin/interface.css';
		wp_enqueue_style( 'thethefly-plugin-panel-interface', $interface_css );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'post' );
	} // end func _hook_admin_print_styles
	
	// }}}
	// {{{ _hook_plugin_action_links
	
	/**
	 * _hook_plugin_action_links
	 * @param array $links
	 * @param string $file
	 */
	public function _hook_plugin_action_links($links, $file)
	{
		if ($file == $this->_config['plugin-hook']) {
			array_unshift($links, '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=' . $this->_config['shortname'] . '">Settings</a>');
		}
		return $links;
	} // end func _hook_plugin_action_links
	
	// }}}
	// {{{ getCurrentViewIndex
	
	/**
	 * Function getCurrentViewIndex
	 */
	public function getCurrentViewIndex()
	{
		$this->viewIndex = (isset($_REQUEST['view']) && isset($this->viewIndexAll[$_REQUEST['view']]))
			? $_REQUEST['view'] : 'overview';
		return $this->viewIndex;
	} // end func getCurrentViewIndex
	
	// }}}
	// {{{ getTabURL
	
	/**
	 * Function getTabURL
	 * @param string $viewIndex
	 * @return string
	 */
	public function getTabURL($viewIndex = null)
	{
		if (!$viewIndex) $viewIndex = 'overview';
		return 'admin.php?page=' . $this->_config['shortname'] . '&amp;view=' . $viewIndex;
	} // end func getTabURL
	
	// }}}
	// {{{ printTabsURL
	
	/**
	 * Function printTabsURL
	 * @param string $viewIndex
	 */
	public function printTabsURL($viewIndex = null)
	{
		print $this->getTabURL($viewIndex);
	} // end func printTabsURL
	
	// }}}

	public function _defaultView() {}
	public function _hook_wp_head() {}
	public function _hook_wp_footer() {}
	public function _hook_init() {}
	public function _hook_widgets_init() {}
	public function _hook_query_vars($args) { return $args; }
	public function _hook_contextual_help($contextual_help, $screen_id, $screen) { return $contextual_help; }
	public function _hook_rewrite_rules_array($rules) { return $rules; }
	public function _hook_save_post($post_id) {}
	public function _hook_add_meta_boxes() {}
	public function _hook_admin_init() {}
	public function _hook_activate() {}
	public function _hook_deactivate() {}
	public function _hook_uninstall() {}

} // end class PluginAbstract
endif;