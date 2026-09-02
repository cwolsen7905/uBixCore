<script>
	/**
	 * @typedef {{ label: string, url: string }} ExternalLink
	 * @typedef {{ slug: string, displayName: string, bio?: string | null, category?: string | null, faithTopic?: string | null, avatarUrl?: string | null, bannerUrl?: string | null, publishedAt?: string | null, externalLinks?: ExternalLink[], tiers?: { id: number, name: string }[] }} CreatorProfile
	 */

	/** @type {{ data: { profile: CreatorProfile } }} */
	let { data } = $props();

	const profile = $derived(data.profile);
</script>

<svelte:head>
	<title>{profile.displayName} | Sowing.me</title>
	<meta name="description" content={profile.bio ?? `Support ${profile.displayName} on Sowing.me`} />
</svelte:head>

<article class="creator-profile">
	{#if profile.bannerUrl}
		<img class="banner" src={profile.bannerUrl} alt="" />
	{/if}

	<header>
		{#if profile.avatarUrl}
			<img class="avatar" src={profile.avatarUrl} alt="{profile.displayName} avatar" />
		{/if}
		<h1>{profile.displayName}</h1>
		{#if profile.category}
			<p class="category">
				{profile.category}{#if profile.faithTopic}&nbsp;·&nbsp;{profile.faithTopic}{/if}
			</p>
		{/if}
	</header>

	{#if profile.bio}
		<section class="bio">
			<p>{profile.bio}</p>
		</section>
	{/if}

	{#if profile.externalLinks?.length}
		<nav class="external-links" aria-label="External links">
			<ul>
				{#each profile.externalLinks as link (link.url)}
					<li>
						<!-- eslint-disable-next-line svelte/no-navigation-without-resolve -- external creator-provided URL -->
						<a href={link.url} rel="noopener noreferrer" target="_blank">{link.label}</a>
					</li>
				{/each}
			</ul>
		</nav>
	{/if}

	{#if profile.tiers?.length}
		<section class="tiers">
			<h2>Membership tiers</h2>
			<ul>
				{#each profile.tiers as tier (tier.id)}
					<li>{tier.name}</li>
				{/each}
			</ul>
		</section>
	{/if}
</article>

<style>
	.creator-profile {
		margin: 0 auto;
		max-width: 44rem;
		padding: 1rem;
	}
	.banner {
		border-radius: 0.5rem;
		max-height: 16rem;
		object-fit: cover;
		width: 100%;
	}
	.avatar {
		border-radius: 50%;
		height: 6rem;
		margin-top: -3rem;
		object-fit: cover;
		width: 6rem;
	}
	.category {
		opacity: 0.7;
		text-transform: capitalize;
	}
	.external-links ul {
		display: flex;
		flex-wrap: wrap;
		gap: 0.75rem;
		list-style: none;
		padding: 0;
	}
</style>
