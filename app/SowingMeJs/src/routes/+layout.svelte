<script>
  import LoginPage from '$lib/LoginPage.svelte';
  import Sidebar from '$lib/Sidebar.svelte';
  import { userData } from '$lib/stores.js';

  let sidebarOpen = true;
  let search = '';

  export let data;

  // Store user data and apiBaseUrl
  if (data) {
    userData.set(data);
  }
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
</style>

<div class="layout" class:blurred={!$userData?.user?.id}>
  <Sidebar bind:sidebarOpen bind:search />
  <div class="content">
    <slot />
  </div>
</div>

{#if !$userData?.user?.id}
  <div class="modal-overlay">
    <LoginPage />
  </div>
{/if}