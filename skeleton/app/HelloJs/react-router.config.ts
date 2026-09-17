import type { Config } from '@react-router/dev/config';

export default {
	// SSR on by default. A route's loader runs before first paint, so the page
	// arrives with its data. Turn this off only when a route genuinely cannot be
	// server-rendered -- not to avoid writing a loader.
	ssr: true
} satisfies Config;
