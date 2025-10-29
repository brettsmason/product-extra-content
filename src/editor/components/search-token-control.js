import { FormTokenField } from '@wordpress/components';
import { useDebounce } from '@wordpress/compose';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import { __, sprintf } from '@wordpress/i18n';

// Generic search + token control for posts or terms (HM-style API);
// adds `kind` and `name` to switch between taxonomy and post type.
// - kind: 'postType' | 'taxonomy'
// - name: post type or taxonomy name
// - value: number[] of selected IDs
// - onChange: (number[]) => void
// - label: string
// - placeholder?: string
// - postStatus?: string | string[] (only for postType)
const SearchTokenControl = ({
	kind,
	name,
	value,
	onChange,
	label,
	placeholder,
	postStatus = 'publish',
}) => {
	const [search, setSearch] = useState('');
	const [debouncedSearch, setDebouncedSearch] = useState('');
	const debouncedSetSearch = useDebounce(
		(val) => setDebouncedSearch(val),
		300
	);

	useEffect(() => {
		debouncedSetSearch(search);
	}, [search, debouncedSetSearch]);

	// Selected records (by ID) so we can display labels immediately
	const selectedRecords = useSelect(
		(select) => {
			if (!Array.isArray(value) || value.length === 0) return [];
			const args = {
				include: value,
				per_page: value.length || 1,
			};
			if (kind === 'postType') {
				args.status = postStatus;
			}
			return select(coreStore).getEntityRecords(kind, name, args) || [];
		},
		[value, kind, name, postStatus]
	);

	// Suggestions based on debounced search text
	const suggestionRecords = useSelect(
		(select) => {
			if (!debouncedSearch) return [];
			const args = {
				search: debouncedSearch,
				per_page: 20,
			};
			if (kind === 'postType') {
				args.status = postStatus;
			}
			return select(coreStore).getEntityRecords(kind, name, args) || [];
		},
		[debouncedSearch, kind, name, postStatus]
	);

	const getBaseLabel = (record) =>
		kind === 'taxonomy'
			? decodeEntities(record?.name ?? '')
			: decodeEntities(
					(record?.title?.rendered || record?.title || '').toString()
				);

	const selected = Array.isArray(selectedRecords)
		? selectedRecords.map((r) => ({ id: r.id, label: getBaseLabel(r) }))
		: [];
	const suggestions = Array.isArray(suggestionRecords)
		? suggestionRecords.map((r) => ({ id: r.id, label: getBaseLabel(r) }))
		: [];

	const selectedLabels = Array.isArray(value)
		? value
				.map((id) => selected.find((s) => s.id === id)?.label)
				.filter(Boolean)
		: [];

	return (
		<FormTokenField
			label={label}
			value={selectedLabels}
			suggestions={(debouncedSearch ? suggestions : []).map(
				(s) => s.label
			)}
			onInputChange={(text) => setSearch(text)}
			onChange={(tokens) => {
				const pool = [...selected, ...suggestions];
				const ids = tokens
					.map((token) => pool.find((p) => p.label === token)?.id)
					.filter((id) => Number.isInteger(id));
				onChange(ids);
			}}
			placeholder={
				placeholder ||
				sprintf(__('Search %s…', 'product-extra-content'), name)
			}
		/>
	);
};

export default SearchTokenControl;
