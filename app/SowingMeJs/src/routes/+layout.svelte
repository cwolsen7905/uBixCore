<script>
  import LoginPage from '$lib/LoginPage.svelte';
  import Sidebar from '$lib/Sidebar.svelte';
  import { userData } from '$lib/stores.js';

  let sidebarOpen = true;
  let search = '';

  export let data;

  if( data?.user ) {
    userData.set(data);
  }
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