<script>

	import Sidebar from '$lib/Sidebar.svelte';
	import { userData } from '$lib/stores.js';

	let sidebarOpen = true;
	let search = '';
	let navLinks = [
		{
			section: 'Main',
			links: [
				{ href: '/', label: 'Home' },
				{ href: '/dashboard', label: 'Dashboard' }
			]
		},
		{
			section: 'Affiliates',
			links: [
				{ href: '/affiliates', label: 'Affiliates' },
				{ href: '/affiliates/banners', label: 'Banners' }
			]
		},
		{
			section: 'Broadcasting',
			links: [
				{ href: '/broadcasting/models', label: 'Models' },
				{ href: '/broadcasting/fanclubs', label: 'Fan Clubs' }
			]
		},
		{
			section: 'Reports',
			links: [
				{ href: '/reports', label: 'Reports' }
			]
		},
		{
			section: 'Settings',
			links: [
				{ href: '/settings', label: 'Settings' }
			]
		}
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
		<pre>DATA: {JSON.stringify($userData?.user, null, 2)}</pre>
	</main>
</div>
