import { writable } from 'svelte/store';

export const userData = writable(null);

// Active role store ('member' or 'creator')
export const activeRole = writable('member');