<?php

if ( !defined( 'ABSPATH' ) ) die();

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://etalkers.org
 * @since      1.0.0
 *
 * @package    Bvg_Event
 * @subpackage Bvg_Event/admin
 */



/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bvg_Event
 * @subpackage Bvg_Event/admin
 * @author     Laurent Dorier <lolo_irie@etalkers.org>
 */
class Bvg_Event_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * The items list.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    public $ausschreibungen_list;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action( 'admin_menu', array( $this, 'plugin_menu') );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bvg_Event_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bvg_Event_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bvg-event-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bvg_Event_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bvg_Event_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bvg-event-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Add admin menu
     *
     * @since    1.0.0
     */
	public function plugin_menu(){

	    // Add menu item
        $hook = add_menu_page(
            'Turniere',
            'Turniere',
            'manage_options',
            'bvg-event-settings',
            array( $this, 'bvg_event_settings' ),
            plugin_dir_url( __FILE__ ).'../icons/bvg_event_icon.png',
            20
        );

        // Add submenu items
        //add_submenu_page( 'bvg-event-settings', 'Auschreibungenliste', 'Auschreibungenliste', 'manage_options', 'edit.php?post_type=ausschreibungen');
        add_submenu_page( 'bvg-event-settings', 'Neue Ausschreibung', 'Neue Ausschreibung', 'manage_options', 'post-new.php?post_type=ausschreibungen');

    }

    /**
     * Page to display the items list
     *
     * @since    1.0.0
     */
    public function bvg_event_settings(){

        header( 'location:edit.php?post_type=ausschreibungen' );
        die();

        return true;
    }
}
