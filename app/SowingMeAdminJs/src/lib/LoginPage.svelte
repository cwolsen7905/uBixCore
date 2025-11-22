<script>

  import { userData } from '$lib/stores.js';

  let username = '';
  let password = '';
  let errorMessage = '';
  let fieldErrors = {};


  async function handleLogin(event) {
    event.preventDefault();

    // Clear previous errors
    errorMessage = '';
    fieldErrors = {};

    // Get API base URL from store (passed from server)
    const apiBaseUrl = $userData?.apiBaseUrl || 'https://api.sowingme.com';
    const apiEndpoint = `${apiBaseUrl}/auth`;

    let res;
    let data;

    try {
      res = await fetch(apiEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ 'username': username, 'password': password })
      });

      data = await res.json();
    } catch (e) {
      errorMessage = 'Network error. Please try again.';
      return;
    }

    // Check for validation errors
    if (data.fields && data.fields.length > 0) {
      errorMessage = data.message || 'Validation failed';
      const errors = {};
      for (const field of data.fields) {
        errors[field.name] = field.error;
      }
      fieldErrors = errors;
      return;
    }

    // Check for successful login
    if (data.status === 'success' || data.user?.id) {
      userData.set(data);
      location.reload();
      return;
    }

	console.log(data);

    // Handle other errors
    errorMessage = data.message || 'Login failed';
  }

</script>

<style>
  .modal-card {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    width: 100%;
    max-width: 400px;
  }
  h1 {
    margin: 0 0 1.5rem;
    text-align: center;
    color: #333;
  }
  .form-group {
    margin-bottom: 1rem;
  }
  label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #555;
  }
  input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
  }
  input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
  }
  button {
    width: 100%;
    padding: 0.75rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 0.5rem;
  }
  button:hover {
    background: #0056b3;
  }
  .error-message {
    background: #fee;
    color: #c00;
    padding: 0.75rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    font-size: 0.875rem;
  }
  .field-error {
    color: #c00;
    font-size: 0.75rem;
    margin-top: 0.25rem;
  }
  input.has-error {
    border-color: #c00;
  }
</style>

<div class="modal-card">
  <h1>Login</h1>
  {#if errorMessage}
    <div class="error-message">{errorMessage}</div>
  {/if}
  <form on:submit|preventDefault={handleLogin}>
    <div class="form-group">
      <label for="username">Username</label>
      <input
        type="text"
        id="username"
        bind:value={username}
        name="username"
        class:has-error={fieldErrors.username}
      />
      {#if fieldErrors.username}
        <div class="field-error">{fieldErrors.username}</div>
      {/if}
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input
        type="password"
        id="password"
        bind:value={password}
        name="password"
        class:has-error={fieldErrors.password}
      />
      {#if fieldErrors.password}
        <div class="field-error">{fieldErrors.password}</div>
      {/if}
    </div>
    <button type="submit">Login</button>
  </form>
</div>