<?php
    
    
wp_enqueue_style('badabing_widgets', BADABING_ESSENTIALS_URL . 'widgets/badabing.widgets.css', false, '1.0.0', 'screen');    

class badabing_simple_posts extends WP_Widget {

  protected $defaults;
  /**
   * Sets up the widgets name etc
   */
  public function __construct() {

   $this->defaults = array(
      'title'                   => '',
      'posts_cat'               => '',
      'posts_num'               => 1,
      'posts_offset'            => 0,
      'orderby'                 => '',
      'order'                   => '',
      'exclude_displayed'       => 0,
      'displayorder'            => 'titlefirst',    // set the layout of the simple posts
    );

    $widget_ops = array(
      'classname'   => 'badabing-simple-posts',
      'description' => __( 'Displays featured posts', 'genesis' ),
    );

    $control_ops = array(
      'id_base' => 'badabing_get_simple',
      'width'   => 505,
      'height'  => 350,
    );

    // widget actual processes
    parent::__construct( 'badabing_get_simple', _b( 'Badabing - Simple Posts', 'bbessentials' ), $widget_ops, $control_ops );

  }

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {
    // outputs the content of the widget

    global $wp_query, $_genesis_displayed_ids;    
    
    echo $args['before_widget'];
    
    if ( ! empty ($instance ['title'] ) && $instance[ 'title' ])
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];

    $query_args = array(
      'post_type' => 'post',
      'cat'       => $instance['posts_cat'],
      'showposts' => $instance['posts_num'],
      'offset'    => $instance['posts_offset'],
      'orderby'   => $instance['orderby'],
      'order'     => $instance['order'],
    );

    //* Exclude displayed IDs from this loop?
    if ( $instance['exclude_displayed'] )
      $query_args['post__not_in'] = (array) $_genesis_displayed_ids;
    
    $wp_query = new WP_Query( $query_args );

    echo '<ul>';

    if ( have_posts() ) : while ( have_posts() ) : the_post();
       
      if ($instance['displayorder'] == 'titlefirst') {
        printf ('<li><a href="%s" alt="%s" title="%s">%s</a><span class="post-date">%s</span></li>', get_permalink(), the_title_attribute( 'echo=0' ), the_title_attribute( 'echo=0' ), get_the_title() , get_the_date() );
      } else if ( $instance['displayorder'] == 'datefirst') {
        printf ('<li><span class="post-date">%s</span><a href="%s" alt="%s" title="%s">%s</a></li>', get_the_date(), get_permalink(), the_title_attribute( 'echo=0' ), the_title_attribute( 'echo=0' ), get_the_title() );
      } else {
        printf ('<li><a href="%s" alt="%s" title="%s">%s</a></li>', get_permalink(), the_title_attribute( 'echo=0' ) ,the_title_attribute( 'echo=0' ), get_the_title() );
      }

      endwhile;
    
    endif; 
    
    //* Restore original query
    wp_reset_query();
    echo '</ul>';
    echo $args['after_widget'];
  }

  /**
   * Outputs the options form on admin
   *
   * @param array $instance The widget options
   */
  public function form( $instance ) {
    // outputs the options form on admin

        //* Merge with defaults
    $instance = wp_parse_args( (array) $instance, $this->defaults );
    
    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
    </p>
    
    <div class="genesis-widget-column">
      <div class="genesis-widget-column-box genisis-widget-column-box-top">
        <p>
          <label for="<?php echo $this->get_field_id( 'posts_cat' ); ?>"><?php _e( 'Category', 'genesis' ); ?>:</label>
          <?php
          $categories_args = array(
            'name'            => $this->get_field_name( 'posts_cat' ),
            'selected'        => $instance['posts_cat'],
            'orderby'         => 'Name',
            'hierarchical'    => 1,
            'show_option_all' => __( 'All Categories', 'genesis' ),
            'hide_empty'      => '0',
          );
          wp_dropdown_categories( $categories_args ); ?>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'posts_num' ); ?>"><?php _e( 'Number of Posts to Show', 'genesis' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'posts_num' ); ?>" name="<?php echo $this->get_field_name( 'posts_num' ); ?>" value="<?php echo esc_attr( $instance['posts_num'] ); ?>" size="2" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'posts_offset' ); ?>"><?php _e( 'Number of Posts to Offset', 'genesis' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'posts_offset' ); ?>" name="<?php echo $this->get_field_name( 'posts_offset' ); ?>" value="<?php echo esc_attr( $instance['posts_offset'] ); ?>" size="2" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order By', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
            <option value="date" <?php selected( 'date', $instance['orderby'] ); ?>><?php _e( 'Date', 'genesis' ); ?></option>
            <option value="title" <?php selected( 'title', $instance['orderby'] ); ?>><?php _e( 'Title', 'genesis' ); ?></option>
            <option value="parent" <?php selected( 'parent', $instance['orderby'] ); ?>><?php _e( 'Parent', 'genesis' ); ?></option>
            <option value="ID" <?php selected( 'ID', $instance['orderby'] ); ?>><?php _e( 'ID', 'genesis' ); ?></option>
            <option value="comment_count" <?php selected( 'comment_count', $instance['orderby'] ); ?>><?php _e( 'Comment Count', 'genesis' ); ?></option>
            <option value="rand" <?php selected( 'rand', $instance['orderby'] ); ?>><?php _e( 'Random', 'genesis' ); ?></option>
          </select>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Sort Order', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
            <option value="DESC" <?php selected( 'DESC', $instance['order'] ); ?>><?php _e( 'Descending (3, 2, 1)', 'genesis' ); ?></option>
            <option value="ASC" <?php selected( 'ASC', $instance['order'] ); ?>><?php _e( 'Ascending (1, 2, 3)', 'genesis' ); ?></option>
          </select>
        </p>
        <p>
          <input id="<?php echo $this->get_field_id( 'exclude_displayed' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'exclude_displayed' ); ?>" value="1" <?php checked( $instance['exclude_displayed'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'exclude_displayed' ); ?>"><?php _e( 'Exclude Previously Displayed Posts?', 'genesis' ); ?></label>
        </p>
      </div>
      <div class= "genesis-widget-column-box">
        <p>
          <label for="<?php echo $this->get_field_id( 'displayorder' ); ?>"><?php _e( 'Display Order', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'displayorder' ); ?>" name="<?php echo $this->get_field_name( 'displayorder' ); ?>">
            <option value="titlefirst" <?php selected( 'titlefirst', $instance['displayorder'] ); ?>><?php _be( 'Title, then date', 'bbessentials' ); ?></option>
            <option value="datefirst" <?php selected( 'datefirst', $instance['displayorder'] ); ?>><?php _be( 'Date, then title', 'bbessentials' ); ?></option>
            <option value="titleonly" <?php selected( 'titleonly', $instance['displayorder'] ); ?>><?php _be( 'Title only', 'bbessentials' ); ?></option>
          </select>
        </p>        
      </div>
    </div>
    
    <?php    
  }

  /**
   * Processing widget options on save
   *
   * @param array $new_instance The new options
   * @param array $old_instance The previous options
   */
  public function update( $new_instance, $old_instance ) {
    // processes widget options to be saved
    $new_instance['title']     = strip_tags( $new_instance['title'] );
    return $new_instance;

  }
}

function register_badabing_simple_posts () {
  register_widget ('badabing_simple_posts');
}

add_action ('widgets_init', 'register_badabing_simple_posts');