<?php
/*
Plugin Name: BVG Turniere
Description: Tool to publish tournament invitations
Version: 1.0
Author: Laurent Dorier
Author URI: http://etalkers.org
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
*/

// Don't call the file directly
if ( !defined( 'ABSPATH' ) ) die();

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ausschreibungen_Liste extends WP_List_Table
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
        _e( 'Noch keine Ausschreibung.', 'sp' );
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

class Bvg_turniere{

    // class instance
    static $instance;

    // Ausschreibung WP_List_Table object
    public $ausschreibungen_obj;

    // class constructor
    public function __construct() {
        add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
        add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
    }

    public static function set_screen( $status, $option, $value ) {
        return $value;
    }

    public function plugin_menu() {

        $hook = add_menu_page(
            'Ausschreibung publizieren',
            'Ausschreibung publizieren',
            'manage_options',
            'bvg_turniere',
            [ $this, 'bvg_turniere_admin' ],
            plugin_dir_url( __FILE__ ).'icons/bvg_turniere_icon.png',
            20
        );

        add_action( "load-$hook", [ $this, 'screen_option' ] );

        add_plugins_page('Neue Ausschreibung', 'Neue Ausschreibung', 'edit_posts', 'bvg_turniere_add', 'bvg_turniere_add');

    }


    /**
     * Plugin settings page
     */
    public function bvg_turniere_admin() {
        ?>
        <div class="wrap">
            <h2>Ausschreibungen</h2>
            <form method="post" action="?page=bvg_turniere_add">
                <input name="doaction" class="button-secondary action" type="submit" value="Neue Ausschreibung"/>
            </form>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $this->ausschreibungen_obj->prepare_items();
                                $this->ausschreibungen_obj->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

    /**
     * Screen options
     */
    public function screen_option() {

        $option = 'per_page';
        $args   = [
            'label'   => 'Ausschreibungen',
            'default' => 10,
            'option'  => 'ausschreibungen_per_page'
        ];

        add_screen_option( $option, $args );

        $this->ausschreibungen_obj = new Ausschreibungen_Liste();
    }


    /** Singleton instance */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}

/**
 * Plugin Add new page
 */
function bvg_turniere_add(){
    // $Ausschreibung_obj = Bvg_turniere::get_instance();

    $content = '';

    //Bvg_turniere::get_instance();

    // Name
    $content .= '<div id="bvg_turniere_add_fieldset" class="postbox">';

    $content .= '<label for="name" class="bvg_label">Turniername:</label><input type="text" name="name" id="name" />';

    $content .= '<label for="date_start" class="bvg_label">Date Start:</label><input type="date" name="date_start" id="date_start" />';

    $content .= '<label for="date_end" class="bvg_label">Date Ende:</label><input type="date" name="date_end" id="date_end" />';

    $content .= '<label for="date_end" class="bvg_label">Adresse:</label><textarea name="adresse" id="adresse" rows="4" cols="45"></textarea>';

    $content .= '<label for="date_end" class="bvg_label">Beschreibung:</label><textarea name="beschreibung" id="beschreibung" rows="4" cols="45"></textarea>';

    $content .= '<label for="name" class="bvg_label">Disziplinen:</label><input type="text" name="disziplinen" id="disziplinen" />';

    $content .= '<label for="name" class="bvg_label">Spielniveau:</label><input type="text" name="spielniveau" id="spielniveau" />';

    $content .= '<label for="name" class="bvg_label">Dateien:</label><input type="text" name="dateien" id="dateien" />';

    $content .= '</div>';

    echo $content;
}


add_action( 'plugins_loaded', function () {
    Bvg_turniere::get_instance();
} );







