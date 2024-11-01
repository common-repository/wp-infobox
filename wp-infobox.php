<?php
/*
Plugin Name: WP-Infobox
Plugin URI: http://jonasnordstrom.se/plugins/wp-infobox/
Description: Add an info box to posts. You can add title, ingress, a bullet list and "more text"
Version: 0.8
Author: Jonas Nordstrom
Author URI: http://jonasnordstrom.se
*/

/*  Copyright 2010  Jonas Nordstrom  (email : jonas.nordstrom@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( ! class_exists( "WPInfobox" ) ) {
	class WPInfobox {

		// Max number of items
		protected $max_items;
		protected $css;
		protected $on_posts;
		protected $on_pages;

		// Constructor
		public function WPInfobox() {
			$this->max_items = get_option( 'wpinfobox_maxitems', '10' );
			$this->css = get_option( 'wpinfobox_css', 'on' );
			$this->on_posts = get_option( 'wpinfobox_on_posts', 'on' );
			$this->on_pages = get_option( 'wpinfobox_on_pages', 'on' );
		}

		// initialization, setup localization
		public function init_wpinfobox() {
			// Set up localization
			$plugin_dir = basename( dirname( __FILE__ ) );
			load_plugin_textdomain( 'wpinfobox', 'wp-content/plugins/'. $plugin_dir.'/languages', $plugin_dir.'/languages' );
		}

		// Admin page for plugin
		function wpinfobox_admin_page() {
			// Handle updates
			if( $_POST[ 'action' ] == 'save' ) {
				check_admin_referer('plugin-name-action_wpinfobox');
				update_option('wpinfobox_maxitems', $_POST[ 'wpinfobox_maxitems' ]);
				update_option('wpinfobox_css', $_POST[ 'wpinfobox_css' ] ? $_POST[ 'wpinfobox_css' ] : 'off');
				update_option('wpinfobox_on_posts', $_POST[ 'wpinfobox_on_posts' ] ? $_POST[ 'wpinfobox_on_posts' ] : 'off');
				update_option('wpinfobox_on_pages', $_POST[ 'wpinfobox_on_pages' ] ? $_POST[ 'wpinfobox_on_pages' ] : 'off');
				?>
				<div class="updated"><p><strong><?php _e('Settings saved.', 'wpinfobox' ); ?></strong></p></div>
				<?php

			}

			// The form, with all names and a checkbox in front
			echo '<div class="wrap">';
			echo "<h2>" . __("WP-Infobox settings", "wpinfobox") . "</h2>";
			?>
			<form name="infobox-admin-form" method="post" action="">
				<input type="hidden" name="action" value="save" />
				<?php wp_nonce_field('plugin-name-action_wpinfobox'); ?>
				<table class="infobox-form-table">
					<tr>
						<td><?php _e("Max items", "wpinfobox"); ?></td>
						<td><input type="text" id="wpinfobox_maxitems" name="wpinfobox_maxitems" value="<?php echo stripslashes(get_option( 'wpinfobox_maxitems' )); ?>" /></td>
					</tr>
					<tr>
						<td><?php _e("Include css", "wpinfobox"); ?></td>
						<td>
							<input type="checkbox" id="wpinfobox_css" name="wpinfobox_css" 
									<?php echo (get_option( 'wpinfobox_css' ) == 'on' ? 'checked="checked"' : '' ); ?> />
						</td>
					</tr>
					<tr>
						<td><?php _e("Activate for posts", "wpinfobox"); ?></td>
						<td>
							<input type="checkbox" id="wpinfobox_on_posts" name="wpinfobox_on_posts" 
									<?php echo (get_option( 'wpinfobox_on_posts' ) == 'on' ? 'checked="checked"' : '' ); ?> />
						</td>
					</tr>
					<tr>
						<td><?php _e("Activate for pages", "wpinfobox"); ?></td>
						<td>
							<input type="checkbox" id="wpinfobox_on_pages" name="wpinfobox_on_pages" 
									<?php echo (get_option( 'wpinfobox_on_pages' ) == 'on' ? 'checked="checked"' : '' ); ?> />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<p class="submit">
								<input type="submit" name="Submit" value="<?php _e('Update options', 'wpinfobox' ) ?>" />
							</p>
						</td>
					</tr>

				</table>
			</form>
			</div>
			<?php
		}

		// A meta box is added to "write post", in main column
		public function init_admin() {
			if (get_option( 'wpinfobox_on_posts' )):
				add_meta_box('wp-infobox', __('Info Box', 'wpinfobox'), array(&$this, 'insert_form'), 'post', 'normal');
			endif;
			if (get_option( 'wpinfobox_on_pages' )):
				add_meta_box('wp-infobox', __('Info Box', 'wpinfobox'), array(&$this, 'insert_form'), 'page', 'normal');
			endif;
			add_options_page(__('Info Box Settings', "wpinfobox"), __('Info Box Settings', "wpinfobox"), 'administrator', basename(__FILE__), array(&$this, 'wpinfobox_admin_page') );
		}

		// Form for infobox input
		public function insert_form($post) {
			$title = get_post_meta($post->ID, 'wpinfobox_title', true);
			$lead = get_post_meta($post->ID, 'wpinfobox_lead', true);
			for ($i=1; $i <= $this->max_items; $i++) {
				$item[$i-1] = get_post_meta($post->ID, 'wpinfobox_item_' . $i, true);
			}
			$copy = get_post_meta($post->ID, 'wpinfobox_copy', true);
			?>
			<table class="form-table">
			<tr valign="top">
				<td><label for="wpinfobox_title"><?php _e("Title:", 'wpinfobox')?></label></td>
				<td>
					<input type="text" size="40" style="width:45%;"
							name="wpinfobox_title" id="wpinfobox_title"
							value="<?php echo htmlspecialchars($title); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<td><label for="wpinfobox_lead"><?php _e("Lead:", 'wpinfobox')?></label></td>
				<td>
					<input type="text" size="40" style="width:45%;"
							name="wpinfobox_lead" id="wpinfobox_lead"
							value="<?php echo htmlspecialchars($lead); ?>" />
				</td>
			</tr>
			<?php
			for ( $i=1; $i <= $this->max_items; $i++ ) { ?>
				<tr valign="top">
					<td><label for="wpinfobox_item_<?php echo $i?>"><?php echo __("Item", 'wpinfobox') .' '. $i . ':';?></label></td>
					<td>
						<input type="text" size="40" style="width:45%;"
								name="wpinfobox_item_<?php echo $i?>" id="wpinfobox_item_<?php echo $i?>"
								value="<?php echo htmlspecialchars($item[$i-1]); ?>" />
					</td>
				</tr>
				<?php
			}
			?>
			<tr valign="top">
				<td><label for="wpinfobox_copy"><?php _e("Copy:", 'wpinfobox')?></label></td>
				<td>
					<input type="text" size="40" style="width:45%;"
							name="wpinfobox_copy" id="wpinfobox_copy"
							value="<?php echo htmlspecialchars($copy); ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php _e("Add an info box to the current post", "wpinfobox")?>
				</td>
			</tr>
			</table>
			<?php
		}

		// Set or unset custom fields
		protected function update_meta($id, $key, $val) {
			if (!empty($val)) {
				update_post_meta($id, $key, $val);
			} else {
				delete_post_meta($id, $key);
			}
		}

		// Save settings
		function add_meta_data($post_ID) {
			// do not let autosave eat up our custom fields
			if ( wp_is_post_revision( $post_ID ) or wp_is_post_autosave( $post_ID ) )
				return;

			$this->update_meta($post_ID, 'wpinfobox_title', $_POST['wpinfobox_title']);
			$this->update_meta($post_ID, 'wpinfobox_lead', $_POST['wpinfobox_lead']);
			for ($i=1; $i <= $this->max_items; $i++) {
				$this->update_meta($post_ID, 'wpinfobox_item_' . $i, $_POST['wpinfobox_item_' . $i]);
			}
			$this->update_meta($post_ID, 'wpinfobox_copy', $_POST['wpinfobox_copy']);
		}

		// Invoked from theme, this is the actual info box rendered
		public function the_box() {
			global $post;
			$title = get_post_meta($post->ID, 'wpinfobox_title', true);

			// Only display infobox on single pages
			// TODO: Set this as an option
			if (is_single() && !empty($title)) {
				$lead = get_post_meta($post->ID, 'wpinfobox_lead', true);
				for ($i=1; $i <= $this->max_items; $i++) {
					$item[$i-1] = get_post_meta($post->ID, 'wpinfobox_item_' . $i, true);
				}
				$title = get_post_meta($post->ID, 'wpinfobox_title', true);
				$copy = get_post_meta($post->ID, 'wpinfobox_copy', true);
				?>
					<div id="wpinfobox">
						<h2 id="wpinfobox-title"><?php echo get_post_meta($post->ID, 'wpinfobox_title', true)?></h2>
						<div>
						<?php
						if (!empty($lead)) {
							echo "<p>" . $lead . "</p>";
						}
						if (!empty($item[0])) {
							echo "<ul>";
							foreach ($item as $tip) {
								if (!empty($tip)) {
									echo "<li>" . $tip . "</li>";
								}
							}
							echo "</ul>";
						}
						if (!empty($copy)) {
							echo "<p>" . $copy . "</p>";
						}
						?>
						</div>
					</div>
					<?php
			} else return "";
		}

		// Add infobox to content
		function content_filter($content)
		{
			return $this->the_box() . $content . '<div style="clear:both"></div>';
		}

		function add_style() {
			if ($this->include_css != 'on')
				return;

      if ( @file_exists(STYLESHEETPATH . '/wp-infobox.css') ) {
				$css_file = get_stylesheet_directory_uri() . '/wp-infobox.css';
			} elseif ( @file_exists(TEMPLATEPATH . '/pagenavi-css.css') ) {
				$css_file = get_template_directory_uri() . '/wp-infobox.css';
			} else {
				$css_file = plugins_url('css/wp-infobox.css', __FILE__);
			}
      wp_register_style( 'wp-infobox-styles', $css_file );
      wp_enqueue_style( 'wp-infobox-styles' );
    }
	}
}

// Init class
if (class_exists("WPInfobox")) {
	$wpinfobox = new WPInfobox();
}

// Hooks, Actions and Filters, oh my!
add_action( 'init', array(&$wpinfobox, 'init_wpinfobox'));
add_action( 'admin_menu', array(&$wpinfobox, 'init_admin' ));
add_action( 'save_post', array(&$wpinfobox, 'add_meta_data'));
add_action( 'wp_print_styles', array(&$wpinfobox, 'add_style'));
add_filter( 'the_content', array (&$wpinfobox, 'content_filter'));
?>
