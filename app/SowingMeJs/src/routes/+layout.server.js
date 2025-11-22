/** @type {import('@sveltejs/kit').LayoutServerLoad} */

import { env } from '$env/dynamic/private';

export async function load({ fetch, request}) {

  const cookie = request.headers.get('cookie');

  console.log('Fetching user authentication status from API...');
  console.log('Individual cookie header:', cookie);

  // If environment variable ENV == 'PROD' api host is https://api.sowingme.com if ENV == 'DEV' host is https://sowing-me-api.dev.ubixsys.com if ENV == 'STAGING' host is https://sowing-me-api.staging.ubixsys.com else localhost:8888
  const apiBaseUrl = env.ENV === 'PROD'
	? 'https://api.sowing.me'
	: env.ENV === 'DEV'
	  ? 'https://sowing-me-api.dev.ubixsys.com'
	  : env.ENV === 'STAGING'
	    ? 'https://sowing-me-api.staging.ubixsys.com'
	    : 'http://localhost:8888';

  const apiEndpoint = `${apiBaseUrl}/auth`;


  console.log(`Using API endpoint: ${apiEndpoint}`);

  // Call your auth endpoint or check cookies/session with 2 second timeout
  let res;
  let data = null;

  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 2000);

    res = await fetch(apiEndpoint, {
      headers: {
        'Content-Type': 'application/json',
        cookie
      },
      signal: controller.signal
    });

    clearTimeout(timeoutId);

    data = await res.json();

    console.log(`Auth endpoint response status: ${res.status}`);
    console.log('Auth endpoint response data:', data);
  } catch (e) {
    console.error('Failed to fetch auth status:', e.message);
    return {
      user: null,
      apiBaseUrl,
      systemError: true
    }
  }

  return {
    user: data || null,
    apiBaseUrl
  }

}