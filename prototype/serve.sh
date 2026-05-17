#!/bin/bash
# Запуск сервера прототипа на порту 8080
# Перейти в папку prototype и запустить
cd "$(dirname "$0")"
nohup python3 -m http.server 8080 > /tmp/prototype-server.log 2>&1 &
echo "Сервер запущен: PID=$!, порт 8080"
echo "Логи: /tmp/prototype-server.log"
echo "Адрес: http://localhost:8080/"
