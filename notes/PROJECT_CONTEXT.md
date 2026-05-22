# Бюро переводов «Ремарка» — moscowtrans.ru
> Полная документация проекта. Обновлять после каждой рабочей сессии.

---

## Реквизиты и контакты

| | |
|---|---|
| **Сайт** | moscowtrans.ru |
| **Компания** | ИП Волшина Елизавета Максимовна |
| **ИНН** | 231149349191 |
| **ОГРНИП** | 323237500359402 |
| **Офис** | Москва, Глинищевский пер., д. 6, оф. 2 (м. Охотный Ряд / Тверская) |
| **Телефон** | +7 (495) 970-44-13 |
| **WhatsApp** | +7 (985) 970-44-13 |
| **Email** | info@moscowtrans.ru |
| **Хостинг** | Рег.ру / per.py (server189.hosting.reg.ru) |
| **TMS** | tms.perevod4.ru |
| **Cloudflare Worker** | https://olga.alefcom1.workers.dev |

> ⚠️ **ВАЖНО:** alefcom1@gmail.com в calc-widget.js менять нельзя — это адрес EmailJS.

---

## Доступы

| Сервис | URL / логин |
|--------|-------------|
| WordPress admin | moscowtrans.ru/wp-admin |
| GitHub репо | github.com/alefcom1/Moscowtrans |
| Хостинг (файлы) | Менеджер файлов Рег.ру |
| TMS | tms.perevod4.ru |
| EmailJS | alefcom1@gmail.com |
| Яндекс.Метрика | счётчик 95836354 |

---

## Стек технологий

| Слой | Технология |
|------|-----------|
| CMS | WordPress (чистая установка, май 2026) |
| Тема | `remarka` — кастомная, без Gutenberg/Elementor |
| CSS | CSS-переменные (tokens.css) + styles.css |
| JS | Ванильный JS (megamenu.js, main.js, hero.js, chat.js, animations.js) |
| Калькулятор | calc-widget.js + calc-hero-content.js |
| AI-чат | Cloudflare Worker (olga.alefcom1.workers.dev) |
| Email | EmailJS (@emailjs/browser v4) |
| OCR | Tesseract.js v5 |
| DOCX | mammoth.js |
| Язык | franc-min |

---

## Тарифы

| Тип перевода | Цена за стр (1800 зн.) |
|---|---|
| Постредактирование ИИ (MTPE) | от 300 ₽ |
| Профессиональный (юр., техн., мед., IT) | от 490 ₽ |
| Премиум (с редактором / носителем) | от 790 ₽ |

**Срочный перевод:** доплата 25–50%  
**Нотариальное заверение и апостиль:** НЕ делаем

---

## Структура репозитория

```
/
├── prototype/               # Статический HTML-прототип (источник контента)
│   ├── index.html
│   ├── yuridicheskiy-perevod/   # 33 подстраницы юридического перевода
│   ├── tekhnicheskiy-perevod/   # 40 подстраниц технического перевода
│   ├── meditsinskiy-perevod/    # 39 подстраниц медицинского перевода
│   ├── it-perevod/              # 35 подстраниц IT-перевода
│   ├── css/
│   └── js/
├── wp-theme/
│   └── remarka/             # WordPress-тема (деплоится на сервер вручную)
│       ├── functions.php
│       ├── header.php
│       ├── footer.php
│       ├── front-page.php   # Главная страница
│       ├── home.php         # Страница блога (posts page)
│       ├── single.php       # Одиночный пост
│       ├── page.php         # Дефолтный шаблон страницы
│       ├── archive.php      # Архив/категории блога
│       ├── 404.php
│       ├── assets/
│       │   ├── css/
│       │   │   ├── tokens.css
│       │   │   ├── styles.css
│       │   │   ├── megamenu.css
│       │   │   └── calc-widget.css
│       │   ├── js/
│       │   │   ├── main.js
│       │   │   ├── megamenu.js
│       │   │   ├── hero.js
│       │   │   ├── chat.js
│       │   │   ├── animations.js
│       │   │   ├── calc-widget.js
│       │   │   └── calc-hero-content.js
│       │   └── images/
│       │       ├── logo-dark.png
│       │       ├── logo-light.png
│       │       ├── olga.jpg
│       │       ├── favicon.ico
│       │       ├── hero-bg.png
│       │       ├── hero-bg-light.png
│       │       ├── world-map-dots.svg
│       │       └── world-map-dots-light.svg
│       ├── inc/
│       │   ├── theme-setup.php
│       │   ├── enqueue.php
│       │   ├── post-types.php
│       │   ├── ajax.php
│       │   ├── seo.php
│       │   └── setup-pages.php  # Скрипт создания всех страниц
│       ├── page-templates/
│       │   ├── template-language.php   # Страница языка
│       │   ├── template-languages.php  # Каталог языков
│       │   ├── template-service.php    # Страница услуги
│       │   ├── template-pricing.php    # Стоимость перевода
│       │   ├── template-contact.php    # Контакты
│       │   ├── template-cases.php      # Кейсы
│       │   └── template-privacy.php    # Политика конфиденциальности
│       └── template-parts/
│           ├── hero-chat-window.php
│           ├── section-calc.php
│           ├── section-how-we-work.php
│           └── sidebar-chat.php
└── notes/
    └── PROJECT_CONTEXT.md   # Этот файл
```

