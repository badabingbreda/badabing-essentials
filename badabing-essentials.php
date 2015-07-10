<?php
/**
 * Plugin Name: Badabing Genesis Essentials
 * Plugin URI: http://www.badabing.nl
 * Description: Reusable Genesis Widgets, Functions etcetera
 * Version: 1.0
 * Author: Didou Schol
 * Author URI: http://www.badabing.nl
 */
define( 'BADABING_ESSENTIALS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BADABING_ESSENTIALS_URL', plugins_url( '/', __FILE__ ) );

add_action( 'plugins_loaded', 'bbessentials_load_textdomain' );


register_activation_hook( __FILE__, 'fst_badabing_essentials_activation_check' );

/* OK then load it up */
include_once ( 'widgets/badabing.widgets.php');
include_once ( 'widgets/badabing.featuredposts.php');
include_once ( 'widgets/badabing.socialicons.php');
include_once ( 'widgets/badabing.team.php');


/**
 * Checks for activated Genesis Framework and its minimum version before allowing plugin to activate
 *
 * @author Nathan Rice, Remkus de Vries
 * @uses fst_genesis_translations_activation_check()
 * @since 1.0
 * @version 2.0.2
 */
function fst_badabing_essentials_activation_check() {

    // Find Genesis Theme Data
    $theme = wp_get_theme( 'genesis' );

    // Get the version
    $version = $theme->get( 'Version' );

    // Set what we consider the minimum Genesis version
    $minimum_genesis_version = '1.9';

    // Restrict activation to only when the Genesis Framework is activated
    if ( basename( get_template_directory() ) != 'genesis' ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );  // Deactivate ourself
        wp_die( sprintf( _b( 'Whoa.. this plugin only works when you have installed the %1$sGenesis Framework%2$s', 'bbessentials' ), '<a href="http://forsitemedia.net/go/genesis/" target="_new">', '</a>' ) );
    }

    // Set a minimum version of the Genesis Framework to be activated on
    if ( version_compare( $version, $minimum_genesis_version, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );  // Deactivate ourself
        wp_die( sprintf( _b( 'Uhm, the thing of it is, you kinda need the %1$sGenesis Framework %2$s%3$s or greater for these translations to make any sense.', 'bbessentials' ), '<a href="http://forsitemedia.net/go/genesis/" target="_new">', $latest, '</a>' ) );
    }
    
}


/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function bbessentials_load_textdomain() {
  load_plugin_textdomain( 'bbessentials', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

/**
 * Retrieve the translation of $text. If there is no translation,
 * or the text domain isn't loaded, the original text is returned.
 *
 * @since 2.1.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text.
 */
if (!function_exists('_b')) {
  function _b( $text, $domain = 'default' ) {
    return translate( $text, $domain );
  }
}

if (!function_exists('_be')) {
  function _be ( $text , $domain = 'default' ) {
    echo translate ($text , $domain );
  }
}
