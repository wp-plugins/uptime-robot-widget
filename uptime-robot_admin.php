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

//Admin init
function uptimerobot_register_settings() {
	//Register settings
	register_setting('uptimerobot_settings', 'uptimerobot_apikey');
	//Add link to the settings on plugins page
	add_filter('plugin_action_links', 'uptimerobot_plugin_action_links', 10, 2);
}
add_action('admin_init', 'uptimerobot_register_settings');

//Link to the settings on plugins page
function uptimerobot_plugin_action_links($action_links, $plugin_file) {
	if(dirname(plugin_basename(__FILE__)).'/uptime-robot.php' == $plugin_file) {
		$action_links[] = '<a href="options-general.php?page=uptime-robot-options">'.__('Settings', 'uptime-robot-widget').'</a>';
	}
    return $action_links;
}

//Create options menu
function uptimerobot_admin_menu() {
	//Global variable
	global $uptimerobot_options_page_hook;
	//Add options page
	$uptimerobot_options_page_hook = add_options_page(__('Uptime Robot Widget', 'uptime-robot-widget'),__('Uptime Robot Widget', 'uptime-robot-widget'), 'manage_options', 'uptime-robot-options', 'uptimerobot_options');
	//Add the needed JavaScript
	add_action('admin_enqueue_scripts', 'uptimerobot_options_enqueue_scripts');
	//Add the needed jQuery script
	add_action('admin_footer-'.$uptimerobot_options_page_hook, 'uptimerobot_options_scripts' );
}
add_action('admin_menu', 'uptimerobot_admin_menu');

//Add the needed JavaScript
function uptimerobot_options_enqueue_scripts($hook_suffix) {
	//Get global variable
	global $uptimerobot_options_page_hook;
	if($hook_suffix == $uptimerobot_options_page_hook) {
		wp_enqueue_script('postbox');
	}
}

//Add the needed jQuery script
function uptimerobot_options_scripts() {
	//Get global variable
	global $uptimerobot_options_page_hook; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			//Toggle postbox
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			//Save postbox status
			postboxes.add_postbox_toggles( '<?php echo $uptimerobot_options_page_hook; ?>' );
		});
		//]]>
	</script>
<?php }

//Add metaboxes
function uptimerobot_add_meta_boxes() {
	//Get global variable
	global $uptimerobot_options_page_hook;
	//Add settings meta box
	add_meta_box(
		'uptimerobot_settings_meta_box',
		__('Settings', 'uptime-robot-widget'),
		'uptimerobot_settings_meta_box',
		$uptimerobot_options_page_hook,
		'normal',
		'default'
	);
	//Add donate meta box
	add_meta_box(
		'uptimerobot_donate_meta_box',
		__('Donations', 'uptime-robot-widget'),
		'uptimerobot_donate_meta_box',
		$uptimerobot_options_page_hook,
		'side',
		'default'
	);
}
add_action('add_meta_boxes', 'uptimerobot_add_meta_boxes');

//Settings meta box
function uptimerobot_settings_meta_box() { ?>
	</div>
	<form id="uptimerobot-form" method="post" action="options.php">
		<?php settings_fields('uptimerobot_settings');
		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
		<div class="inside" style="margin-top:-18px;">
			<ul>
				<li>
					<label for="uptimerobot_apikey"><?php _e('API key', 'uptime-robot-widget'); ?>:&nbsp;<input type="text" size="40" name="uptimerobot_apikey" id="uptimerobot_apikey" value="<?php echo get_option('uptimerobot_apikey') ?>" /></label>
					</br><small><?php printf(__('To get your API key visit <a target="_blank" href="%s">Uptime Robot webpage</a>.', 'uptime-robot-widget'), 'https://uptimerobot.com/dashboard#mySettings'); ?>
					</small>
				</li>
			</ul>
		</div>
		<div id="major-publishing-actions">
			<div id="publishing-action">
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save settings', 'uptime-robot-widget'); ?>" />
			</div>
			<div class="clear"></div>
		</div>
	</form>
	<div>
<?php }

//Donate meta box
function uptimerobot_donate_meta_box() { ?>
	<p><?php _e('If you like this plugin, please send a donation to support its development and maintenance', 'uptime-robot-widget'); ?></p>
	<form style="width: 178px; height: 52px; margin: 0 auto;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="X2NFKDZSPSUVJ">
		<input type="image" src="<?php echo plugin_dir_url(__FILE__); ?>img/paypal.png" border="0" name="submit" alt="PayPal">
	</form>
<?php }

//Display options page
function uptimerobot_options() {
	//Global variable
	global $uptimerobot_options_page_hook;
	//Enable add_meta_boxes function
	do_action('add_meta_boxes', $uptimerobot_options_page_hook); ?>
	<div class="wrap">
		<h2><?php _e('Uptime Robot Widget', 'uptime-robot-widget'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes($uptimerobot_options_page_hook, 'normal', null); ?>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes($uptimerobot_options_page_hook, 'side', null); ?>
				</div>
			</div>
		</div>
	</div>
<?php }
