<?php
/**
 * Badabing Widgets.
 *
 *
 * @package /lib/widgets/
 * @author  Badabing
 * @license GPL-2.0+
 * @link    http://www.badabing.nl/
 * 
 * @version 1.0.1
 */

/**
 * Badabing Featured Post widget class.
 *
 * @package /lib/widgets/
 */
 
wp_enqueue_style('badabing_featuredposts', BADABING_ESSENTIALS_URL . 'widgets/badabing.featuredposts.css', false, '1.0.0', 'screen');
 
class Badabing_Featured_Post extends WP_Widget {

  /**
   * Holds widget settings defaults, populated in constructor.
   *
   * @var array
   */
  protected $defaults;

  /**
   * Constructor. Set the default widget options and create widget.
   *
   * @since 0.1.8
   */
  function __construct() {

    $this->defaults = array(
      'title'                   => '',
      'posts_cat'               => '',
      'posts_num'               => 1,
      'posts_offset'            => 0,
      'orderby'                 => '',
      'order'                   => '',
      'exclude_displayed'       => 0,
      'show_image'              => 0,
      'image_alignment'         => '',
      'image_size'              => '',
      'num_cols'                => 3,
      'layout_style'            => 'type1',
      'permalink_button'        => 0,
      'permalink_button_style'  => 'flbuilder',
      'permalink_button_style'  => 'default',
      'show_gravatar'           => 0,
      'gravatar_alignment'      => '',
      'gravatar_size'           => '',
      'show_title'              => 0,
      'show_byline'             => 0,
      'post_info'               => '[post_date] ' . __( 'By', 'genesis' ) . ' [post_author_posts_link] [post_comments]',
      'show_content'            => 'excerpt',
      'content_limit'           => '',
      'more_text'               => __( '[Read More...]', 'genesis' ),
      'extra_num'               => '',
      'extra_title'             => '',
      'more_from_category'      => '',
      'more_from_category_text' => __( 'More Posts from this Category', 'genesis' ),
    );

    $widget_ops = array(
      'classname'   => 'featured-content featuredpost',
      'description' => __( 'Displays featured posts with thumbnails', 'genesis' ),
    );

    $control_ops = array(
      'id_base' => 'badabing-featured-post',
      'width'   => 505,
      'height'  => 350,
    );

    parent::__construct( 'badabing-featured-post', _b( 'Badabing - Featured Posts', 'bbessentials' ), $widget_ops, $control_ops );

  }

