import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * @return {Element} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
            {__('Hello from Product Extra Content', 'product-extra-content')}
		</div>
	);
}
