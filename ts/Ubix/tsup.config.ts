import { defineConfig } from 'tsup';

// Publish-time build only. In-repo consumers resolve `exports` -> src/*.ts and let
// their own bundler compile the source (see README "Consuming it"); this exists so
// the npm tarball ships something an external consumer can actually run.
export default defineConfig({
	entry: ['src/index.ts', 'src/media/index.ts'],
	format: ['esm'],
	dts: true,
	sourcemap: true,
	clean: true,
	treeshake: true,
	external: ['react', 'react-dom']
});
