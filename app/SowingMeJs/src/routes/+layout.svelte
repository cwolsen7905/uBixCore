<script>
  import { page } from '$app/stores';
  import LoginPage from '$lib/LoginPage.svelte';
  import Sidebar from '$lib/Sidebar.svelte';
  import CreatorSidebar from '$lib/CreatorSidebar.svelte';
  import { userData } from '$lib/stores.js';

  let sidebarOpen = true;
  let search = '';

  export let data;

  // Store user data and apiBaseUrl
  if (data) {
    userData.set(data);
  }

  // Determine if we're in creator mode based on the route
  $: isCreatorMode = $page.url.pathname.startsWith('/creator');
</script>

<style>
  .layout {
    display: flex;
    min-height: 100vh;
    background: #f6f8fa;
  }
  .layout.blurred {
    filter: blur(5px);
    pointer-events: none;
    user-select: none;
  }
  .content {
    flex: 1;
    padding: 2rem;
    overflow: auto;
    min-width: 0;
  }
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }
  .error-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background: #f6f8fa;
    padding: 2rem;
    text-align: center;
  }
  .error-page h1 {
    color: #333;
    margin-bottom: 1rem;
  }
  .error-page p {
    color: #666;
    margin-bottom: 1.5rem;
    max-width: 400px;
  }
  .error-page button {
    padding: 0.75rem 1.5rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
  }
  .error-page button:hover {
    background: #0056b3;
  }
</style>

{#if $userData?.systemError}
  <div class="error-page">
    <h1>System Unavailable</h1>
    <p>We're sorry, the system is currently unavailable. Please try again in several minutes.</p>
    <button on:click={() => location.reload()}>Try Again</button>
  </div>
{:else}
  <div class="layout" class:blurred={!$userData?.user?.id}>
    {#if isCreatorMode}
      <CreatorSidebar bind:sidebarOpen />
    {:else}
      <Sidebar bind:sidebarOpen bind:search />
    {/if}
    <div class="content">
      <slot />
    </div>
  </div>

  {#if !$userData?.user?.id}
    <div class="modal-overlay">
      <LoginPage />
    </div>
  {/if}
{/if}