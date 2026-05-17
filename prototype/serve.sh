#!/bin/bash
# Запуск сервера прототипа на порту 8080
# Запускать из любого места — всегда встаёт в корень репозитория
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO_ROOT"
nohup python3 -m http.server 8080 > /tmp/prototype-server.log 2>&1 &
echo "Сервер запущен: PID=$!, порт 8080"
echo "Корень: $REPO_ROOT"
echo "Логи: /tmp/prototype-server.log"
echo "Адрес: http://localhost:8080/prototype/"
