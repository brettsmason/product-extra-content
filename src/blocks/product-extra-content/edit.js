import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import './editor.scss';

/**
 * @return {Element} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			{__(
				'Product extra content will be displayed here if available.',
				'product-extra-content'
			)}
		</div>
	);
}
