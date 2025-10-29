<?php
/**
 * Plugin Name:       WooCommerce Product Extra Content
 * Plugin URI:        https://eighteen73.co.uk
 * Update URI:        https://eighteen73.co.uk
 * Description:       Display extra content for your WooCommerce products.
 * Version:           0.2.1
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            eighteen73
 * Author URI:        https://eighteen73.co.uk
 * Text Domain:       product-extra-content
 * Domain Path:       /languages
 *
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package           Eighteen73\ProductExtraContent
 */

namespace Eighteen73\ProductExtraContent;

defined( 'ABSPATH' ) || exit;

// Useful global constants.
define( 'PRODUCT_EXTRA_CONTENT_URL', plugin_dir_url( __FILE__ ) );
define( 'PRODUCT_EXTRA_CONTENT_PATH', plugin_dir_path( __FILE__ ) );
define( 'PRODUCT_EXTRA_CONTENT_INC', PRODUCT_EXTRA_CONTENT_PATH . 'includes/' );

// Require the autoloader.
$autoloader = PRODUCT_EXTRA_CONTENT_PATH . '/vendor/autoload.php';

if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
} else {
	add_action(
		'admin_notices',
		function () {
			printf(
				'<div class="notice notice-error"><p><strong>%s</strong>: %s</p></div>',
				esc_html__( 'Product Extra Content', 'product-extra-content' ),
				sprintf(
					/* translators: %s: composer install command */
					esc_html__( 'Composer dependencies not found. Please run %s to install required dependencies.', 'product-extra-content' ),
					'composer install'
				)
			);
		}
	);
	return;
}

// Initialise the plugin.
Plugin::instance()->setup();

// Register activation and deactivation hooks.
register_activation_hook( __FILE__, [ Plugin::class, 'activation' ] );
register_deactivation_hook( __FILE__, [ Plugin::class, 'deactivation' ] );
