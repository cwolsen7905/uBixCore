<script>
  import LoginPage from '$lib/LoginPage.svelte';
  import Sidebar from '$lib/Sidebar.svelte';
  import { userData } from '$lib/stores.js';

  let sidebarOpen = true;
  let search = '';

  export let data;

  if( data?.user ) {
    userData.set(data);
  }
</script>

<style>
  .layout {
    display: flex;
    min-height: 100vh;
    background: #f6f8fa;
  }
  .content {
    flex: 1;
    padding: 2rem;
    overflow: auto;
    min-width: 0;
  }
</style>

<div class="layout">
  <Sidebar bind:sidebarOpen bind:search />
  <div class="content">
    {#if !$userData?.user}
      <LoginPage />
    {:else}
      <slot />
    {/if}
  </div>
</div>