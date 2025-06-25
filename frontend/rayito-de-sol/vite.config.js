import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import { fileURLToPath, URL } from 'node:url'
import { viteStaticCopy } from 'vite-plugin-static-copy'

const isDev = process.env.NODE_ENV !== 'production'

export default defineConfig({
  plugins: [
    vue(),
    isDev && vueDevTools(),
    viteStaticCopy({
      targets: [
        {
          src: '_redirects',
          dest: '.'
        }
      ]
    })
  ].filter(Boolean),
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    watch: {
      ignored: [
        '**/node_modules/**',
        '**/vendor/**',
        '**/storage/**',
        '**/.git/**',
      ],
    },
    proxy: {
      '/api': 'http://localhost:8000',
      '/portal/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        rewrite: path => path.replace(/^\/portal\/api/, '/api'),
      },
      '/contact-support': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
