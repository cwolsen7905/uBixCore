import { writable } from 'svelte/store';
import { browser } from '$app/environment';

// Get system theme preference
const getSystemTheme = () => {
  if (browser && window.matchMedia) {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }
  return 'light';
};

// Get initial theme preference from localStorage or default to 'system'
const getInitialThemePreference = () => {
  if (browser) {
    const stored = localStorage.getItem('themePreference');
    if (stored && ['light', 'dark', 'system'].includes(stored)) {
      return stored;
    }
  }
  return 'system';
};

// Get the actual theme to apply based on preference
const getActualTheme = (preference) => {
  if (preference === 'system') {
    return getSystemTheme();
  }
  return preference;
};

// Create theme preference store (light, dark, or system)
export const themePreference = writable(getInitialThemePreference());

// Create actual theme store (only light or dark, resolved from preference)
export const theme = writable(getActualTheme(getInitialThemePreference()));

// Subscribe to theme preference changes
if (browser) {
  themePreference.subscribe(preference => {
    localStorage.setItem('themePreference', preference);
    const actualTheme = getActualTheme(preference);
    theme.set(actualTheme);
    document.documentElement.setAttribute('data-theme', actualTheme);
  });

  // Listen for system theme changes
  if (window.matchMedia) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', (e) => {
      themePreference.update(preference => {
        if (preference === 'system') {
          const newTheme = e.matches ? 'dark' : 'light';
          theme.set(newTheme);
          document.documentElement.setAttribute('data-theme', newTheme);
        }
        return preference;
      });
    });
  }

  // Set initial theme on document
  document.documentElement.setAttribute('data-theme', getActualTheme(getInitialThemePreference()));
}

// Helper function to set theme preference
export function setThemePreference(preference) {
  if (['light', 'dark', 'system'].includes(preference)) {
    themePreference.set(preference);
  }
}

// Helper function to toggle theme (for backwards compatibility)
export function toggleTheme() {
  themePreference.update(current => {
    if (current === 'light') return 'dark';
    if (current === 'dark') return 'system';
    return 'light';
  });
}
