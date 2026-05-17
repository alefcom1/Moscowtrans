#!/usr/bin/env python3
"""
Прототип-сервер: статические файлы + прокси Claude API для чата.
Запуск из корня репо:
  python3 prototype/server.py
  ANTHROPIC_API_KEY=sk-ant-... python3 prototype/server.py
"""
import json
import os
import sys
from http.server import HTTPServer, SimpleHTTPRequestHandler
from pathlib import Path

SYSTEM_PROMPT = (
    "Ты Ольга, менеджер бюро переводов «Ремарка». "
    "Помогай клиентам с вопросами о профессиональных переводах. "
    "Отвечай кратко, дружелюбно и по-деловому на русском языке. "
    "Специализации бюро: технический, юридический, медицинский, "
    "IT-перевод, локализация сайтов. "
    "Цены: от 300–400 руб./стр. в зависимости от вида перевода. "
    "Если клиент хочет точный расчёт — предложи загрузить файл."
)

DEMO_REPLIES = [
    "Подскажите, какой документ вам нужно перевести и на какой язык?",
    "Для точного расчёта стоимости и сроков загрузите файл — я всё рассчитаю.",
    "Стандартный срок — 1–3 рабочих дня. Срочный заказ — в тот же день.",
    "С юридическими документами работаем с сертифицированными переводчиками, возможно нотариальное заверение.",
    "Работаем с более чем 20 языками, включая редкие. Уточните нужный язык — я проверю наличие специалиста.",
    "Стоимость технического перевода — от 400 руб./стр., IT и сайты — от 300 руб./стр.",
]

_demo_idx = 0


def check_deps():
    """Проверяет зависимости и выводит инструкции если чего-то не хватает."""
    try:
        import anthropic  # noqa: F401
        return True
    except ImportError:
        print("\n  ⚠️  Пакет 'anthropic' не установлен.")
        print("  Установите: pip3 install anthropic")
        print("  Сервер запустится в demo-режиме.\n")
        return False


class ChatHandler(SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=str(Path(__file__).parent), **kwargs)

    def do_OPTIONS(self):
        self.send_response(200)
        self._cors()
        self.end_headers()

    def do_POST(self):
        global _demo_idx
        if self.path != "/api/chat":
            self.send_error(404)
            return

        length = int(self.headers.get("Content-Length", 0))
        body = json.loads(self.rfile.read(length))
        messages = body.get("messages", [])

        api_key = os.environ.get("ANTHROPIC_API_KEY", "")
        if api_key and HAS_ANTHROPIC:
            reply = self._claude(messages, api_key)
        else:
            if api_key and not HAS_ANTHROPIC:
                print("[chat] ANTHROPIC_API_KEY задан, но пакет anthropic не установлен → demo-режим")
            reply = DEMO_REPLIES[_demo_idx % len(DEMO_REPLIES)]
            _demo_idx += 1

        self.send_response(200)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self._cors()
        self.end_headers()
        self.wfile.write(json.dumps({"text": reply}, ensure_ascii=False).encode())

    def _claude(self, messages, api_key):
        import anthropic
        try:
            client = anthropic.Anthropic(api_key=api_key)
            resp = client.messages.create(
                model="claude-sonnet-4-5",
                max_tokens=512,
                system=SYSTEM_PROMPT,
                messages=messages,
            )
            text = resp.content[0].text
            print(f"[chat] Claude ответил ({len(text)} символов)")
            return text
        except anthropic.AuthenticationError:
            print("[chat] ОШИБКА: неверный API-ключ. Проверьте ANTHROPIC_API_KEY.", file=sys.stderr)
            return "⚠️ Ошибка авторизации. Проверьте ANTHROPIC_API_KEY."
        except anthropic.RateLimitError:
            print("[chat] ОШИБКА: превышен лимит запросов.", file=sys.stderr)
            return "Слишком много запросов, попробуйте через минуту."
        except Exception as exc:
            print(f"[chat] ОШИБКА Claude API: {exc}", file=sys.stderr)
            return "Произошла ошибка при обращении к AI. Подробности в консоли сервера."

    def _cors(self):
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "POST, GET, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type")

    def log_message(self, fmt, *args):
        msg = args[0] if args else ""
        if "/api/chat" in msg:
            return  # логируем сами в _claude/_demo
        if not any(x in msg for x in [".css", ".js", ".png", ".svg", ".jpg", ".ico", ".woff"]):
            super().log_message(fmt, *args)


if __name__ == "__main__":
    HAS_ANTHROPIC = check_deps()

    api_key = os.environ.get("ANTHROPIC_API_KEY", "")
    if api_key:
        mode = "Claude API ✓" if HAS_ANTHROPIC else "⚠️  ключ есть, но anthropic не установлен → demo"
    else:
        mode = "demo-режим  (задайте ANTHROPIC_API_KEY для реального AI)"

    port = int(os.environ.get("PORT", 8080))

    # Передаём HAS_ANTHROPIC в пространство имён обработчика
    ChatHandler.do_POST.__globals__["HAS_ANTHROPIC"] = HAS_ANTHROPIC

    server = HTTPServer(("", port), ChatHandler)
    print(f"  Сервер:    http://localhost:{port}/")
    print(f"  Режим:     {mode}")
    print(f"  Стоп:      Ctrl+C\n")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nОстановлен.")
