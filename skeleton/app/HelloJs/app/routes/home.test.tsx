import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import Home from './home';

// The component reads parent loader data by route id; stub that rather than
// mounting a whole router for a render test.
vi.mock('react-router', () => ({
	useRouteLoaderData: () => ({ apiBaseUrl: 'http://localhost', apiReachable: true })
}));

describe('routes/home', () => {
	it('renders the heading', () => {
		render(<Home />);
		expect(screen.getByRole('heading', { level: 1 })).toHaveTextContent('Hello from React Router');
	});
});
