<?php
/*
Plugin Name: Uptime Robot Widget
Plugin URI: http://beherit.pl/en/wordpress/plugins/uptime-robot-widget
Description: Adds a widget that shows the status of the monitored services in the Uptime Robot service.
Version: 1.1
Author: Krzysztof Grochocki
Author URI: http://beherit.pl/
Text Domain: uptimerobot
Domain Path: /languages
License: GPLv3
*/

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

//Translate plugin meta-data
__('http://beherit.pl/en/wordpress/plugins/uptime-robot-widget', 'uptimerobot');
__('Adds a widget that shows the status of the monitored services in the Uptime Robot service.', 'uptimerobot');

//Define plugin version variable
define('UPTIME_ROBOT_WIDGET_VERSION', '1.1');

//Define translations
function uptimerobot_textdomain() {
	load_plugin_textdomain('uptimerobot', false, dirname(plugin_basename(__FILE__)).'/languages');
}
add_action('init', 'uptimerobot_textdomain');

//Localization filter (Ajax bugifx)
function uptimerobot_localization_filter($locale) {
	if(!empty($_GET['lang']))
		return $_GET['lang'];
	return $locale;
}
add_filter('locale', 'uptimerobot_localization_filter', 99);

//Include admin settings
include_once dirname(__FILE__).'/uptime-robot_admin.php';

//Include widget
include_once dirname(__FILE__).'/uptime-robot_widget.php';
