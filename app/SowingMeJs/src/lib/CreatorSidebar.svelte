<script>
  import { userData, activeRole } from '$lib/stores.js';

  export let sidebarOpen = true;
  export let navLinks = [
    { href: '/creator/dashboard', label: 'Dashboard', icon: '📊' },
    { href: '/creator/library', label: 'Library', icon: '📚' },
    { href: '/creator/audience', label: 'Audience', icon: '👥' },
    { href: '/creator/insights', label: 'Insights', icon: '📈' },
    { href: '/creator/payouts', label: 'Payouts', icon: '💰' },
    { href: '/creator/chats', label: 'Chats', icon: '💬' },
    { href: '/creator/notifications', label: 'Notifications', icon: '🔔' },
    { href: '/creator/settings', label: 'Settings', icon: '⚙️' }
  ];
  export let user = {
    name: 'John Doe',
    avatar: 'https://ui-avatars.com/api/?name=John+Doe&background=4a90e2&color=fff'
  };
  let showAccountMenu = false;
  let showCreateMenu = false;
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

  function switchToMemberMode() {
    activeRole.set('member');
    showAccountMenu = false;
    window.location.href = '/';
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

  function handleCreateOption(option) {
    console.log('Create option selected:', option);
    showCreateMenu = false;
    // TODO: Navigate to create page or open modal
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
    height: 100vh;
    border-right: 1px solid #e5e5e5;
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
    border-bottom: 1px solid #e5e5e5;
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
    color: #666;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .sidebar.collapsed .nav-link {
    padding: 12px;
    justify-content: center;
  }
  .nav-link:hover {
    background: #f5f5f5;
    color: #333;
  }
  .nav-icon {
    font-size: 1.2rem;
    flex-shrink: 0;
  }
  .nav-label {
    flex: 1;
  }
  .create-section {
    padding: 16px 20px;
    border-top: 1px solid #e5e5e5;
    position: relative;
  }
  .sidebar.collapsed .create-section {
    padding: 16px 8px;
  }
  .create-button {
    width: 100%;
    padding: 12px 16px;
    background: #4a90e2;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s;
    box-shadow: 0 2px 6px rgba(74, 144, 226, 0.3);
  }
  .create-button:hover {
    background: #357abd;
  }
  .sidebar.collapsed .create-button {
    padding: 12px 8px;
    font-size: 1.5rem;
  }
  .create-icon {
    font-size: 1.2rem;
    font-weight: 700;
  }
  .create-menu {
    position: absolute;
    bottom: 100%;
    left: 20px;
    right: 20px;
    margin-bottom: 8px;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 10;
  }
  .sidebar.collapsed .create-menu {
    left: 8px;
    right: 8px;
  }
  .create-menu-item {
    display: block;
    width: 100%;
    padding: 12px 16px;
    background: none;
    border: none;
    text-align: left;
    font-size: 0.95rem;
    color: #333;
    cursor: pointer;
    transition: background 0.15s;
    border-radius: 6px;
  }
  .create-menu-item:hover {
    background: #f5f5f5;
  }
  .create-menu-item:first-child {
    border-radius: 8px 8px 0 0;
  }
  .create-menu-item:last-child {
    border-radius: 0 0 8px 8px;
  }
  .user-section {
    padding: 20px;
    border-top: 1px solid #e5e5e5;
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
    transition: background 0.15s, color 0.15s;
  }
  .dots:hover {
    background: #f5f5f5;
    color: #4a90e2;
  }
  .account-dropdown {
    position: absolute;
    right: 0;
    bottom: 100%;
    margin-bottom: 8px;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    background: #f5f5f5;
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
    background: #e5e5e5;
    margin: 4px 0;
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
    <a href="/creator/dashboard">
      {#if sidebarOpen}
        <img src="https://sowing.me/assets/horizontal_logo_white.png" alt="Sowing.me" />
      {:else}
        <img src="https://sowing.me/assets/horizontal_logo_white_small.png" alt="Sowing.me" class="logo-small" />
      {/if}
    </a>
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
  <div class="create-section">
    {#if showCreateMenu}
      <div class="create-menu">
        <button class="create-menu-item" on:click={() => handleCreateOption('post')}>
          📝 Post
        </button>
        <button class="create-menu-item" on:click={() => handleCreateOption('product')}>
          🛍️ Product
        </button>
      </div>
    {/if}
    <button class="create-button" on:click={() => showCreateMenu = !showCreateMenu}>
      <span class="create-icon">+</span>
      {#if sidebarOpen}
        <span>Create</span>
      {/if}
    </button>
  </div>
  <div class="user-section" class:expanded={showRoleExpanded && hasMultipleRoles}>
    {#if hasMultipleRoles && showRoleExpanded}
      <!-- Creator Role Row -->
      <div class="user-row" on:click={() => { showRoleExpanded = false; }}>
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

      <div class="role-divider"></div>

      <!-- Member Role Row -->
      <div class="user-row" on:click={switchToMemberMode}>
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
    {:else}
      <!-- Single Row (collapsed or single role) -->
      <div class="user-row" on:click={() => { if (hasMultipleRoles) showRoleExpanded = true; }}>
        <div class="user-info">
          <img class="user-avatar" src={user.avatar} alt="Avatar" />
          {#if sidebarOpen}
            <div class="user-details">
              <span class="user-name">{$userData?.user?.creatorName || $userData?.creatorName || 'Creator'}</span>
              <span class="user-role">Creator</span>
            </div>
          {/if}
        </div>
        <div class="account-menu">
          <span class="dots" on:click|stopPropagation={() => showAccountMenu = !showAccountMenu} title="Account options">&#8942;</span>
          {#if showAccountMenu}
            <div class="account-dropdown">
              <a href="/profile">Profile</a>
              <a href="/account">Account Settings</a>
              <button type="button" on:click={handleLogout}>Logout</button>
            </div>
          {/if}
        </div>
      </div>
    {/if}
  </div>
</aside>
