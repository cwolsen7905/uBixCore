import { defineConfig } from 'vitest/config';
import tsconfigPaths from 'vite-tsconfig-paths';

// jsdom, NOT vitest browser mode. Browser mode needs Playwright browsers that a
// plain node CI image does not carry, so those tests silently never run.
//
// The React Router plugin is deliberately absent: it owns the route/SSR build,
// and loading it here makes unit tests depend on a full app build.
export default defineConfig({
	plugins: [tsconfigPaths()],
	css: { modules: { localsConvention: 'camelCaseOnly' } },
	test: {
		environment: 'jsdom',
		globals: true,
		setupFiles: ['./vitest.setup.ts'],
		include: ['app/**/*.{test,spec}.{ts,tsx}'],
		expect: { requireAssertions: true }
	}
});
