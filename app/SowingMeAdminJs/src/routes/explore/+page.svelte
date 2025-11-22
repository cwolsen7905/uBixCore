<script>
  let searchQuery = '';

  const topics = [
    'All', 'Music', 'Podcasts', 'Video', 'Writing', 'Art', 'Education', 'Gaming', 'Photography', 'Crafts', 'Science', 'Comedy'
  ];

  let selectedTopic = 'All';

  // Placeholder creator data
  const recommendedCreators = [
    { id: 1, name: 'Sarah Johnson', title: 'Worship Music & Devotionals', image: 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=4a90e2&color=fff&size=200' },
    { id: 2, name: 'David Chen', title: 'Bible Study Teacher', image: 'https://ui-avatars.com/api/?name=David+Chen&background=2e7d32&color=fff&size=200' },
    { id: 3, name: 'Emily Grace', title: 'Christian Podcast', image: 'https://ui-avatars.com/api/?name=Emily+Grace&background=ff8f00&color=fff&size=200' },
    { id: 4, name: 'Michael Brooks', title: 'Faith & Family Vlog', image: 'https://ui-avatars.com/api/?name=Michael+Brooks&background=1565c0&color=fff&size=200' },
    { id: 5, name: 'Rachel Adams', title: 'Christian Art & Design', image: 'https://ui-avatars.com/api/?name=Rachel+Adams&background=c62828&color=fff&size=200' }
  ];

  const popularCreators = [
    { id: 6, name: 'James Mitchell', title: 'Daily Devotionals', image: 'https://ui-avatars.com/api/?name=James+Mitchell&background=6a1b9a&color=fff&size=150' },
    { id: 7, name: 'Lisa Thompson', title: 'Youth Ministry', image: 'https://ui-avatars.com/api/?name=Lisa+Thompson&background=00695c&color=fff&size=150' },
    { id: 8, name: 'Robert Phillips', title: 'Sermon Series', image: 'https://ui-avatars.com/api/?name=Robert+Phillips&background=ef6c00&color=fff&size=150' },
    { id: 9, name: 'Anna Williams', title: 'Worship Leader', image: 'https://ui-avatars.com/api/?name=Anna+Williams&background=283593&color=fff&size=150' },
    { id: 10, name: 'Mark Davis', title: 'Christian Comedy', image: 'https://ui-avatars.com/api/?name=Mark+Davis&background=4e342e&color=fff&size=150' },
    { id: 11, name: 'Grace Kim', title: 'Prayer & Meditation', image: 'https://ui-avatars.com/api/?name=Grace+Kim&background=00838f&color=fff&size=150' }
  ];

  const newCreators = [
    { id: 12, name: 'John Carter', title: 'Apologetics & Theology', image: 'https://ui-avatars.com/api/?name=John+Carter&background=5d4037&color=fff&size=200' },
    { id: 13, name: 'Maria Santos', title: 'Spanish Worship', image: 'https://ui-avatars.com/api/?name=Maria+Santos&background=ad1457&color=fff&size=200' },
    { id: 14, name: 'Peter Brown', title: 'Men\'s Ministry', image: 'https://ui-avatars.com/api/?name=Peter+Brown&background=1565c0&color=fff&size=200' },
    { id: 15, name: 'Hannah White', title: 'Women\'s Bible Study', image: 'https://ui-avatars.com/api/?name=Hannah+White&background=7b1fa2&color=fff&size=200' },
    { id: 16, name: 'Chris Taylor', title: 'Christian Hip Hop', image: 'https://ui-avatars.com/api/?name=Chris+Taylor&background=0277bd&color=fff&size=200' },
    { id: 17, name: 'Laura Martinez', title: 'Family Devotions', image: 'https://ui-avatars.com/api/?name=Laura+Martinez&background=558b2f&color=fff&size=200' },
    { id: 18, name: 'Daniel Lee', title: 'Christian Film Reviews', image: 'https://ui-avatars.com/api/?name=Daniel+Lee&background=d84315&color=fff&size=200' }
  ];

  let newCreatorsScroll = 0;
  let newCreatorsContainer;

  function scrollNewCreators(direction) {
    const scrollAmount = 300;
    if (newCreatorsContainer) {
      newCreatorsContainer.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
      });
    }
  }
</script>

