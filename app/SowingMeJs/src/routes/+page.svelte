<h1>Welcome to SvelteKit</h1>
<p>Visit <a href="https://svelte.dev/docs/kit">svelte.dev/docs/kit</a> to read the documentation</p>
<script>
  import { userData } from '$lib/stores.js';
</script>

<pre>DATA: {JSON.stringify($userData?.user, null, 2)}</pre>