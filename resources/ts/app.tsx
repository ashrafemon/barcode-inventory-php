import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import AppProvider from './providers/AppProvider';

createInertiaApp({
	resolve: (name) => {
		const pages = import.meta.glob('./pages/**/*.tsx');
		return pages[`./pages/${name}.tsx`]();
	},
	setup({ el, App, props }) {
		createRoot(el).render(
			<AppProvider>
				<App {...props} />
			</AppProvider>,
		);
	},
	progress: {
		delay: 250,
		color: '#29b',
		includeCSS: true,
		showSpinner: true,
	},
});
