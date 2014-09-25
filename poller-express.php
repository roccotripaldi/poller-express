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
 * Our main controller: has $settings which we can get with, viola!, the get_settings() method
 */
class Poller_express {
    public $settings;
    public $showing_shortcode;
    public $is_voting;
    public $voting_error;
    public $voting_message;

    function __construct() {
        add_shortcode('poller_express', array( &$this, 'render_poll'));
        add_action('init', array(&$this, 'register_poll_assets'));
        add_action('wp_footer', array(&$this, 'print_scripts'));
        if( isset( $_POST['action'] ) && $_POST['action'] == 'poex_vote' ) {
            add_action( 'init', array(&$this, 'vote') );
        }
    }

    function vote() {

        $nonce = isset( $_POST['poex_vote_nonce'] ) ? $_POST['poex_vote_nonce'] : '';
        if( ! wp_verify_nonce( $nonce, 'poex_vote' ) ) {
            return false;
        }
        $this->is_voting = true;
        $this->voting_error = true;
        $votes = isset( $_POST['poex_vote'] ) ? $_POST['poex_vote'] : array();
        if( empty($votes) || ! is_array( $votes ) ) {
            $this->voting_message = 'You didn\'t cast a vote chief';
            return false;
        }
        $this->voting_error = false;
        foreach( $votes as $v ) {
            $existing_votes = get_option( 'poex_votes', array() );
            if( isset( $existing_votes[$v] ) ) {
                $existing_votes[$v]++;
            } else {
                $existing_votes[$v] = 1;
            }
        }
        update_option( 'poex_votes', $existing_votes );
    }

    function print_scripts() {
        if( $this->showing_shortcode === true ) {
            wp_print_scripts('charts-js');
            wp_print_scripts('results-js');
            wp_enqueue_style( 'poex-css' );
        }
    }

    function register_poll_assets() {
        wp_register_script( 'charts-js', POEX_PATH . 'js/charts.js' );
        $results_dependencies = array( 'jquery', 'charts-js' );
        wp_register_script( 'results-js', POEX_PATH . 'js/poex-results.js', $results_dependencies );
        wp_register_style( 'poex-css', POEX_PATH . 'css/poex.css' );
    }

    function render_poll() {
        $this->showing_shortcode = true;
        $settings = $this->get_settings();
        ob_start();
        if( $this->is_voting === true && empty($this->voting_error) ) {
            include POEX_DIR . 'views/results.php';
        } else {
            $nonce = wp_create_nonce( 'poex_vote' );
            include POEX_DIR . 'views/poll.php';
        }
        $poll = ob_get_contents();
        ob_end_clean();
        return $poll;
    }

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