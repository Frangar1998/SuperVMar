import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react-swc'
import fs from 'fs'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 443,
    allowedHosts: ['supervmar.es'],
    https: {
      key: fs.readFileSync('./supervmar.es-key.pem'),
      cert: fs.readFileSync('./supervmar.es.pem'),
    },
  },
})
