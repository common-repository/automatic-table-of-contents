<?php

class TOC_Widget extends WP_Widget {

  public function __construct() {
    parent::__construct(
      'toc_widget',
      __('Table of Contents', 'text_domain'),
      array( 'description' => __( 'Displays a table of contents for the current page or post', 'text_domain' ), )
    );
  }

  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if (!empty($instance['title'])) {
        echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
    }
    echo do_shortcode( "[toc]" );
    echo $args['after_widget'];
  }

  public function form( $instance ) {
    $title = !empty( $instance['title'] ) ? $instance['title'] : __( 'Table of Contents', 'text_domain' );
    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <?php
  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    return $instance;
  }
}

function register_toc_widget() {
    register_widget( 'TOC_Widget' );
}
add_action( 'widgets_init', 'register_toc_widget' );
