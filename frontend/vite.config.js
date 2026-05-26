import { defineConfig } from 'vite'

// Docker コンテナ内で動かす前提の設定
export default defineConfig({
  server: {
    host: true,         // 0.0.0.0 で待ち受け（ホストのブラウザからアクセスできるように）
    port: 5173,
    watch: {
      usePolling: true  // バインドマウント上の変更を検知（Mac / Windows でのライブリロード対策）
    },
    proxy: {
      // フロントから /api/... を叩くと php コンテナへ中継する（同一オリジン扱いになり CORS 不要）
      '/api': {
        target: 'http://php:80',
        changeOrigin: true
      }
    }
  }
})
