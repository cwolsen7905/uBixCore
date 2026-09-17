import js from '@eslint/js';
import globals from 'globals';
import reactHooks from 'eslint-plugin-react-hooks';
import tseslint from 'typescript-eslint';
import prettier from 'eslint-config-prettier';

export default tseslint.config(
	{ ignores: ['build/**', '.react-router/**', 'node_modules/**'] },
	js.configs.recommended,
	...tseslint.configs.recommended,
	// v7 keeps the eslintrc-shaped configs at `configs[...]`; the flat-config
	// versions live under `configs.flat[...]`. Using the former fails with
	// "A config object has a 'plugins' key defined as an array of strings".
	reactHooks.configs.flat['recommended-latest'],
	prettier,
	{ languageOptions: { globals: { ...globals.browser, ...globals.node } } }
);
