import { page } from '@vitest/browser/context';
import { describe, expect, it } from 'vitest';
import { render } from 'vitest-browser-svelte';
import Page from './+page.svelte';

describe('/c/[slug]/+page.svelte', () => {
	it('should render the creator display name as h1', async () => {
		// @ts-expect-error -- the page's generated $types data shape is validated at runtime
		render(Page, {
			props: {
				data: {
					profile: {
						slug: 'grace',
						displayName: 'Grace Chapel',
						bio: 'A church.',
						category: 'pastor',
						externalLinks: []
					}
				}
			}
		});

		const heading = page.getByRole('heading', { level: 1 });
		await expect.element(heading).toHaveTextContent('Grace Chapel');
	});
});
