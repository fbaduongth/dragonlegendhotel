#!/bin/bash
# Dragon Legend Hotel - Khởi động máy chủ WordPress cục bộ

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

# Dừng tiến trình cũ nếu có trên cổng 8000
lsof -ti:8000 | xargs kill -9 2>/dev/null || true

echo "====================================================="
echo "🏨 Đang khởi động máy chủ Dragon Legend Hotel..."
echo "====================================================="

nohup ./bin/frankenphp php-server --listen 127.0.0.1:8000 --root . > server.log 2>&1 &
SERVER_PID=$!
echo $SERVER_PID > .server.pid

sleep 2

echo "🎉 Máy chủ đã sẵn sàng hoạt động!"
echo "👉 Trang chủ Website:  http://localhost:8000"
echo "👉 Trang Quản trị:     http://localhost:8000/wp-admin"
echo "🔑 Tài khoản:         admin / ********"
echo "====================================================="
