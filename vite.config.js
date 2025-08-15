import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/scss/admin.scss',
                'resources/scss/admin-statistics.scss',
                'resources/scss/admin-questions.scss',
                'resources/css/auth.css', 
                'resources/css/forgot-password.css',
                'resources/css/admin-users.css',
                'resources/css/admin-dashboard.css',
                'resources/css/admin-statistics.css',
                'resources/css/admin-questions.css',
                'resources/js/app.js',
                'resources/js/admin-users.js',
                'resources/js/admin-dashboard.js',
                'resources/js/admin-questions.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
