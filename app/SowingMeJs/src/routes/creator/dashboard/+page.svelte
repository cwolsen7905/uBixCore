<script>
  import { userData } from '$lib/stores.js';

  let activeTab = 'home';
  let showPageMenu = false;

  const tabs = [
    { id: 'home', label: 'Home' },
    { id: 'collections', label: 'Collections' },
    { id: 'shop', label: 'Shop' },
    { id: 'membership', label: 'Membership' },
    { id: 'recommendations', label: 'Recommendations' }
  ];

  const setupSteps = [
    { id: 1, title: 'Start with the basics', description: 'Set up your profile and cover image', completed: false },
    { id: 2, title: 'Make your first post', description: 'Share something with your audience', completed: false },
    { id: 3, title: 'Add a product to your shop', description: 'Start selling to your fans', completed: false },
    { id: 4, title: 'Create a membership tier', description: 'Offer exclusive content', completed: false },
    { id: 5, title: 'Share your page', description: 'Get the word out about your creator page', completed: false }
  ];

  function handleEditPage() {
    console.log('Edit page clicked');
    // TODO: Navigate to edit page
  }

  function handleViewPage() {
    showPageMenu = false;
    console.log('View page clicked');
    // TODO: Open creator public page in new tab
  }

  function handlePageDetails() {
    showPageMenu = false;
    console.log('Page details clicked');
    // TODO: Show page details modal
  }

  function handleSharePage() {
    showPageMenu = false;
    console.log('Share page clicked');
    // TODO: Show share modal
  }
</script>

<style>
  .dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
  }

  .creator-header {
    text-align: center;
    margin-bottom: 32px;
  }

  .creator-name {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 24px 0;
  }

  .nav-tabs {
    display: flex;
    justify-content: center;
    gap: 8px;
    border-bottom: 2px solid #eee;
    padding-bottom: 2px;
    margin-bottom: 32px;
  }

  .nav-tab {
    padding: 12px 24px;
    background: none;
    border: none;
    font-size: 1rem;
    color: #666;
    cursor: pointer;
    position: relative;
    transition: color 0.2s;
    white-space: nowrap;
  }

  .nav-tab:hover {
    color: #4a90e2;
  }

  .nav-tab.active {
    color: #4a90e2;
    font-weight: 600;
  }

  .nav-tab.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #4a90e2;
  }

  .page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  }

  .page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin: 0;
  }

  .page-actions {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .edit-btn {
    padding: 10px 20px;
    background: #4a90e2;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .edit-btn:hover {
    background: #357abd;
  }

  .menu-toggle {
    position: relative;
  }

  .dots-btn {
    width: 36px;
    height: 36px;
    background: #f5f5f5;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1.2rem;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
  }

  .dots-btn:hover {
    background: #e8e8e8;
  }

  .page-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 8px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 180px;
    z-index: 10;
  }

  .dropdown-item {
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
  }

  .dropdown-item:hover {
    background: #f5f5f5;
    color: #4a90e2;
  }

  .dropdown-item:first-child {
    border-radius: 8px 8px 0 0;
  }

  .dropdown-item:last-child {
    border-radius: 0 0 8px 8px;
  }

  .welcome-section {
    background: #fff;
    border-radius: 8px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 24px;
  }

  .welcome-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 8px 0;
  }

  .welcome-subtitle {
    font-size: 1rem;
    color: #666;
    margin: 0 0 24px 0;
  }

  .setup-steps {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 24px;
  }

  .setup-header {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 16px 0;
  }

  .step-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .step-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid #eee;
    transition: border-color 0.2s;
  }

  .step-item:hover {
    border-color: #4a90e2;
  }

  .step-checkbox {
    width: 24px;
    height: 24px;
    border: 2px solid #ddd;
    border-radius: 4px;
    flex-shrink: 0;
    margin-top: 2px;
    cursor: pointer;
  }

  .step-checkbox.completed {
    background: #4a90e2;
    border-color: #4a90e2;
    position: relative;
  }

  .step-checkbox.completed::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-size: 0.9rem;
    font-weight: bold;
  }

  .step-content {
    flex: 1;
  }

  .step-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 4px 0;
  }

  .step-description {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
  }

  @media (max-width: 768px) {
    .dashboard-container {
      padding: 24px 16px;
    }

    .creator-name {
      font-size: 1.5rem;
    }

    .nav-tabs {
      overflow-x: auto;
      justify-content: flex-start;
      -webkit-overflow-scrolling: touch;
    }

    .nav-tab {
      padding: 10px 16px;
      font-size: 0.9rem;
    }

    .page-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
    }

    .page-actions {
      width: 100%;
      justify-content: space-between;
    }

    .welcome-section {
      padding: 24px 20px;
    }

    .welcome-title {
      font-size: 1.5rem;
    }
  }
