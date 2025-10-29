<?php
/**
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @package Eighteen73\\ProductExtraContent
 */

use Eighteen73\ProductExtraContent\Content;

$product_id = $block->context['postId'];

if ( ! $product_id ) {
	return;
}
$content = Content::instance()->get_product_extra_content( $product_id );
if ( empty( $content ) ) {
	return;
}
?>

<div
	<?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>
>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
