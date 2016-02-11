<?php
/*
Plugin Name: Sidebars
Plugin URI: http://eddsmith.me
Description: Creates a post type for creating Sidebars dynamically and adds a META box for sidebar select on a page to page basis
Version: 1.0
Author: Edd Smith
Author URI: http://eddsmith.me
*/
?>
<?php

/*
TODO: add var for quickly setting what pages / post types select box should appear on.
TODO: add var for quickly changing the sidebar post type name via
*/

/**
 * Calling Sidebar On The Front End
 *
 * To call a sidebar on the front end of the website simply feed 
 * the META box value into a sidebar calling function eg:
 *
 * $sidebar = get_post_meta($post->ID,'_selected-sidebar',true);
 * get_sidebar($sidebar);
 *
 */


/**
 * Register Sidebar Post Type
 *
 * Each post made in this post type, will be registered as a sidebar in
 * the next function below.
 */

add_action( 'init', 'create_side_post_type' );

function create_side_post_type() {

	register_post_type( 'pt-sidebars',
		array(
			'labels' => array(
				'name'           => __( 'Sidebar Registration' ),
				'singular_name'  => __( 'Sidebar' )
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_menu'  => 'themes.php',
			'supports'      => array('title')
		)
	);

}




/**
 * Disable Permalink on Sidebar Post Type
 *
 * We do not need the sidebar post type to have a pemalink the below...
 *  - Removes the permalink
 *  - Removes the view post button
 *  - Removes get shortlink button
 */

add_filter('get_sample_permalink_html', 'disable_sidebar_permalinks', 10, 5);

function disable_sidebar_permalinks($return, $post_id, $new_title, $new_slug, $post)
{
    if($post->post_type == 'pt-sidebars') {
        return '';
    }
    return $return;
}




/**
 * Register Sidebars
 *
 * Get all the posts in the sidebar post type and register them as an
 * actual sidebar. 
 */

// Get the posts
$sidebars = get_posts(
	array (
    	'post_type'       => array ( 'pt-sidebars' ),
    	'posts_per_page'  => -1
    )
);

// Loop through each post and register
foreach ( $sidebars as $sidebar ) {

	// Register the sidebar
	register_sidebar(array(
		'name'        => $sidebar->post_title,
		'id'          => $sidebar->post_name
	));

}






/**
 * Add Sidebar Select To Pages
 *
 * Adds a META box to specified pages and allows the user to select
 * a sidebar of their choice on a page by page basis. 
 */


add_action('admin_init','sidebar_select_meta_box_init');

function sidebar_select_meta_box_init() {
    
    // Create our custom META box
    add_meta_box(
    	'sidebar-select-meta',
    	__('Sidebar Select','sidebarselect-plugin'), 
    	'sidebar_select_meta_box',
    	'page',
    	'side',
    	'high'
    ); 	
    
    // Hook to save meta box value
    add_action('save_post','sidebar_save_meta_box'); 
}

function sidebar_select_meta_box($post,$box) {
 
    // Retrieve the selected sidebar if already set
    $selectedSidebar = get_post_meta($post->ID,'_selected-sidebar',true);
	
	// Grab an array of all registered sidebars
	$wp_registered_sidebars = $GLOBALS['wp_registered_sidebars'];


	// Echo out a select box. When looping through 
	// options. If the saved sidebar id matches
	// the current option, set it to selected
    ?>
    <p> 
    	<select name="sidebar_product_type" id="sidebar_product_type">
    		<option value=''></option>
		    <?php foreach ( $wp_registered_sidebars as $sidebar ) : ?>
		    	<option value="<?php echo $sidebar['id'];?>" <?php if($selectedSidebar == $sidebar['id']): ?>selected='selected'><?php endif; ?>"> 
		    		<?php echo $sidebar['name']; ?>
		    	</option>;
		    <?php endforeach; ?>
		   
    	</select>
    </p>
    <?php
}

function sidebar_save_meta_box($post_id,$post = null) {
    
    // if post is a revision skip saving our meta box data
    if($post->post_type == 'revision') { 
    	return; 
    }
    
    // process form data if $_POST is set
    if(isset($_POST['sidebar_product_type'])) {

        // save the meta box data as post meta using the post ID as a unique prefix
        update_post_meta($post_id,'_selected-sidebar',esc_attr($_POST['sidebar_product_type'])); 

    }
}

?>