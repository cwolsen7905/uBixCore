<script>
  import { userData } from '$lib/stores.js';

  export let sidebarOpen = true;
  export let search = '';
  export let navLinks = [
    { href: '/', label: 'Home', icon: '🏠' },
    { href: '/explore', label: 'Explore', icon: '🔍' },
    { href: '/chats', label: 'Chats', icon: '💬' },
    { href: '/notifications', label: 'Notifications', icon: '🔔' },
    { href: '/settings', label: 'Settings', icon: '⚙️' }
  ];
  export let user = {
    name: 'John Doe',
    avatar: 'https://ui-avatars.com/api/?name=John+Doe&background=4a90e2&color=fff'
  };
  let showAccountMenu = false;

  async function handleLogout() {
    const apiBaseUrl = $userData?.apiBaseUrl || 'http://localhost:8888';
    const apiEndpoint = `${apiBaseUrl}/logout`;

    try {
      await fetch(apiEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
      });
    } catch (e) {
      console.error('Logout error:', e);
    }

    // Clear user data to show login modal
    userData.set({});
    showAccountMenu = false;
  }
</script>

<style>
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
  .logo {
    padding: 20px;
    text-align: left;
    border-bottom: 1px solid #eee;
  }
  .logo a {
    display: block;
  }
  .logo img {
    max-width: 100%;
    height: auto;
    max-height: 40px;
  }
  .logo .logo-small {
    max-height: 32px;
  }
  .sidebar.collapsed .logo {
    padding: 12px 8px;
  }
  .search {
    padding: 24px 20px 12px 20px;
    border-bottom: 1px solid #eee;
  }
  .sidebar.collapsed .search {
    padding: 24px 8px 12px 8px;
  }
  .search input {
    width: 100%;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1rem;
  }
  .sidebar.collapsed .search input {
    padding: 8px 4px;
    text-align: center;
  }
  .nav {
    flex: 1;
    padding: 16px 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    overflow-y: auto;
  }
  .nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: background 0.15s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .sidebar.collapsed .nav-link {
    padding: 12px;
    justify-content: center;
  }
  .nav-link:hover {
    background: #eaf4fd;
    color: #4a90e2;
  }
  .nav-icon {
    font-size: 1.2rem;
    flex-shrink: 0;
  }
  .nav-label {
    flex: 1;
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
  .sidebar.collapsed .user-section {
    padding: 12px 8px;
    flex-direction: column;
    gap: 8px;
  }
  .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .sidebar.collapsed .user-info {
    gap: 0;
  }
  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #4a90e2;
  }
  .sidebar.collapsed .user-avatar {
    width: 32px;
    height: 32px;
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
    bottom: 100%;
    margin-bottom: 8px;
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
  .account-dropdown a:hover,
  .account-dropdown button:hover {
    background: #eaf4fd;
    color: #4a90e2;
  }
  .account-dropdown button {
    display: block;
    width: 100%;
    padding: 10px 16px;
    color: #333;
    background: none;
    border: none;
    font-size: 1rem;
    text-align: left;
    cursor: pointer;
    border-radius: 6px;
    transition: background 0.15s;
  }
  @media (max-width: 700px) {
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
      z-index: 100;
    }
  }
</style>

<aside class="sidebar" class:collapsed={!sidebarOpen}>
  <div class="sidebar-toggle" on:click={() => sidebarOpen = !sidebarOpen} title="Toggle menu">
    {#if sidebarOpen}
      <span>&#x25C0;</span>
    {:else}
      <span>&#x25B6;</span>
    {/if}
  </div>
  <div class="logo">
    <a href="/">
      {#if sidebarOpen}
        <img src="https://sowing.me/assets/horizontal_logo_white.png" alt="Sowing.me" />
      {:else}
        <img src="https://sowing.me/assets/horizontal_logo_white_small.png" alt="Sowing.me" class="logo-small" />
      {/if}
    </a>
  </div>
  <div class="search">
    <input type="text" bind:value={search} placeholder={sidebarOpen ? "Search..." : "🔍"} />
  </div>
  <nav class="nav">
    {#each navLinks as link}
      <a class="nav-link" href={link.href}>
        <span class="nav-icon">{link.icon}</span>
        {#if sidebarOpen}
          <span class="nav-label">{link.label}</span>
        {/if}
      </a>
    {/each}
  </nav>
  <div class="user-section">
    <div class="user-info">
      <img class="user-avatar" src={user.avatar} alt="Avatar" />
      {#if sidebarOpen}
        <span class="user-name">{$userData?.user?.firstName || $userData?.firstName || ''} {$userData?.user?.lastName || $userData?.lastName || ''}</span>
      {/if}
    </div>
    <div class="account-menu">
      <span class="dots" on:click={() => showAccountMenu = !showAccountMenu} title="Account options">&#8942;</span>
      {#if showAccountMenu}
        <div class="account-dropdown">
          <a href="/profile">Profile</a>
          <a href="/account">Account Settings</a>
          <button type="button" on:click={handleLogout}>Logout</button>
        </div>
      {/if}
    </div>
  </div>
</aside>
