<?php

namespace Eighteen73\ProductExtraContent;

class PostType {
	use Singleton;

	public function setup(): void {
		add_action( 'init', [ $this, 'register' ] );
		add_action( 'init', [ $this, 'register_meta' ] );
	}

	public function register(): void {
		$labels = [
			'name'                  => _x( 'Extra Content', 'Post type general name', 'product-extra-content' ),
			'singular_name'         => _x( 'Extra Content', 'Post type singular name', 'product-extra-content' ),
			'menu_name'             => _x( 'Extra Content', 'Admin Menu text', 'product-extra-content' ),
			'name_admin_bar'        => _x( 'Extra Content', 'Add New on Toolbar', 'product-extra-content' ),
			'add_new'               => __( 'Add New', 'product-extra-content' ),
			'add_new_item'          => __( 'Add New Extra Content', 'product-extra-content' ),
			'new_item'              => __( 'New Extra Content', 'product-extra-content' ),
			'edit_item'             => __( 'Edit Extra Content', 'product-extra-content' ),
			'view_item'             => __( 'View Extra Content', 'product-extra-content' ),
			'all_items'             => __( 'Extra Content', 'product-extra-content' ),
			'search_items'          => __( 'Search Extra Content', 'product-extra-content' ),
			'parent_item_colon'     => __( 'Parent Extra Content:', 'product-extra-content' ),
			'not_found'             => __( 'No Extra Content found.', 'product-extra-content' ),
			'not_found_in_trash'    => __( 'No Extra Content found in Trash.', 'product-extra-content' ),
			'insert_into_item'      => _x( 'Insert into Extra Content', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'product-extra-content' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this Extra Content', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'product-extra-content' ),
			'filter_items_list'     => _x( 'Filter Extra Content list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'product-extra-content' ),
			'items_list_navigation' => _x( 'Extra Content list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'product-extra-content' ),
			'items_list'            => _x( 'Extra Content list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'product-extra-content' ),
		];

		$args = [
			'labels'             => $labels,
			'description'        => 'Extra Content custom post type.',
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=product',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'supports'           => [ 'title', 'editor', 'custom-fields' ],
			'show_in_rest'       => true
		];

		register_post_type( 'product_extra', $args );
	}

	// Register product ids and category ids meta fields.
	public function register_meta(): void {
		register_post_meta( 'product_extra', 'product_ids', [
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [ 'type' => 'integer' ],
				],
			],
			'single'  => true,
			'type'    => 'array',
			'default' => [],
		] );

		register_post_meta( 'product_extra', 'category_ids', [
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [ 'type' => 'integer' ],
				],
			],
			'single'  => true,
			'type'    => 'array',
			'default' => [],
		] );
	}
}
