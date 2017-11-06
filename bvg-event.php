<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://etalkers.org
 * @since             1.0.0
 * @package           Bvg_Event
 *
 * @wordpress-plugin
 * Plugin Name:       BVG Event
 * Plugin URI:        http://etalkers.org
 * Description:       Plugin für die Turniere Auschreibungen
 * Version:           1.0.0
 * Author:            Laurent Dorier
 * Author URI:        http://etalkers.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bvg-event
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
// Don't call the file directly
if ( !defined( 'ABSPATH' ) ) die();


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bvg-event-activator.php
 */
function activate_bvg_event() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bvg-event-activator.php';
	bvg_event_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bvg-event-deactivator.php
 */
function deactivate_bvg_event() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bvg-event-deactivator.php';
	bvg_event_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bvg_event' );
register_deactivation_hook( __FILE__, 'deactivate_bvg_event' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bvg-event.php';







/**
 * Creating a function to create our CPT
 *
 *
 *
 */

function custom_ausschreibungen_post_type() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Ausschreibungen', 'Post Type General Name', 'twentythirteen' ),
        'singular_name'       => _x( 'Ausschreibung', 'Post Type Singular Name', 'twentythirteen' ),
        'menu_name'           => __( 'Ausschreibungen', 'twentythirteen' ),
        'parent_item_colon'   => __( 'Parent Ausschreibung', 'twentythirteen' ),
        'all_items'           => __( 'Alle Ausschreibungen', 'twentythirteen' ),
        'view_item'           => __( 'Ausschreibungen anzeigen', 'twentythirteen' ),
        'add_new_item'        => __( 'Neue Ausschreibung', 'twentythirteen' ),
        'add_new'             => __( 'Neue Ausschreibung', 'twentythirteen' ),
        'edit_item'           => __( 'Ausschreibung editieren', 'twentythirteen' ),
        'update_item'         => __( 'Ausschreibung aktualisieren', 'twentythirteen' ),
        'search_items'        => __( 'Ausschreibung suchen', 'twentythirteen' ),
        'not_found'           => __( 'Nicht gefunden', 'twentythirteen' ),
        'not_found_in_trash'  => __( 'Nicht gefunde in Trash', 'twentythirteen' ),
    );

// Set other options for Custom Post Type

    $args = array(
        'label'               => __( 'Ausschreibungen', 'twentythirteen' ),
        'description'         => __( 'Turnier Ausschreibungen', 'twentythirteen' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions' ),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies'          => array( 'turniere' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );

    // Registering your Custom Post Type
    register_post_type( 'ausschreibungen', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'custom_ausschreibungen_post_type', 0 );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bvg_event() {

	$plugin = new bvg_event();
	$plugin->run();

}
run_bvg_event();
