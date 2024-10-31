<?php
/**
* Plugin Name: Elegant Apps Integration of Google Sheet With Contact Form 7
* Description: This plugin allows create Elegant Apps Integration of Google Sheet With Contact Form 7 plugin.
* Version: 1.0
* Copyright: 2020
* Text Domain: oc-google-sheet-with-contact-form-7
* Domain Path: /languages 
*/

if (!defined('ABSPATH')) {
   	die('-1');
}
if (!defined('CF7GLGSHEET_PLUGIN_NAME')) {
   	define('CF7GLGSHEET_PLUGIN_NAME', 'Elegant Apps Integration of Google Sheet With Contact Form 7');
}
if (!defined('CF7GLGSHEET_PLUGIN_VERSION')) {
   	define('CF7GLGSHEET_PLUGIN_VERSION', '1.0.0');
}
if (!defined('CF7GLGSHEET_PLUGIN_FILE')) {
   	define('CF7GLGSHEET_PLUGIN_FILE', __FILE__);
}
if (!defined('CF7GLGSHEET_PLUGIN_DIR')) {
   	define('CF7GLGSHEET_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('CF7GLGSHEET_BASE_NAME')) {
    define('CF7GLGSHEET_BASE_NAME', plugin_basename(CF7GLGSHEET_PLUGIN_FILE));
}
if (!defined('CF7GLGSHEET_DOMAIN')) {
   	define('CF7GLGSHEET_DOMAIN', 'oc-google-sheet-with-contact-form-7');
}
if (!defined('DEBUG')) {
    define('DEBUG', 0);
}
if (!defined('CF7GLGSHEETPREFIX')) {
    define('CF7GLGSHEETPREFIX', "cf7glgsheet_");
}
if (!defined('PAGE_SLUG')) {
    define('PAGE_SLUG', "elgent_cf7glgsheet_googlesheet");
}
// if (!defined('CF7GLGSHEET_PLUGIN_DIR_PATH')) {
define( 'CF7GLGSHEET_PLUGIN_DIR_PATH' , plugin_dir_path( __FILE__ ));
/*}
*/
    require_once CF7GLGSHEET_PLUGIN_DIR_PATH.'/lib/vendor/autoload.php';

    require_once CF7GLGSHEET_PLUGIN_DIR_PATH. '/lib/google-sheets.php';

if (!class_exists('CF7GLGSHEET')) {

    add_action('plugins_loaded', array('CF7GLGSHEET', 'CF7GLGSHEET_instance'));


    class CF7GLGSHEET {

        protected static $CF7GLGSHEET_instance; 

        public static function CF7GLGSHEET_instance() {
          if (!isset(self::$CF7GLGSHEET_instance)) {
              self::$CF7GLGSHEET_instance = new self();
              self::$CF7GLGSHEET_instance->init();
               self::$CF7GLGSHEET_instance->includes();
             
          }
          return self::$CF7GLGSHEET_instance;
        }

        function includes() {

        
          include_once('admin/cf7glgsheet-backend.php');     
         
        }

        function CF7GLGSHEET_load_plugin() {
            if ( ! ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ) {
                add_action( 'admin_notices', array($this,'CF7GLGSHEET_install_error') );
            }
        }

        function init() {

            add_action( 'admin_init', array($this, 'CF7GLGSHEET_load_plugin'), 11 );

            add_action( 'admin_enqueue_scripts', array($this, 'CF7GLGSHEET_load_admin_script_style'));

            add_filter( 'plugin_row_meta', array( $this, 'CF7GLGSHEET_plugin_row_meta' ), 10, 2 );
        }

        function CF7GLGSHEET_install_error() {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            ?>
              <div class="error">

                  <p>
                      <?php _e( ' cf7 calculator plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=contact+form+7">Contact Form 7</a> plugin installed and activated.', CF7GLGSHEET_DOMAIN );?>
                  </p>
                  
              </div>
            <?php
        }


        function CF7GLGSHEET_load_admin_script_style() {
            $translation_array_imgss = CF7GLGSHEET_PLUGIN_DIR;
            wp_enqueue_style( 'CF7GLGSHEET-back-style', CF7GLGSHEET_PLUGIN_DIR . '/includes/css/back_style.css', false, '1.0.0' );
            wp_enqueue_script( 'CF7GLGSHEET-back-js', CF7GLGSHEET_PLUGIN_DIR . '/includes/js/oc_googlesheetcf7_back.js', false, '2.0.0' , true );
            wp_localize_script( 'CF7GLGSHEET-back-js', 'CF7GLGSHEET_jsdata', array(
                     'image_name' => $translation_array_imgss) 
            );
            wp_localize_script( 'ajaxloadpost', 'ajax_postajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }

        function CF7GLGSHEET_plugin_row_meta( $links, $file ) {
          if ( CF7GLGSHEET_BASE_NAME === $file ) {
              $row_meta = array(
                  'rating'    =>  '<a href="https://xthemeshop.com/how-to-create-google-sheet-with-contact-form-7/" target="_blank">Documentation</a> | <a href="https://xthemeshop.com/contact/" target="_blank">Support</a> | <a href="https://wordpress.org/support/plugin/oc-google-sheet-with-contact-form-7/reviews/?filter=5" target="_blank"><img src="'.CF7GLGSHEET_PLUGIN_DIR.'/includes/images/star.png" class="costcf7oc_rating_div"></a>',
              );
              return array_merge( $links, $row_meta );
          }
          return (array) $links;
        } 

    }  
}


add_action( 'plugins_loaded', 'CF7GLGSHEET_load_textdomain' );
function CF7GLGSHEET_load_textdomain() {
    load_plugin_textdomain( 'oc-google-sheet-with-contact-form-7', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function CF7GLGSHEET_load_my_own_textdomain( $mofile, $domain ) {
    if ( 'oc-google-sheet-with-contact-form-7' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
    }
    return $mofile;
}
add_filter( 'load_textdomain_mofile', 'CF7GLGSHEET_load_my_own_textdomain', 10, 2 );