---

## Git

| Ветка | Назначение |
|-------|-----------|
| `main` | Базовая |
| `claude/fix-directory-listing-4FgXg` | **Актуальная рабочая ветка** |

**Скачать архив темы:**
```
https://download-directory.github.io/?url=https://github.com/alefcom1/Moscowtrans/tree/claude/fix-directory-listing-4FgXg/wp-theme/remarka
```

---

## Социальные сети и внешние ссылки

| Платформа | URL |
|-----------|-----|
| VK | vk.com/bp_remarka |
| Telegram | t.me/massimoalef |
| YouTube | youtube.com/@alefcom1 |
| WhatsApp | wa.me/79859704413 |
| Max | max.ru/u/f9LHodD0cOIwtt2kTAD_gJn3zYxDjhwwJZSSxLByxFe-1BytFxz5bjvjN3s |
| Стать переводчиком | tms.perevod4.ru/register?role=translator |

---

## Плагины WordPress

| Плагин | Статус | Назначение |
|--------|--------|-----------|
| WP Fastest Cache | ✅ Установить | Кеширование |
| Wordfence Security | ✅ Установить | Защита |
| Rank Math / Yoast SEO | ✅ Установить | SEO |
| Smush / ShortPixel | Опционально | Сжатие изображений |
| Duplicate Post | Опционально | Дублирование страниц |
| UpdraftPlus | ✅ Установить | Автобэкапы |
| Elementor, Contact Form 7, Royal Addons | ❌ Не нужны | Старый хлам |

---

## Что сделано

### Тема Remarka
- [x] Полная кастомная WordPress-тема без page-builder
- [x] Тёмная/светлая тема (localStorage, по умолчанию тёмная)
- [x] Anti-FOUC скрипт в `<head>` (логотип не мигает)
- [x] Мегаменю (desktop hover, mobile accordion)
- [x] Адаптив (мобильное меню, бургер)
- [x] Калькулятор стоимости с загрузкой файлов (OCR, DOCX, PDF)
- [x] Чат с Ольгой (Cloudflare Worker AI)
- [x] Голосовой ввод (Web Speech API, RU/EN/IT)
- [x] EmailJS интеграция
- [x] Яндекс.Метрика
- [x] Schema.org JSON-LD (BreadcrumbList, Organization)
- [x] Страница 404 с навигацией
- [x] Страница контактов (карта, реквизиты, форма)
- [x] Страница стоимости (калькулятор)
- [x] Шаблон языковых страниц (34 языка)
- [x] Шаблон страниц услуг
- [x] Шаблон каталога языков
- [x] home.php для страницы блога
- [x] archive.php для категорий

### Скрипт создания страниц (`?remarka_setup_pages=1`)
- [x] 34 языковые страницы
- [x] 14 страниц услуг (с персональными приветствиями Ольги)
- [x] 5 утилитарных страниц (языки, стоимость, контакты, кейсы, политика)
- [x] Страница блога

