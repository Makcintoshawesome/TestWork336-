<?php
/*
Template Name: Countries, Cities, and Temperatures
*/

get_header();
?>
<div>
    <input type="text" id="city-search" placeholder="<?php _e( 'Search cities...', 'testwork7748' ); ?>" />
    <div id="search-results"></div>
</div>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        do_action( 'testwork7748_before_countries_cities_temperatures' );

        global $wpdb;
        $table_name = $wpdb->prefix . 'posts';
        $query = "
            SELECT p.post_title AS city, t.name AS country, m.meta_value AS latitude, n.meta_value AS longitude
            FROM $table_name p
            JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
            JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
            JOIN $wpdb->terms t ON tt.term_id = t.term_id
            LEFT JOIN $wpdb->postmeta m ON p.ID = m.post_id AND m.meta_key = '_latitude'
            LEFT JOIN $wpdb->postmeta n ON p.ID = n.post_id AND n.meta_key = '_longitude'
            WHERE p.post_type = 'cities' AND p.post_status = 'publish'
        ";

        $results = $wpdb->get_results( $query );

        if ( $results ) {
            echo '<table>';
            echo '<thead>';
            echo '<tr><th>' . __( 'Country', 'testwork7748' ) . '</th><th>' . __( 'City', 'testwork7748' ) . '</th><th>' . __( 'Temperature (°C)', 'testwork7748' ) . '</th></tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ( $results as $row ) {
                $weather_data = testwork7748_get_weather_data( $row->latitude, $row->longitude );
                $temperature = $weather_data ? $weather_data['temp'] : __( 'N/A', 'testwork7748' );

                echo '<tr>';
                echo '<td>' . esc_html( $row->country ) . '</td>';
                echo '<td>' . esc_html( $row->city ) . '</td>';
                echo '<td>' . esc_html( $temperature ) . '°C</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>' . __( 'No cities found.', 'testwork7748' ) . '</p>';
        }

        do_action( 'testwork7748_after_countries_cities_temperatures' );
        ?>
    </main>
</div>

<?php
get_sidebar();
get_footer();