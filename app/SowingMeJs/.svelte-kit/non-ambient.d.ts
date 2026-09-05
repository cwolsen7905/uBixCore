
// this file is generated — do not edit it


declare module "svelte/elements" {
	export interface HTMLAttributes<T> {
		'data-sveltekit-keepfocus'?: true | '' | 'off' | undefined | null;
		'data-sveltekit-noscroll'?: true | '' | 'off' | undefined | null;
		'data-sveltekit-preload-code'?:
			| true
			| ''
			| 'eager'
			| 'viewport'
			| 'hover'
			| 'tap'
			| 'off'
			| undefined
			| null;
		'data-sveltekit-preload-data'?: true | '' | 'hover' | 'tap' | 'off' | undefined | null;
		'data-sveltekit-reload'?: true | '' | 'off' | undefined | null;
		'data-sveltekit-replacestate'?: true | '' | 'off' | undefined | null;
	}
}

export {};


declare module "$app/types" {
	export interface AppTypes {
		RouteId(): "/" | "/affiliates" | "/affiliates/banners" | "/confirm-email" | "/creator" | "/creator/dashboard" | "/creator/library" | "/creator/onboarding" | "/c" | "/c/[slug]" | "/explore" | "/forgot-password" | "/login" | "/reset-password" | "/settings";
		RouteParams(): {
			"/c/[slug]": { slug: string }
		};
		LayoutParams(): {
			"/": { slug?: string };
			"/affiliates": Record<string, never>;
			"/affiliates/banners": Record<string, never>;
			"/confirm-email": Record<string, never>;
			"/creator": Record<string, never>;
			"/creator/dashboard": Record<string, never>;
			"/creator/library": Record<string, never>;
			"/creator/onboarding": Record<string, never>;
			"/c": { slug?: string };
			"/c/[slug]": { slug: string };
			"/explore": Record<string, never>;
			"/forgot-password": Record<string, never>;
			"/login": Record<string, never>;
			"/reset-password": Record<string, never>;
			"/settings": Record<string, never>
		};
		Pathname(): "/" | "/affiliates" | "/affiliates/" | "/affiliates/banners" | "/affiliates/banners/" | "/confirm-email" | "/confirm-email/" | "/creator" | "/creator/" | "/creator/dashboard" | "/creator/dashboard/" | "/creator/library" | "/creator/library/" | "/creator/onboarding" | "/creator/onboarding/" | "/c" | "/c/" | `/c/${string}` & {} | `/c/${string}/` & {} | "/explore" | "/explore/" | "/forgot-password" | "/forgot-password/" | "/login" | "/login/" | "/reset-password" | "/reset-password/" | "/settings" | "/settings/";
		ResolvedPathname(): `${"" | `/${string}`}${ReturnType<AppTypes['Pathname']>}`;
		Asset(): "/logo.png" | "/robots.txt" | string & {};
	}
}