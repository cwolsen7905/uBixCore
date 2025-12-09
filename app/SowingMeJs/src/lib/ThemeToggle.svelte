<script>
  import { theme, toggleTheme } from '$lib/themeStore.js';
</script>

<style>
  .theme-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border-light);
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s;
  }

  .theme-toggle:hover {
    background: var(--color-bg-hover);
    border-color: var(--color-border-medium);
  }

  .theme-icon {
    font-size: 1.2rem;
  }

  .theme-label {
    font-size: 0.9rem;
    color: var(--color-text-secondary);
    font-weight: 500;
  }
</style>

<button class="theme-toggle" on:click={toggleTheme} title="Toggle theme">
  <span class="theme-icon">{$theme === 'light' ? '🌙' : '☀️'}</span>
  <span class="theme-label">{$theme === 'light' ? 'Dark' : 'Light'}</span>
</button>
