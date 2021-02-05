<?php
/*
Plugin Name: Stylist
Plugin URI: http://stylistwp.com
Description: Simple yet powerful visual style editor.
Version: 0.2.6
Author: StylistWP
Author URI: http://www.stylistwp.com
*/

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

define('STLST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STLST_PLUGIN_URL', plugin_dir_url(__FILE__));
/**
 * Launch the plugin.
 *
 * @return void
 */
if ( ! function_exists( 'stlst_plugin_launch' ) ) {
    function stlst_plugin_launch() {
        require_once dirname( __FILE__ ) . '/lib/class-stylist-core.php';
        $stlst_core = new Stylist_Core;

    } add_action( 'plugins_loaded', 'stlst_plugin_launch', 10 );

} else {
    // @todo: show message that two copies of the plugin can't be active at the same time.
}

require_once dirname( __FILE__ ) . '/lib/inherited-code.php';
require_once dirname( __FILE__ ) . '/lib/inherited-code-css.php';
