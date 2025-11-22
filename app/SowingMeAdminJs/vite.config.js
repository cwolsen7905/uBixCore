import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vitest/config';
import { sveltekit } from '@sveltejs/kit/vite';

export default defineConfig({
	plugins: [tailwindcss(), sveltekit()],
	test: {
		expect: { requireAssertions: true },
		projects: [
			{
				extends: './vite.config.js',
				test: {
					name: 'client',
					environment: 'browser',
					browser: {
						enabled: true,
						provider: 'playwright',
						instances: [{ browser: 'chromium' }]
					},
					include: ['src/**/*.svelte.{test,spec}.{js,ts}'],
					exclude: ['src/lib/server/**'],
					setupFiles: ['./vitest-setup-client.js']
				}
			},
			{
				extends: './vite.config.js',
				test: {
					name: 'server',
					environment: 'node',
					include: ['src/**/*.{test,spec}.{js,ts}'],
					exclude: ['src/**/*.svelte.{test,spec}.{js,ts}']
				}
			}
		]
	},
	server: {
        host: true,
        allowedHosts: [
            '.vsmedia.net' // allows all subdomains of vsmedia.net
        ],
	    cors: {
          origin: '*', // or '*' for all origins
          credentials: true
       }
    },
	preview: {
		allowedHosts: [
			'app-sowing-me.dev.ubixsys.com',
			'app-sowing-me.staging.ubixsys.com',
			'app-sowing-me.sandbox.ubixsys.com',
			'app.sowing.me'
		]
	}
});
