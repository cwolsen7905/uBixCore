import { useRouteLoaderData } from 'react-router';

import type { loader as rootLoader } from '~/root';

export function meta() {
	return [{ title: 'Hello' }];
}

export default function Home() {
	// Parent loader data is read by route id, not drilled through props.
	const root = useRouteLoaderData<typeof rootLoader>('root');

	return (
		<main>
			<h1>Hello from React Router</h1>
			<p>
				API: <code>{root?.apiBaseUrl}</code> — {root?.apiReachable ? 'reachable' : 'unreachable'}
			</p>
		</main>
	);
}
