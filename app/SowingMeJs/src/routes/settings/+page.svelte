<script>
  let activeTab = 'basics';
  let moreDropdownOpen = false;

  const tabs = [
    { id: 'basics', label: 'Basics' },
    { id: 'account', label: 'Account' },
    { id: 'email-notifications', label: 'Email Notifications' },
    { id: 'memberships', label: 'Memberships' },
    { id: 'billing-history', label: 'Billing History' }
  ];

  const moreOptions = [
    { id: 'payment-methods', label: 'Payment Methods' },
    { id: 'connected-apps', label: 'Connected Apps' },
    { id: 'blocked-users', label: 'Blocked Users' }
  ];

  function selectMoreOption(id) {
    activeTab = id;
    moreDropdownOpen = false;
  }

  function toggleMoreDropdown() {
    moreDropdownOpen = !moreDropdownOpen;
  }

  function closeDropdown() {
    moreDropdownOpen = false;
  }

  // Form data for basics
  let avatar = 'https://ui-avatars.com/api/?name=User&background=4a90e2&color=fff';
  let displayName = '';
  let email = '';
  let country = '';
  let state = '';
  let postalCode = '';

  function handleAvatarChange(event) {
    const file = event.target.files[0];
    if (file) {
      avatar = URL.createObjectURL(file);
    }
  }

  function saveBasics() {
    // TODO: Implement save functionality
    console.log('Saving basics:', { displayName, email, country, state, postalCode });
    alert('Settings saved!');
  }
</script>

<style>
  .settings-page {
    max-width: 900px;
    margin: 0 auto;
  }

  h1 {
    margin-bottom: 24px;
    font-size: 1.75rem;
    color: #333;
  }

  .tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid #eee;
    margin-bottom: 32px;
    overflow-x: auto;
    padding-bottom: 2px;
  }

  .tab {
    padding: 12px 16px;
    background: none;
    border: none;
    font-size: 0.95rem;
    color: #666;
    cursor: pointer;
    white-space: nowrap;
    position: relative;
    transition: color 0.2s;
  }

  .tab:hover {
    color: #4a90e2;
  }

  .tab.active {
    color: #4a90e2;
    font-weight: 600;
  }

  .tab.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #4a90e2;
  }

  .tab-content {
    background: #fff;
    border-radius: 8px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  }

  .section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 24px;
    color: #333;
  }

  .avatar-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid #eee;
  }

  .avatar-preview {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #4a90e2;
  }

  .avatar-upload {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .avatar-upload label {
    font-weight: 500;
    color: #333;
  }

  .avatar-upload input[type="file"] {
    font-size: 0.9rem;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 0.95rem;
  }

  .form-group input,
  .form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s;
  }

  .form-group input:focus,
  .form-group select:focus {
    outline: none;
    border-color: #4a90e2;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }

  .save-btn {
    margin-top: 24px;
    padding: 12px 24px;
    background: #4a90e2;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .save-btn:hover {
    background: #357abd;
  }

  .coming-soon {
    text-align: center;
    padding: 60px 20px;
    color: #888;
  }

  .coming-soon h3 {
    margin-bottom: 8px;
    color: #666;
  }

  .more-dropdown {
    position: relative;
  }

  .more-btn {
    padding: 12px 16px;
    background: none;
    border: none;
    font-size: 0.95rem;
    color: #666;
    cursor: pointer;
    white-space: nowrap;
    transition: color 0.2s;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .more-btn:hover {
    color: #4a90e2;
  }

  .more-btn.active {
    color: #4a90e2;
    font-weight: 600;
  }

  .more-btn::after {
    content: '▼';
    font-size: 0.6rem;
  }

  .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 180px;
    z-index: 100;
    margin-top: 4px;
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

  @media (max-width: 600px) {
    .form-row {
      grid-template-columns: 1fr;
    }

    .tabs {
      gap: 4px;
    }

    .tab {
      padding: 10px 12px;
      font-size: 0.85rem;
    }
  }
</style>

<div class="settings-page">
  <h1>Settings</h1>

  <div class="tabs">
    {#each tabs as tab}
      <button
        class="tab"
        class:active={activeTab === tab.id}
        on:click={() => activeTab = tab.id}
      >
        {tab.label}
      </button>
    {/each}
    <div class="more-dropdown">
      <button
        class="more-btn"
        class:active={moreOptions.some(opt => opt.id === activeTab)}
        on:click={toggleMoreDropdown}
      >
        More
      </button>
      {#if moreDropdownOpen}
        <div class="dropdown-menu">
          {#each moreOptions as option}
            <button class="dropdown-item" on:click={() => selectMoreOption(option.id)}>
              {option.label}
            </button>
          {/each}
        </div>
      {/if}
    </div>
  </div>

  <div class="tab-content">
    {#if activeTab === 'basics'}
      <h2 class="section-title">Profile Information</h2>

      <div class="avatar-section">
        <img src={avatar} alt="Avatar" class="avatar-preview" />
        <div class="avatar-upload">
          <label>Profile Photo</label>
          <input type="file" accept="image/*" on:change={handleAvatarChange} />
        </div>
      </div>

      <div class="form-group">
        <label for="displayName">Display Name</label>
        <input type="text" id="displayName" bind:value={displayName} placeholder="Enter your display name" />
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" bind:value={email} placeholder="Enter your email" />
      </div>

      <div class="form-group">
        <label for="country">Country of Residence</label>
        <input type="text" id="country" bind:value={country} placeholder="Enter your country" />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="state">State of Residence</label>
          <input type="text" id="state" bind:value={state} placeholder="Enter your state" />
        </div>

        <div class="form-group">
          <label for="postalCode">Postal Code</label>
          <input type="text" id="postalCode" bind:value={postalCode} placeholder="Enter postal code" />
        </div>
      </div>

      <button class="save-btn" on:click={saveBasics}>Save Changes</button>

    {:else if activeTab === 'account'}
      <div class="coming-soon">
        <h3>Account Settings</h3>
        <p>Coming soon...</p>
      </div>

    {:else if activeTab === 'email-notifications'}
      <div class="coming-soon">
        <h3>Email Notifications</h3>
        <p>Coming soon...</p>
      </div>

    {:else if activeTab === 'memberships'}
      <div class="coming-soon">
        <h3>Memberships</h3>
        <p>Coming soon...</p>
      </div>

    {:else if activeTab === 'billing-history'}
      <div class="coming-soon">
        <h3>Billing History</h3>
        <p>Coming soon...</p>
      </div>

    {:else if activeTab === 'more'}
      <div class="coming-soon">
        <h3>More Settings</h3>
        <p>Payment Methods and Blocked Users coming soon...</p>
      </div>
    {/if}
  </div>
</div>
