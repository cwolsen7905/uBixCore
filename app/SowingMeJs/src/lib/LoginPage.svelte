<script>

  import { userData } from '$lib/stores.js';

  let username = '';
  let password = '';

  async function handleLogin(event) {
    event.preventDefault();
    
    // Your bearer token (replace with your actual logic)
    const bearerToken = 'a8f3c9d7e2b6f1a4c5e8d9b0f7a3c6e1';

    const res = await fetch('https://sowing-me-api.dev.ubixsys.com/auth', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${bearerToken}`
      },
      credentials: 'include',
      body: JSON.stringify({ 'username': username, 'password': password })
    });

    const data = await res.json();
    userData.set(data); // <-- Set global data

    console.log('userData after login:', userData);
    // Handle response (e.g., redirect, show error, etc.)
    if ($userData.success == '1') {
      // Authorized, reload or redirect
      location.reload();
    } else {  
      alert('Login failed: ' + JSON.stringify($userData, null, 2));
    }
  }

</script>

<h1>Login</h1>
<form on:submit|preventDefault={handleLogin}>
  <label>
    Username:
    <input type="text" bind:value={username} name="username" />
  </label>
  <br />
  <label>
    Password:
    <input type="password" bind:value={password} name="password" />
  </label>
  <br />
  <button type="submit">Login</button>
</form>