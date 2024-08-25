class TestWork7748_Weather_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'testwork7748_weather_widget',
            __( 'TestWork7748 Weather', 'testwork7748' ),
            array( 'description' => __( 'Displays the current temperature of a city.', 'testwork7748' ) )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $city_id = ! empty( $instance['city'] ) ? absint( $instance['city'] ) : 0;
        if ( $city_id ) {
            $city = get_post( $city_id );
            if ( $city && 'cities' === $city->post_type ) {
                $latitude = get_post_meta( $city_id, '_latitude', true );
                $longitude = get_post_meta( $city_id, '_longitude', true );
                $weather_data = testwork7748_get_weather_data( $latitude, $longitude );
                if ( $weather_data ) {
                    echo '<p>' . sprintf( __( 'Current temperature in %s: %s°C', 'testwork7748' ), $city->post_title, $weather_data['temp'] ) . '</p>';
                } else {
                    echo '<p>' . __( 'Failed to retrieve weather data.', 'testwork7748' ) . '</p>';
                }
            } else {
                echo '<p>' . __( 'Invalid city selected.', 'testwork7748' ) . '</p>';
            }
        } else {
            echo '<p>' . __( 'Please select a city in the widget settings.', 'testwork7748' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $city = ! empty( $instance['city'] ) ? $instance['city'] : '';

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'testwork7748' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'city' ); ?>"><?php _e( 'City:', 'testwork7748' ); ?></label>
            <?php
            $cities = get_posts( array(
                'post_type' => 'cities',
                'posts_per_page' => -1,
            ) );
            ?>
            <select class="widefat" id="<?php echo $this->get_field_id( 'city' ); ?>" name="<?php echo $this->get_field_name( 'city' ); ?>">
                <option value=""><?php _e( '-- Select a city --', 'testwork7748' ); ?></option>
                <?php foreach ( $cities as $city_post ) : ?>
                    <option value="<?php echo $city_post->ID; ?>" <?php selected( $city_post->ID, $city ); ?>><?php echo $city_post->post_title; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['city'] = ( ! empty( $new_instance['city'] ) ) ? absint( $new_instance['city'] ) : '';
        return $instance;
    }
}

function testwork7748_register_weather_widget() {
    register_widget( 'TestWork7748_Weather_Widget' );
}
add_action( 'widgets_init', 'testwork7748_register_weather_widget' );