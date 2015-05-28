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
	//Add row to plugin page
	add_filter('plugin_row_meta', 'uptimerobot_plugin_row_meta', 10, 2);
}
add_action('admin_init', 'uptimerobot_register_settings');

//Settings row on plugin page
function uptimerobot_plugin_row_meta($plugin_meta, $plugin_file) {
	if(dirname(plugin_basename(__FILE__)).'/uptime-robot.php'==$plugin_file)
		$plugin_meta[] = '<a href="options-general.php?page=uptime-robot-options">'.__('Settings', 'uptimerobot').'</a>';
    return $plugin_meta;
}

//Create options menu
function uptimerobot_admin_menu() {
	//Global variable
	global $uptimerobot_options_page_hook;
	//Add options page
	$uptimerobot_options_page_hook = add_options_page(__('Uptime Robot Widget', 'uptimerobot'),__('Uptime Robot Widget', 'uptimerobot'), 'manage_options', 'uptime-robot-options', 'uptimerobot_options');
	//Add the needed JavaScript
	add_action('admin_enqueue_scripts', 'uptimerobot_options_enqueue_scripts');
	//Add the needed jQuery script
	add_action('admin_footer-'.$uptimerobot_options_page_hook, 'uptimerobot_options_scripts' );
	//Set number of available columns
	add_filter('screen_layout_columns', 'uptimerobot_options_layout_column', 10, 2);
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

//Number of columns available in options page
function uptimerobot_options_layout_column($columns, $screen) {
	//Get global variable
	global $uptimerobot_options_page_hook;
	if($screen == $uptimerobot_options_page_hook) {
		$columns[$uptimerobot_options_page_hook] = 2;
	}
	return $columns;
}

//Add metaboxes
function uptimerobot_add_meta_boxes() {
	//Get global variable
	global $uptimerobot_options_page_hook;
	//Add settings meta box
	add_meta_box(
		'uptimerobot_settings_meta_box',
		__('Settings', 'uptimerobot'),
		'uptimerobot_settings_meta_box',
		$uptimerobot_options_page_hook,
		'normal',
		'default'
	);
	//Add donate meta box
	add_meta_box(
		'uptimerobot_donate_meta_box',
		__('Donations', 'uptimerobot'),
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
		<?php settings_fields('uptimerobot_settings'); ?>
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
		<div class="inside" style="margin-top:-18px;">
			<ul>
				<li>
					<label for="uptimerobot_apikey"><?php _e('API key', 'uptimerobot'); ?>:&nbsp;<input type="text" size="40" name="uptimerobot_apikey" id="uptimerobot_apikey" value="<?php echo get_option('uptimerobot_apikey') ?>" /></label>
					</br><small><?php printf(__('To get your API key visit <a target="_blank" href="%s">Uptime Robot webpage</a>.', 'uptimerobot'), 'https://uptimerobot.com/dashboard#mySettings'); ?>					
					</small>
				</li>
			</ul>
		</div>
		<div id="major-publishing-actions">
			<div id="publishing-action">
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save settings', 'uptimerobot'); ?>" />
			</div>
			<div class="clear"></div>
		</div>
	</form>
	<div>
<?php }

//Donate meta box
function uptimerobot_donate_meta_box() { ?>
	<p><?php _e('If you like this plugin, please send a donation to support its development and maintenance', 'uptimerobot'); ?></p>
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
		<h2><?php _e('Uptime Robot Widget', 'uptimerobot'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
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