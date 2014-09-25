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

// we will be doing a lot of includes, we should define our base path
define( 'POEX_DIR', dirname(__FILE__).'/' );

/**
 * Our main controller - Empty for now
 * In the step 2 we will fill it with properties and methods!
 */
class Poller_express {

}
$poex = new Poller_express(); // let's get it started in here

if( is_admin() ) {
    // if we are in the dashboard, let's get that started too!
    require POEX_DIR . '/controllers/poex_admin.php';
    $poex_admin = new Poex_admin();
}

?>