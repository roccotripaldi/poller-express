<?php

/**
 * @package Poller_express
 */
/*
 * Plugin Name: Poller Express
 * Plugin URI: https://github.com/roccotripaldi/poller-express
 * Description: Makes a poll, quickly
 * Version: 1.0
 * Author: Rocco Tripaldi
 * Author URI: http://roccotripaldi.com/
 * License: GPL2
 */

/**
 * Our main controller
 */
class Poller_express {

}

// initialize our main controller
$poex = new Poller_express();
// if we are in the WordPress admin, initialize our admin controller
if( is_admin() ) {
    require_once dirname( __FILE__ ) . '/controllers/poex_settings.php';
    $poex_settings = new Poex_settings();
}

?>