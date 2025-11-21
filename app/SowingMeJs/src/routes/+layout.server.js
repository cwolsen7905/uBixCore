/** @type {import('@sveltejs/kit').LayoutServerLoad} */

import { env } from '$env/dynamic/private';

export async function load({ fetch, request}) {

  const cookie = request.headers.get('cookie');

  console.log('Fetching user authentication status from API...');
  console.log('Individual cookie header:', cookie);

  // If environment variable ENV == production, use production API endpoint if dev use dev endpoint otherwise use localost
  const apiEndpoint = env.ENV === 'PROD'
	? 'https://api.sowingme.com/auth'
	: env.ENV === 'DEV'
	  ? 'https://dev-api.sowingme.com/auth'
	  : 'http://localhost:8888/auth';
  

  console.log(`Using API endpoint: ${apiEndpoint}`);

  // Call your auth endpoint or check cookies/session
  const res = await fetch(apiEndpoint, {
    headers: {
    	'Content-Type': 'application/json',
		cookie
    }
  });

  // Check if res is json data or set data to null
  let data = null;
  
  try {
    data = await res.json();

  } catch (e) {
    data = null;
  }
  
  console.log(`Auth endpoint response status: ${res.status}`);
  console.log('Auth endpoint response data:', data);


  // Determine API base URL for client-side use
  const apiBaseUrl = env.ENV === 'PROD'
    ? 'https://api.sowingme.com'
    : env.ENV === 'DEV'
      ? 'https://dev-api.sowingme.com'
      : 'http://localhost:8888';

  return {
    user: data || null,
    apiBaseUrl
  }

}