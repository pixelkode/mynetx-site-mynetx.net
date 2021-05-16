<?php
/*
Plugin Name: WP Connect Coil
Plugin URI: 
Description: This plugin can register a payment pointer for Web monetization with Coil.
Version: 1.0.0
Author: tarotaro080808
Author URI: https://github.com/tarotaro080808
License: GPL2
Text Domain: WpConnectCoilTrtr
Domain Path: /languages
*/

if ( !class_exists('Wp_Connect_Coil_Trtr') ) {
  class Wp_Connect_Coil_Trtr {
    const DOMAIN = 'WpConnectCoilTrtr';
    static public $checkResult = false;

    public function __construct() {
      add_action( 'admin_menu', array( $this, 'setPluginTextDomain' ) );      
      add_filter( 'wp_head', array( $this, 'customMetaTagForCoil' ) );
      add_action( 'admin_menu', array( $this, 'addMenuForCoil' ) );
    }

    // Set TextDomain
    function setPluginTextDomain() {
      load_plugin_textdomain(self::DOMAIN, false, basename( dirname( __FILE__ ) ).'/languages' );
    }

    // Add meta tag to <head> for Coil. 
    function customMetaTagForCoil() {
      if (get_option('_coil_payment_pointer_trtr')) {
        echo '<meta name="monetization" content="' . esc_attr(get_option('_coil_payment_pointer_trtr')) . '" />';
      }
    }

    // Add submenu to Admin Settings page.
    function addMenuForCoil() {
      add_submenu_page( 'options-general.php', __('Coil Setting', self::DOMAIN), __('Coil Setting', self::DOMAIN), 'manage_options', 'coil-setting', array(&$this, 'options_page') );
    }
    // Validation for check $_POST data.
    private function checkValidation() {
      if ( is_admin() ) {
        // Check nonce is set.
        if ( !isset( $_POST['wp_connect_coil_nonce'] ) ) {
          return;
        }
        // Check nonce is correct.
        if ( !wp_verify_nonce( $_POST['wp_connect_coil_nonce'], 'wp_connect_coil_update_options' ) ) {
          return;
        }
        // If auto save then don't do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
          return;
        }
        // Check set data.
        if ( !isset( $_POST['coil_payment_pointer_trtr'] ) ) {
          return;
        }
        // Sanitize input data.
        $sanitizedData = sanitize_text_field( $_POST['coil_payment_pointer_trtr'] );
        // Update field
        update_option( '_coil_payment_pointer_trtr', $sanitizedData );
        self::$checkResult = true;
      }
    }

    // Display message when saved payment pointer.
    private function displayNofity() {
      if( self::$checkResult ) {
      ?>
      <div class="updated">
        <p>
          <?php _e('Saved Setting', self::DOMAIN); ?>
        </p>
      </div>
      <?php
      }
    }

    // Callback function.
    function options_page() {
      self::checkValidation();
      ?>
      
      <div class="wrap">
        <h1><?php _e('Connection setting for Coil', self::DOMAIN); ?></h1>
        <?php self::displayNofity(); ?>

        <h2><?php _e('Get ready', self::DOMAIN); ?></h2>
        <p>
          <?php _e('First, please obtain payment pointer from XRP Tip Bot or others.', self::DOMAIN); ?>
          <br>
          <?php _e('Then, please register the acquired payment pointer in Coil.', self::DOMAIN); ?>
        </p>

        <form action="options-general.php?page=coil-setting" method="post">
          <?php wp_nonce_field( 'wp_connect_coil_update_options', 'wp_connect_coil_nonce' ); ?>
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row"><?php _e('Payment Pointer', self::DOMAIN); ?></th>
                <td>
                  <input name="coil_payment_pointer_trtr" type="text" id="paymentPointer" 
                    value="<?php echo esc_attr(get_option('_coil_payment_pointer_trtr')); ?>" class="regular-text">
                  <p class="description" id="tagline-description"><?php _e('Example: $twitter.xrptipbot.com/YOUR_ACCOUNT', self::DOMAIN); ?></p>
                </td>
              </tr>
            </tbody>
          </table>
          <p class="submit">
            <?php submit_button( __( 'Save Changes', self::DOMAIN ), 'primary', 'Update' ); ?>
          </p>
        </form>
      </div>      
      <?php
    }
  }
}
$ConnectCoilClass = new Wp_Connect_Coil_Trtr();