</style>

<div class="dashboard-container">
  <div class="creator-header">
    <h1 class="creator-name">
      {$userData?.user?.firstName || $userData?.firstName || 'Creator'} {$userData?.user?.lastName || $userData?.lastName || 'Name'}
    </h1>

    <nav class="nav-tabs">
      {#each tabs as tab}
        <button
          class="nav-tab"
          class:active={activeTab === tab.id}
          on:click={() => activeTab = tab.id}
        >
          {tab.label}
        </button>
      {/each}
    </nav>
  </div>

  {#if activeTab === 'home'}
    <div class="page-header">
      <h2 class="page-title">Home</h2>
      <div class="page-actions">
        <button class="edit-btn" on:click={handleEditPage}>Edit Page</button>
        <div class="menu-toggle">
          <button class="dots-btn" on:click={() => showPageMenu = !showPageMenu}>
            &#8942;
          </button>
          {#if showPageMenu}
            <div class="page-dropdown">
              <button class="dropdown-item" on:click={handleViewPage}>View Page</button>
              <button class="dropdown-item" on:click={handlePageDetails}>Page Details</button>
              <button class="dropdown-item" on:click={handleSharePage}>Share</button>
            </div>
          {/if}
        </div>
      </div>
    </div>

    <div class="welcome-section">
      <h2 class="welcome-title">Welcome to Sowing.Me</h2>
      <p class="welcome-subtitle">Let's get your creator page set up and ready to grow your audience</p>

      <div class="setup-steps">
        <h3 class="setup-header">Getting Started</h3>
        <div class="step-list">
          {#each setupSteps as step}
            <div class="step-item">
              <div class="step-checkbox" class:completed={step.completed}></div>
              <div class="step-content">
                <h4 class="step-title">{step.title}</h4>
                <p class="step-description">{step.description}</p>
              </div>
            </div>
          {/each}
        </div>
      </div>
    </div>

  {:else if activeTab === 'collections'}
    <div class="page-header">
      <h2 class="page-title">Collections</h2>
    </div>
    <div class="welcome-section">
      <h2 class="welcome-title">Collections</h2>
      <p class="welcome-subtitle">Organize your content into collections</p>
    </div>

  {:else if activeTab === 'shop'}
    <div class="page-header">
      <h2 class="page-title">Shop</h2>
    </div>
    <div class="welcome-section">
      <h2 class="welcome-title">Shop</h2>
      <p class="welcome-subtitle">Manage your products and sales</p>
    </div>

  {:else if activeTab === 'membership'}
    <div class="page-header">
      <h2 class="page-title">Membership</h2>
    </div>
    <div class="welcome-section">
      <h2 class="welcome-title">Membership</h2>
      <p class="welcome-subtitle">Create and manage membership tiers</p>
    </div>

  {:else if activeTab === 'recommendations'}
    <div class="page-header">
      <h2 class="page-title">Recommendations</h2>
    </div>
    <div class="welcome-section">
      <h2 class="welcome-title">Recommendations</h2>
      <p class="welcome-subtitle">Recommend other creators to your audience</p>
    </div>
  {/if}
</div>
