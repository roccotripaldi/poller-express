<?php
/**
 * @package Poller_express
 */

/**
 * Our controller for the WordPress dashboard area - empty for now
 * In step 2 we will fill it with properties and functions!
 */
class Poex_admin extends Poller_express {

    public $is_submitting;
    public $notice_type;
    public $notice_content;
    public $submission_error;
    public $submitted_settings;

    function __construct() {
        add_action( 'admin_menu', array( &$this, 'add_menu_items' ) );
        add_action( 'admin_init', array( &$this, 'register_assets' ) );
        if( isset( $_POST['action'] ) && $_POST['action'] == 'save_poex_settings' ) {
            add_action( 'admin_init', array( &$this, 'save_settings' ) );
        }
    }

    function show_notices() {
        include POEX_DIR . 'views/notices.php';
    }

    function save_settings() {
        $this->is_submitting = true;
        if( empty( $_POST['poex'] ) ) {
            return false;
        }

        $nonce = isset( $_POST['poex_settings_nonce'] ) ? $_POST['poex_settings_nonce'] : '';
        if( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'poex_save_settings' ) ) {
            return false;
        }

        if( ! current_user_can( 'activate_plugins' ) ) {
            return false;
        }

        $this->submission_error = true;
        add_action( 'poex_notices', array( &$this, 'show_notices' ) );
        $this->submitted_settings = $_POST['poex'];

        if( empty( $this->submitted_settings['title'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'A title for you poll is required!';
            return false;
        }

        if( empty( $this->submitted_settings['question'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'A question for you poll is required!';
            return false;
        }

        if( empty( $this->submitted_settings['input_type'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'You forgot to allow / disallow multiple votes.';
            return false;
        }

        if( ! is_array( $this->submitted_settings['answers'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'You did not set any answers.';
            return false;
        }

        foreach( $this->submitted_settings['answers'] as $a ) {
            if( ! empty( $a ) ) {
                $valid_answers[] = $a;
            }
        }

        if( empty ( $valid_answers ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'You did not set any answers.';
            return false;
        }

        $this->submission_error = false;
        $this->submitted_settings['answers'] = $valid_answers;
        $this->notice_type = 'updated';
        $this->notice_content = 'Settings saved!';

        update_option( 'poex_settings', $this->submitted_settings );
        return true;
    }

    function register_assets() {
        $js_dependencies = array( 'jquery' );
        wp_register_script( 'poex-js', POEX_PATH . 'js/poex-settings.js', $js_dependencies );
        wp_register_style( 'poex-css', POEX_PATH . 'css/poex.css' );
    }

    function enqueue_assets() {
        wp_enqueue_script( 'poex-js' );
        wp_enqueue_style( 'poex-css' );
    }

    function add_menu_items() {
        $settings_page_title = 'Poller Express Settings';
        $settings_menu_title = 'Poller Express';
        $settings_capability = 'activate_plugins';
        $settings_menu_slug = 'poex_settings';
        $settings_function = array( &$this, 'render_settings_page' );
        $settings_hook_suffix = add_menu_page(
            $settings_page_title,
            $settings_menu_title,
            $settings_capability,
            $settings_menu_slug,
            $settings_function
        );
        add_action('admin_print_scripts-' . $settings_hook_suffix, array( &$this, 'enqueue_assets' ) );
    }

    function render_settings_page() {
        if( !empty($this->is_submitting) ){
            do_action('poex_notices');
        }

        if( $this->submission_error == true ) {
            $settings = $this->submitted_settings;
        } else {
            $settings = $this->get_settings();
        }

        $poex_settings_nonce = wp_create_nonce( 'poex_save_settings' );
        include POEX_DIR . 'views/settings.php';
    }
}

?>