<?php
/**
 * Main plugin class.
 *
 * @package Eighteen73\ProductExtraContent
 */

namespace Eighteen73\ProductExtraContent;

defined( 'ABSPATH' ) || exit;

/**
 * Main Plugin class.
 */
class Plugin {

	use Singleton;

	/**
	 * Setup the plugin.
	 *
	 * @return void
	 */
	public function setup(): void {
		add_action( 'init', [ $this, 'load_textdomain' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'init', [ $this, 'register_blocks' ] );

		PostType::instance()->setup();
		Content::instance()->setup();
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'product-extra-content',
			false,
			PRODUCT_EXTRA_CONTENT_PATH . '/languages'
		);
	}

	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	public static function activation(): void {}

	/**
	 * Plugin deactivation.
	 *
	 * @return void
	 */
	public static function deactivation(): void {}

	/**
	 * Enqueue frontend scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts(): void {
		$asset_file = require PRODUCT_EXTRA_CONTENT_PATH . 'build/editor.asset.php';

		wp_enqueue_script(
			'product-extra-content-editor',
			PRODUCT_EXTRA_CONTENT_URL . 'build/editor.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			[
				'in_footer' => true,
			]
		);
	}

	/**
	 * Register blocks.
	 *
	 * @return void
	 */
	public function register_blocks(): void {
		wp_register_block_types_from_metadata_collection(
			PRODUCT_EXTRA_CONTENT_PATH . 'build/blocks',
			PRODUCT_EXTRA_CONTENT_PATH . 'build/blocks-manifest.php',
		);
	}
}
