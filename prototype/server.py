#!/usr/bin/env python3
"""
Прототип-сервер: статические файлы + прокси Claude API для чата.
Запуск из корня репо:
  python3 prototype/server.py
  ANTHROPIC_API_KEY=sk-ant-... python3 prototype/server.py
"""
import json
import os
from functools import partial
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
        if api_key:
            reply = self._claude(messages, api_key)
        else:
            reply = DEMO_REPLIES[_demo_idx % len(DEMO_REPLIES)]
            _demo_idx += 1

        self.send_response(200)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self._cors()
        self.end_headers()
        self.wfile.write(json.dumps({"text": reply}, ensure_ascii=False).encode())

    def _claude(self, messages, api_key):
        try:
            import anthropic
            client = anthropic.Anthropic(api_key=api_key)
            resp = client.messages.create(
                model="claude-sonnet-4-6",
                max_tokens=512,
                system=SYSTEM_PROMPT,
                messages=messages,
            )
            return resp.content[0].text
        except Exception as exc:
            return f"Извините, произошла ошибка: {exc}"

    def _cors(self):
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "POST, GET, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type")

    def log_message(self, fmt, *args):
        msg = args[0] if args else ""
        if "/api/chat" in msg:
            print(f"[chat] {args[1]} {args[0][:60]}")
        elif not any(x in msg for x in [".css", ".js", ".png", ".svg", ".jpg", ".ico"]):
            super().log_message(fmt, *args)


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 8080))
    server = HTTPServer(("", port), ChatHandler)
    mode = "Claude API ✓" if os.environ.get("ANTHROPIC_API_KEY") else "demo-режим (нет ANTHROPIC_API_KEY)"
    print(f"  Сервер: http://localhost:{port}/  [{mode}]")
    print(f"  Прототип: http://localhost:{port}/")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nОстановлен.")
