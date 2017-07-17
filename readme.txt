=== Plugin Name ===
Contributors: dvg
Donate link: http://www.davidgagne.net/about/
Tags: admin, posts, editing, control panel, WordPress Admin, Administration
Requires at least: 2.8
Tested up to: 2.8
Stable tag: trunk

Allows you to control the number of posts displayed in lists in the WP Admin.

== Description ==

This is a pretty simple plugin.  I wrote it because I have thousands of posts on my blog and I often have twenty or more posts scheduled to be posted.  WordPress defaults to only showing you the latest fifteen (15) posts on the "Posts" list page.  Now you can change this so you can view up to 9,999 posts per page.  (Although I don't recommend showing more than a few hundred at a time.)

== Installation ==

1. Upload the davids_admin_post_control file to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What happens when this plugin is activated? =

The plugin attempts to add a default value WordPress edit_per_page option.  If there is already a value for this option nothing happens.

= What happens when this plugin is deactivated? =

When you deactivate the plugin it will remove the edit_per_page option.  The WordPress core engine knows to default to 15 posts per list page if there is no value for this option.

== Screenshots ==

none