import { reactRouter } from '@react-router/dev/vite';
import { defineConfig } from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';

export default defineConfig({
	plugins: [reactRouter(), tsconfigPaths()],
	css: {
		// React does not scope component styles the way some frameworks do, and
		// real apps use generic class names (.content, .card, .title) that collide
		// instantly as global CSS. CSS Modules are the default here;
		// `.my-thing` is then `styles.myThing`.
		modules: { localsConvention: 'camelCaseOnly' }
	},
	server: { host: true }
});
