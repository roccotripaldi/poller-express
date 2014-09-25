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
define( 'POEX_PATH', plugins_url() . '/poller-express/' );

/**
 * Our main controller: has a $settings which we can get with, viola!, the get_settings() method
 */
class Poller_express {
    public $settings;

    /**
     * Grabs the settings from storage in the wp_options table, sets them, and returns them
     * If no settings exist, uses defaults
     * @return array
     */
    public function get_settings() {
        $defaults = array(
            'title' => 'Poller Express Poll',
            'input_type' => 'radio',
            'question' => 'Do you love Poller Express?',
            'answers' => array(
                'yes',
                'no',
                'I\'m not sure',
            ),
        );
        $settings = get_option( 'poex_settings', array() );
        // use defaults if nothing is stored
        if( empty( $settings ) ) {
            $settings = $defaults;
        }
        // there should never be an empty set of answers
        if( empty( $settings['answers'] )) {
            $settings['answers'] = $defaults['answers'];
        }
        $settings = apply_filters( 'poex_settings', $settings );
        $this->settings = $settings;
        return $settings;
    }

}
$poex = new Poller_express(); // let's get it started in here

if( is_admin() ) {
    // if we are in the dashboard, let's get that started too!
    require POEX_DIR . '/controllers/poex_admin.php';
    $poex_admin = new Poex_admin();
}

?>