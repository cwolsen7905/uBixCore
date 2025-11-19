<script>
	let sidebarOpen = true;
	let search = '';
	let navLinks = [
		{ name: 'Home', href: '/' },
		{ name: 'Explore', href: '/explore' },
		{ name: 'Messages', href: '/messages' },
		{ name: 'Settings', href: '/settings' }
	];
	let user = {
		name: 'John Doe',
		avatar: 'https://ui-avatars.com/api/?name=John+Doe&background=4a90e2&color=fff'
	};
	let showAccountMenu = false;
</script>

<style>
	.layout {
		display: flex;
		min-height: 100vh;
		background: #f6f8fa;
	}
	.sidebar {
		width: 260px;
		background: #fff;
		box-shadow: 2px 0 8px rgba(0,0,0,0.04);
		display: flex;
		flex-direction: column;
		transition: width 0.2s;
		position: relative;
	}
	.sidebar.collapsed {
		width: 64px;
	}
	.sidebar-toggle {
		position: absolute;
		top: 16px;
		right: -20px;
		background: #4a90e2;
		color: #fff;
		border-radius: 50%;
		width: 32px;
		height: 32px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		z-index: 2;
	}
	.search {
		padding: 24px 20px 12px 20px;
		border-bottom: 1px solid #eee;
	}
	.search input {
		width: 100%;
		padding: 8px 12px;
		border-radius: 6px;
		border: 1px solid #ccc;
		font-size: 1rem;
	}
	.nav {
		flex: 1;
		padding: 16px 0;
		display: flex;
		flex-direction: column;
		gap: 4px;
	}
	.nav-link {
		padding: 12px 24px;
		color: #333;
		text-decoration: none;
		border-radius: 6px;
		font-size: 1rem;
		transition: background 0.15s;
	}
	.nav-link:hover {
		background: #eaf4fd;
		color: #4a90e2;
	}
	.user-section {
		padding: 20px;
		border-top: 1px solid #eee;
		display: flex;
		align-items: center;
		justify-content: space-between;
		position: sticky;
		bottom: 0;
		background: #fff;
	}
	.user-info {
		display: flex;
		align-items: center;
		gap: 12px;
	}
	.user-avatar {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		object-fit: cover;
		border: 2px solid #4a90e2;
	}
	.user-name {
		font-weight: 500;
		color: #333;
		font-size: 1rem;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 100px;
	}
	.account-menu {
		position: relative;
	}
	.dots {
		cursor: pointer;
		font-size: 1.5rem;
		color: #888;
		padding: 4px;
		border-radius: 50%;
		transition: background 0.15s;
	}
	.dots:hover {
		background: #eaf4fd;
		color: #4a90e2;
	}
	.account-dropdown {
		position: absolute;
		right: 0;
		top: 32px;
		background: #fff;
		border: 1px solid #eee;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		min-width: 140px;
		z-index: 10;
	}
	.account-dropdown a {
		display: block;
		padding: 10px 16px;
		color: #333;
		text-decoration: none;
		font-size: 1rem;
		border-radius: 6px;
		transition: background 0.15s;
	}
	.account-dropdown a:hover {
		background: #eaf4fd;
		color: #4a90e2;
	}
	.main {
		flex: 1;
		padding: 40px;
		background: #f6f8fa;
		min-width: 0;
	}
	@media (max-width: 700px) {
		.sidebar {
			position: fixed;
			left: 0;
			top: 0;
			height: 100vh;
			z-index: 100;
		}
		.main {
			padding: 16px;
		}
	}
</style>

<div class="layout">
	<aside class="sidebar {sidebarOpen ? '' : 'collapsed'}">
		<div class="sidebar-toggle" on:click={() => sidebarOpen = !sidebarOpen} title="Toggle menu">
			{#if sidebarOpen}
				<span>&#x25C0;</span>
			{:else}
				<span>&#x25B6;</span>
			{/if}
		</div>
		<div class="search">
			<input type="text" bind:value={search} placeholder="Search..." />
		</div>
		<nav class="nav">
			{#each navLinks as link}
				<a class="nav-link" href={link.href}>{link.name}</a>
			{/each}
		</nav>
		<div class="user-section">
			<div class="user-info">
				<img class="user-avatar" src={user.avatar} alt="Avatar" />
				{#if sidebarOpen}
					<span class="user-name">{user.name}</span>
				{/if}
			</div>
			<div class="account-menu">
				<span class="dots" on:click={() => showAccountMenu = !showAccountMenu} title="Account options">&#8942;</span>
				{#if showAccountMenu}
					<div class="account-dropdown">
						<a href="/profile">Profile</a>
						<a href="/account">Account Settings</a>
						<a href="/logout">Logout</a>
					</div>
				{/if}
			</div>
		</div>
	</aside>
	<main class="main">
		<h1>Welcome to SowingMe Platform</h1>
		<p>This is your main page content. Add your components and routes here.</p>
	</main>
</div>
<h1>Welcome to SvelteKit</h1>
<p>Visit <a href="https://svelte.dev/docs/kit">svelte.dev/docs/kit</a> to read the documentation</p>
