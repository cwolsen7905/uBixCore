<script>
	import { userData } from '$lib/stores.js';

	let email = '';
	let submitted = false;
	let submitting = false;

	/** @param {SubmitEvent} event */
	async function handleSubmit(event) {
		event.preventDefault();
		submitting = true;

		const apiBaseUrl = $userData?.apiBaseUrl || 'https://api.sowingme.com';
		try {
			await fetch(`${apiBaseUrl}/auth/password-reset/request`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				credentials: 'include',
				body: JSON.stringify({ email })
			});
		} catch {
			// The confirmation is neutral either way (no user enumeration)
		}
		submitting = false;
		submitted = true;
	}
</script>

<div class="reset-container">
	{#if submitted}
		<h1>Check your email</h1>
		<p>If that email is registered, a reset link is on its way. The link is valid for one hour.</p>
	{:else}
		<h1>Forgot your password?</h1>
		<p>Enter your account email and we'll send you a reset link.</p>
		<form on:submit={handleSubmit}>
			<label for="email">Email</label>
			<input id="email" type="email" bind:value={email} required autocomplete="email" />
			<button type="submit" disabled={submitting}>Send reset link</button>
		</form>
	{/if}
</div>

<style>
	.reset-container {
		max-width: 420px;
		margin: 4rem auto;
		padding: 2rem;
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}
	input {
		width: 100%;
		padding: 0.6rem;
	}
	button {
		padding: 0.6rem 1rem;
	}
</style>
