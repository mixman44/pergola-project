import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/pergola/pergola-editor.js',
            refresh: true,
        }),
    ],
});
