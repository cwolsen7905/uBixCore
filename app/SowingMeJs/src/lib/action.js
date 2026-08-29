/**
 * Svelte action: run `callback` when a mousedown lands outside `node`.
 *
 * @param {HTMLElement} node
 * @param {() => void} callback
 */
export function onClickOutside(node, callback) {
	/** @param {MouseEvent} event */
	const handleClick = (event) => {
		if (!node.contains(/** @type {Node} */ (event.target))) {
			callback();
		}
	};

	document.addEventListener('mousedown', handleClick, true);

	return {
		destroy() {
			document.removeEventListener('mousedown', handleClick, true);
		}
	};
}
