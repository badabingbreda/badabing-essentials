<?php



class badabing_team_display extends WP_Widget {

  protected $defaults;
  /**
   * Sets up the widgets name etc
   */
  public function __construct() {

   $this->defaults = array(
      'title'                   => '',
      'posts_cat'               => '',
      'posts_num'               => 10,
      'posts_offset'            => 0,
      'style'                   => 'style1',
      'showquotes'              => 1,
      'orderby'                 => '',
      'order'                   => '',
    );

    $widget_ops = array(
      'classname'   => 'badabing-team-display',
      'description' => _b( 'Displays Team Members', 'bbessentials' ),
    );

    $control_ops = array(
      'id_base' => 'badabing_team_display',
      'width'   => 200,
      'height'  => 200,
    );

    //add_action ('load-widgets.php' , array ( &$this , 'badabing_team_display_custom_load' ) );

    // widget actual processes
    parent::__construct( 'badabing_team_display', _b( 'Badabing - Team Display', 'bbessentials' ), $widget_ops, $control_ops );

  }

  function badabing_team_display_custom_load() {    
    wp_enqueue_style( 'wp-color-picker' );        
    wp_enqueue_script( 'wp-color-picker' );    
  }

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {

    switch ($instance['style']) {
      
      case "style1":
        wp_enqueue_style('badabing_team', BADABING_ESSENTIALS_URL . 'widgets/badabing.team.css', false, '1.0.0', 'screen');    
        wp_enqueue_script( 'badabing_teamjs' , BADABING_ESSENTIALS_URL . 'widgets/badabing.team.js' ,array( 'jquery' ), '1.0.0' ); 
        break;
      case "style2":
        break;      
    }

    
    global $wp_query;
    // outputs the content of the widget
  
    echo $args['before_widget'];
    
    $args = array(
      'post_type' => 'post',
      'cat'       => $instance['posts_cat'],
      'showposts' => $instance['posts_num'],
      'offset'    => $instance['posts_offset'],
      'orderby'   => $instance['orderby'],
      'order'     => $instance['order'],
    );

    $wp_query = new WP_query ( $args );
    
    if ( have_posts( ) ):
      
      $picnum = 1;
      
      while ( have_posts( ) ): the_post();
      
      $image = genesis_get_image(
                                  array('format'      =>  'url',
                                        'size'        =>  $instance['image_size'],
                                        'fallback'    =>  array('url'  => $instance['fallbackurl']),
                                        )
                                );
                                
      $the_pictures .=    sprintf ('          <li class="quote%s"><a href="%s"><img src="%s" alt="%s"></a></li>', $picnum , 'javascript:void();' , $image , get_the_title() );
      if ($instance['show_quotes']) $the_quotes   .=    sprintf ('          <li class="quote%s"><p>%s</p></li>', $picnum, get_the_content());
      $picnum++;
      
      endwhile;
    
      echo '<div class="testimonials">';
      printf ('    <h3>%s</h3>', $instance['title']);
      echo '    <div class="container">';
      echo '      <div class="photos">';
      echo '        <ul class="clearfix">';
      echo $the_pictures;
      echo '        </ul>';
      echo '        <div class="author"></div>';  // leave empty, this will be filled using jquery
      echo '      </div>';
      if ($instance['show_quotes']):
        echo '      <div class="quotes">';
        echo '        <ul>';
        echo $the_quotes;
        echo '        </ul>';
        echo '      </div>';
      endif;
      echo ' <div style="clear:both;"></div>';
      echo '    </div>';
      echo '</div>';    
    
    else :
      printf ('<!-- %s -->', __('Nothing to see here'  ,'bbessentials'));
            
    endif;

    wp_reset_query();

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
        <label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Display Style', 'bbessentials' ); ?>:</label>
        <select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
          <option value="style1" <?php selected( 'style1', $instance['style'] ); ?>><?php _e( 'Style 1', 'bbessentials' ); ?></option>
        </select>
      </p>        <p>
          <input id="<?php echo $this->get_field_id( 'show_quotes' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_quotes' ); ?>" value="1" <?php checked( $instance['show_quotes'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'show_quotes' ); ?>"><?php _e( 'Show post content?', 'bbessentials' ); ?></label>
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

function register_badabing_team_display () {
  register_widget ('badabing_team_display');
}

add_action ('widgets_init', 'register_badabing_team_display');