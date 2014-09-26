<?php
/**
 * @package Poller_express
 */

/**
 * Our controller for the WordPress dashboard area
 * Creates a menu item and page in the dash
 * Saves user submitted settings
 */
class Poex_admin extends Poller_express {

    // declare our properties
    public $is_submitting; // will be true if a user has submitted a form
    public $notice_type; // what type of notice to show the user, 'updated' or 'error'
    public $notice_content; // what message to show the user
    public $submission_error; // will be true if one of our checks fail during submission
    public $submitted_settings; // we may want to display the submitted settings rather than the stored settings

    /**
     * Our construct simply attaches internal methods to wp hooks
     */
    function __construct() {
        // this will allow us to declare a menu item
        add_action( 'admin_menu', array( &$this, 'add_menu_items' ) );
        // this will allow us to register .js and .css files
        add_action( 'admin_init', array( &$this, 'register_assets' ) );
        // if a user clicks save on the settings page, we can run that process during admin_init
        if( isset( $_POST['action'] ) && $_POST['action'] == 'save_poex_settings' ) {
            add_action( 'admin_init', array( &$this, 'save_settings' ) );
        }
    }

    /**
     * We may want to display a notice to the user.
     * We've conveniently stored the UI for these in our views folder
     */
    function show_notices() {
        include POEX_DIR . 'views/notices.php';
    }

    /**
     * This method will validate user submitted settings, store the settings, and display a notice
     * @return bool
     */
    function save_settings() {

        // first we check to make sure we have any data
        if( empty( $_POST['poex'] ) ) {
            return false;
        }

        // next we verify our nonce to make sure the submission is coming from the right place
        $nonce = isset( $_POST['poex_settings_nonce'] ) ? $_POST['poex_settings_nonce'] : '';
        if( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'poex_save_settings' ) ) {
            return false;
        }

        // we make sure our user has the right pledges to make any changes to the settings
        if( ! current_user_can( 'activate_plugins' ) ) {
            return false;
        }

        // we've made it this far, we are probably not dealing with a trouble maker or a bot
        // we should now let the rest of the code know that we'll need to display a notice of results
        $this->is_submitting = true;
        add_action( 'poex_notices', array( &$this, 'show_notices' ) );

        // there still could be issues with the submission though, so let's not get excited yet
        $this->submission_error = true;

        // save the submission to our class
        $this->submitted_settings = $_POST['poex'];

        // check for a title
        if( empty( $this->submitted_settings['title'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'A title for you poll is required!';
            return false;
        }

        // check for a quesiton
        if( empty( $this->submitted_settings['question'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'A question for you poll is required!';
            return false;
        }

        // check for an input_type
        if( empty( $this->submitted_settings['input_type'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'You forgot to allow / disallow multiple votes.';
            return false;
        }

        // check for answers
        if( ! is_array( $this->submitted_settings['answers'] ) ) {
            $this->notice_type = 'error';
            $this->notice_content = 'You did not set any answers.';
            return false;
        }

        // make sure all answers have content
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

        // Yay! Everything looks good
        $this->submission_error = false;
        $this->submitted_settings['answers'] = $valid_answers;
        $this->notice_type = 'updated';
        $this->notice_content = 'Settings saved!';
        // we are safe to save the settings
        update_option( 'poex_settings', $this->submitted_settings );
        return true;
    }

    /**
     * We can register our .js and .css using their absolute paths
     * Dependencies are amazing!
     */
    function register_assets() {
        $js_dependencies = array( 'jquery' );
        wp_register_script( 'poex-js', POEX_PATH . 'js/poex-settings.js', $js_dependencies );
        wp_register_style( 'poex-css', POEX_PATH . 'css/poex.css' );
    }

    /**
     * We can enqueue our assets when needed.
     */
    function enqueue_assets() {
        wp_enqueue_script( 'poex-js' );
        wp_enqueue_style( 'poex-css' );
    }

    /**
     * This will add a "Poller Express" menu item to the admin
     */
    function add_menu_items() {
        $settings_page_title = 'Poller Express Settings'; // <title> for our new admin page
        $settings_menu_title = 'Poller Express'; // This is the label in the menu
        $settings_capability = 'activate_plugins'; // This means only admins or above will see our menu item
        $settings_menu_slug = 'poex_settings'; // ?page=poex_settings will be our url
        $settings_function = array( &$this, 'render_settings_page' ); // this internal method will render our menu

        // add_menu_page will create a new hook suffix. what's that? scroll down to find out.
        $settings_hook_suffix = add_menu_page(
            $settings_page_title,
            $settings_menu_title,
            $settings_capability,
            $settings_menu_slug,
            $settings_function
        );

        // we can use our newly minted hook suffix to enqueu our .js and .css on our page only
        // no need to clutter up the rest of the dashboard with unneeded assets
        add_action('admin_print_scripts-' . $settings_hook_suffix, array( &$this, 'enqueue_assets' ) );
    }

    /**
     * Runs some logic, and then include a view file
     */
    function render_settings_page() {

        // if we are in submission mode, we'll need to display a notice
        if( !empty($this->is_submitting) ){
            do_action('poex_notices');
        }


        if( $this->submission_error == true ) {
            // if there was a submssion error, we should display their submission so they can see where the went wrong
            $settings = $this->submitted_settings;
        } else {
            // otherwise, just display their stored settings
            $settings = $this->get_settings();
        }

        // these vars determine what should be pre-checked
        $radio_type_checked = ( $settings['input_type'] == 'radio' ) ? 'checked="checked"' : '';
        $checkbox_type_checked = ( $settings['input_type'] == 'checkbox' ) ? 'checked="checked"' : '';

        // at this point we'll create a nonce, which we'll then verify on submission
        $poex_settings_nonce = wp_create_nonce( 'poex_save_settings' );
        include POEX_DIR . 'views/settings.php';
    }
}

?>