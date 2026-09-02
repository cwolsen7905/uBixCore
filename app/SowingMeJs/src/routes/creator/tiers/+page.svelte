<script>
	import { onMount } from 'svelte';
	import { userData } from '$lib/stores.js';

	/** @type {Array<{id: number, name: string, description: string|null, priceAmount: number, priceCurrency: string, billingInterval: string, position: number, status: string, benefits: string[]}>} */
	let tiers = [];
	let message = '';
	let name = '';
	let description = '';
	let price = '';
	let billingInterval = 'month';
	let benefitsText = '';
	let submitting = false;

	function apiBase() {
		return $userData?.apiBaseUrl || 'https://api.sowingme.com';
	}

	async function loadTiers() {
		try {
			const res = await fetch(`${apiBase()}/creator/tiers`, { credentials: 'include' });
			const data = await res.json();
			if (res.ok && data.status === 'success') {
				tiers = data.tiers;
			} else {
				message = data.message ?? 'Could not load tiers';
			}
		} catch {
			message = 'Could not load tiers';
		}
	}

	/** @param {SubmitEvent} event */
	async function createTier(event) {
		event.preventDefault();
		message = '';
		submitting = true;
		try {
			const res = await fetch(`${apiBase()}/creator/tiers`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				credentials: 'include',
				body: JSON.stringify({
					name,
					description: description || null,
					priceAmount: Math.round(Number(price) * 100),
					priceCurrency: 'USD',
					billingInterval,
					benefits: benefitsText
						.split('\n')
						.map((line) => line.trim())
						.filter(Boolean)
				})
			});
			const data = await res.json();
			if (res.ok && data.status === 'success') {
				name = '';
				description = '';
				price = '';
				benefitsText = '';
				await loadTiers();
			} else {
				message = data.message ?? 'Could not create the tier';
			}
		} catch {
			message = 'Could not create the tier';
		}
		submitting = false;
	}

	/** @param {number} tierId @param {string} status */
	async function setStatus(tierId, status) {
		await fetch(`${apiBase()}/creator/tiers/${tierId}/status`, {
			method: 'PATCH',
			headers: { 'Content-Type': 'application/json' },
			credentials: 'include',
			body: JSON.stringify({ status })
		});
		await loadTiers();
	}

	onMount(loadTiers);
</script>

<div class="tiers-page">
	<h1>Subscription tiers</h1>
	<p class="hint">Every creator has a free tier automatically — paid tiers start at position 1.</p>
	{#if message}<p class="error">{message}</p>{/if}

	<ul class="tier-list">
		{#each tiers as tier (tier.id)}
			<li class="tier-card" class:archived={tier.status === 'archived'}>
				<header>
					<strong>#{tier.position} {tier.name}</strong>
					<span>${(tier.priceAmount / 100).toFixed(2)} / {tier.billingInterval}</span>
				</header>
				{#if tier.description}<p>{tier.description}</p>{/if}
				<ul>
					{#each tier.benefits as benefit (benefit)}
						<li>{benefit}</li>
					{/each}
				</ul>
				{#if tier.status === 'active'}
					<button on:click={() => setStatus(tier.id, 'archived')}>Archive</button>
				{:else}
					<button on:click={() => setStatus(tier.id, 'active')}>Reactivate</button>
				{/if}
			</li>
		{/each}
	</ul>

	<h2>New tier</h2>
	<form on:submit={createTier}>
		<label for="name">Name</label>
		<input id="name" bind:value={name} required maxlength="80" />
		<label for="description">Description</label>
		<textarea id="description" bind:value={description}></textarea>
		<label for="price">Price (USD / period)</label>
		<input id="price" type="number" step="0.01" min="0" bind:value={price} required />
		<label for="interval">Billing interval</label>
		<select id="interval" bind:value={billingInterval}>
			<option value="month">Monthly</option>
			<option value="year">Yearly</option>
		</select>
		<label for="benefits">Benefits (one per line)</label>
		<textarea id="benefits" bind:value={benefitsText}></textarea>
		<button type="submit" disabled={submitting}>Create tier</button>
	</form>
</div>

<style>
	.tiers-page {
		max-width: 640px;
		margin: 2rem auto;
		padding: 1rem;
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}
	.tier-list {
		list-style: none;
		padding: 0;
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}
	.tier-card {
		border: 1px solid var(--color-border-light, #ddd);
		border-radius: 8px;
		padding: 1rem;
	}
	.tier-card.archived {
		opacity: 0.6;
	}
	.tier-card header {
		display: flex;
		justify-content: space-between;
	}
	form {
		display: flex;
		flex-direction: column;
		gap: 0.5rem;
	}
	input,
	textarea,
	select {
		padding: 0.5rem;
	}
	.error {
		color: #c0392b;
	}
</style>
