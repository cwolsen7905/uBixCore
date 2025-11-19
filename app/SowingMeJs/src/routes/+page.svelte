<!--

<script>

	import Sidebar from '$lib/Sidebar.svelte';
	import { userData } from '$lib/stores.js';

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
</script>

<style>
	.layout {
		display: flex;
		min-height: 100vh;
		background: #f6f8fa;
	}
	.main {
		flex: 1;
		padding: 40px;
		background: #f6f8fa;
		min-width: 0;
	}
	@media (max-width: 700px) {
		.main {
			padding: 16px;
		}
	}
</style>

<div class="layout">
		<Sidebar bind:sidebarOpen bind:search {navLinks} {user} />
	<main class="main">
		<h1>Welcome to SowingMe Platform</h1>
		<p>This is your main page content. Add your components and routes here.</p>
	</main>
</div>


<pre>DATA: {JSON.stringify($userData?.user, null, 2)}</pre>

-->

Hi