  /**
   * Echo the widget content.
   *
   * @since 0.1.8
   *
   * @global WP_Query $wp_query               Query object.
   * @global array    $_genesis_displayed_ids Array of displayed post IDs.
   * @global $integer $more
   *
   * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
   * @param array $instance The settings for the particular instance of the widget
   */
  function widget( $args, $instance ) {

    global $wp_query, $_genesis_displayed_ids;

    //* Merge with defaults
    $instance = wp_parse_args( (array) $instance, $this->defaults );

    echo $args['before_widget'];

    //* Set up the author bio
    if ( ! empty( $instance['title'] ) )
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

      $coldata = badabing_get_colclass($instance['num_cols']);

    $wp_query = new WP_Query( $query_args );

    echo '<div class="masonry">';
    
    if ( have_posts() ) : while ( have_posts() ) : the_post();

      $is_first = ($article_num%$coldata['cols']==0)?" first":"";
      echo "<div class='" . $coldata['class'] . $is_first . " '>";
      $article_num ++;

      $_genesis_displayed_ids[] = get_the_ID();
    
      
      genesis_markup( array(
        'html5'   => '<article %s>',
        'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
        'context' => 'entry',
      ) );


      $image = genesis_get_image( array(
        'format'  => 'html',
        'size'    => $instance['image_size'],
        'context' => 'featured-post-widget',
        'attr'    => genesis_parse_attr( 'entry-image-widget' ),
        ) );

      /* get the title for this post */
      if ( ! empty( $instance['show_title'] ) ) {
        $title = get_the_title() ? get_the_title() : __( '(no title)', 'genesis' );
        $thetitle = (genesis_html5() ? sprintf( '<h2 class="entry-title"><a href="%s">%s</a></h2>', get_permalink(), esc_html( $title ) ): sprintf( '<h2><a href="%s">%s</a></h2>', get_permalink(), esc_html( $title ) ) );
      }

      /* get the entrymeta data */
      if ( ! empty( $instance['show_byline'] ) && ! empty( $instance['post_info'] ) )
        $entrymeta = sprintf( genesis_html5() ? '<p class="entry-meta">%s</p>' : '<p class="byline post-info">%s</p>', do_shortcode( $instance['post_info'] ) );
      
      
      /* get the gravatar */      
      if ( ! empty( $instance['show_gravatar'] ) ) {
        $gravatar = '<span class="' . esc_attr( $instance['gravatar_alignment'] ) . '">';
        $gravatar .= get_avatar( get_the_author_meta( 'ID' ), $instance['gravatar_size'] );
        $gravatar .= '</span>';
      }
      
      /* get the content */
      if ( ! empty ( $instance['show_content'] ) ) {
        
        switch ($instance['show_content']) {
          case 'excerpt':
            $content = ( (int) $instance['content_limit'] > 0 ) ? substr(get_the_excerpt(), 0, (int) $instance['content_limit'] ).'...' : get_the_excerpt() ;
            break;
          case 'content-limit':
            $content = get_the_content_limit( (int) $instance['content_limit'], esc_html( $instance['more_text'] ) );
            break;
          case 'content':
          case '':
          default:
            $content = apply_filters ('the_content', get_the_content( $instance['more_text'] ));
            break;
        }
      }
    /* get the permalink button */      
    if ( ! empty( $instance['permalink_button']) && $instance['permalink_button'] ) {
      switch ($instance['permalink_button_style']) {
        
        case 'flbuilder':
        default:
          $permalink_button = sprintf ( '<div class="fl-button-wrap fl-button-width-auto fl-button-right"><a href="%s" target="_self" class="fl-button" role="button"><span class="fl-button-text">%s</span></a></div>' , get_permalink() , $instance['more_text'] );
        break;
        case 'themed':
          $permalink_button = sprintf ( '<a href="%s" class="badbtn btn-default btn-%s">%s</a>' , get_permalink() , $instance['permalink_button_class'] , $instance['more_text'] );
        break;
      }
    }
      

      switch ($instance['layout_style']) {
        case 'style1':
        default:
          if ( $instance['show_image'] && $image )
            printf( '<div class="bb-feat-style1">%s<a href="%s" title="%s" class="%s img-link"><i class="fa fa-3x fa-arrow-circle-right"></i></a></div>%s %s',  $image , get_permalink(), the_title_attribute( 'echo=0' ), esc_attr( $instance['image_alignment'] ) , $thetitle , $entrymeta );
          echo $gravatar;
          echo $content;
          echo $permalink_button;
        break;
        case 'style2':
          if ( $instance['show_image'] && $image )
            printf( '<div class="bb-feat-style2"><a href="%s" title="%s" class="%s">%s</a></div>%s %s', get_permalink(), the_title_attribute( 'echo=0' ), esc_attr( $instance['image_alignment'] ), $image , $thetitle , $entrymeta );
          echo $gravatar;
          echo $content;
          echo $permalink_button;
          break;
         
        case 'style3':
          if ( $instance['show_image'] && $image )
            printf( '<div class="bb-feat-style3"><a href="%s" class="bb-feat3-link %s"><div class="bb-feat3-overlay"><span>+</span></div><div class="bb-feat3-image">%s</div><h3>%s</h3></a></div>' , get_permalink() , the_title_attribute('echo=0'), $image , $title );
            echo $gravatar;
            echo $content;
          echo $permalink_button;
          break;
        case 'style4':
          if ( $instance['show_image'] && $image )
          
            printf( '<div class="bb-feat-style4"><a href="%s" class="bb-feat4-link %s"><div class="bb-feat4-overlay"><span>+</span></div><div class="bb-feat4-image">%s</div><h3>%s</h3></a>' , get_permalink() , the_title_attribute('echo=0'), $image , $title );
              echo (! empty( $instance['show_byline'] ) && ! empty( $instance['post_info'] )) ? '<div class="bb-feat4-meta">'.$entrymeta.'</div>':'';
            printf ('</div>');
            echo $gravatar;
            echo $content;
          echo $permalink_button;
          break;
          
        case 'style5':
          printf('<a href="%s" class="bb-feat-style5"><div class="image">%s<h4 class="posted-date">%s</div>'.
                   '<div class="the-excerpt">'.
                        '<h3>%s</h3>'.
                        '%s<br>'.
                        '<span>%s</span>'.
                    '</div></a>', get_permalink() , $image, $entrymeta , $title , $content , $instance['more_text'] );
        break;
      }
      
    
    genesis_markup( array(
      'html5' => '</article>',
      'xhtml' => '</div>',
    ) );
     

           
    echo "</div>";
    endwhile;
    
    while ($article_num%$coldata['cols']!=0) {
      echo "<div class='".$coldata['class']."'></div>";
      $article_num++;
    }
    endif;
    
    echo '</div>';  // end .masonry class

    //* Restore original query
    wp_reset_query();

    //* The EXTRA Posts (list)
    if ( ! empty( $instance['extra_num'] ) ) {
      if ( ! empty( $instance['extra_title'] ) )
        echo $args['before_title'] . esc_html( $instance['extra_title'] ) . $args['after_title'];

      $offset = intval( $instance['posts_num'] ) + intval( $instance['posts_offset'] );

      $query_args = array(
        'cat'       => $instance['posts_cat'],
        'showposts' => $instance['extra_num'],
        'offset'    => $offset,
      );

      $wp_query = new WP_Query( $query_args );

      $listitems = '';

      if ( have_posts() ) {
        while ( have_posts() ) {
          the_post();
          $_genesis_displayed_ids[] = get_the_ID();
          $listitems .= sprintf( '<li><a href="%s">%s</a></li>', get_permalink(), get_the_title() );
        }

        if ( mb_strlen( $listitems ) > 0 )
          printf( '<ul>%s</ul>', $listitems );
      }

      //* Restore original query
      wp_reset_query();
    }

    if ( ! empty( $instance['more_from_category'] ) && ! empty( $instance['posts_cat'] ) )
      printf(
        '<p class="more-from-category"><a href="%1$s" title="%2$s">%3$s</a></p>',
        esc_url( get_category_link( $instance['posts_cat'] ) ),
        esc_attr( get_cat_name( $instance['posts_cat'] ) ),
        esc_html( $instance['more_from_category_text'] )
      );

    echo $args['after_widget'];

  }

