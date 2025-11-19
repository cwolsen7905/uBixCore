<script>
  import { onClickOutside } from '$lib/action.js';

  import LoginPage from '$lib/LoginPage.svelte'; // <-- Add this import

  import { userData } from '$lib/stores.js';

  let user = { name: 'Jane Doe' };
  let menuOpen = false;
  let navCollapsed = false; // <-- Add this

  export let data;

  if( data?.user ) {
    userData.set(data); 
  }

  const navLinks = [
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

  function toggleMenu() { menuOpen = !menuOpen; }
  function closeMenu() { menuOpen = false; }
  function logout() { alert('Logging out...'); closeMenu(); }
  function toggleNav() { navCollapsed = !navCollapsed; }
</script>

<style>
  .main-area {
    display: flex;
    flex: 1;
    min-height: 0;
  }
  nav {
    background: #f4f4f4;
    width: 200px;
    padding: 1rem 0;
    flex-shrink: 0;
    border-right: 1px solid #ddd;
    transition: width 0.2s;
    overflow: hidden;
  }
  nav.collapsed {
    width: 48px;
    padding: 1rem 0.25rem;
  }
  nav a {
  display: block;
  padding: 0.5rem 1rem;
  color: #333;
  text-decoration: none;
}
nav a:hover {
  background: #e0e0e0;
}
  .collapse-btn {
    display: block;
    margin: 0 auto 1rem auto;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    color: #333;
  }
  .nav-label {
    display: inline;
    transition: opacity 0.2s;
  }
  nav.collapsed .nav-label {
    opacity: 0;
    width: 0;
    overflow: hidden;
  }
  .content {
    flex: 1;
    padding: 2rem;
    overflow: auto;
  }
  .header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #222;
  color: #fff;
  padding: 1rem 2rem;
  flex-shrink: 0;
}
.logo {
  display: flex;
  align-items: center;
  font-weight: bold;
  font-size: 1.2rem;
}
  .user-menu {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
  }
  .user-name {
    margin-right: 0.5rem;
  }
  .menu-dropdown {
    position: absolute;
    right: 0;
    top: 2.5rem;
    background: #fff;
    color: #222;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 120px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 10;
  }
  .menu-dropdown a,
  .menu-dropdown button {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    text-align: left;
    color: inherit;
    cursor: pointer;
    font: inherit;
  }
  .menu-dropdown a:hover,
  .menu-dropdown button:hover {
    background: #f4f4f4;
  }
</style>

<div class="layout">
<header class="header">
  <div class="logo">
    <img src="/logo.png" alt="Logo" style="height:32px; margin-right: 1rem;" />
    Internal Admin POC
  </div>
  <div class="user-menu" on:click={toggleMenu} use:onClickOutside={closeMenu}>
    <span class="user-name">{user.name}</span>
    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.5L14.75 7.5H5.25Z"/></svg>
    {#if menuOpen}
      <div class="menu-dropdown">
        <a href="/profile">Profile</a>
        <button on:click={logout}>Logout</button>
      </div>
    {/if}
  </div>
</header>
  <div class="main-area">
<nav class:collapsed={navCollapsed}>
  <button class="collapse-btn" on:click={toggleNav}>
    {#if navCollapsed}
      &rarr;
    {:else}
      &larr;
    {/if}
  </button>
  {#each navLinks as section}
    <div class="nav-section">
      <div class="nav-section-label">
        <span class="nav-label">{section.section}</span>
      </div>
      {#each section.links as link}
        <a href={link.href}>
          <span class="nav-label">{link.label}</span>
        </a>
      {/each}
    </div>
  {/each}
</nav>
    <div class="content">
      {#if !$userData?.user}
        <LoginPage />
      {:else}
        <slot />
      {/if}
    </div>
  </div>
</div>