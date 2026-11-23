#!/bin/bash
# Dragon Legend Hotel - Dừng máy chủ WordPress

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

if [ -f .server.pid ]; then
    PID=$(cat .server.pid)
    kill -9 $PID 2>/dev/null || true
    rm -f .server.pid
fi

lsof -ti:8000 | xargs kill -9 2>/dev/null || true
echo "🛑 Đã tắt máy chủ WordPress Dragon Legend Hotel."
