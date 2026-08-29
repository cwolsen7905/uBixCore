import { writable } from 'svelte/store';

/** @type {import('svelte/store').Writable<App.UserData | null>} */
export const userData = writable(null);

// Active role store ('member' or 'creator')
export const activeRole = writable('member');
