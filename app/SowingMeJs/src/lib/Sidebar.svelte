<script>
  import { userData, activeRole } from '$lib/stores.js';
  import { themePreference, setThemePreference } from '$lib/themeStore.js';

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
  let hasMultipleRoles = false;
  let showRoleExpanded = false;

  // Check if user has multiple roles
  $: userRoles = $userData?.user?.roles || $userData?.roles || 'user';
  $: {
    // Handle both string and array formats for roles
    const rolesString = Array.isArray(userRoles) ? userRoles.join(',') : String(userRoles);
    const rolesLower = rolesString.toLowerCase();
    hasMultipleRoles = rolesLower.includes('creator') && (rolesLower.includes('user') || rolesLower.includes('member'));
  }

  function switchToCreatorMode() {
    activeRole.set('creator');
    showAccountMenu = false;
    window.location.href = '/creator/dashboard';
  }

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

  // Example memberships data (will be dynamic in the future)
  const memberships = [
    {
      id: 1,
      creatorName: 'Jane Smith',
      avatar: 'https://ui-avatars.com/api/?name=Jane+Smith&background=e91e63&color=fff'
    },
    {
      id: 2,
      creatorName: 'Alex Johnson',
      avatar: 'https://ui-avatars.com/api/?name=Alex+Johnson&background=9c27b0&color=fff'
    },
    {
      id: 3,
      creatorName: 'Sarah Williams',
      avatar: 'https://ui-avatars.com/api/?name=Sarah+Williams&background=ff9800&color=fff'
    }
  ];
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
    position: sticky;
    bottom: 0;
    background: #fff;
  }
  .sidebar.collapsed .user-section {
    padding: 12px 8px;
  }
  .user-section.expanded {
    padding: 12px 20px;
  }
  .sidebar.collapsed .user-section.expanded {
    padding: 12px 8px;
  }
  .user-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    cursor: pointer;
    transition: background 0.15s;
    border-radius: 6px;
  }
  .user-row:hover {
    background: #f5f5f5;
  }
  .sidebar.collapsed .user-row {
    flex-direction: column;
    gap: 4px;
    padding: 8px 4px;
  }
  .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
  }
  .sidebar.collapsed .user-info {
    flex-direction: column;
    gap: 4px;
    text-align: center;
  }
  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #4a90e2;
    flex-shrink: 0;
  }
  .sidebar.collapsed .user-avatar {
    width: 32px;
    height: 32px;
  }
  .user-details {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }
  .sidebar.collapsed .user-details {
    align-items: center;
  }
  .user-name {
    font-weight: 500;
    color: #333;
    font-size: 1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .user-role {
    font-size: 0.75rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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
  .role-divider {
    height: 1px;
    background: #eee;
    margin: 4px 0;
  }
  .menu-section-title {
    padding: 10px 16px 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .theme-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    color: #333;
    background: none;
    border: none;
    font-size: 1rem;
    text-align: left;
    cursor: pointer;
    border-radius: 6px;
    transition: background 0.15s;
    width: 100%;
  }
  .theme-option:hover {
    background: #eaf4fd;
    color: #4a90e2;
  }
  .theme-option.active {
    background: #eaf4fd;
    color: #4a90e2;
  }
  .theme-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid currentColor;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .theme-indicator.active::after {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
  }
  .memberships-section {
    padding: 16px 20px;
    border-top: 1px solid #eee;
  }
  .sidebar.collapsed .memberships-section {
    padding: 16px 8px;
  }
  .memberships-heading {
    font-size: 0.85rem;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    padding-left: 4px;
  }
  .sidebar.collapsed .memberships-heading {
    display: none;
  }
  .membership-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .membership-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    transition: background 0.15s;
    cursor: pointer;
  }
  .sidebar.collapsed .membership-item {
    justify-content: center;
    padding: 8px 4px;
  }
  .membership-item:hover {
    background: #eaf4fd;
  }
  .membership-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
  }
  .membership-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
  <div class="memberships-section">
    {#if sidebarOpen}
      <div class="memberships-heading">Memberships</div>
    {/if}
    <div class="membership-list">
      {#each memberships as membership (membership.id)}
        <a href="/creator/{membership.id}" class="membership-item">
          <img src={membership.avatar} alt={membership.creatorName} class="membership-avatar" />
          {#if sidebarOpen}
            <span class="membership-name">{membership.creatorName}</span>
          {/if}
        </a>
      {/each}
    </div>
  </div>
  <div class="user-section" class:expanded={showRoleExpanded && hasMultipleRoles}>
    {#if hasMultipleRoles && showRoleExpanded}
      <!-- Member Role Row -->
      <div class="user-row" on:click={() => { showRoleExpanded = false; }}>
        <div class="user-info">
          <img class="user-avatar" src={user.avatar} alt="Avatar" />
          {#if sidebarOpen}
            <div class="user-details">
              <span class="user-name">{$userData?.user?.firstName || $userData?.firstName || ''} {$userData?.user?.lastName || $userData?.lastName || ''}</span>
              <span class="user-role">Member</span>
            </div>
          {/if}
        </div>
      </div>

      <div class="role-divider"></div>

      <!-- Creator Role Row -->
      <div class="user-row" on:click={switchToCreatorMode}>
        <div class="user-info">
          <img class="user-avatar" src={user.avatar} alt="Avatar" />
          {#if sidebarOpen}
            <div class="user-details">
              <span class="user-name">{$userData?.user?.creatorName || $userData?.creatorName || 'Creator'}</span>
              <span class="user-role">Creator</span>
            </div>
          {/if}
        </div>
      </div>
    {:else}
      <!-- Single Row (collapsed or single role) -->
      <div class="user-row" on:click={() => { if (hasMultipleRoles) showRoleExpanded = true; }}>
        <div class="user-info">
          <img class="user-avatar" src={user.avatar} alt="Avatar" />
          {#if sidebarOpen}
            <div class="user-details">
              <span class="user-name">{$userData?.user?.firstName || $userData?.firstName || ''} {$userData?.user?.lastName || $userData?.lastName || ''}</span>
              <span class="user-role">Member</span>
            </div>
          {/if}
        </div>
        <div class="account-menu">
          <span class="dots" on:click|stopPropagation={() => showAccountMenu = !showAccountMenu} title="Account options">&#8942;</span>
          {#if showAccountMenu}
            <div class="account-dropdown">
              <div class="menu-section-title">Appearance</div>
              <button
                class="theme-option"
                class:active={$themePreference === 'light'}
                on:click|stopPropagation={() => { setThemePreference('light'); showAccountMenu = false; }}
              >
                <span class="theme-indicator" class:active={$themePreference === 'light'}></span>
                Light
              </button>
              <button
                class="theme-option"
                class:active={$themePreference === 'dark'}
                on:click|stopPropagation={() => { setThemePreference('dark'); showAccountMenu = false; }}
              >
                <span class="theme-indicator" class:active={$themePreference === 'dark'}></span>
                Dark
              </button>
              <button
                class="theme-option"
                class:active={$themePreference === 'system'}
                on:click|stopPropagation={() => { setThemePreference('system'); showAccountMenu = false; }}
              >
                <span class="theme-indicator" class:active={$themePreference === 'system'}></span>
                System
              </button>
              <div class="role-divider"></div>
              <a href="/profile" on:click={() => showAccountMenu = false}>Profile</a>
              <a href="/account" on:click={() => showAccountMenu = false}>Account Settings</a>
              <button type="button" on:click={handleLogout}>Logout</button>
            </div>
          {/if}
        </div>
      </div>
    {/if}
  </div>
</aside>
