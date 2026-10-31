
import { reactRouter } from '@react-router/dev/vite';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [tailwindcss(), reactRouter()],
  resolve: {
    tsconfigPaths: true,
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    allowedHosts: [
      'curiosity-conference.ddev.site',
      'localhost',
      '.ngrok-free.app',
    ],
    hmr: {
      port: 5173,
      clientPort: 5173,
    },
  },
});
