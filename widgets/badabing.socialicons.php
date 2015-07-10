<?php


wp_enqueue_style('badabing_socialicons', BADABING_ESSENTIALS_URL . 'widgets/badabing.socialicons.css', false, '1.0.0', 'screen');    

class badabing_social_icons extends WP_Widget {

  protected $defaults;
  /**
   * Sets up the widgets name etc
   */
  public function __construct() {

   $this->defaults = array(
      'title'                   => '',
      'bdisplay'                => 'left',
      'facebookurl'             => '#',
      'facebooktext'            => 'Facebook',
      'twitterurl'              => '#',
      'twittertext'             => 'Twitter',
      'googleplusurl'           => '#',
      'googleplustext'          => 'Google+',
      'youtubeurl'              => '#',
      'youtubetext'             => 'Youtube',
      'target'                  => '_blank',
      'order'                   => '',
      'outcolor'                => '',
      'hovercolor'                => '',
    );

    $widget_ops = array(
      'classname'   => 'badabing-social-icons',
      'description' => _b( 'Displays Social Icons', 'bbessentials' ),
    );

    $control_ops = array(
      'id_base' => 'badabing_social_icons',
      'width'   => 505,
      'height'  => 350,
    );

    add_action ('load-widgets.php' , array ( &$this , 'my_custom_load' ) );

    // widget actual processes
    parent::__construct( 'badabing_social_icons', _b( 'Badabing - Social Icons', 'bbessentials' ), $widget_ops, $control_ops );

  }

  function my_custom_load() {    
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
    // outputs the content of the widget
  
    echo $args['before_widget'];
    
    if ( ! empty ($instance ['title'] ) && $instance[ 'title' ])
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];

    if ($instance['facebookurl']!="" | $instance['twitterurl'] !="" | $instance['googleplusurl'] !="" | $instance['youtubeurl'] !="") :

      echo ('<div class="social-icons appear-'.$instance[ 'bdisplay' ].'">');      
      if ( $instance['facebookurl']   != "" ) printf ('<a href="%s" class="facebook" data-title="%s" target="%s"><i class="fa fa-facebook"></i><span>%s</span></a>', $instance['facebookurl'] , $instance['facebooktext'] , $instance['target'], $instance['facebooktext']);
      if ( $instance['twitterurl']    != "" ) printf ('<a href="%s" class="twitter" data-title="%s" target="%s"><i class="fa fa-twitter"></i><span>%s</span></a>', $instance['twitterurl'] , $instance['twittertext'] , $instance['target'], $instance['twittertext']);
      if ( $instance['googleplusurl'] != "" ) printf ('<a href="%s" class="gplus" data-title="%s" target="%s"><i class="fa fa-google-plus"></i><span>%s</span></a>', $instance['googleplusurl'] , $instance['googleplustext'] , $instance['target'], $instance['googleplustext']);
      if ( $instance['youtubeurl']    != "" ) printf ('<a href="%s" class="youtube" data-title="%s" target="%s"><i class="fa fa-youtube"></i><span>%s</span></a>', $instance['youtubeurl'] , $instance['youtubetext'] , $instance['target'], $instance['youtubetext']);
      echo '</div>';
    endif;
    
    if ($instance['outcolor']!="" | $instance['overcolor'] !="" ) :
      echo "<style>";
      if ( $instance['outcolor'] != "") printf ('.social-icons A{background: %s;}', $instance['outcolor'] );
      if ( $instance['hovercolor'] != "") printf ('.social-icons A:hover, .social-icons A SPAN {background: %s;} .social-icons.appear-left A SPAN:after{border-color: transparent %s transparent transparent;}', $instance['hovercolor'], $instance['hovercolor'] );
      echo "</style>";
        
