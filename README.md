# Introduction: wordpress-sidebar-select
WP Plugin that allows you to easily create sidebars and then set them on a page by page or post by post basis


## What the plugin does
The plugin has two primary functions...

### Create Sidebars

The plugin creates a custom post type and adds it to the 'Appearance' tab. When you create a new post in this post type, the plugin will automatically convert the post type into a registered sidebar. 

This means that it will appear as an area which you can add widgets too on your WP installs widget page. 

### Allow you to select a sidebar

The plugin also adds a custom field to all pages. In this custom field you can select the sidebar you want to display on this page.


## Placing The Sidebar Into You Template 

Simply put the following PHP in your template wherever you want the sidebar to appear.

<pre>
	<code>
		$sidebar = get_post_meta($post->ID,'_selected-sidebar',true);	
		dynamic_sidebar($sidebar);	
	</code>
</pre>