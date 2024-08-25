<?php
function testwork7748_register_cities_post_type() {
    $labels = array(
        'name'                  => _x( 'Cities', 'Post type general name', 'testwork7748' ),
        'singular_name'         => _x( 'City', 'Post type singular name', 'testwork7748' ),
        'menu_name'             => _x( 'Cities', 'Admin Menu text', 'testwork7748' ),
        'name_admin_bar'        => _x( 'City', 'Add New on Toolbar', 'testwork7748' ),
        'add_new'               => __( 'Add New', 'testwork7748' ),
        'add_new_item'          => __( 'Add New City', 'testwork7748' ),
        'new_item'              => __( 'New City', 'testwork7748' ),
        'edit_item'             => __( 'Edit City', 'testwork7748' ),
        'view_item'             => __( 'View City', 'testwork7748' ),
        'all_items'             => __( 'All Cities', 'testwork7748' ),
        'search_items'          => __( 'Search Cities', 'testwork7748' ),
        'parent_item_colon'     => __( 'Parent Cities:', 'testwork7748' ),
        'not_found'             => __( 'No cities found.', 'testwork7748' ),
        'not_found_in_trash'    => __( 'No cities found in Trash.', 'testwork7748' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'testwork7748' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'cities' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
    );

    register_post_type( 'cities', $args );
}
add_action( 'init', 'testwork7748_register_cities_post_type' );

function testwork7748_add_cities_meta_box() {
    add_meta_box(
        'cities_meta_box',
        __( 'City Location', 'testwork7748' ),
        'testwork7748_cities_meta_box_callback',
        'cities',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'testwork7748_add_cities_meta_box' );

function testwork7748_cities_meta_box_callback( $post ) {
    wp_nonce_field( 'testwork7748_save_cities_meta_box_data', 'testwork7748_cities_meta_box_nonce' );

    $latitude = get_post_meta( $post->ID, '_latitude', true );
    $longitude = get_post_meta( $post->ID, '_longitude', true );

    echo '<label for="latitude">' . __( 'Latitude:', 'testwork7748' ) . '</label>';
    echo '<input type="text" id="latitude" name="latitude" value="' . esc_attr( $latitude ) . '" size="25" /><br>';

    echo '<label for="longitude">' . __( 'Longitude:', 'testwork7748' ) . '</label>';
    echo '<input type="text" id="longitude" name="longitude" value="' . esc_attr( $longitude ) . '" size="25" /><br>';
}

function testwork7748_save_cities_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['testwork7748_cities_meta_box_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['testwork7748_cities_meta_box_nonce'], 'testwork7748_save_cities_meta_box_data' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( ! isset( $_POST['latitude'] ) || ! isset( $_POST['longitude'] ) ) {
        return;
    }

    $latitude = sanitize_text_field( $_POST['latitude'] );
    $longitude = sanitize_text_field( $_POST['longitude'] );

    update_post_meta( $post_id, '_latitude', $latitude );
    update_post_meta( $post_id, '_longitude', $longitude );
}
add_action( 'save_post', 'testwork7748_save_cities_meta_box_data' );

function testwork7748_register_countries_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Countries', 'Taxonomy general name', 'testwork7748' ),
        'singular_name'              => _x( 'Country', 'Taxonomy singular name', 'testwork7748' ),
        'search_items'               => __( 'Search Countries', 'testwork7748' ),
        'popular_items'              => __( 'Popular Countries', 'testwork7748' ),
        'all_items'                  => __( 'All Countries', 'testwork7748' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Country', 'testwork7748' ),
        'update_item'                => __( 'Update Country', 'testwork7748' ),
        'add_new_item'               => __( 'Add New Country', 'testwork7748' ),
        'new_item_name'              => __( 'New Country Name', 'testwork7748' ),
        'separate_items_with_commas' => __( 'Separate countries with commas', 'testwork7748' ),
        'add_or_remove_items'        => __( 'Add or remove countries', 'testwork7748' ),
        'choose_from_most_used'      => __( 'Choose from the most used countries', 'testwork7748' ),
        'not_found'                  => __( 'No countries found.', 'testwork7748' ),
        'menu_name'                  => __( 'Countries', 'testwork7748' ),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'country' ),
    );

    register_taxonomy( 'countries', 'cities', $args );
}
add_action( 'init', 'testwork7748_register_countries_taxonomy' );

function testwork7748_get_weather_data( $latitude, $longitude ) {
    $api_key = 'c4d7cac41150a490560d03c615a899bf'; // Replace with your actual API key
    $api_url = "http://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&appid=$api_key&units=metric";

    $response = wp_remote_get( $api_url );
    if ( is_wp_error( $response ) ) {
        return false;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( isset( $data['main']['temp'] ) ) {
        return array( 'temp' => $data['main']['temp'] );
    }

    return false;
}

function testwork7748_enqueue_scripts() {
    wp_enqueue_script( 'testwork7748-ajax', get_stylesheet_directory_uri() . '/js/ajax-search.js', array( 'jquery' ), null, true );
    wp_localize_script( 'testwork7748-ajax', 'testwork7748_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'testwork7748_enqueue_scripts' );

function testwork7748_ajax_search() {
    global $wpdb;

    $search_term = sanitize_text_field( $_POST['search_term'] );
    $table_name = $wpdb->prefix . 'posts';
    $query = $wpdb->prepare("
        SELECT post_title
        FROM $table_name
        WHERE post_type = 'cities' AND post_status = 'publish' AND post_title LIKE %s
    ", '%' . $wpdb->esc_like( $search_term ) . '%');

    $results = $wpdb->get_results( $query );

    if ( $results ) {
        foreach ( $results as $city ) {
            echo '<div>' . esc_html( $city->post_title ) . '</div>';
        }
    } else {
        echo '<div>' . __( 'No results found.', 'testwork7748' ) . '</div>';
    }

    wp_die();
}
add_action( 'wp_ajax_testwork7748_search', 'testwork7748_ajax_search' );
add_action( 'wp_ajax_nopriv_testwork7748_search', 'testwork7748_ajax_search' );