    endif;
    
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
        <p>
          <label for="<?php echo $this->get_field_id( 'bdisplay' ); ?>"><?php _be( 'Display on', 'bbessentials' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'bdisplay' ); ?>" name="<?php echo $this->get_field_name( 'bdisplay' ); ?>">
            <option value="left" <?php selected( 'left', $instance['bdisplay'] ); ?>><?php _be( 'Left', 'bbessentials' ); ?></option>
            <option value="right" <?php selected( 'right', $instance['bdisplay'] ); ?>><?php _be( 'Right', 'bbessentials' ); ?></option>
            <option value="bottom" <?php selected( 'bottom', $instance['bdisplay'] ); ?>><?php _be( 'Bottom', 'bbessentials' ); ?></option>
          </select>
        </p>
      <div class="genesis-widget-column-box genesis-widget-column-box-top">
        <p>
          <label for="<?php echo $this->get_field_id( 'facebookurl' ); ?>"><?php _be( 'Url to Facebook Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'facebookurl' ); ?>" name="<?php echo $this->get_field_name( 'facebookurl' ); ?>" value="<?php echo esc_attr( $instance['facebookurl'] ); ?>" class="widefat" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'facebooktext' ); ?>"><?php _be( 'Text for Facebook Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'facebooktext' ); ?>" name="<?php echo $this->get_field_name( 'facebooktext' ); ?>" value="<?php echo esc_attr( $instance['facebooktext'] ); ?>" class="widefat" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'twitterurl' ); ?>"><?php _be( 'Url to Twitter Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'twitterurl' ); ?>" name="<?php echo $this->get_field_name( 'twitterurl' ); ?>" value="<?php echo esc_attr( $instance['twitterurl'] ); ?>" class="widefat" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'twittertext' ); ?>"><?php _be( 'Text for Twitter Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'twittertext' ); ?>" name="<?php echo $this->get_field_name( 'twittertext' ); ?>" value="<?php echo esc_attr( $instance['twittertext'] ); ?>" class="widefat" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'googleplusurl' ); ?>"><?php _be( 'Url to Google+ Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'googleplusurl' ); ?>" name="<?php echo $this->get_field_name( 'googleplusurl' ); ?>" value="<?php echo esc_attr( $instance['googleplusurl'] ); ?>" class="widefat" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'googleplustext' ); ?>"><?php _be( 'Text for Google+ Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'googleplustext' ); ?>" name="<?php echo $this->get_field_name( 'googleplustext' ); ?>" value="<?php echo esc_attr( $instance['googleplustext'] ); ?>" class="widefat" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'youtubeurl' ); ?>"><?php _be( 'Url to Youtube Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'youtubeurl' ); ?>" name="<?php echo $this->get_field_name( 'youtubeurl' ); ?>" value="<?php echo esc_attr( $instance['youtubeurl'] ); ?>" class="widefat" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'youtubetext' ); ?>"><?php _be( 'Text for Youtube Page', 'bbessentials' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'youtubetext' ); ?>" name="<?php echo $this->get_field_name( 'youtubetext' ); ?>" value="<?php echo esc_attr( $instance['youtubetext'] ); ?>" class="widefat" />
        </p>
      </div>
      <div class="genesis-widget-column-box">
         <script type='text/javascript'>
            jQuery(document).ready(function($) {
                $('.my-color-picker').wpColorPicker();
            });
        </script>
        <p>
            <label for="<?php echo $this->get_field_id( 'outcolor' ); ?>"><?php _be( 'Out Color', 'bbessentials' ); ?></label>
        </p>
        <p>
            <input class="my-color-picker" type="text" id="<?php echo $this->get_field_id( 'outcolor' ); ?>" name="<?php echo $this->get_field_name( 'outcolor' ); ?>" value="<?php echo esc_attr( $instance['outcolor'] ); ?>" />                            
        </p>        
        <p>
            <label for="<?php echo $this->get_field_id( 'hovercolor' ); ?>"><?php _be( 'Hover Color', 'bbessentials' ); ?></label>
        </p>
        <p>
            <input class="my-color-picker" type="text" id="<?php echo $this->get_field_id( 'hovercolor' ); ?>" name="<?php echo $this->get_field_name( 'hovercolor' ); ?>" value="<?php echo esc_attr( $instance['hovercolor'] ); ?>" />                            
        </p>        
        
      </div>
      
      <div class= "genesis-widget-column-box">
        <p>
          <label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php _be( 'Target when clicked', 'bbessentials' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>">
            <option value="_blank" <?php selected( '_blank', $instance['target'] ); ?>><?php _be( 'Blank window', 'bbessentials' ); ?></option>
            <option value="_self" <?php selected( '_self', $instance['target'] ); ?>><?php _be( 'In self', 'bbessentials' ); ?></option>
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

function register_badabing_social_icons () {
  register_widget ('badabing_social_icons');
}

add_action ('widgets_init', 'register_badabing_social_icons');