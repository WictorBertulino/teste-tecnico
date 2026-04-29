#!/bin/sh
set -e

cd /app

if [ ! -f node_modules/.bin/vite ] || [ ! -f node_modules/@tailwindcss/vite/package.json ]; then
  npm install
fi

npm run dev -- --host 0.0.0.0 --port 5173
