import { error, redirect } from '@sveltejs/kit';

/** @type {import('./$types').PageServerLoad} */
export async function load({ params, parent, fetch }) {
	const { apiBaseUrl } = await parent();

	let res;
	try {
		res = await fetch(`${apiBaseUrl}/creators/${encodeURIComponent(params.slug)}`, {
			headers: { 'Content-Type': 'application/json' }
		});
	} catch {
		error(404, 'Creator not found');
	}

	if (!res.ok) {
		error(404, 'Creator not found');
	}

	const profile = await res.json();

	// A retired slug is followed server-side by fetch; canonicalise the URL (301, TDS §3)
	if (profile.slug && profile.slug !== params.slug) {
		redirect(301, `/c/${profile.slug}`);
	}

	return { profile };
}
