#!/bin/bash
# update.sh — подтянуть изменения + перезапустить сервер
# Ключ читается из prototype/.env.local (не коммитится)
# Формат .env.local:  ANTHROPIC_API_KEY=sk-ant-...

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
BRANCH="claude/fix-directory-listing-4FgXg"
ENV_FILE="$SCRIPT_DIR/.env.local"

# Загружаем ключ из .env.local если он есть
if [ -f "$ENV_FILE" ]; then
  export $(grep -v '^#' "$ENV_FILE" | xargs)
fi

echo "📥  Обновляем код..."
git -C "$SCRIPT_DIR/.." fetch origin "$BRANCH"
git -C "$SCRIPT_DIR/.." reset --hard "origin/$BRANCH"

echo "🔄  Перезапускаем сервер на порту 8080..."
lsof -ti:8080 | xargs kill -9 2>/dev/null || true
sleep 0.3

if [ -z "$ANTHROPIC_API_KEY" ]; then
  echo "⚠️   ANTHROPIC_API_KEY не задан — запуск в demo-режиме"
  echo "     Создайте файл prototype/.env.local со строкой:"
  echo "     ANTHROPIC_API_KEY=sk-ant-..."
fi

python3 "$SCRIPT_DIR/server.py"
