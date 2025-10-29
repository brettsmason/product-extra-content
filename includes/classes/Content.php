<?php
/**
 * Content class.
 *
 * @package Eighteen73\ProductExtraContent
 */

namespace Eighteen73\ProductExtraContent;

/**
 * Content class.
 */
class Content {
	use Singleton;

	/**
	 * Setup the content.
	 *
	 * @return void
	 */
	public function setup(): void {
		add_action( 'save_post_product_extra', [ $this, 'on_save_product_extra' ], 10, 3 );
		add_action( 'before_delete_post', [ $this, 'on_before_delete_post' ] );
		add_action( 'publish_product_extra', [ $this, 'on_publish_product_extra' ], 10, 2 );
	}

	/**
	 * Output the product extra content for a product.
	 *
	 * @param int $product_id The product ID.
	 * @return void
	 */
	public function product_extra_content( $product_id ): void {
		echo $this->get_product_extra_content( $product_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render the block content of matching product_extra posts, sorted by menu_order.
	 *
	 * @param int $product_id The product ID.
	 * @return string The rendered HTML.
	 */
	public function get_product_extra_content( int $product_id ): string {
		$extra_ids = $this->get_product_extra_content_ids( $product_id );
		if ( empty( $extra_ids ) ) {
			return '';
		}

		$html = '';
		foreach ( $extra_ids as $extra_id ) {
			$content = get_the_content( null, false, $extra_id );
			if ( is_string( $content ) && $content !== '' ) {
				$html .= do_blocks( $content );
			}
		}

		return $html;
	}

	/**
	 * Get the matching extra content IDs for a product.
	 *
	 * @param int $product_id The product ID.
	 * @return array The matching extra content.
	 */
	public function get_product_extra_content_ids( int $product_id ): array {
		$cache_key = $this->get_cache_key( $product_id );
		$extra_ids = get_transient( $cache_key );
		if ( is_array( $extra_ids ) ) {
			return $extra_ids;
		}

		$product_category_ids = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'ids' ] );
		$product_category_ids = array_map( 'absint', is_array( $product_category_ids ) ? $product_category_ids : [] );

		$all_extra_ids = get_posts(
			[
				'post_type'   => 'product_extra',
				'post_status' => 'publish',
				'numberposts' => 100,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'fields'      => 'ids',
			]
		);

		$matched_ids = [];
		foreach ( $all_extra_ids as $extra_id ) {
			$meta_product_ids  = get_post_meta( $extra_id, 'product_ids', true );
			$meta_category_ids = get_post_meta( $extra_id, 'category_ids', true );

			$meta_product_ids  = array_map( 'absint', is_array( $meta_product_ids ) ? $meta_product_ids : [] );
			$meta_category_ids = array_map( 'absint', is_array( $meta_category_ids ) ? $meta_category_ids : [] );

			$matches_product  = in_array( $product_id, $meta_product_ids, true );
			$matches_category = ! empty( array_intersect( $product_category_ids, $meta_category_ids ) );

			if ( $matches_product || $matches_category ) {
				$matched_ids[] = (int) $extra_id;
			}
		}

		set_transient( $cache_key, $matched_ids, DAY_IN_SECONDS );

		return $matched_ids;
	}

	/**
	 * Generate the product-specific transient cache key.
	 *
	 * @param int $product_id Product ID.
	 * @return string
	 */
	private function get_cache_key( int $product_id ): string {
		return "product_extra_content_{$product_id}";
	}

	/**
	 * Handle save of a product_extra post: flush caches for affected products.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @param bool     $update  Whether this is an existing post being updated.
	 * @return void
	 */
	public function on_save_product_extra( int $post_id, $post, bool $update ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$this->flush_caches_for_product_extra( $post_id );
	}

	/**
	 * Handle deletion of a product_extra post: flush caches for affected products.
	 * Uses before_delete_post so meta is still available.
	 *
	 * @param int $post_id Post being deleted.
	 * @return void
	 */
	public function on_before_delete_post( int $post_id ): void {
		if ( get_post_type( $post_id ) !== 'product_extra' ) {
			return;
		}

		$this->flush_caches_for_product_extra( $post_id );
	}

	/**
	 * When a product_extra is published, flush caches for affected products so new content appears.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function on_publish_product_extra( int $post_id ): void {
		$this->flush_caches_for_product_extra( $post_id );
	}

	/**
	 * Compute affected product IDs for a product_extra and delete their transients.
	 *
	 * @param int $extra_id Product extra post ID.
	 * @return void
	 */
	private function flush_caches_for_product_extra( int $extra_id ): void {
		$product_ids  = get_post_meta( $extra_id, 'product_ids', true );
		$category_ids = get_post_meta( $extra_id, 'category_ids', true );

		$product_ids  = array_map( 'absint', is_array( $product_ids ) ? $product_ids : [] );
		$category_ids = array_map( 'absint', is_array( $category_ids ) ? $category_ids : [] );

		$products_from_categories = [];
		if ( ! empty( $category_ids ) ) {
			$products_from_categories = $this->get_products_in_categories( $category_ids );
		}

		$affected = array_values( array_unique( array_merge( $product_ids, $products_from_categories ), SORT_NUMERIC ) );

		foreach ( $affected as $product_id ) {
			$cache_key = $this->get_cache_key( (int) $product_id );
			delete_transient( $cache_key );
		}
	}

	/**
	 * Get product IDs belonging to any of the provided product_cat term IDs.
	 *
	 * @param array<int> $category_ids Category term IDs.
	 * @return array<int> Product post IDs.
	 */
	private function get_products_in_categories( array $category_ids ): array {
		$category_ids = array_filter( array_map( 'absint', $category_ids ) );
		if ( empty( $category_ids ) ) {
			return [];
		}

		$product_ids = get_posts(
			[
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'numberposts'            => 100,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'tax_query'              => [
					[
						'taxonomy'         => 'product_cat',
						'field'            => 'term_id',
						'terms'            => $category_ids,
						'include_children' => true,
					],
				],
			]
		);

		return array_map( 'absint', is_array( $product_ids ) ? $product_ids : [] );
	}
}
