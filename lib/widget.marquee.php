<?php
/**
 * @version		$Id: widget.marquee.php 900 2011-08-08 11:38:53Z xagero $
 */
class PluginMarquee_Widget extends WP_Widget
{
	/**
	 * __construct
	 */
	public function __construct()
	{
		$widget_ops = array( 'classname' => 'widget_marquee' );
		$control_ops = array( 'id_base' => 'thethe_marquee' );
		parent::__construct( 'thethe_marquee', 'TheThe Marquee', $widget_ops, $control_ops);
	} // end func __construct
	
	// }}}
	// {{{ form
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::form()
	 */
	public function form($instance)
	{
		$title = esc_attr($instance['title']);
		?>
			<p>
				<label for="<?php print $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
				<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print $title; ?>" />
			</p>
		<?php 		
	} // end func form
	
	// }}}
	// {{{ update
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::update()
	 */
	public function update($new, $old)
	{
		$instance = $old;
		$instance['title'] = strip_tags($new['title']);
		return $instance;
	} // end func update
	
	// }}}
	// {{{ widget
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::widget()
	 */
	public function widget($args, $instance)
	{
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		/**
		 * @var PluginMarquee
		 */
		echo $GLOBALS['PluginMarquee']->marquee($instance);
		echo $after_widget;
	} // end func widget
	
} // end class PluginMarquee_Widget