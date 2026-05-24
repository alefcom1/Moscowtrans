# Деплой RTAP на moscowtrans.ru (Рег.ру)

## Что нужно загрузить

Папку `wp-plugin/rtap/` целиком на сервер в:
```
/var/www/u1978717/data/www/moscowtrans.ru/wp-content/plugins/rtap/
```

## Способ 1 — Менеджер файлов Рег.ру (без SSH)

1. Зайти в панель Рег.ру → **Файловый менеджер**
2. Перейти в `/www/moscowtrans.ru/wp-content/plugins/`
3. Создать папку `rtap`
4. Загрузить все файлы из `wp-plugin/rtap/` в эту папку

> ⚠️ Папку `frontend/` со всем содержимым тоже загрузить —
> нужна только `frontend/dist/` (собранный бандл).
> Остальные файлы (src/, node_modules/) загружать не нужно.

## Что загружать (список нужных папок):

```
rtap/
├── rtap.php                     ✅ загрузить
├── includes/                    ✅ загрузить (все .php файлы)
├── admin/                       ✅ загрузить (все .php файлы)
├── assets/                      ✅ загрузить
├── data/                        ✅ загрузить (вопросы в JSON)
├── frontend/
│   └── dist/                    ✅ ТОЛЬКО эту папку загрузить!
│       ├── .vite/manifest.json
│       └── assets/
│           ├── index.js
│           ├── main.css
│           └── ... (остальные .js чанки)
└── deploy-config.php            ❌ НЕ загружать (там пароли)
```

## Способ 2 — Через ZIP (удобнее)

Скачать из GitHub репо архив папки `wp-plugin/rtap/`,
распаковать локально, загрузить через менеджер файлов.

## После загрузки в WordPress

1. **wp-admin → Плагины** → найти «RTAP» → **Активировать**
2. **wp-admin → RTAP → Настройки** → вставить TMS данные:
   - URL: `https://tms.perevod4.ru/api/v1/public/translators`
   - API Key: `tms_e3CdhEYae3uOxaji0IgBT-JXwDI3bOsxaJmQhEiGnXw`
3. **Настройки → Постоянные ссылки** → нажать «Сохранить» (без изменений)
   — это активирует URL `/test-perevodchika/`
4. **wp-admin → RTAP → Вопросы** → Импортировать JSON-файлы из папки `data/`

## Создание страниц тестов

После активации плагина создать 4 страницы WordPress:

| Страница | Шорткод |
|---|---|
| `/test-perevodchika/tekhnicheskiy/` | `[rtap_quiz topic="technical" lang="en"]` |
| `/test-perevodchika/yuridicheskiy/` | `[rtap_quiz topic="legal" lang="en"]` |
| `/test-perevodchika/meditsinskiy/`  | `[rtap_quiz topic="medical" lang="en"]` |
| `/test-perevodchika/it/`            | `[rtap_quiz topic="it" lang="en"]` |
| Главная или `/test-perevodchika/`   | `[rtap_quiz]` |
| На странице верификации             | `[rtap_verify]` |
| Виджет «Вопрос недели»              | `[rtap_qow]` |

## База данных

Плагин создаёт таблицы автоматически при активации.
База данных: `u1978717_Moscow_translator` (используется через wp-config.php WP автоматически).
