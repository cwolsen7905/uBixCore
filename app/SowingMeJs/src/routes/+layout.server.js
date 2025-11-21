/** @type {import('@sveltejs/kit').LayoutServerLoad} */

export async function load({ fetch, request}) {

  const cookie = request.headers.get('cookie');

  console.log('Fetching user authentication status from API...');
  console.log('Individual cookie header:', cookie);

  // If environment variable ENV == production, use production API endpoint if dev use dev endpoint otherwise use localost
  const apiEndpoint = import.meta.env.VITE_ENV === 'production'
	? 'https://api.sowingme.com/auth'
	: import.meta.env.VITE_ENV === 'dev'
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


  return {
    user: data || null
  }

}