  /**
   * Update a particular instance.
   *
   * This function should check that $new_instance is set correctly.
   * The newly calculated value of $instance should be returned.
   * If "false" is returned, the instance won't be saved/updated.
   *
   * @since 0.1.8
   *
   * @param array $new_instance New settings for this instance as input by the user via form()
   * @param array $old_instance Old settings for this instance
   * @return array Settings to save or bool false to cancel saving
   */
  function update( $new_instance, $old_instance ) {

    $new_instance['title']     = strip_tags( $new_instance['title'] );
    $new_instance['more_text'] = strip_tags( $new_instance['more_text'] );
    $new_instance['post_info'] = wp_kses_post( $new_instance['post_info'] );
    return $new_instance;

  }

  /**
   * Echo the settings update form.
   *
   * @since 0.1.8
   *
   * @param array $instance Current settings
   */
  function form( $instance ) {

    //* Merge with defaults
    $instance = wp_parse_args( (array) $instance, $this->defaults );

    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
    </p>
    <div class="genesis-widget-column">
      <div class="genesis-widget-column-box genesis-widget-column-box-top">
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
      <div class="genesis-widget-column-box">

         <p>
          <label for="<?php echo $this->get_field_id( 'num_cols' ); ?>"><?php _be( 'Number cols', 'bbessentials' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'num_cols' ); ?>" name="<?php echo $this->get_field_name( 'num_cols' ); ?>">
            <option value="1" <?php selected( '1', $instance['num_cols'] ); ?>>- 1 -</option>
            <option value="2" <?php selected( '2', $instance['num_cols'] ); ?>>- 2 -</option>
            <option value="3" <?php selected( '3', $instance['num_cols'] ); ?>>- 3 -</option>
            <option value="4" <?php selected( '4', $instance['num_cols'] ); ?>>- 4 -</option>
            <option value="5" <?php selected( '5', $instance['num_cols'] ); ?>>- 5 -</option>
            <option value="6" <?php selected( '6', $instance['num_cols'] ); ?>>- 6 -</option>
          </select>
        </p>
         <p>
          <label for="<?php echo $this->get_field_id( 'layout_style' ); ?>"><?php _be( 'Layout style', 'bbessentials' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'layout_style' ); ?>" name="<?php echo $this->get_field_name( 'layout_style' ); ?>">
            <option value="style1" <?php selected( 'style1', $instance['layout_style'] ); ?>>- style 1 -</option>
            <option value="style2" <?php selected( 'style2', $instance['layout_style'] ); ?>>- style 2 -</option>
            <option value="style3" <?php selected( 'style3', $instance['layout_style'] ); ?>>- style 3 -</option>
            <option value="style4" <?php selected( 'style4', $instance['layout_style'] ); ?>>- style 4 -</option>
            <option value="style5" <?php selected( 'style5', $instance['layout_style'] ); ?>>- style 5 -</option>
          </select>
        </p>

        <p>
          <input id="<?php echo $this->get_field_id( 'permalink_button' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'permalink_button' ); ?>" value="1" <?php checked( $instance['permalink_button'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'permalink_button' ); ?>"><?php _be( 'Show permalink button', 'bbessentials' ); ?></label>
        </p>

         <p>
          <label for="<?php echo $this->get_field_id( 'permalink_button_style' ); ?>"><?php _be( 'Permalink Button Style', 'bbessentials' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'permalink_button_style' ); ?>" name="<?php echo $this->get_field_name( 'permalink_button_style' ); ?>">
            <option value="flbuilder" <?php selected( 'flbuilder', $instance['permalink_button_style'] ); ?>>FL Builder</option>
            <option value="themed" <?php selected( 'themed', $instance['permalink_button_style'] ); ?>><?php _be('Themed style', 'bbessentials');?></option>
          </select>
        </p>
        
         <p>
          <label for="<?php echo $this->get_field_id( 'permalink_button_class' ); ?>"><?php _be( 'Permalink Button Class', 'bbessentials' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'permalink_button_class' ); ?>" name="<?php echo $this->get_field_name( 'permalink_button_class' ); ?>">
            <option value="default" <?php selected( 'default', $instance['permalink_button_class'] ); ?>><?php _e('Default', 'bbessentials');?></option>
            <option value="primary" <?php selected( 'primary', $instance['permalink_button_class'] ); ?>><?php _e('Primary', 'bbessentials');?></option>
            <option value="success" <?php selected( 'success', $instance['permalink_button_class'] ); ?>><?php _e('Success', 'bbessentials');?></option>
            <option value="info" <?php selected( 'info', $instance['permalink_button_class'] ); ?>><?php _be('Info', 'bbessentials');?></option>
          </select>
        </p>
          
      </div>

      <div class="genesis-widget-column-box">

        <p>
          <input id="<?php echo $this->get_field_id( 'show_gravatar' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_gravatar' ); ?>" value="1" <?php checked( $instance['show_gravatar'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'show_gravatar' ); ?>"><?php _e( 'Show Author Gravatar', 'genesis' ); ?></label>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'gravatar_size' ); ?>"><?php _e( 'Gravatar Size', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'gravatar_size' ); ?>" name="<?php echo $this->get_field_name( 'gravatar_size' ); ?>">
            <option value="45" <?php selected( 45, $instance['gravatar_size'] ); ?>><?php _e( 'Small (45px)', 'genesis' ); ?></option>
            <option value="65" <?php selected( 65, $instance['gravatar_size'] ); ?>><?php _e( 'Medium (65px)', 'genesis' ); ?></option>
            <option value="85" <?php selected( 85, $instance['gravatar_size'] ); ?>><?php _e( 'Large (85px)', 'genesis' ); ?></option>
            <option value="125" <?php selected( 105, $instance['gravatar_size'] ); ?>><?php _e( 'Extra Large (125px)', 'genesis' ); ?></option>
          </select>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'gravatar_alignment' ); ?>"><?php _e( 'Gravatar Alignment', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'gravatar_alignment' ); ?>" name="<?php echo $this->get_field_name( 'gravatar_alignment' ); ?>">
            <option value="alignnone">- <?php _e( 'None', 'genesis' ); ?> -</option>
            <option value="alignleft" <?php selected( 'alignleft', $instance['gravatar_alignment'] ); ?>><?php _e( 'Left', 'genesis' ); ?></option>
            <option value="alignright" <?php selected( 'alignright', $instance['gravatar_alignment'] ); ?>><?php _e( 'Right', 'genesis' ); ?></option>
          </select>
        </p>

      </div>

      <div class="genesis-widget-column-box">

        <p>
          <input id="<?php echo $this->get_field_id( 'show_image' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="1" <?php checked( $instance['show_image'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Show Featured Image', 'genesis' ); ?></label>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Image Size', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'image_size' ); ?>" class="genesis-image-size-selector" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
            <option value="thumbnail" <?php echo selected( 'thumbnail', $instance['image_size'], FALSE ); ?>>thumbnail (<?php echo get_option( 'thumbnail_size_w' ); ?>x<?php echo get_option( 'thumbnail_size_h' ); ?>)</option>
            <option value="medium" <?php echo selected( 'medium', $instance['image_size'], FALSE ); ?>>medium (<?php echo get_option( 'medium_size_w' ); ?>x<?php echo get_option( 'medium_size_h' ); ?>)</option>
            <option value="large" <?php echo selected( 'large', $instance['image_size'], FALSE ); ?>>large (<?php echo get_option( 'large_size_w' ); ?>x<?php echo get_option( 'large_size_h' ); ?>)</option>
            <?php
            $sizes = genesis_get_additional_image_sizes();
            foreach( (array) $sizes as $name => $size )
              echo '<option value="'.esc_attr( $name ).'" '.selected( $name, $instance['image_size'], FALSE ).'>'.esc_html( $name ).' ( '.$size['width'].'x'.$size['height'].' )</option>';
            ?>
          </select>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'image_alignment' ); ?>"><?php _e( 'Image Alignment', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'image_alignment' ); ?>">
            <option value="alignnone">- <?php _e( 'None', 'genesis' ); ?> -</option>
            <option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php _e( 'Left', 'genesis' ); ?></option>
            <option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php _e( 'Right', 'genesis' ); ?></option>
            <option value="aligncenter" <?php selected( 'aligncenter', $instance['image_alignment'] ); ?>><?php _e( 'Center', 'genesis' ); ?></option>
          </select>
        </p>

      </div>

    </div>

    <div class="genesis-widget-column genesis-widget-column-right">

      <div class="genesis-widget-column-box genesis-widget-column-box-top">

        <p>
          <input id="<?php echo $this->get_field_id( 'show_title' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>" value="1" <?php checked( $instance['show_title'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Show Post Title', 'genesis' ); ?></label>
        </p>

        <p>
          <input id="<?php echo $this->get_field_id( 'show_byline' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_byline' ); ?>" value="1" <?php checked( $instance['show_byline'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'show_byline' ); ?>"><?php _e( 'Show Post Info', 'genesis' ); ?></label>
          <input type="text" id="<?php echo $this->get_field_id( 'post_info' ); ?>" name="<?php echo $this->get_field_name( 'post_info' ); ?>" value="<?php echo esc_attr( $instance['post_info'] ); ?>" class="widefat" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php _e( 'Content Type', 'genesis' ); ?>:</label>
          <select id="<?php echo $this->get_field_id( 'show_content' ); ?>" name="<?php echo $this->get_field_name( 'show_content' ); ?>">
            <option value="content" <?php selected( 'content', $instance['show_content'] ); ?>><?php _e( 'Show Content', 'genesis' ); ?></option>
            <option value="excerpt" <?php selected( 'excerpt', $instance['show_content'] ); ?>><?php _e( 'Show Excerpt', 'genesis' ); ?></option>
            <option value="content-limit" <?php selected( 'content-limit', $instance['show_content'] ); ?>><?php _e( 'Show Content Limit', 'genesis' ); ?></option>
            <option value="" <?php selected( '', $instance['show_content'] ); ?>><?php _e( 'No Content', 'genesis' ); ?></option>
          </select>
          <br />
          <label for="<?php echo $this->get_field_id( 'content_limit' ); ?>"><?php _e( 'Limit content to', 'genesis' ); ?>
            <input type="text" id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'content_limit' ); ?>" value="<?php echo esc_attr( intval( $instance['content_limit'] ) ); ?>" size="3" />
            <?php _e( 'characters', 'genesis' ); ?>
          </label>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php _e( 'More Text (if applicable)', 'genesis' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
        </p>

      </div>

      <div class="genesis-widget-column-box">

        <p><?php _e( 'To display an unordered list of more posts from this category, please fill out the information below', 'genesis' ); ?>:</p>

        <p>
          <label for="<?php echo $this->get_field_id( 'extra_title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'extra_title' ); ?>" name="<?php echo $this->get_field_name( 'extra_title' ); ?>" value="<?php echo esc_attr( $instance['extra_title'] ); ?>" class="widefat" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'extra_num' ); ?>"><?php _e( 'Number of Posts to Show', 'genesis' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'extra_num' ); ?>" name="<?php echo $this->get_field_name( 'extra_num' ); ?>" value="<?php echo esc_attr( $instance['extra_num'] ); ?>" size="2" />
        </p>

      </div>

      <div class="genesis-widget-column-box">

        <p>
          <input id="<?php echo $this->get_field_id( 'more_from_category' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'more_from_category' ); ?>" value="1" <?php checked( $instance['more_from_category'] ); ?>/>
          <label for="<?php echo $this->get_field_id( 'more_from_category' ); ?>"><?php _e( 'Show Category Archive Link', 'genesis' ); ?></label>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'more_from_category_text' ); ?>"><?php _e( 'Link Text', 'genesis' ); ?>:</label>
          <input type="text" id="<?php echo $this->get_field_id( 'more_from_category_text' ); ?>" name="<?php echo $this->get_field_name( 'more_from_category_text' ); ?>" value="<?php echo esc_attr( $instance['more_from_category_text'] ); ?>" class="widefat" />
        </p>

      </div>

    </div>
    <?php

  }

}

function register_badabing_featured_posts () {
  register_widget ('Badabing_Featured_Post');
}

add_action ('widgets_init', 'register_badabing_featured_posts');

if ( !function_exists('badabing_get_colclass')) {
  function badabing_get_colclass ($cols) {
      switch ($cols) {
        case '1':
          $col_class = '';
          break;
        case '2':
          $col_class = 'one-half';
          break;
        case '3':
          $col_class = 'one-third';
          break;
        case '4':
          $col_class = 'one-fourth';
          break;
        case '6':
          $col_class = 'one-sixth';
          break;
        default:
          $col_class = 'one-third';
      } 
      
      return ( array ( 'cols'=> $cols,  'class'=>$col_class ) );
    
  }
  
}