<style>
  .explore-page {
    max-width: 1200px;
    margin: 0 auto;
  }

  h1 {
    margin-bottom: 24px;
    font-size: 1.75rem;
    color: #333;
  }

  .search-section {
    margin-bottom: 24px;
  }

  .search-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    background: #fff;
  }

  .search-input:focus {
    outline: none;
    border-color: #4a90e2;
  }

  .search-input::placeholder {
    color: #999;
  }

  .topics-section {
    margin-bottom: 32px;
    overflow-x: auto;
    padding-bottom: 8px;
  }

  .topics-list {
    display: flex;
    gap: 8px;
  }

  .topic-btn {
    padding: 8px 16px;
    border: 1px solid #ddd;
    border-radius: 20px;
    background: #fff;
    font-size: 0.9rem;
    color: #666;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
  }

  .topic-btn:hover {
    border-color: #4a90e2;
    color: #4a90e2;
  }

  .topic-btn.active {
    background: #4a90e2;
    border-color: #4a90e2;
    color: #fff;
  }

  .section {
    margin-bottom: 40px;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }

  .section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
  }

  .see-all {
    color: #4a90e2;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .see-all:hover {
    text-decoration: underline;
  }

  /* Recommended creators - horizontal scroll */
  .creators-row {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding-bottom: 8px;
  }

  .creator-card {
    flex-shrink: 0;
    width: 160px;
    text-align: center;
    cursor: pointer;
  }

  .creator-card:hover .creator-image {
    transform: scale(1.05);
  }

  .creator-image {
    width: 160px;
    height: 160px;
    border-radius: 8px;
    object-fit: cover;
    margin-bottom: 12px;
    transition: transform 0.2s;
  }

  .creator-name {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .creator-title {
    color: #666;
    font-size: 0.8rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Popular creators - grid */
  .creators-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }

  .creator-card-small {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    cursor: pointer;
    transition: box-shadow 0.2s;
  }

  .creator-card-small:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  .creator-image-small {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
  }

  .creator-info {
    overflow: hidden;
  }

  .creator-card-small .creator-name {
    font-size: 0.9rem;
  }

  .creator-card-small .creator-title {
    font-size: 0.75rem;
  }

  /* New creators with scroll arrows */
  .scroll-section {
    position: relative;
  }

  .scroll-container {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding-bottom: 8px;
    scroll-behavior: smooth;
  }

  .scroll-container::-webkit-scrollbar {
    display: none;
  }

  .scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #666;
    z-index: 10;
    transition: all 0.2s;
  }

  .scroll-btn:hover {
    background: #f5f5f5;
    color: #4a90e2;
  }

  .scroll-btn.left {
    left: -20px;
  }

  .scroll-btn.right {
    right: -20px;
  }

  @media (max-width: 900px) {
    .creators-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 600px) {
    .creators-grid {
      grid-template-columns: 1fr;
    }

    .creator-card {
      width: 140px;
    }

    .creator-image {
      width: 140px;
      height: 140px;
    }

    .scroll-btn {
      display: none;
    }
  }
</style>

<div class="explore-page">
  <h1>Explore</h1>

  <div class="search-section">
    <input
      type="text"
      class="search-input"
      placeholder="Search creators, topics, tags..."
      bind:value={searchQuery}
    />
  </div>

  <div class="topics-section">
    <div class="topics-list">
      {#each topics as topic}
        <button
          class="topic-btn"
          class:active={selectedTopic === topic}
          on:click={() => selectedTopic = topic}
        >
          {topic}
        </button>
      {/each}
    </div>
  </div>

  <div class="section">
    <div class="section-header">
      <h2 class="section-title">Recommended for you</h2>
      <a href="/explore/recommended" class="see-all">See all</a>
    </div>
    <div class="creators-row">
      {#each recommendedCreators as creator}
        <div class="creator-card">
          <img src={creator.image} alt={creator.name} class="creator-image" />
          <div class="creator-name">{creator.name}</div>
          <div class="creator-title">{creator.title}</div>
        </div>
      {/each}
    </div>
  </div>

  <div class="section">
    <div class="section-header">
      <h2 class="section-title">Popular this week</h2>
      <a href="/explore/popular" class="see-all">See all</a>
    </div>
    <div class="creators-grid">
      {#each popularCreators as creator}
        <div class="creator-card-small">
          <img src={creator.image} alt={creator.name} class="creator-image-small" />
          <div class="creator-info">
            <div class="creator-name">{creator.name}</div>
            <div class="creator-title">{creator.title}</div>
          </div>
        </div>
      {/each}
    </div>
  </div>

  <div class="section">
    <div class="section-header">
      <h2 class="section-title">New on Sowing.me</h2>
      <a href="/explore/new" class="see-all">See all</a>
    </div>
    <div class="scroll-section">
      <button class="scroll-btn left" on:click={() => scrollNewCreators(-1)}>←</button>
      <div class="scroll-container" bind:this={newCreatorsContainer}>
        {#each newCreators as creator}
          <div class="creator-card">
            <img src={creator.image} alt={creator.name} class="creator-image" />
            <div class="creator-name">{creator.name}</div>
            <div class="creator-title">{creator.title}</div>
          </div>
        {/each}
      </div>
      <button class="scroll-btn right" on:click={() => scrollNewCreators(1)}>→</button>
    </div>
  </div>
</div>
