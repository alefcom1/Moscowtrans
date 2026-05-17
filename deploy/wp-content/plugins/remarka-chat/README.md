# Remarka Chat — WordPress Plugin v2.0

## Структура плагина
```
remarka-chat/
├── remarka-chat.php              ← Bootstrap, активация, хелперы
├── includes/
│   ├── class-admin.php           ← Admin Panel (настройки, тарифы, сессии)
│   ├── class-ajax.php            ← WP Ajax handlers (GPT proxy, сессии, заказы, geo)
│   ├── class-shortcode.php       ← Шорткоды + enqueue assets
│   └── class-orders-cpt.php      ← Custom Post Type "Заказы"
├── assets/
│   ├── js/
│   │   ├── context.js            ← PageContext (детект страницы, приветствия)
│   │   ├── pricing.js            ← PricingEngine (расчёт + чтение файлов)
│   │   ├── ai.js                 ← IntentService, SlotExtractor, StateMachine, OpenAIAPI
│   │   ├── chat.js               ← ChatEngine (UI, voice, EmailJS)
│   │   └── wp-adapter.js         ← WP-адаптер (ajax override, сессии, sidebar)
│   └── css/
│       ├── chat.css              ← Стили чата (скопировать из index.html)
│       └── admin.css             ← Стили админки
└── templates/
    └── chat-widget.php           ← HTML разметка чата
```

## Установка
1. Скопировать папку `remarka-chat/` в `wp-content/plugins/`
2. Скопировать `assets/js/context.js`, `pricing.js`, `ai.js`, `chat.js` из основного проекта
3. Скопировать стили из `index.html` в `assets/css/chat.css` (заменив классы на `.remarka-*`)
4. Активировать плагин в WP Admin → Плагины
5. Настроить: WP Admin → Remarka Chat → Настройки

## Использование шорткода
```
[remarka_chat]
[remarka_chat context="technical"]
[remarka_chat context="legal"]
[remarka_chat_widget]
```

## Настройка EmailJS
1. Зарегистрироваться на emailjs.com
2. Создать service и два шаблона (order_template, callback_template)
3. Вставить Public Key в WP Admin → Remarka Chat → Настройки

## API Flow
Клиент → wp-adapter.js → WP Ajax (admin-ajax.php) → class-ajax.php → api/gpt.php → OpenAI
