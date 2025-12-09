<script>
  import { userData, activeRole } from '$lib/stores.js';
  import { themePreference, setThemePreference } from '$lib/themeStore.js';

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

  // Generate avatar URLs based on user data
  $: memberName = `${$userData?.user?.firstName || $userData?.firstName || ''} ${$userData?.user?.lastName || $userData?.lastName || ''}`.trim();
  $: creatorName = $userData?.user?.creatorName || $userData?.creatorName || 'Creator';
  $: memberAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(memberName)}&background=4a90e2&color=fff`;
  $: creatorAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(creatorName)}&background=4a90e2&color=fff`;

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
    background: var(--color-bg-primary);
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    transition: width 0.2s;
    position: relative;
    height: 100vh;
    border-right: 1px solid var(--color-border-light);
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
    border-bottom: 1px solid var(--color-border-light);
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
    color: var(--color-text-secondary);
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
    background: var(--color-bg-hover);
    color: var(--color-text-primary);
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
    border-top: 1px solid var(--color-border-light);
    position: relative;
  }
  .sidebar.collapsed .create-section {
    padding: 16px 8px;
  }
  .create-button {
    width: 100%;
    padding: 12px 16px;
    background: var(--color-accent-primary);
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
    background: var(--color-accent-hover);
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
    background: var(--color-bg-primary);
    border: 1px solid var(--color-border-light);
    border-radius: 8px;
    box-shadow: var(--shadow-md);
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
    color: var(--color-text-primary);
    cursor: pointer;
    transition: background 0.15s;
    border-radius: 6px;
  }
  .create-menu-item:hover {
    background: var(--color-bg-hover);
  }
  .create-menu-item:first-child {
    border-radius: 8px 8px 0 0;
  }
  .create-menu-item:last-child {
    border-radius: 0 0 8px 8px;
  }
  .user-section {
    padding: 20px;
    border-top: 1px solid var(--color-border-light);
    background: var(--color-bg-primary);
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
    background: var(--color-bg-hover);
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
    border: 2px solid var(--color-accent-primary);
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
    color: var(--color-text-primary);
    font-size: 1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .user-role {
    font-size: 0.75rem;
    color: var(--color-text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .account-menu {
    position: relative;
  }
  .dots {
    cursor: pointer;
    font-size: 1.5rem;
    color: var(--color-text-tertiary);
    padding: 4px;
    border-radius: 50%;
    transition: background 0.15s, color 0.15s;
  }
  .dots:hover {
    background: var(--color-bg-hover);
    color: var(--color-accent-primary);
  }
  .account-dropdown {
    position: absolute;
    right: 0;
    bottom: 100%;
    margin-bottom: 8px;
    background: var(--color-bg-primary);
    border: 1px solid var(--color-border-light);
    border-radius: 8px;
    box-shadow: var(--shadow-md);
    min-width: 140px;
    z-index: 10;
  }
  .account-dropdown a {
    display: block;
    padding: 10px 16px;
    color: var(--color-text-primary);
    text-decoration: none;
    font-size: 1rem;
    border-radius: 6px;
    transition: background 0.15s;
  }
  .account-dropdown a:hover,
  .account-dropdown button:hover {
    background: var(--color-bg-hover);
    color: var(--color-accent-primary);
  }
  .account-dropdown button {
    display: block;
    width: 100%;
    padding: 10px 16px;
    color: var(--color-text-primary);
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
    background: var(--color-border-light);
    margin: 4px 0;
  }
  .menu-section-title {
    padding: 10px 16px 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--color-text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .theme-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    color: var(--color-text-primary);
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
    background: var(--color-bg-hover);
    color: var(--color-accent-primary);
  }
  .theme-option.active {
    background: var(--color-bg-hover);
    color: var(--color-accent-primary);
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
          <img class="user-avatar" src={creatorAvatar} alt="Avatar" />
          {#if sidebarOpen}
            <div class="user-details">
              <span class="user-name">{creatorName}</span>
              <span class="user-role">Creator</span>
            </div>
          {/if}
        </div>
      </div>

      <div class="role-divider"></div>

      <!-- Member Role Row -->
      <div class="user-row" on:click={switchToMemberMode}>
        <div class="user-info">
          <img class="user-avatar" src={memberAvatar} alt="Avatar" />
          {#if sidebarOpen}
            <div class="user-details">
              <span class="user-name">{memberName}</span>
              <span class="user-role">Member</span>
            </div>
          {/if}
        </div>
      </div>
    {:else}
      <!-- Single Row (collapsed or single role) -->
      <div class="user-row" on:click={() => { if (hasMultipleRoles) showRoleExpanded = true; }}>
        <div class="user-info">
          <img class="user-avatar" src={creatorAvatar} alt="Avatar" />
          {#if sidebarOpen}
            <div class="user-details">
              <span class="user-name">{creatorName}</span>
              <span class="user-role">Creator</span>
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
