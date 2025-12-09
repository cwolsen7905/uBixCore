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
    background: #fff;
    min-height: 100vh;
    color: #333;
  }

  .page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 32px 0;
  }

  .tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #e5e5e5;
    margin-bottom: 24px;
  }

  .tab {
    padding: 12px 24px;
    background: none;
    border: none;
    font-size: 1rem;
    color: #888;
    cursor: pointer;
    position: relative;
    transition: color 0.2s;
    white-space: nowrap;
  }

  .tab:hover {
    color: #333;
  }

  .tab.active {
    color: #4a90e2;
  }

  .tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: #4a90e2;
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
    background: #fff;
    color: #333;
    border: 1px solid #d0d0d0;
    border-radius: 8px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s;
    white-space: nowrap;
  }

  .filter-button:hover {
    background: #f5f5f5;
    border-color: #b0b0b0;
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
    color: #888;
    font-size: 1.1rem;
    pointer-events: none;
  }

  .search-input {
    width: 100%;
    padding: 12px 16px 12px 48px;
    background: #fff;
    color: #333;
    border: 1px solid #d0d0d0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: border-color 0.2s;
  }

  .search-input:focus {
    outline: none;
    border-color: #4a90e2;
  }

  .search-input::placeholder {
    color: #999;
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
    background: #f0f0f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
  }

  .empty-icon-content {
    font-size: 2rem;
    color: #888;
  }

  .empty-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 12px 0;
  }

  .empty-description {
    font-size: 1rem;
    color: #666;
    margin: 0 0 32px 0;
    max-width: 500px;
    line-height: 1.5;
  }

  .create-post-button {
    padding: 14px 32px;
    background: #4a90e2;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .create-post-button:hover {
    background: #357abd;
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
