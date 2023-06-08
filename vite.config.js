import react from "@vitejs/plugin-react"
import { r3f } from "@react-three/editor/vite"

export default ({ command }) => ({
    base: command === 'serve' ? '' : '/build/',
    publicDir: 'fake_dir_so_nothing_gets_copied',
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
        },
    },
    plugins: [
      r3f(),
    ],
});
