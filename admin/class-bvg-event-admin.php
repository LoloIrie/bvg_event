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
 * The list functionnality
 *
 *
 */

/**
 * The WP list class for the plugin.
 *
 * Class to display items lists
 *
 * @package    Bvg_Event
 * @subpackage Bvg_Event/admin
 * @author     Laurent Dorier <lolo_irie@etalkers.org>
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Items_Liste extends WP_List_Table
{

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Ausschreibung', 'sp'), //singular name of the listed records
            'plural' => __('Ausschreibungen', 'sp'), //plural name of the listed records
            'ajax' => false //should this table support ajax?

        ]);

    }

    /**
     * Retrieve ausschreibung data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_ausschreibungen($per_page = 10, $page_number = 1)
    {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ausschreibungen";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


        $result = $wpdb->get_results($sql, 'ARRAY_A');
        // var_dump( $result );
        return $result;
    }

    /**
     * Delete a record.
     *
     * @param int $id ausschreibung ID
     */
    public static function delete_ausschreibung( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}ausschreibungen",
            [ 'ID' => $id ],
            [ '%d' ]
        );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ausschreibungen";

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no record data is available */
    public function no_items() {
        __( 'Noch keine Ausschreibung.', 'sp' );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'sp_delete_ausschreibung' );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&ausschreibung=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
        ];

        return $title . $this->row_actions( $actions );
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'name':
            case 'date_start':
            case 'date_end':
            case 'location':
            case 'disciplines':
            case 'levels':
            case 'description':
            case 'files':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'name'    => __( 'Name', 'sp' ),
            'date_start' => __( 'Date', 'sp' ),
            'date_end' => __( 'bis', 'sp' ),
            'location' => __( 'Address', 'sp' ),
            'disciplines'    => __( 'Disziplinen', 'sp' ),
            'levels'    => __( 'Spielklasse', 'sp' ),
            'description'    => __( 'Beschreibung', 'sp' ),
            'files'    => __( 'Dateien', 'sp' )
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true ),
            'location' => array( 'location', true ),
            'date_start' => array( 'date', false )
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'ausschreibungen_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );


        $this->items = self::get_ausschreibungen( $per_page, $current_page );
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'sp_delete_ausschreibung' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_ausschreibung( absint( $_GET['ausschreibung'] ) );

                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_ausschreibung( $id );

            }

            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }
}

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

        add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
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
            [ $this, 'bvg_event_settings' ],
            plugin_dir_url( __FILE__ ).'../icons/bvg_event_icon.png',
            20
        );

        // Add submenu items
        //add_submenu_page( 'bvg-event-settings', 'Auschreibungenliste', 'Auschreibungenliste', 'manage_options', 'edit.php?post_type=ausschreibungen');
        add_submenu_page( 'bvg-event-settings', 'Neue Ausschreibung', 'Neue Ausschreibung', 'manage_options', 'post-new.php?post_type=ausschreibungen');

        add_action( "load-$hook", [ $this, 'screen_option' ] );
    }

    /**
     * Page to add a new item
     *
     * @since    1.0.0
     */
    public function bvg_event_add(){

        // add_meta_box("ausschreibungen_infos", "Infos", "vecb_tag_options", "ausschreibungen", "normal", "low");

        $event_name = 'Default';
        $event_date_start = '';
        $event_date_end = '';
        $event_ort = '';
        $event_discipline = array();
        $event_level = array();
        $event_description = '';


        $html_form = '';

        $html_form .= '<div class="wrap">';
        $html_form .= '<form name="bvg_event_form" method="post" action="" >';

        $html_form .= '<input type="hidden" name="event_action_hidden" value="new" />';

        $html_form .= '<p>'. __("Turniername: ", 'bvg_event' ) .'<input class="bvg_admin_field" type="text" id="event_name" name="event_name" value="'. $event_name .'" /></p>';

        $html_form .= '<p>'. __("Turnier Datum Start: ", 'bvg_event' ) .'<input class="bvg_admin_field" type="date" id="event_date_start" name="event_date_start" value="'. $event_date_start .'" /></p>';

        $html_form .= '<p>'. __("Turnier Datum Ende: ", 'bvg_event' ) .'<input class="bvg_admin_field" type="date" id="event_date_end" name="event_date_end" value="'. $event_date_end .'" /></p>';

        $html_form .= '<p>'. __("Ort: ", 'bvg_event' ) .'<input class="bvg_admin_field" type="text" id="event_ort" name="event_ort" value="'. $event_ort .'" /></p>';

        $html_form .= '<p>'. __("Disziplinen: ", 'bvg_event' ) .'</p>';
        $html_form .= '<p>';
        $html_form .= __("HE: " ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_discipline_1" name="event_discipline[]" value="1" '.( in_array( 1 , $event_discipline ) ? 'selected="selected"' : '' ).' />';
        $html_form .= __("DE: " ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_discipline_2" name="event_discipline[]" value="2" '.( in_array( 2 , $event_discipline ) ? 'selected="selected"' : '' ).' />';
        $html_form .= __("HD: " ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_discipline_3" name="event_discipline[]" value="3" '.( in_array( 3 , $event_discipline ) ? 'selected="selected"' : '' ).' />';
        $html_form .= __("DD: " ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_discipline_4" name="event_discipline[]" value="4" '.( in_array( 4 , $event_discipline ) ? 'selected="selected"' : '' ).' />';
        $html_form .= __("MX: " ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_discipline_5" name="event_discipline[]" value="5" '.( in_array( 5 , $event_discipline ) ? 'selected="selected"' : '' ).' />';
        $html_form .= __("MM: " ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_discipline_6" name="event_discipline[]" value="6" '.( in_array( 6 , $event_discipline ) ? 'selected="selected"' : '' ).' />';
        $html_form .= __("MA: " ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_discipline_7" name="event_discipline[]" value="7" '.( in_array( 7 , $event_discipline ) ? 'selected="selected"' : '' ).' />';
        $html_form .= '</p>';

        $html_form .= '<p>'. __("Turnierklassen/Spielniveau: ", 'bvg_event' ) .'</p>';
        $html_form .= '<p>';
        $html_form .= __("Hobby: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_1" name="event_level[]" value="1" />';
        $html_form .= __("C-Klasse: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_2" name="event_level[]" value="2" />';
        $html_form .= __("B-Klasse: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_3" name="event_level[]" value="3" />';
        $html_form .= __("A-Klasse: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_4" name="event_level[]" value="4" />';
        $html_form .= __("Bezirklasse: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_5" name="event_level[]" value="5" />';
        $html_form .= __("Verbandsliga: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_6" name="event_level[]" value="6" />';
        $html_form .= __("Hessenliga: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_7" name="event_level[]" value="7" />';
        $html_form .= __("Oberliga: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_8" name="event_level[]" value="8" />';
        $html_form .= __("Regionalliga: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_9" name="event_level[]" value="9" />';
        $html_form .= __("2. Bundesliga: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_10" name="event_level[]" value="10" />';
        $html_form .= __("1. Bundesliga: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_11" name="event_level[]" value="11" />';

        $html_form .= __("Jugend: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_12" name="event_level[]" value="12" />';
        $html_form .= __("Schüler: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_13" name="event_level[]" value="13" />';
        $html_form .= __("Minis: ", 'bvg_event' ) .'<input class="bvg_admin_field checkbox_row" type="checkbox" id="event_level_14" name="event_level[]" value="14" />';
        $html_form .= '</p>';

        $html_form .= '<p>'. __("Beschreibung: ", 'bvg_event' ) .'<textarea class="bvg_admin_field" id="event_description" name="event_description" value="" />'. $event_description .'</textarea></p>';

        $html_form .= '<p><input class="bvg_admin_field" type="submit" id="event_button_submit" name="event_button_submit" value="'. __("Publizieren", 'bvg_event' ) .'" /></p>';

        $html_form .= '</form>';
        $html_form .= '</div>';

        echo $html_form;

        return true;
    }

    /**
     * Page to display the items list
     *
     * @since    1.0.0
     */

    public function screen_option()
    {

        $option = 'per_page';
        $args = [
            'label' => 'Ausschreibungen',
            'default' => 10,
            'option' => 'ausschreibungen_per_page'
        ];

        add_screen_option($option, $args);

        $this->ausschreibungen_list = new Items_Liste();

    }
    /**
     * Page to display the items list
     *
     * @since    1.0.0
     */

    public function bvg_event_settings(){

        header( 'location:edit.php?post_type=ausschreibungen' );
        die();

        echo 'SETTINGS !!!!!!';

        ?>
        <div class="wrap">
            <h2>Ausschreibungen</h2>
            <form method="post" action="?page=bvg-event-add">
                <input name="doaction" class="button-secondary action" type="submit" value="Neue Ausschreibung"/>
            </form>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $this->ausschreibungen_list->prepare_items();
                                $this->ausschreibungen_list->display();
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php

        return true;
    }
}


