// See https://svelte.dev/docs/kit/types#app.d.ts
// for information about these interfaces
declare global {
	namespace App {
		interface SessionUser {
			id?: number | null;
			displayName?: string | null;
			email?: string | null;
			firstName?: string | null;
			lastName?: string | null;
			creatorName?: string | null;
			roles?: string | null;
		}
		/** What the server layout load hands to the client and the `userData` store holds */
		interface UserData {
			user?: SessionUser | null;
			apiBaseUrl?: string;
			systemError?: string | null;
			[key: string]: unknown;
		}
		// interface Error {}
		interface Locals {
			authData?: UserData | null;
		}
		// interface PageData {}
		// interface PageState {}
		// interface Platform {}
	}
}

export {};
