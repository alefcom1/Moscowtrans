#!/bin/bash
# Запуск dev-сервера с поддержкой чата Claude API
# Использование:
#   bash prototype/serve.sh                         # demo-режим
#   ANTHROPIC_API_KEY=sk-ant-... bash prototype/serve.sh  # с реальным AI
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO_ROOT"
echo "Запуск сервера прототипа..."
python3 prototype/server.py
