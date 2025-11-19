/** @type {import('@sveltejs/kit').LayoutServerLoad} */

export async function load({ fetch, cookies}) {
   // Get all cookies as a string
  const cookieHeader = cookies.getAll().map(({ name, value }) => `${name}=${value}`).join('; ');

  // Call your auth endpoint or check cookies/session
  const res = await fetch('http://internal-admin-api.sb.vsmedia.net/auth', {
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer a8f3c9d7e2b6f1a4c5e8d9b0f7a3c6e1`, // Example token, replace as needed
      cookie: cookieHeader
    },
    credentials: 'include'
  });

  // Check if res is json data or set data to null
  let data = null;
  
  try {
    data = await res.json();

  } catch (e) {
    data = null;
  }
  
  // Return sample user data of username: mrolsen, firstName: Mike, lastName: Rolsen if response is 401
  if (res.status === 405) {
    console.log('User not logged in, returning sample user data.');
    return {
      user: {
        username: 'mrolsen',
        firstName: 'Mike',
        lastName: 'Rolsen'
      }
    }
  }

  return {
    user: data || null
  }


}