### Деплой (май 2026)
- [x] Чистая установка WordPress
- [x] Тема `remarka` загружена и активирована
- [x] Все страницы созданы через setup-pages.php
- [x] Старые статические папки удалены с сервера
- [x] Старый `calc-upload.php` (дыра в безопасности) удалён

---

## Что нужно сделать

### Срочно (после установки WP)
- [ ] Настройки → Постоянные ссылки → «Название записи»
- [ ] Настройки → Чтение → Главная страница + Страница записей (Блог)
- [ ] Установить плагины (WP Fastest Cache, Wordfence, SEO, UpdraftPlus)
- [ ] Проверить все страницы на доступность

### Подстраницы услуг (147 страниц)
Все подстраницы уже написаны в `prototype/` с уникальным SEO-контентом.
Нужно добавить в WordPress как дочерние страницы:

| Раздел | Кол-во | Пример URL |
|--------|--------|-----------|
| Юридический перевод | 33 | `/yuridicheskiy-perevod/dogovory/` |
| Технический перевод | 40 | `/tekhnicheskiy-perevod/mashinostroenie/` |
| Медицинский перевод | 39 | `/meditsinskiy-perevod/kliniceskie-rukovodstva/` |
| IT-перевод | 35 | `/it-perevod/api-dokumentatsiya/` |

**Задача:** написать скрипт создания этих страниц с извлечением контента из HTML-прототипов.

### Контент
- [ ] Написать посты для блога (старые не импортируем)
- [ ] Добавить реальные фото к постам
- [ ] Заполнить кейсы

### SEO
- [ ] Прописать meta title/description для всех страниц
- [ ] Настроить Rank Math / Yoast
- [ ] Проверить sitemap.xml

### Технические
- [ ] Настроить автобэкапы (UpdraftPlus → облако)
- [ ] Настроить Wordfence
- [ ] Проверить скорость (PageSpeed Insights)

---

## Архитектура чата (chat.js + Cloudflare Worker)

**Файл:** `wp-theme/remarka/assets/js/chat.js`  
**Worker URL:** `https://olga.alefcom1.workers.dev` — не менять  
**EmailJS:** отправляет на `alefcom1@gmail.com` — не менять

**Функции чата:**
- Голосовой ввод (Web Speech API, 3 языка: RU/EN/IT)
- Загрузка файлов (DOCX через mammoth, изображения через Tesseract OCR)
- Определение языка (franc-min)
- Отправка результата через EmailJS

---

## Фирменный стиль (CSS-переменные из tokens.css)

| Переменная | Значение | Назначение |
|-----------|---------|-----------|
| `--brand-blue` | `#393185` | Основной синий |
| `--brand-cyan` | `#00A0F0` | Акцентный голубой |
| `--brand-purple` | `#783CF0` | Фиолетовый |
| `--header-h` | `80px` (desktop) / `64px` (mobile) | Высота хедера |

**Шрифты:** Inter (400/500/600/700) + Sora (600/700/800) — Google Fonts  
**Логотип:** `assets/images/logo-dark.png` (на тёмном фоне) / `logo-light.png` (на светлом)

---

## Известные особенности кода

- `main.js` → тема по умолчанию всегда `dark` (не авто по времени суток, хотя `getAutoTheme()` определена)
- Anti-FOUC: inline скрипт в `<head>` устанавливает `data-theme` до рендера CSS
- Логотип переключается через MutationObserver на `data-theme`
- `template-service.php` вызывает `the_content()` — если в БД есть старый контент, он отобразится
- `setup-pages.php` при повторном запуске обновляет существующие страницы (очищает `post_content`, переназначает шаблон)

---

## Запуск скрипта создания страниц

Выполнить в браузере (залогинившись как admin):
```
https://moscowtrans.ru/?remarka_setup_pages=1
```
Показывает отчёт: ✅ created / 🔄 updated / ❌ error  
Безопасно запускать повторно.

---

*Последнее обновление: 2026-05-22*
