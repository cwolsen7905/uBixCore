<script>
	import { onMount } from 'svelte';
	import { resolve } from '$app/paths';
	import { userData } from '$lib/stores.js';

	let token = '';
	let password = '';
	let confirmPassword = '';
	let message = '';
	let done = false;
	let submitting = false;

	onMount(() => {
		token = new URLSearchParams(window.location.search).get('token') ?? '';
		if (!token) {
			message = 'This reset link is missing its token — please request a new one.';
		}
	});

	/** @param {SubmitEvent} event */
	async function handleSubmit(event) {
		event.preventDefault();
		message = '';
		if (password !== confirmPassword) {
			message = 'Passwords do not match';
			return;
		}
		submitting = true;

		const apiBaseUrl = $userData?.apiBaseUrl || 'https://api.sowingme.com';
		try {
			const res = await fetch(`${apiBaseUrl}/auth/password-reset/confirm`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				credentials: 'include',
				body: JSON.stringify({ token, password, confirmPassword })
			});
			const data = await res.json();
			if (res.ok && data.status === 'success') {
				done = true;
			} else {
				message = data.message ?? 'Something went wrong — please try again.';
			}
		} catch {
			message = 'Something went wrong — please try again.';
		}
		submitting = false;
	}
</script>

<div class="reset-container">
	{#if done}
		<h1>Password updated</h1>
		<p>You can now log in with your new password.</p>
		<a href={resolve('/login')}>Go to login</a>
	{:else}
		<h1>Choose a new password</h1>
		<form on:submit={handleSubmit}>
			<label for="password">New password</label>
			<input
				id="password"
				type="password"
				bind:value={password}
				required
				autocomplete="new-password"
			/>
			<label for="confirm">Confirm password</label>
			<input
				id="confirm"
				type="password"
				bind:value={confirmPassword}
				required
				autocomplete="new-password"
			/>
			<button type="submit" disabled={submitting || !token}>Set new password</button>
		</form>
		{#if message}<p class="error">{message}</p>{/if}
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
	.error {
		color: #c0392b;
	}
</style>
