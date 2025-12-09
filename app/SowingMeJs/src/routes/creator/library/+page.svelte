<script>
  let activeTab = 'posts';
  let searchQuery = '';
  let showFilter = false;

  const tabs = [
    { id: 'posts', label: 'Posts' },
    { id: 'collections', label: 'Collections' },
    { id: 'drafts', label: 'Drafts' }
  ];

  function handleCreatePost() {
    console.log('Create post clicked');
    // TODO: Navigate to create post page or open modal
  }

  function handleSearch(event) {
    searchQuery = event.target.value;
    console.log('Searching for:', searchQuery);
    // TODO: Implement search functionality
  }

  function toggleFilter() {
    showFilter = !showFilter;
    console.log('Filter toggled:', showFilter);
    // TODO: Implement filter functionality
  }
</script>

<style>
  .library-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 20px;
    background: var(--color-bg-primary);
    min-height: 100vh;
    color: var(--color-text-primary);
  }

  .page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin: 0 0 32px 0;
  }

  .tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--color-border-light);
    margin-bottom: 24px;
  }

  .tab {
    padding: 12px 24px;
    background: none;
    border: none;
    font-size: 1rem;
    color: var(--color-text-tertiary);
    cursor: pointer;
    position: relative;
    transition: color 0.2s;
    white-space: nowrap;
  }

  .tab:hover {
    color: var(--color-text-primary);
  }

  .tab.active {
    color: var(--color-accent-primary);
  }

  .tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--color-accent-primary);
  }

  .toolbar {
    display: flex;
    gap: 12px;
    margin-bottom: 32px;
  }

  .filter-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: var(--color-bg-primary);
    color: var(--color-text-primary);
    border: 1px solid var(--color-border-medium);
    border-radius: 8px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s;
    white-space: nowrap;
  }

  .filter-button:hover {
    background: var(--color-bg-hover);
    border-color: var(--color-border-dark);
  }

  .filter-icon {
    font-size: 1.1rem;
  }

  .search-container {
    flex: 1;
    position: relative;
  }

  .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-tertiary);
    font-size: 1.1rem;
    pointer-events: none;
  }

  .search-input {
    width: 100%;
    padding: 12px 16px 12px 48px;
    background: var(--color-bg-primary);
    color: var(--color-text-primary);
    border: 1px solid var(--color-border-medium);
    border-radius: 8px;
    font-size: 0.95rem;
    transition: border-color 0.2s;
  }

  .search-input:focus {
    outline: none;
    border-color: var(--color-accent-primary);
  }

  .search-input::placeholder {
    color: var(--color-text-placeholder);
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 20px;
    text-align: center;
  }

  .empty-icon {
    width: 80px;
    height: 80px;
    background: var(--color-bg-tertiary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
  }

  .empty-icon-content {
    font-size: 2rem;
    color: var(--color-text-tertiary);
  }

  .empty-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin: 0 0 12px 0;
  }

  .empty-description {
    font-size: 1rem;
    color: var(--color-text-secondary);
    margin: 0 0 32px 0;
    max-width: 500px;
    line-height: 1.5;
  }

  .create-post-button {
    padding: 14px 32px;
    background: var(--color-accent-primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .create-post-button:hover {
    background: var(--color-accent-hover);
  }

  @media (max-width: 768px) {
    .library-page {
      padding: 24px 16px;
    }

    .page-title {
      font-size: 2rem;
    }

    .toolbar {
      flex-direction: column;
    }

    .tabs {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .tab {
      padding: 10px 16px;
      font-size: 0.9rem;
    }
  }
</style>

<div class="library-page">
  <h1 class="page-title">Library</h1>

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
  </div>

  <div class="toolbar">
    <button class="filter-button" on:click={toggleFilter}>
      <span class="filter-icon">☰</span>
      <span>Filter</span>
    </button>
    <div class="search-container">
      <span class="search-icon">🔍</span>
      <input
        type="text"
        class="search-input"
        placeholder="Search"
        bind:value={searchQuery}
        on:input={handleSearch}
      />
    </div>
  </div>

  {#if activeTab === 'posts'}
    <div class="empty-state">
      <div class="empty-icon">
        <span class="empty-icon-content">📄</span>
      </div>
      <h2 class="empty-title">No posts yet</h2>
      <p class="empty-description">
        Use your library to keep track of everything you publish. Post an update today to kick things off.
      </p>
      <button class="create-post-button" on:click={handleCreatePost}>
        Create a post
      </button>
    </div>

  {:else if activeTab === 'collections'}
    <div class="empty-state">
      <div class="empty-icon">
        <span class="empty-icon-content">📚</span>
      </div>
      <h2 class="empty-title">No collections yet</h2>
      <p class="empty-description">
        Create collections to organize your posts into categories.
      </p>
    </div>

  {:else if activeTab === 'drafts'}
    <div class="empty-state">
      <div class="empty-icon">
        <span class="empty-icon-content">📝</span>
      </div>
      <h2 class="empty-title">No drafts yet</h2>
      <p class="empty-description">
        Save your work-in-progress posts as drafts to finish later.
      </p>
    </div>
  {/if}
</div>
