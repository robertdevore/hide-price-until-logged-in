<?php
/**
 * Plugin Name: Hide Price Until Logged In
 * Description: Hides WooCommerce product prices until the user is logged in.
 * Version:     1.0
 * Author:      Plugin Pal
 * Author URI:  https://pluginpal.app
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Hide_Price_Until_Logged_In {

    /**
     * Constructor to initialize the plugin.
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_inline_scripts' ] );
        add_filter( 'woocommerce_get_price_html', [ $this, 'hide_price' ], 10, 2 );
    }

    /**
     * Adds the settings page under WooCommerce menu.
     */
    public function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            __( 'Hide Price Settings', 'hide-price-until-logged-in' ),
            __( 'Hide Price', 'hide-price-until-logged-in' ),
            'manage_options',
            'hide-price-until-logged-in',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Registers the plugin settings.
     */
    public function register_settings() {
        register_setting( 'hide_price_settings', 'hide_price_enabled' );
    }

    /**
     * Renders the settings page.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Hide Price Settings', 'hide-price-until-logged-in' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'hide_price_settings' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Enable', 'hide-price-until-logged-in' ); ?></th>
                        <td>
                            <input type="checkbox" name="hide_price_enabled" value="1" <?php checked( get_option( 'hide_price_enabled' ), 1 ); ?> />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueues inline CSS and JS scripts.
     */
    public function enqueue_inline_scripts() {
        if ( ! is_user_logged_in() && get_option( 'hide_price_enabled' ) ) {
            add_action( 'wp_head', function() {
                ?>
                <style>
                    .price {
                        display: none;
                    }
                </style>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('Prices are hidden until logged in.');
                    });
                </script>
                <?php
            });
        }
    }

    /**
     * Hides the price if the user is not logged in and setting is enabled.
     *
     * @param string $price The product price HTML.
     * @param object $product The product object.
     * @return string The modified price HTML.
     */
    public function hide_price( $price, $product ) {
        if ( ! is_user_logged_in() && get_option( 'hide_price_enabled' ) ) {
            return '';
        }
        return $price;
    }
}

new Hide_Price_Until_Logged_In();