/**
 * Base URL of this product's API for the current environment.
 *
 * Server-side only -- it reads `process.env`, which is why the value is handed
 * to the client through the root loader rather than recomputed in the browser.
 * Replace these hosts with the product's own.
 */
export function apiBaseUrl(): string {
	switch (process.env.ENV) {
		case 'PROD':
			return 'https://api.example.com';
		case 'STAGING':
			return 'https://api.staging.example.com';
		case 'DEV':
			return 'https://api.dev.example.com';
		default:
			return 'http://localhost:8888';
	}
}
