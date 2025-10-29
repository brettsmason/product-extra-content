import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import SearchTokenControl from './components/search-token-control';
import { __ } from '@wordpress/i18n';

const ProductExtraContentPanel = () => {
	const postType = useSelect(
		(select) => select('core/editor').getCurrentPostType(),
		[]
	);

	const [meta, setMeta] = useEntityProp('postType', postType, 'meta');

	return (
		<PluginDocumentSettingPanel
			name="product-extra-content"
			title={__('Settings', 'product-extra-content')}
			className="product-extra-content"
		>
			<SearchTokenControl
				kind="postType"
				name="product"
				label={__('Products', 'product-extra-content')}
				value={meta?.product_ids || []}
				onChange={(ids) =>
					setMeta({ ...(meta || {}), product_ids: ids })
				}
				placeholder={__(
					'Search for products…',
					'product-extra-content'
				)}
			/>
			<SearchTokenControl
				kind="taxonomy"
				name="product_cat"
				label={__('Categories', 'product-extra-content')}
				value={meta?.category_ids || []}
				onChange={(ids) =>
					setMeta({ ...(meta || {}), category_ids: ids })
				}
				placeholder={__(
					'Search for categories…',
					'product-extra-content'
				)}
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin('product-extra-content-panel', {
	render: ProductExtraContentPanel,
});
