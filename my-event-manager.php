<?php
/**
 * Plugin Name: My Event Manager
 * Plugin URI: https://yourwebsite.com/my-event-manager
 * Description: A simple WordPress plugin to manage and display events.
 * Version: 1.0.0
 * Author: Simejiya Zeel Rajenbhai
 * License: GPL2
 * Text Domain: my-event-manager
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class for My Event Manager.
 * Handles custom post type, meta boxes, shortcodes, and enqueueing.
 */
class My_Event_Manager {

    /**
     * Constructor.
     * Initializes the plugin by setting up all necessary hooks.
     */
    public function __construct() {
        // Load plugin text domain for internationalization
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // Activation and Deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Core plugin hooks
        add_action( 'init', array( $this, 'register_event_cpt' ) );
        add_action( 'init', array( $this, 'register_event_category_taxonomy' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_event_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_event_meta_data' ) );
        
        // Enqueue frontend styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

        // Register the shortcode
        add_shortcode( 'my_events', array( $this, 'events_shortcode' ) );

        // Filter for single post templates to allow custom template for events
        add_filter( 'template_include', array( $this, 'include_event_template' ) );

        // Admin Custom Columns and Sortable Columns
        add_filter( 'manage_event_posts_columns', array( $this, 'set_event_columns' ) );
        add_action( 'manage_event_posts_custom_column', array( $this, 'display_event_columns' ), 10, 2 );
        add_filter( 'manage_edit-event_sortable_columns', array( $this, 'set_sortable_event_columns' ) );
        add_action( 'pre_get_posts', array( $this, 'sort_event_columns' ) );

        // Enqueue admin scripts for date picker
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * Loads the plugin text domain for internationalization.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'my-event-manager', // Your text domain
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Path to translations
        );
    }

    /**
     * Plugin activation hook.
     * Registers CPTs and flushes rewrite rules to ensure URLs work immediately.
     */
    public function activate() {
        $this->register_event_cpt(); 
        $this->register_event_category_taxonomy(); 
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation hook.
     * Flushes rewrite rules.
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Registers the 'Event' Custom Post Type.
     */
    public function register_event_cpt() {
        $labels = array(
            'name'                  => _x( 'Events', 'Post Type General Name', 'my-event-manager' ),
            'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'my-event-manager' ),
            'menu_name'             => __( 'Events', 'my-event-manager' ),
            'name_admin_bar'        => __( 'Event', 'my-event-manager' ),
            'archives'              => __( 'Event Archives', 'my-event-manager' ),
            'attributes'            => __( 'Event Attributes', 'my-event-manager' ),
            'parent_item_colon'     => __( 'Parent Event:', 'my-event-manager' ),
            'all_items'             => __( 'All Events', 'my-event-manager' ),
            'add_new_item'          => __( 'Add New Event', 'my-event-manager' ),
            'add_new'               => __( 'Add New', 'my-event-manager' ),
            'new_item'              => __( 'New Event', 'my-event-manager' ),
            'edit_item'             => __( 'Edit Event', 'my-event-manager' ),
            'update_item'           => __( 'Update Event', 'my-event-manager' ),
            'view_item'             => __( 'View Event', 'my-event-manager' ),
            'view_items'            => __( 'View Events', 'my-event-manager' ),
            'search_items'          => __( 'Search Event', 'my-event-manager' ),
            'not_found'             => __( 'No events found', 'my-event-manager' ),
            'not_found_in_trash'    => __( 'No events found in Trash', 'my-event-manager' ),
            'featured_image'        => __( 'Featured Image', 'my-event-manager' ),
            'set_featured_image'    => __( 'Set featured image', 'my-event-manager' ),
            'remove_featured_image' => __( 'Remove featured image', 'my-event-manager' ),
            'use_featured_image'    => __( 'Use as featured image', 'my-event-manager' ),
            'insert_into_item'      => __( 'Insert into event', 'my-event-manager' ),
            'uploaded_to_this_item' => __( 'Uploaded to this event', 'my-event-manager' ),
            'items_list'            => __( 'Events list', 'my-event-manager' ),
            'items_list_navigation' => __( 'Events list navigation', 'my-event-manager' ),
            'filter_items_list'     => __( 'Filter events list', 'my-event-manager' ),
        );
        
        $args = array(
            'label'                 => __( 'Event', 'my-event-manager' ),
            'description'           => __( 'Manage and display events', 'my-event-manager' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'            => array( 'event_category' ), 
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-calendar-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type( 'event', $args );
    }

    /**
     * Registers the 'Event Category' custom taxonomy.
     */
    public function register_event_category_taxonomy() {
        $labels = array(
            'name'                       => _x( 'Event Categories', 'Taxonomy General Name', 'my-event-manager' ),
            'singular_name'              => _x( 'Event Category', 'Taxonomy Singular Name', 'my-event-manager' ),
            'menu_name'                  => __( 'Event Categories', 'my-event-manager' ),
            'all_items'                  => __( 'All Event Categories', 'my-event-manager' ),
            'parent_item'                => __( 'Parent Event Category', 'my-event-manager' ),
            'parent_item_colon'          => __( 'Parent Event Category:', 'my-event-manager' ),
            'new_item_name'              => __( 'New Event Category Name', 'my-event-manager' ),
            'add_new_item'               => __( 'Add New Event Category', 'my-event-manager' ),
            'edit_item'                  => __( 'Edit Event Category', 'my-event-manager' ),
            'update_item'                => __( 'Update Event Category', 'my-event-manager' ),
            'view_item'                  => __( 'View Event Category', 'my-event-manager' ),
            'separate_items_with_commas' => __( 'Separate event categories with commas', 'my-event-manager' ),
            'add_or_remove_items'        => __( 'Add or remove event categories', 'my-event-manager' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'my-event-manager' ),
            'popular_items'              => __( 'Popular Event Categories', 'my-event-manager' ),
            'search_items'               => __( 'Search Event Categories', 'my-event-manager' ),
            'not_found'                  => __( 'No Event Categories Found', 'my-event-manager' ),
            'no_terms'                   => __( 'No Event Categories', 'my-event-manager' ),
            'items_list'                 => __( 'Event Categories list', 'my-event-manager' ),
            'items_list_navigation'      => __( 'Event Categories list navigation', 'my-event-manager' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true, // Set to true for a category-like taxonomy (can have parents/children)
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true, // Shows column on the All Events screen
            'query_var'                  => true,
            'rewrite'                    => array( 'slug' => 'event-category' ), // The slug for URLs
        );
        register_taxonomy( 'event_category', array( 'event' ), $args ); // 'event_category' is the taxonomy slug, 'event' is the post type it applies to.
    }

    /**
     * Adds the meta box container to the 'Event' CPT screen.
     */
    public function add_event_meta_box() {
        add_meta_box(
            'my_event_manager_event_details',
            __( 'Event Details', 'my-event-manager' ),
            array( $this, 'render_event_meta_box' ),
            'event',
            'normal',
            'high'
        );
    }

    /**
     * Renders the meta box content for the Event CPT.
     *
     * @param WP_Post $post The post object.
     */
    public function render_event_meta_box( $post ) {
        // Add a nonce field for security
        wp_nonce_field( 'my_event_manager_save_event_meta', 'my_event_manager_event_nonce' );

        // Get existing meta data
        $event_date = get_post_meta( $post->ID, '_my_event_manager_event_date', true );
        $event_time = get_post_meta( $post->ID, '_my_event_manager_event_time', true );
        $event_location = get_post_meta( $post->ID, '_my_event_manager_event_location', true );
        $event_speakers = get_post_meta( $post->ID, '_my_event_manager_event_speakers', true );
        $event_short_description = get_post_meta( $post->ID, '_my_event_manager_event_short_description', true );
        ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="my_event_manager_event_date"><?php _e( 'Event Date', 'my-event-manager' ); ?></label></th>
                    <td>
                        <input type="date" id="my_event_manager_event_date" name="my_event_manager_event_date"
                               value="<?php echo esc_attr( $event_date ); ?>" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th><label for="my_event_manager_event_time"><?php _e( 'Event Time', 'my-event-manager' ); ?></label></th>
                    <td>
                        <input type="time" id="my_event_manager_event_time" name="my_event_manager_event_time"
                               value="<?php echo esc_attr( $event_time ); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th><label for="my_event_manager_event_location"><?php _e( 'Location', 'my-event-manager' ); ?></label></th>
                    <td>
                        <input type="text" id="my_event_manager_event_location" name="my_event_manager_event_location"
                               value="<?php echo esc_attr( $event_location ); ?>" class="large-text" placeholder="<?php esc_attr_e( 'e.g., Online, Main Hall, Auditorium A', 'my-event-manager' ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="my_event_manager_event_speakers"><?php _e( 'Speaker(s)', 'my-event-manager' ); ?></label></th>
                    <td>
                        <input type="text" id="my_event_manager_event_speakers" name="my_event_manager_event_speakers"
                               value="<?php echo esc_attr( $event_speakers ); ?>" class="large-text" placeholder="<?php esc_attr_e( 'e.g., John Doe, Jane Smith', 'my-event-manager' ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="my_event_manager_event_short_description"><?php _e( 'Short Description', 'my-event-manager' ); ?></label></th>
                    <td>
                        <textarea id="my_event_manager_event_short_description" name="my_event_manager_event_short_description"
                                    rows="3" class="large-text" placeholder="<?php esc_attr_e( 'A brief summary of the event.', 'my-event-manager' ); ?>"><?php echo esc_textarea( $event_short_description ); ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php
    }

    /**
     * Saves the custom meta box data for the Event CPT.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save_event_meta_data( $post_id ) {
        // Check if our nonce is set and valid for security.
        if ( ! isset( $_POST['my_event_manager_event_nonce'] ) ||
            ! wp_verify_nonce( $_POST['my_event_manager_event_nonce'], 'my_event_manager_save_event_meta' ) ) {
            return $post_id;
        }

        // Check if the current user has permission to edit the post.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        // Check if this is an autosave. If so, our form has not been submitted.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the post type. Only save for our 'event' CPT.
        if ( get_post_type( $post_id ) !== 'event' ) {
            return $post_id;
        }

        // Sanitize and save the custom fields.
        $fields = array(
            '_my_event_manager_event_date'          => 'date', // Sanitize as date
            '_my_event_manager_event_time'          => 'time', // Sanitize as time
            '_my_event_manager_event_location'      => 'text',
            '_my_event_manager_event_speakers'      => 'text',
            '_my_event_manager_event_short_description' => 'textarea', // Sanitize as textarea
        );

        foreach ( $fields as $field_name => $type ) {
            // Remove the leading underscore to get the $_POST key.
            $post_key = substr( $field_name, 1 );

            if ( isset( $_POST[ $post_key ] ) ) {
                $data = '';
                switch ( $type ) {
                    case 'date':
                        $data = sanitize_text_field( $_POST[ $post_key ] ); // Dates are strings
                        // Basic date validation (optional but good)
                        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $data)) {
                            $data = ''; // Clear invalid date
                        }
                        break;
                    case 'time':
                        $data = sanitize_text_field( $_POST[ $post_key ] ); // Times are strings
                        // Basic time validation (optional)
                        if (!preg_match("/^([01]\d|2[0-3]):([0-5]\d)$/", $data)) {
                             $data = ''; // Clear invalid time
                        }
                        break;
                    case 'textarea':
                        $data = sanitize_textarea_field( $_POST[ $post_key ] );
                        break;
                    case 'text':
                    default:
                        $data = sanitize_text_field( $_POST[ $post_key ] );
                        break;
                }
                update_post_meta( $post_id, $field_name, $data );
            } else {
                delete_post_meta( $post_id, $field_name ); // Delete if the field is empty or not set.
            }
        }
    }

    /**
     * Enqueues the plugin's frontend stylesheet.
     */
    public function enqueue_styles() {
        // Only enqueue if the shortcode is actually on the page to optimize performance.
        global $post;
        // Check if it's a post object and if the shortcode exists in its content.
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'my_events' ) ) {
            wp_enqueue_style(
                'my-event-manager-style',
                plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
                array(),
                '1.0.0' // Version number, increment on updates
            );
        }
    }

    /**
     * Enqueues admin scripts and styles for the date picker.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_admin_scripts( $hook ) {
        // Only load on the 'event' post type edit screen
        if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
            global $post;
            if ( $post && 'event' === $post->post_type ) {
                // Enqueue jQuery UI Datepicker assets
                wp_enqueue_script( 'jquery-ui-datepicker' );
                wp_enqueue_style( 'jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

                // Your custom script to initialize the datepicker
                wp_enqueue_script(
                    'my-event-manager-admin-script',
                    plugin_dir_url( __FILE__ ) . 'assets/js/admin.js',
                    array( 'jquery', 'jquery-ui-datepicker' ),
                    '1.0.0',
                    true // In footer
                );
            }
        }
    }

    /**
     * Callback function for the [my_events] shortcode.
     * Displays a list of upcoming events, with optional category filtering.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output for the events list.
     */
    public function events_shortcode( $atts ) {
        // Parse shortcode attributes
        $atts = shortcode_atts(
            array(
                'category' => '', // Default to no specific category
                'count'    => -1, // Number of events to show (-1 for all)
            ),
            $atts,
            'my_events'
        );

        $category_slug = sanitize_text_field( $atts['category'] );
        $posts_per_page = intval( $atts['count'] );

        // Start building the output HTML.
        $output = '<div class="my-event-manager-events-list-wrapper">'; // Added wrapper for better styling control
        $output .= '<h2>' . __( 'Upcoming Events', 'my-event-manager' ) . '</h2>';
        $output .= '<div class="my-event-manager-events-list">';

        // Define our query arguments for the 'event' custom post type.
        $args = array(
            'post_type'      => 'event',
            'posts_per_page' => $posts_per_page,
            'post_status'    => 'publish', // Only published events
            'meta_key'       => '_my_event_manager_event_date', // Order by event date
            'orderby'        => 'meta_value',
            'order'          => 'ASC', // Ascending order (oldest to newest)
            'meta_query'     => array( // Show only events from today onwards
                array(
                    'key'     => '_my_event_manager_event_date',
                    'value'   => current_time( 'Y-m-d' ), // Get today's date in YYYY-MM-DD format
                    'compare' => '>=', // Greater than or equal to today
                    'type'    => 'DATE',
                ),
            ),
        );

        // Add tax_query if a category slug is provided
        if ( ! empty( $category_slug ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'event_category', // The slug of our custom taxonomy
                    'field'    => 'slug',           // Query by category slug
                    'terms'    => $category_slug,   // The slug(s) to query
                ),
            );
        }

        // Create a new WP_Query instance.
        $events_query = new WP_Query( $args );

        // Check if there are any events to display.
        if ( $events_query->have_posts() ) {
            while ( $events_query->have_posts() ) {
                $events_query->the_post(); // Setup post data for the loop.

                // Get custom fields for the current event.
                $event_date              = get_post_meta( get_the_ID(), '_my_event_manager_event_date', true );
                $event_time              = get_post_meta( get_the_ID(), '_my_event_manager_event_time', true );
                $event_location          = get_post_meta( get_the_ID(), '_my_event_manager_event_location', true );
                $event_speakers          = get_post_meta( get_the_ID(), '_my_event_manager_event_speakers', true );
                $event_short_description = get_post_meta( get_the_ID(), '_my_event_manager_event_short_description', true );

                // Format the date for display (optional)
                $formatted_date = ! empty( $event_date ) ? date_i18n( get_option( 'date_format' ), strtotime( $event_date ) ) : '';
                $formatted_time = ! empty( $event_time ) ? date_i18n( get_option( 'time_format' ), strtotime( '1970-01-01 ' . $event_time ) ) : ''; // Use a dummy date for time formatting

                // Build HTML for each event.
                $output .= '<div class="my-event-manager-event-item">';
                if ( has_post_thumbnail() ) {
                    $output .= '<div class="event-thumbnail">' . get_the_post_thumbnail( null, 'medium' ) . '</div>'; // Display featured image
                }
                $output .= '<h3><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h3>'; // Event title with link
                
                if ( ! empty( $formatted_date ) ) {
                    $output .= '<p class="event-date"><strong>' . __( 'Date:', 'my-event-manager' ) . '</strong> ' . esc_html( $formatted_date ) . '</p>';
                }
                if ( ! empty( $formatted_time ) ) {
                    $output .= '<p class="event-time"><strong>' . __( 'Time:', 'my-event-manager' ) . '</strong> ' . esc_html( $formatted_time ) . '</p>';
                }
                if ( ! empty( $event_location ) ) {
                    $output .= '<p class="event-location"><strong>' . __( 'Location:', 'my-event-manager' ) . '</strong> ' . esc_html( $event_location ) . '</p>';
                }
                if ( ! empty( $event_speakers ) ) {
                    $output .= '<p class="event-speakers"><strong>' . __( 'Speaker(s):', 'my-event-manager' ) . '</strong> ' . esc_html( $event_speakers ) . '</p>';
                }
                if ( ! empty( $event_short_description ) ) {
                    $output .= '<p class="event-description">' . esc_html( $event_short_description ) . '</p>';
                }
                $output .= '<p class="event-read-more"><a href="' . esc_url( get_permalink() ) . '">' . __( 'View Details', 'my-event-manager' ) . '</a></p>';
                $output .= '</div>';
            }
        } else {
            $output .= '<p>' . __( 'No upcoming events found.', 'my-event-manager' ) . '</p>';
        }

        // Reset post data to avoid conflicts with other queries on the page.
        wp_reset_postdata();

        $output .= '</div>'; // Close .my-event-manager-events-list
        $output .= '</div>'; // Close .my-event-manager-events-list-wrapper

        return $output;
    }

    /**
     * Filters the template hierarchy to use single-event.php for 'event' CPT.
     *
     * @param string $template The path to the template file.
     * @return string The filtered template path.
     */
    public function include_event_template( $template ) {
        // If it's a single 'event' and the theme doesn't have its own single-event.php
        if ( is_singular( 'event' ) && ! file_exists( get_stylesheet_directory() . '/single-event.php' ) ) {
            $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/single-event.php';
            // Check if our plugin's template exists
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Sets custom columns for the 'Event' CPT list table in admin.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function set_event_columns( $columns ) {
        $new_columns = array(
            'cb'                => '<input type="checkbox" />',
            'title'             => __( 'Event Title', 'my-event-manager' ),
            'event_date'        => __( 'Date', 'my-event-manager' ),
            'event_time'        => __( 'Time', 'my-event-manager' ),
            'event_location'    => __( 'Location', 'my-event-manager' ),
            'event_speakers'    => __( 'Speaker(s)', 'my-event-manager' ),
            'event_category'    => __( 'Category', 'my-event-manager' ), // Add category column
            'date'              => __( 'Published Date', 'my-event-manager' ), // WordPress default 'date' column
        );
        return $new_columns;
    }

    /**
     * Displays content for custom columns in the 'Event' CPT list table.
     *
     * @param string $column_name The name of the column to display.
     * @param int    $post_id     The current post ID.
     */
    public function display_event_columns( $column_name, $post_id ) {
        switch ( $column_name ) {
            case 'event_date':
                $event_date = get_post_meta( $post_id, '_my_event_manager_event_date', true );
                echo esc_html( $event_date ? date_i18n( get_option( 'date_format' ), strtotime( $event_date ) ) : '-' );
                break;
            case 'event_time':
                $event_time = get_post_meta( $post_id, '_my_event_manager_event_time', true );
                echo esc_html( $event_time ? date_i18n( get_option( 'time_format' ), strtotime( '1970-01-01 ' . $event_time ) ) : '-' );
                break;
            case 'event_location':
                $event_location = get_post_meta( $post_id, '_my_event_manager_event_location', true );
                echo esc_html( $event_location ?: '-' );
                break;
            case 'event_speakers':
                $event_speakers = get_post_meta( $post_id, '_my_event_manager_event_speakers', true );
                echo esc_html( $event_speakers ?: '-' );
                break;
            case 'event_category':
                $terms = get_the_terms( $post_id, 'event_category' );
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    $category_names = array();
                    foreach ( $terms as $term ) {
                        $category_names[] = '<a href="' . esc_url( admin_url( 'edit.php?post_type=event&event_category=' . $term->slug ) ) . '">' . esc_html( $term->name ) . '</a>';
                    }
                    echo implode( ', ', $category_names );
                } else {
                    echo '-';
                }
                break;
        }
    }

    /**
     * Makes custom columns sortable in the 'Event' CPT list table.
     *
     * @param array $columns Existing sortable columns.
     * @return array Modified sortable columns.
     */
    public function set_sortable_event_columns( $columns ) {
        $columns['event_date'] = 'event_date'; // Make 'Event Date' column sortable
        return $columns;
    }

    /**
     * Handles custom sorting for 'Event' CPT based on custom columns.
     *
     * @param WP_Query $query The current WP_Query object.
     */
    public function sort_event_columns( $query ) {
        // Only modify the main query on the admin screen for our 'event' post type.
        if ( ! is_admin() || ! $query->is_main_query() || 'event' !== $query->get( 'post_type' ) ) {
            return;
        }

        $orderby = $query->get( 'orderby' );

        if ( 'event_date' === $orderby ) {
            $query->set( 'meta_key', '_my_event_manager_event_date' );
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'type', 'DATE' ); // Ensure it sorts as a date
        }
    }

} // End of My_Event_Manager class

// Instantiate the plugin class.
// This creates an instance of your plugin, which triggers the constructor
// and sets up all the hooks.
new My_Event_Manager();
