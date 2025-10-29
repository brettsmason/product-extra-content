<?php
// This file is generated. Do not modify it manually.
return array(
	'product-extra-content' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'eighteen73/product-extra-content',
		'version' => '0.1.0',
		'title' => 'Product Extra Content',
		'category' => 'woocommerce',
		'description' => 'Display extra content for your WooCommerce products.',
		'usesContext' => array(
			'postId'
		),
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			)
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'full'
			),
			'layout' => array(
				'type' => 'object',
				'default' => array(
					'type' => 'constrained'
				)
			)
		),
		'textdomain' => 'product-extra-content',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'render' => 'file:./render.php'
	)
);
