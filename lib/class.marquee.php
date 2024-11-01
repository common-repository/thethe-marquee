<?php
class PluginMarquee extends PluginAbstract
{
	protected $dataMarqueeIndex = 0;
	protected $dataMarqueeArray = array();
	
	// }}}
	// {{{ init

	public function init()
	{
		parent::init();
		$this->viewIndexAll = array(
			'overview' => array('title' => $this->_config['meta']['Name'] . '&nbsp;Overview'),
			'settings' => array('title' => $this->_config['meta']['Name'] . '&nbsp;Settings'),
			'style' => array('title' => $this->_config['meta']['Name'] . '&nbsp;Style'),
		);
		add_shortcode('thethe-marquee', array($this,'marquee'));
	} // end func init
	
	// }}}
	// {{{ config
	
	/**
	 * Config
	 * @return array|mixed
	 */
	public function config($ns = 'default')
	{	
		if (($ns != 'default') && $ns) {
			return stripslashes_deep(get_option(
				'_ttf-' . $this->_config['shortname'] . '-' . $ns,
				$this->_config['options'][$ns]
			));
		} else {
			return stripslashes_deep(get_option(
				'_ttf-' . $this->_config['shortname'],
				$this->_config['options']['default']
			));
		}
	} // end func config
	
	// }}}
	// {{{ marquee
	
	/**
	 * Show marquee
	 */
	public function marquee( $args = array() )
	{
		++$this->dataMarqueeIndex;
		
		$config = wp_parse_args($args,$this->config());
		$this->dataMarqueeArray[($this->dataMarqueeIndex)] = $config;
		
		$loop = new WP_Query(array(
			'showposts' => $config['limit']
		) + ( ($config['categories'])
				? array('category__in' => $config['categories'])
				: array()
		));
		
		$buff = "<div class='marquee' id='marquee-{$this->dataMarqueeIndex}'>" . chr(10);
		while ($loop->have_posts()) {
			$loop->the_post();
			$p = $loop->post;
			if (trim($config['date-format'])) {
				$title = date($config['date-format'],strtotime($p->post_date_gmt)) . ' - ' . $p->post_title;
			} else {
				$title = $p->post_title;
			}
			$buff.= "<div class='mul'><a href='" . get_permalink($p->ID) . "' title='{$p->post_title}'>{$title}</a></div>" . chr(10);
		}
		$buff.= '</div>' . chr(10);
		
		return $buff;
	} // end func marquee
	
	// }}}
	// {{{ _hook_activate()
	
	/**
	 * (non-PHPdoc)
	 * @see PluginAbstract::_hook_activate()
	 */
	public function _hook_activate()
	{
		update_option(
			'_ttf-' . $this->_config['shortname'],
			$this->_config['options']['default']
		);
		update_option(
			'_ttf-' . $this->_config['shortname'] . '-style',
			$this->_config['options']['style']
		);
	} // end func _hook_activate
	
	// }}}
	// {{{ _hook_init
	
	/**
	 * (non-PHPdoc)
	 * @see PluginAbstract::_hook_init()
	 */
	public function _hook_init()
	{
		if (is_admin()) return false;
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script(
			'jquery-easing',
			$this->_config['meta']['wp_plugin_dir_url'] . 'style/js/jquery.easing.1.3.js',
			array('jquery')
		);
		wp_enqueue_script(
			'thethe-marquee',
			$this->_config['meta']['wp_plugin_dir_url'] . 'style/js/jquery.marquee.js',
			array('jquery','jquery-easing')
		);
		
	} // end func _hook_init
	
	// }}}
	// {{{ _hook_widgets_init
	
	/**
	 * (non-PHPdoc)
	 * @see PluginAbstract::_hook_widgets_init()
	 */
	public function _hook_widgets_init()
	{
		register_widget('PluginMarquee_Widget');
	} // end func _hook_widgets_init
	
	// }}}
	// {{{ _hook_wp_head
	
	public function _hook_wp_head()
	{
		$config = $this->config('style');
		if ($config['custom-css']) {
			echo '<!-- TheThe Marquee v.' . $this->_config['meta']['Version'] . ' Custom CSS begin //-->' . chr(10);
			echo '<style type="text/css" media="screen">' . chr(10);
			echo stripslashes_deep($config['custom-css']) . chr(10);
			echo '</style>' . chr(10);
			echo '<!-- TheThe Marquee Custom CSS end //-->' . chr(10);
		}
	} // end func _hook_wp_head

	// }}}
	// {{{ _hook_wp_footer

