import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.scss",
                "resources/js/app.js",
                "resources/js/lineup.js",
            ],
            refresh: true,
        }),
    ],

    css: {
        preprocessorOptions: {
            scss: {
                style: "compressed", // الحل
                silenceDeprecations: [
                    "import",
                    "global-builtin",
                    "color-functions",
                    "if-function",
                ],
            },
        },
    },

    build: {
        cssMinify: "lightningcss",

    },
});
