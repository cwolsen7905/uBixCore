<script>

  import { userData } from '$lib/stores.js';

  let isRegistering = false;
  let username = '';
  let password = '';
  let email = '';
  let firstName = '';
  let lastName = '';
  let confirmPassword = '';
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

  async function handleRegister(event) {
    event.preventDefault();

    // Clear previous errors
    errorMessage = '';
    fieldErrors = {};

    // Get API base URL from store (passed from server)
    const apiBaseUrl = $userData?.apiBaseUrl || 'https://api.sowingme.com';
    const apiEndpoint = `${apiBaseUrl}/register`;

    let res;
    let data;

    try {
      res = await fetch(apiEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({
          username: username,
          firstName: firstName,
          lastName: lastName,
          email: email,
          password: password,
          confirmPassword: confirmPassword
        })
      });

      data = await res.json();
    } catch (e) {
      errorMessage = 'Network error. Please try again.';
      return;
    }

    // Check for validation errors
    if (data.fields) {
      errorMessage = data.message || 'Validation failed';
      fieldErrors = data.fields;
      return;
    }

    // Check for successful registration
    if (data.status === 'success') {
      errorMessage = '';
      alert(data.message || 'Registration successful! Please check your email to confirm your account.');
      // Switch back to login mode
      isRegistering = false;
      // Clear form
      username = '';
      email = '';
      firstName = '';
      lastName = '';
      password = '';
      confirmPassword = '';
      return;
    }

    // Handle other errors
    errorMessage = data.message || 'Registration failed';
  }

  function toggleMode() {
    isRegistering = !isRegistering;
    errorMessage = '';
    fieldErrors = {};
    // Clear form when switching
    username = '';
    password = '';
    email = '';
    firstName = '';
    lastName = '';
    confirmPassword = '';
  }

</script>

<style>
  .modal-card {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    width: 100%;
    max-width: 450px;
    max-height: 90vh;
    overflow-y: auto;
  }
  h1 {
    margin: 0 0 1.5rem;
    text-align: center;
    color: #333;
  }
  .form-group {
    margin-bottom: 1rem;
  }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
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
    font-weight: 600;
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
  .toggle-mode {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
  }
  .toggle-link {
    color: #007bff;
    cursor: pointer;
    text-decoration: none;
    font-weight: 500;
  }
  .toggle-link:hover {
    text-decoration: underline;
  }
</style>

<div class="modal-card">
  <h1>{isRegistering ? 'Create Account' : 'Login'}</h1>
  {#if errorMessage}
    <div class="error-message">{errorMessage}</div>
  {/if}

  {#if isRegistering}
    <form on:submit|preventDefault={handleRegister}>
      <div class="form-row">
        <div class="form-group">
          <label for="firstName">First Name</label>
          <input
            type="text"
            id="firstName"
            bind:value={firstName}
            name="firstName"
            required
            class:has-error={fieldErrors.firstName}
          />
          {#if fieldErrors.firstName}
            <div class="field-error">{fieldErrors.firstName[0] || fieldErrors.firstName}</div>
          {/if}
        </div>
        <div class="form-group">
          <label for="lastName">Last Name</label>
          <input
            type="text"
            id="lastName"
            bind:value={lastName}
            name="lastName"
            required
            class:has-error={fieldErrors.lastName}
          />
          {#if fieldErrors.lastName}
            <div class="field-error">{fieldErrors.lastName[0] || fieldErrors.lastName}</div>
          {/if}
        </div>
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          bind:value={username}
          name="username"
          required
          class:has-error={fieldErrors.username}
        />
        {#if fieldErrors.username}
          <div class="field-error">{fieldErrors.username[0] || fieldErrors.username}</div>
        {/if}
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          bind:value={email}
          name="email"
          required
          class:has-error={fieldErrors.email}
        />
        {#if fieldErrors.email}
          <div class="field-error">{fieldErrors.email[0] || fieldErrors.email}</div>
        {/if}
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          bind:value={password}
          name="password"
          required
          class:has-error={fieldErrors.password}
        />
        {#if fieldErrors.password}
          <div class="field-error">{fieldErrors.password[0] || fieldErrors.password}</div>
        {/if}
      </div>
      <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <input
          type="password"
          id="confirmPassword"
          bind:value={confirmPassword}
          name="confirmPassword"
          required
          class:has-error={fieldErrors.confirmPassword || fieldErrors.confirm_password}
        />
        {#if fieldErrors.confirmPassword || fieldErrors.confirm_password}
          <div class="field-error">{fieldErrors.confirmPassword?.[0] || fieldErrors.confirm_password?.[0] || fieldErrors.confirmPassword || fieldErrors.confirm_password}</div>
        {/if}
      </div>
      <button type="submit">Create Account</button>
    </form>
  {:else}
    <form on:submit|preventDefault={handleLogin}>
      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          bind:value={username}
          name="username"
          required
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
          required
          class:has-error={fieldErrors.password}
        />
        {#if fieldErrors.password}
          <div class="field-error">{fieldErrors.password}</div>
        {/if}
      </div>
      <button type="submit">Login</button>
    </form>
  {/if}

  <div class="toggle-mode">
    {#if isRegistering}
      <span>Already have an account? <a class="toggle-link" on:click={toggleMode}>Login</a></span>
    {:else}
      <span>Don't have an account? <a class="toggle-link" on:click={toggleMode}>Sign Up</a></span>
    {/if}
  </div>
</div>
