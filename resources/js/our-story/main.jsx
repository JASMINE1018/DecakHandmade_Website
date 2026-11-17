import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

import '../../css/app.css';
import './story.css';
import OurStoryPage from './OurStoryPage';

const config = window.__OUR_STORY_CONFIG__ ?? {};
const rootElement = document.getElementById('our-story-root');

if (rootElement) {
    createRoot(rootElement).render(
        <StrictMode>
            <OurStoryPage
                homeUrl={config.homeUrl ?? '/home'}
                catalogUrl={config.catalogUrl ?? '/catalog'}
                storyUrl={config.storyUrl ?? '/our-story'}
                testimonialUrl={config.testimonialUrl ?? '/home#testimoni'}
                assetUrl={config.assetUrl ?? '/'}
            />
        </StrictMode>,
    );
}