	/**
	 * (non-PHPdoc)
	 * @see PluginAbstract::_hook_wp_footer()
	 */
	public function _hook_wp_footer()
	{
		if (is_admin()) return false;
		$config = wp_parse_args($this->config(),$this->_config['options']['default']);
		$eol = chr(10);
		echo '<script type="text/javascript">' . $eol;
		//echo '<!--' . $eol;
		echo 'jQuery(document).ready(function($) {' . $eol;
		if (is_array($this->dataMarqueeArray)) foreach ($this->dataMarqueeArray as $index=>$config) {
			switch ($config['effect']) {
				case 'left':
				case 1 : $side = chr(39) . 'left' . chr(39); break;
				case 'right':
				case 2 : $side = chr(39) . 'right' . chr(39); break;
				case 'up':
				case 3 : $side = chr(39) . 'up' . chr(39); break;
				case 'down':
				case 4 : $side = chr(39) . 'down' . chr(39); break;
				default:
				case 0 : $side = 'null';break;
			}
			$xTemplate = "$('div#marquee-{$index}').marquee({width: {$config['width']}, height: {$config['height']}, showtime: {$config['showtime']}, stoptime: {$config['stoptime']}, move:'easeInOutQuint', side: {$side}});";
			echo $xTemplate . $eol;
		}
		echo '});' . $eol;
		//echo '//-->' . $eol;
		echo '</script>' . $eol;
	} // end func _hook_wp_head
	
	public function _defaultView() { return $this->_overviewView(); }
	
	public function _overviewView()
	{
		include ($this->_config['meta']['wp_plugin_dir'] . '/inc/view-default.php');
	} // end func _overviewView
	
	// }}}
	// {{{ _settingsView
	
	/**
	 * Function _settingsView
	 */
	public function _settingsView()
	{
		if (isset($_POST['data']) && isset($_POST['submit'])) {
			$dataValid = $this->_settingsValidate($_POST['data']);
			if ($dataValid) {
				update_option('_ttf-' . $this->_config['shortname'], $dataValid);
			}
		} elseif (isset($_POST['reset'])) {
			update_option('_ttf-' . $this->_config['shortname'],$this->_config['options']['default']);
		}
		include ($this->_config['meta']['wp_plugin_dir'] . '/inc/view-default.php');
	} // end func _settingsView
	
	// }}}
	// {{{ _styleView
	
	/**
	 * Function _styleView
	 */
	public function _styleView()
	{
		if (isset($_POST['data']) && isset($_POST['submit'])) {
			$dataValid = $this->_styleValidate($_POST['data']);
			if ($dataValid) {
				update_option(
					'_ttf-' . $this->_config['shortname'] . '-style',$dataValid
				);
			}
		} elseif (isset($_POST['reset'])) {
			update_option(
				'_ttf-' . $this->_config['shortname'] . '-style',
				$this->_config['options']['style']
			);
		}
		include ($this->_config['meta']['wp_plugin_dir'] . '/inc/view-default.php');
	} // end func _settingsView
	
	// }}}
	// {{{ _settingsValidate
	
	/**
	 * Function _settingsValidate
	 * @param array $data
	 */
	public function _settingsValidate($data)
	{
		if (!is_array($data)) return false;
		foreach (($dataValid = array(
				'limit' => null,
				//'categories' => array(),
				//'tags' => array(),
				'width' => null,
				'height' => null,
				'showtime' => null,
				'stoptime' => null,
				'effect' => null,
				'date-format' => null
			)
		) as $k=>$v ) {
			if (!isset($data[$k])) return false;
			$dataValid[$k] = trim($data[$k]);
		}
		
		$dataValid['limit'] = absint($dataValid['limit']);
		$dataValid['width'] = absint($dataValid['width']);
		$dataValid['height'] = absint($dataValid['height']);
		$dataValid['showtime'] = absint($dataValid['showtime']);
		$dataValid['stoptime'] = absint($dataValid['stoptime']);
		$dataValid['effect'] = absint($dataValid['effect']);
		
		if (isset($data['categories'])) {
			$dataValid['categories'] = $data['categories'];
		} else {
			$dataValid['categories'] = array();
		}
		
		
		return $dataValid;
	} // end func _settingsValidate
	
	// }}}
	// {{{ _styleValidate
	
	/**
	 * Function _styleValidate
	 * @param array $data
	 */
	public function _styleValidate($data)
	{
		if (!is_array($data)) return false;
		foreach (($dataValid = array(
				'custom-css' => null
			)
		) as $k=>$v ) {
			if (!isset($data[$k])) return false;
			$dataValid[$k] = trim($data[$k]);
		}
		
		return $dataValid;
	} // end func _settingsValidate
	
	// }}}
	
} // end class PluginMarquee