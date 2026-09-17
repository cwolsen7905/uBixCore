import {
	isRouteErrorResponse,
	Links,
	Meta,
	Outlet,
	Scripts,
	ScrollRestoration
} from 'react-router';

import type { Route } from './+types/root';
import { apiBaseUrl } from '~/lib/api';

export function Layout({ children }: { children: React.ReactNode }) {
	return (
		<html lang="en">
			<head>
				<meta charSet="utf-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<Meta />
				<Links />
			</head>
			<body>
				{children}
				<ScrollRestoration />
				<Scripts />
			</body>
		</html>
	);
}

/**
 * Root loader: whatever every route needs.
 *
 * Typically the session. Note two things this template is showing on purpose:
 *
 *   1. The cookie is forwarded from the inbound request. A loader runs on the
 *      server and has no browser cookie jar, so an authenticated call that does
 *      not forward it is an anonymous call.
 *   2. The upstream call is bounded. An unreachable API must not hold the page
 *      open; return a degraded shape and let the UI say so.
 */
export async function loader({ request }: Route.LoaderArgs) {
	const base = apiBaseUrl();
	const cookie = request.headers.get('cookie') ?? '';

	const controller = new AbortController();
	const timeout = setTimeout(() => controller.abort(), 2000);

	try {
		const res = await fetch(`${base}/health`, {
			headers: { 'Content-Type': 'application/json', cookie },
			signal: controller.signal
		});
		return { apiBaseUrl: base, apiReachable: res.ok };
	} catch {
		return { apiBaseUrl: base, apiReachable: false };
	} finally {
		clearTimeout(timeout);
	}
}

export default function App() {
	return <Outlet />;
}

export function ErrorBoundary({ error }: Route.ErrorBoundaryProps) {
	let message = 'Something went wrong';
	let detail = 'An unexpected error occurred.';

	if (isRouteErrorResponse(error)) {
		message = error.status === 404 ? 'Not found' : String(error.status);
		detail = error.status === 404 ? 'That page does not exist.' : error.statusText;
	}

	return (
		<main style={{ padding: '2rem', fontFamily: 'system-ui, sans-serif' }}>
			<h1>{message}</h1>
			<p>{detail}</p>
		</main>
	);
}
