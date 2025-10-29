<?php
/**
 * Singleton trait.
 *
 * @package Eighteen73\ProductExtraContent
 */

namespace Eighteen73\ProductExtraContent;

trait Singleton {

	/**
	 * The class instance.
	 *
	 * @var self|null Single instance of this class.
	 */
	private static $instance = null;

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'product-extra-content' ), false );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'product-extra-content' ), false );
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Intentionally empty.
	}

	/**
	 * Gets the main instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @return self Single instance of this class.
	 */
	final public static function instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
