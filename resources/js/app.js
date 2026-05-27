import './bootstrap';
import '../css/app.css';
import Alpine from 'alpinejs';

const THEME_STORAGE_KEY = 'workflow-theme-preference';

const getStoredThemePreference = () => {
    const preference = window.localStorage.getItem(THEME_STORAGE_KEY);

    return ['light', 'dark', 'system'].includes(preference) ? preference : 'system';
};

const getSystemTheme = () =>
    window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

const playThemeTransition = (fromTheme, toTheme) => {
    if (!document.body || fromTheme === toTheme) {
        return;
    }

    document.getElementById('workflow-theme-transition')?.remove();

    const overlay = document.createElement('div');
    overlay.id = 'workflow-theme-transition';
    overlay.className = `theme-transition-overlay theme-transition-overlay--to-${toTheme}`;
    overlay.innerHTML = `
        <div class="theme-transition-stars"></div>
        <div class="theme-transition-glow"></div>
        <div class="theme-transition-sun"></div>
        <div class="theme-transition-moon"></div>
        <div class="theme-transition-horizon"></div>
    `;

    document.body.appendChild(overlay);

    window.requestAnimationFrame(() => {
        overlay.classList.add('is-active');
    });

    window.setTimeout(() => {
        overlay.classList.add('is-fading');
    }, 720);

    window.setTimeout(() => {
        overlay.remove();
    }, 1200);
};

const applyTheme = (effectiveTheme) => {
    const root = document.documentElement;

    root.classList.toggle('dark', effectiveTheme === 'dark');
    root.dataset.theme = effectiveTheme;
    root.style.colorScheme = effectiveTheme;
};

const dispatchThemeChange = (preference, { animate = false } = {}) => {
    const effectiveTheme = preference === 'system' ? getSystemTheme() : preference;
    const previousTheme = document.documentElement.dataset.theme
        || (document.documentElement.classList.contains('dark') ? 'dark' : 'light');

    if (animate) {
        playThemeTransition(previousTheme, effectiveTheme);
    }

    applyTheme(effectiveTheme);

    window.dispatchEvent(new CustomEvent('workflow-theme-changed', {
        detail: {
            preference,
            effectiveTheme,
        },
    }));
};

window.workflowTheme = {
    getPreference() {
        return getStoredThemePreference();
    },
    getEffectiveTheme() {
        const preference = getStoredThemePreference();

        return preference === 'system' ? getSystemTheme() : preference;
    },
    setPreference(preference) {
        const normalizedPreference = ['light', 'dark', 'system'].includes(preference)
            ? preference
            : 'system';

        window.localStorage.setItem(THEME_STORAGE_KEY, normalizedPreference);
        dispatchThemeChange(normalizedPreference, { animate: true });
    },
    sync() {
        dispatchThemeChange(getStoredThemePreference());
    },
};

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (getStoredThemePreference() === 'system') {
        dispatchThemeChange('system', { animate: true });
    }
});

window.workflowTheme.sync();

window.Alpine = Alpine;
Alpine.start();
