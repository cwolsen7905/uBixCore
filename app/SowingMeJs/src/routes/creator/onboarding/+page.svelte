<script>
	import { onMount } from 'svelte';
	import { userData } from '$lib/stores.js';

	const steps = [
		{ id: 'profile', label: 'Profile' },
		{ id: 'tier', label: 'First tier' },
		{ id: 'payout', label: 'Payouts' }
	];

	let currentStep = $state('profile');
	let profileDone = $state(false);
	let loading = $state(true);

	let displayName = $state('');
	let slug = $state('');
	let slugEdited = $state(false);
	let bio = $state('');
	let category = $state('other');
	let faithTopic = $state('');
	let errorMessage = $state('');
	/** @type {Record<string, string>} */
	let fieldErrors = $state({});
	let submitting = $state(false);

	const categories = ['pastor', 'worship', 'teacher', 'podcaster', 'author', 'artist', 'other'];

	function apiBaseUrl() {
		let base = null;
		userData.subscribe((v) => (base = v?.apiBaseUrl ?? null))();
		return base || 'https://api.sowing.me';
	}

	/** @param {string} value */
	function slugify(value) {
		return value
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '')
			.slice(0, 64);
	}

	$effect(() => {
		if (!slugEdited) {
			slug = slugify(displayName);
		}
	});

	onMount(async () => {
		try {
			const res = await fetch(`${apiBaseUrl()}/creator/onboarding`, {
				credentials: 'include',
				headers: { 'Content-Type': 'application/json' }
			});
			if (res.ok) {
				const stateData = await res.json();
				currentStep = stateData.currentStep ?? 'profile';
				profileDone = stateData.steps?.profile ?? false;
			}
		} catch {
			// Fall through to the profile step; the submit will surface real errors
		}
		loading = false;
	});

	/** @param {SubmitEvent} event */
	async function submitProfile(event) {
		event.preventDefault();
		errorMessage = '';
		fieldErrors = {};
		submitting = true;

		let res;
		let data;
		try {
			res = await fetch(`${apiBaseUrl()}/creator/profile`, {
				method: 'POST',
				credentials: 'include',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ displayName, slug, bio, category, faithTopic })
			});
			data = await res.json();
		} catch {
			errorMessage = 'Network error. Please try again.';
			submitting = false;
			return;
		}

		if (res.status === 201) {
			profileDone = true;
			currentStep = 'tier';
		} else {
			errorMessage = data?.message ?? 'Something went wrong.';
			for (const field of data?.fields ?? []) {
				fieldErrors[field.name] = field.error;
			}
		}
		submitting = false;
	}
</script>

<svelte:head>
	<title>Creator onboarding | Sowing.me</title>
</svelte:head>

<section class="onboarding">
	<h1>Set up your creator page</h1>

	<ol class="steps">
		{#each steps as step (step.id)}
			<li class:active={currentStep === step.id} class:done={step.id === 'profile' && profileDone}>
				{step.label}
			</li>
		{/each}
	</ol>

	{#if loading}
		<p>Loading…</p>
	{:else if currentStep === 'profile' && !profileDone}
		<form onsubmit={submitProfile}>
			{#if errorMessage}
				<p class="error" role="alert">{errorMessage}</p>
			{/if}
			<label>
				Display name
				<input name="displayName" required bind:value={displayName} />
				{#if fieldErrors.displayName}<span class="error">{fieldErrors.displayName}</span>{/if}
			</label>
			<label>
				Page address
				<span class="slug-preview">sowing.me/c/</span>
				<input
					name="slug"
					required
					bind:value={slug}
					oninput={() => (slugEdited = true)}
					pattern="[a-z0-9]([a-z0-9-]*[a-z0-9])?"
				/>
				{#if fieldErrors.slug}<span class="error">{fieldErrors.slug}</span>{/if}
			</label>
			<label>
				Category
				<select name="category" bind:value={category}>
					{#each categories as value (value)}
						<option {value}>{value}</option>
					{/each}
				</select>
			</label>
			<label>
				Faith topic or denomination (optional)
				<input name="faithTopic" bind:value={faithTopic} />
			</label>
			<label>
				Bio (optional)
				<textarea name="bio" rows="4" bind:value={bio}></textarea>
			</label>
			<button type="submit" disabled={submitting}>
				{submitting ? 'Creating…' : 'Create my page'}
			</button>
		</form>
	{:else}
		<div class="next-steps">
			<p>✅ Your page draft is ready{slug ? ` at /c/${slug}` : ''}.</p>
			<p>Membership tiers and payouts are coming soon — we'll walk you through them here.</p>
		</div>
	{/if}
</section>

<style>
	.onboarding {
		margin: 0 auto;
		max-width: 34rem;
		padding: 1rem;
	}
	.steps {
		display: flex;
		gap: 1rem;
		list-style: none;
		padding: 0;
	}
	.steps li {
		opacity: 0.5;
	}
	.steps li.active,
	.steps li.done {
		font-weight: 600;
		opacity: 1;
	}
	form {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}
	label {
		display: flex;
		flex-direction: column;
		gap: 0.25rem;
	}
	.slug-preview {
		font-size: 0.85rem;
		opacity: 0.7;
	}
	.error {
		color: #c0392b;
	}
</style>
