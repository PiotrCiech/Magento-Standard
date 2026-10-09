import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';

const __dirname = path.dirname(fileURLToPath(import.meta.url)),
    magentoRoot = path.resolve(__dirname, '../src');

export default defineConfig({
  resolve: {
    alias: {
      '@magento': magentoRoot
    }
  },
  css: {
    preprocessorOptions: {
      scss: {
        // optional: additionalData: `@use "src/variables" as *;`
      },
      less: {
        javascriptEnabled: true
      }
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    cors: {
      origin: ['http://rokezzz-app.local', 'http://localhost', 'http://127.0.0.1'],
    },
    watch: {
      usePolling: true,
      interval: 100
    },
    hmr: {
      host: 'localhost',
      port: 5173
    }
  }
});
