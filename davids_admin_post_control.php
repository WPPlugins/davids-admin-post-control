<?php /*

**************************************************************************

Plugin Name:  David's Admin Post Control
Plugin URI:   http://www.davidgagne.net/tags/plugins/
Description:  Allows you to control the number of posts displayed in lists in the WP Admin.
Version:      1.0.3
Author:       David Vincent Gagne
Author URI:   http://www.davidgagne.net/

**************************************************************************

Copyright (C) 2009 David Vincent Gagne

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************/

	if ( !class_exists( 'AdminPostControl' ) ){
		class AdminPostControl{

			var $plugin_name = "David's Admin Post Control";
			var $plugin_shortname = "Admin Post Control";
			var $plugin_version = "1.0.3";
			var $plugin_dir = "../wp-content/plugins/";
			var $defaultcount = 15;

			
			/*-----------------------------------------------------
				PHP 4 Compatible Constructor
			-----------------------------------------------------*/
			function AdminPostControl(){
				$this->__construct();
			}
			
			/*-----------------------------------------------------
				PHP 5 Constructor
				This function is called *every time* the plugin is
				instantiated.
			-----------------------------------------------------*/
			function __construct(){
				global $wpdb;
				$this->plugin_dir = plugins_url( '', __FILE__ );
				/*-----------------------------------------------------
					When the plugin is deactivated, execute the kill function.
				-----------------------------------------------------*/
				register_deactivation_hook( __FILE__, array( &$this, "killPlugin" ) );
				
				/*-----------------------------------------------------
					Add the WP Admin menu item link to the plugin
					options menu and a Settings link to the plugin page
				-----------------------------------------------------*/
				add_action( "admin_menu", array( &$this, "addMenuItem" ) );
				add_filter( 'plugin_action_links', array( &$this, 'addPluginLink' ), 10, 2);
			}
			
			/*-----------------------------------------------------
				This function is called *only* when the plugin is
				activated from the WordPress plugins page.
			-----------------------------------------------------*/
			function initializePlugin(){
				add_option( "plugin_name", $this->plugin_name );
				add_option( "plugin_version", $this->plugin_version );
				add_option( "edit_per_page", $this->defaultcount );
			}
			
			/*-----------------------------------------------------
				This function is called *only* when the plugin is
				deactivated from the WordPress plugins page.
			-----------------------------------------------------*/
			function killPlugin(){
				delete_option( "edit_per_page" );
				delete_option( "plugin_version" );
			}
			/*-----------------------------------------------------
				This function is called by the class constructor.
				All it does is add the menu item under the "Settings"
				menu in the WordPress Administration system.
			-----------------------------------------------------*/
			function addMenuItem(){
				add_options_page(
					$this->plugin_name,		// the <title> tag of the WP Admin options page
					$this->plugin_shortname,	// what appears under the "Settings" menu in the WP Admin
					"manage_options",		// WP Admin permission level required to see this menu item
					__FILE__,				// file to execute when menu item clicked
					array( &$this, "managementPage" )	// the function called by the menu item
					);
			}

			/*-----------------------------------------------------
				Add a link to the settings page to the plugins list
			-----------------------------------------------------*/
			function addPluginLink( $links, $file ){
				$plugin = plugin_basename(__FILE__);
				// create link
				if ($file == $plugin) {
					return array_merge( $links, array( sprintf( '<a style="font-weight:bold;" href="options-general.php?page=%s">%s</a>', $plugin, __('Settings') ) ) );
				}
				return $links;
			}


			function managementPage(){
				echo "<div class=\"wrap\">";
				echo "<h2>{$this->plugin_name}</h2><br />";
				$this->handlePostGet();
				$postsperpage = get_option( 'edit_per_page' );
				echo "
				<p>This is a pretty simple plugin.  I wrote it because I have thousands of posts on my blog and I often have twenty or more posts scheduled to be posted.  WordPress defaults to only showing you the latest fifteen (15) posts on the \"Posts\" list page.  Now you can change this so you can view up to 9,999 posts per page.  (Although I don't recommend showing more than a few hundred at a time.)</p>
				<form method=\"post\">
				<p>Display <input type=\"text\" maxlength=\"4\" style=\"width:50px;text-align:right;\" value=\"{$postsperpage}\" name=\"edit_per_page_value\" /> posts per page in the WordPress Admin.&nbsp;&nbsp;<input type=\"submit\" value=\"Update &raquo;\" /></p>
				</form>";
				echo "<br /><hr /><br /><ul>";
				echo "
					<div style=\"float:left;background:#ddd;border:2px outset #b29e90;\">
					<div style=\"color:#fff;background:#2a5b8e;margin:0;padding:4px;font:normal medium Futura, Tahoma, verdana, sans-serif;\">Thanks for using my plugin!</div>
					<div style=\"padding:10px 20px 0 20px;\">
					<p>If you like this plugin you should <a href=\"http://wordpress.org/extend/plugins/profile/dvg\">try some of my others</a>.</p>
					<p>You can also <a href=\"http://twitter.com/davidgagne\">follow me on Twitter</a>, <a href=\"http://www.davidgagne.net/\">visit my website</a>, and <a href=\"http://feeds.feedburner.com/davidgagne\">subscribe to my feed via RSS</a>.</p>
					<p style=\"text-align:right;\">Thanks!</p>
					<p style=\"text-align:right;\">dvg</p><hr />
					<ul>
					<li>Plugin Version: {$this->plugin_version}</li>
					<li>Plugin Directory: {$this->plugin_dir}</li>
					</ul>
					<br style=\"clear:both;\" />
					</div>
					</div>
				";
				echo "\n</div>";
			}
			
			/*-----------------------------------------------------
				This function is called whenever the managementPage
				is displayed.  It checks the GET and POST variables
				to see if the user has submitted anything for
				processing ... and then it processes.
			-----------------------------------------------------*/
			function handlePostGet(){
				/*-----------------------------------------------------
					The $message variable is for displaying anything
					that has happened in this function.
				-----------------------------------------------------*/
				$message = "";

				/*-----------------------------------------------------
					Handle new quotes.
				-----------------------------------------------------*/
				if ( isset( $_POST['edit_per_page_value'] ) ){
					$newvalue = $_POST['edit_per_page_value'];
					if ( strlen( $newvalue ) > 4 ) $error[] = "You cannot display more than 9999 posts per page.";
					if ( !strlen( $newvalue ) ) $error[] = "You cannot set this value to an empty string.";
					if ( !is_numeric( $newvalue ) ) $error[] = "The <b>number</b> of posts to display must be a numeric value.";
					if ( !isset( $error ) ) $result[] = $this->updatePostCount( $newvalue );
				}

				/*-----------------------------------------------------
					This is the end of the line.  Check to see if there's
					anything in the $error or $result variables and display ...
				-----------------------------------------------------*/
				if ( isset( $error ) ){
					foreach ( $error as $e ){
						$message .= "<li>{$e}</li>";
					}
					$message = "<ul>{$message}</ul>";
				}
				if ( isset( $result ) ){
					foreach ( $result as $r ){
						$message .= "<li>{$r}</li>";
					}
					$message = "<ul>{$message}</ul>";
				}
				if ( strlen( $message ) ) echo "<div style=\"position: relative;font-weight: bold;background-color: #f0f6fb;-moz-border-radius: 3px;-webkit-border-radius: 3px;border: 1px solid #c3d5e7;color: #d54e21;margin: 7px 0 15px;top: 8px;width: 90%;padding: 6px;\">{$message}</div>";
			}
			
			function updatePostCount( $number ){
				update_option( 'edit_per_page', $number );
				return "The number of posts to display in the Admin has been updated!";
			}
		}
	}
	
	if ( class_exists( 'AdminPostControl' ) ){
		$AdminPostControl = new AdminPostControl();
		/*-----------------------------------------------------
			When the plugin is activated, execute the install
			function.  This must be called here, in the
			execution of the plugin itself, because the
			constructor function of the class doesn't get
			called until *after* the plugin has been
			activated.  (I *think* that's what's happening.)
		-----------------------------------------------------*/
		register_activation_hook( __FILE__, array( &$AdminPostControl, "initializePlugin" ) );
		}