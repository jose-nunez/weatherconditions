<?php
/*
 * Weather Conditions Widget Class
 */

/*require_once (THEME_FRAMEWORK_DIR . '/widgets/calidadaire.php');
register_widget('bcwc_Widget');

*/

class bcwc_Widget extends WP_Widget {

	function bcwc_Widget() {
		$widget_ops = array('classname' => 'bcwc_Widget', 'description' =>  '');
		$this->WP_Widget('bcwc_Widget','Bicicultura Weather Conditions', $widget_ops);

	}
	
	function widget($args,$instance) {
		extract($args);
		
		$title = $instance['title'];
		$title_today = $instance['title_today'];
		if($title_today) $title .= ' <span class="bcwc_today"><span>';
		
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		// ____________________________________________________
		$servicios = array();
		if($instance['condicionclima'])array_push($servicios,'cc');
		if($instance['calidadaire'])array_push($servicios,'ca');
		if($instance['radiacionsolar'])array_push($servicios,'rs');
		if($instance['today'])array_push($servicios,'td');
		global $bcwc_plugin;
		echo $bcwc_plugin->cliente($servicios);
		// ____________________________________________________

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_today'] = strip_tags($new_instance['title_today']);
		$instance['radiacionsolar'] = strip_tags($new_instance['radiacionsolar']);
		$instance['calidadaire'] = strip_tags($new_instance['calidadaire']);
		$instance['condicionclima'] = strip_tags($new_instance['condicionclima']);
		$instance['today'] = strip_tags($new_instance['today']);
		return $instance;
	}

	function form($instance) {
		//Defaults
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$title_today = isset($instance['title_today']) ? esc_attr($instance['title_today']) : '';	
		$radiacionsolar = isset($instance['radiacionsolar']) ? esc_attr($instance['radiacionsolar']) : '';	
		$calidadaire = isset($instance['calidadaire']) ? esc_attr($instance['calidadaire']) : '';	
		$condicionclima = isset($instance['condicionclima']) ? esc_attr($instance['condicionclima']) : '';	
		$today = isset($instance['today']) ? esc_attr($instance['today']) : '';	
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Título:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('title_today'); ?>">
			<input class="widefat" id="<?php echo $this->get_field_id('title_today'); ?>" name="<?php echo $this->get_field_name('title_today'); ?>" type="checkbox" <?php echo $title_today?'checked="checked"':''; ?> />
			Adjuntar fecha de hoy en el título
		</label></p>
		<p><label for="<?php echo $this->get_field_id('today'); ?>">
			<input class="widefat" id="<?php echo $this->get_field_id('today'); ?>" name="<?php echo $this->get_field_name('today'); ?>" type="checkbox" <?php echo $today?'checked="checked"':''; ?> />
			Mostrar fecha de hoy
		</label></p>
		<p><label for="<?php echo $this->get_field_id('radiacionsolar'); ?>">
			<input class="widefat" id="<?php echo $this->get_field_id('radiacionsolar'); ?>" name="<?php echo $this->get_field_name('radiacionsolar'); ?>" type="checkbox" <?php echo $radiacionsolar?'checked="checked"':''; ?> />
			Mostrar radiación solar
		</label></p>
		<p><label for="<?php echo $this->get_field_id('calidadaire'); ?>">
			<input class="widefat" id="<?php echo $this->get_field_id('calidadaire'); ?>" name="<?php echo $this->get_field_name('calidadaire'); ?>" type="checkbox" <?php echo $calidadaire?'checked="checked"':''; ?> />
			Mostrar calidad del aire
		</label></p>
		<p><label for="<?php echo $this->get_field_id('condicionclima'); ?>">
			<input class="widefat" id="<?php echo $this->get_field_id('condicionclima'); ?>" name="<?php echo $this->get_field_name('condicionclima'); ?>" type="checkbox" <?php echo $condicionclima?'checked="checked"':''; ?> />
			Mostrar condición del clima
		</label></p>
	<?php
	}
	
	static function todayHTML($id){
		global $bcwc_plugin;
		$bcwc_plugin->todayHTML($id);
	}

}

?>
