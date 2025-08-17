import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/scss/app.scss',
        'resources/scss/admin/admin.scss',
        'resources/scss/admin/admin-statistics.scss',
        'resources/scss/admin/admin-questions.scss',
        'resources/scss/admin/terms-of-service.scss',
        'resources/scss/auth/terms-of-service.scss',
        'resources/css/auth/auth.css',
        'resources/css/auth/forgot-password.css',
        'resources/css/auth/reset-password.css',
        'resources/css/auth/terms-of-service.css',
        'resources/css/auth/user-dashboard.css',
        'resources/css/auth/mobile-auth.css',
        'resources/css/admin/admin-users.css',
        'resources/css/admin/admin-dashboard.css',
        'resources/css/admin/admin-statistics.css',
        'resources/css/admin/admin-questions.css',
        'resources/css/admin/terms-of-service.css',
        'resources/css/mobile-responsive.css',
        'resources/css/mobile-utilities.css',
        'resources/css/pagination.css',
        'resources/js/app.js',
        'resources/js/admin/admin-users.js',
        'resources/js/admin/admin-dashboard.js',
        'resources/js/admin/admin-questions.js',
        'resources/js/admin/terms-of-service.js',
        'resources/js/auth/terms-of-service.js',
      ],
      refresh: true,
    }),
    tailwindcss(),
  ],
});
