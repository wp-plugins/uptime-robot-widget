<?php
/*
	Copyright (C) 2015 Krzysztof Grochocki

	This file is part of Uptime Robot Widget.

	Uptime Robot Widget is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3, or
	(at your option) any later version.

	Uptime Robot Widget is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with GNU Radio. If not, see <http://www.gnu.org/licenses/>.
*/

//Text status of monitor
function uptimerobot_text_status($status){
	switch($status) {
		case 0:
			$r = __('paused', 'uptimerobot');
			break;
		case 1:
			$r = __('n/d', 'uptimerobot');
			break;
		case 2:
			$r = __('up', 'uptimerobot');
			break;
		case 8:
			$r = __('seems down', 'uptimerobot');
			break;
		case 9:
			$r = __('down', 'uptimerobot');
			break;
		default:
			$r = __('unk', 'uptimerobot');
	}
	return $r;
}

//Enqueue styles & jQuery script
function uptimerobot_enqueue_styles() {
	if(is_active_widget(false, false, 'uptimerobot_widget')) {
		wp_enqueue_style('uptimerobot', plugin_dir_url(__FILE__).'css/uptime-robot.css', array(), UPTIME_ROBOT_WIDGET_VERSION, 'all');
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.4.0', 'all');
		wp_enqueue_script('uptimerobot', plugin_dir_url(__FILE__).'js/jquery.uptimerobot.min.js', array('jquery'), UPTIME_ROBOT_WIDGET_VERSION, true);
		wp_localize_script('uptimerobot', 'uptimerobot', array(
			'url' => admin_url('admin-ajax.php?action=get_uptimerobot&lang='.get_locale())
		));
	}
}
add_action('wp_enqueue_scripts', 'uptimerobot_enqueue_styles');

//Enqueue ajax function
function uptimerobot_ajax() {
	//Get API Key
	$apikey = get_option('uptimerobot_apikey');
	//GET arguments
	$url_args = array(
		'timeout' => 5,
		'redirection' => 0
	);
	//Get data
	$response = wp_remote_get('http://api.uptimerobot.com/getMonitors?apiKey='.$apikey.'&format=json&noJsonCallback=1', $url_args);
	$http_code = wp_remote_retrieve_response_code($response);
	//Verify response
	if($http_code == 200) {
		$json = json_decode(wp_remote_retrieve_body($response));
		//Foreach monitors
		if(!empty($json->monitors)) {
			foreach($json->monitors->monitor as $monitor) {
				$sc.='<div class="monitor">
					<span class="status stat'.$monitor->status.'">'.uptimerobot_text_status($monitor->status).'</span>
					<span class="name">'.$monitor->friendlyname.'</span>
					<span class="ratio">'.$monitor->alltimeuptimeratio.'%</span>
				</div>';
			}
			echo $sc;
		}
		//No data - wrong API key or no active monitors?
		else {
			echo __('Oops! Something went wrong and failed to get the status, check again soon.', 'uptimerobot');
		}
	}
	//Connection problems
	else {
		echo __('Oops! Something went wrong and failed to get the status, check again soon.', 'uptimerobot');
	}

	exit;
}
add_action('wp_ajax_nopriv_get_uptimerobot', 'uptimerobot_ajax');
add_action('wp_ajax_get_uptimerobot', 'uptimerobot_ajax');

//Create widget instance
add_action('widgets_init', function(){
	register_widget('uptimerobot_widget');
});
class uptimerobot_widget extends WP_Widget {
	//Constructor
	function uptimerobot_widget() {
		//Widget variable settings
		$this->widget_cssclass    = 'widget_uptimerobot';
		$this->widget_description =  __('Status of the monitored services in the Uptime Robot service.', 'uptimerobot');
		$this->widget_name        = 'Uptime Robot';
		//Widget settings
		$widget_ops = array('classname' => $this->widget_cssclass, 'description' => $this->widget_description);
		//Create the widget
		$this->WP_Widget(false, $this->widget_name, $widget_ops);
    }
	//Display function
	function widget($args, $instance) {
		//Widget title
		$instance['title'] = apply_filters('widget_title', $instance['title']);
		echo $args['before_widget'];
		if(!empty($instance['title'])) echo $args['before_title'] . $instance['title'] . $args['after_title'];
		//Widget content
		$sc = '<div id="uptimerobot" class="uptimerobot">
			<i title="'.__('Loading...', 'uptimerobot').'" class="fa fa-spinner fa-pulse" style="font-size: 34px;"></i>
		</div>';
		echo $sc;
		//Widget end
		echo $args['after_widget'];
	}
	//Update function
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
	//Settings form function
	function form($instance) {
		$sc = '<p>
			<label for="'.$this->get_field_id('title').'">'.__('Title', 'uptimerobot').':</label>
			<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$instance['title'].'" />
		</p>
		<p>
			'.sprintf(__('Please enter API key in <a href="%s">plugin settings</a>.', 'uptimerobot'), 'options-general.php?page=uptime-robot-options').'
		</p>';
		echo $sc;
	}
}
