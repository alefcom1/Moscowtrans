/* ============================================================
   РЕМАРКА — логика калькулятора документов (v2)
   Разделение логики: upload → OCR/parse → pricing → UI → order
   ============================================================ */
(function() {
'use strict';

/* ---------- 1. КОНФИГУРАЦИЯ ---------- */

// EmailJS — инициализируем лениво прямо перед отправкой, а не при загрузке скрипта.
// Если SDK загрузился через defer/async (WordPress добавляет это автоматически),
// вызов emailjs.init() при старте IIFE мог упасть в пустоту.
const EJS_PUBLIC_KEY  = 'qIHC--GaJ6MMVCOg5';
const EJS_SERVICE_ID  = 'service_htuz6bm';
const EJS_TEMPLATE_ID = 'template_zl1knyb';
let _ejsReady = false;

function ensureEmailjs() {
  if (typeof emailjs === 'undefined') {
    throw new Error('Библиотека EmailJS не загружена. Проверьте подключение к интернету и отключите блокировщик рекламы.');
  }
  if (!_ejsReady) {
    emailjs.init({ publicKey: EJS_PUBLIC_KEY });
    _ejsReady = true;
  }
}

/* ------------------------------------------------------------
   Загрузка файлов на сервер — конфигурация
   UPLOAD_ENDPOINT — URL PHP-скрипта (calc-upload.php в корне сайта)
   UPLOAD_TOKEN    — должен совпадать с UPLOAD_SECRET в PHP-файле
   ------------------------------------------------------------ */
const UPLOAD_ENDPOINT = '/calc-upload.php';
const UPLOAD_TOKEN    = 'rem-msc-2026';
const SITE_SOURCE     = 'Москва · moscowtrans.ru'; // ← то же, что в PHP

/**
 * Загружает массив File-объектов на сервер через PHP-обработчик.
 * Возвращает { files: [{name, url, size_fmt}], errors: [] }
 */
async function uploadFilesToServer(files, onProgress) {
  if (!files || !files.length) return { files: [], errors: [] };

  const fd = new FormData();
  fd.append('token', UPLOAD_TOKEN);
  files.forEach(f => fd.append('files[]', f, f.name));

  onProgress && onProgress('Загружаем файлы на сервер…');

  const resp = await fetch(UPLOAD_ENDPOINT, {
    method: 'POST',
    body:   fd,
    // Намеренно НЕ выставляем Content-Type — браузер сам ставит
    // multipart/form-data с правильным boundary
  });

  if (!resp.ok) {
    const txt = await resp.text().catch(() => '');
    throw new Error(`Сервер вернул HTTP ${resp.status}: ${txt.slice(0, 200)}`);
  }

  const json = await resp.json();
  return {
    files:  json.files  || [],
    errors: json.errors || [],
  };
}

/* ------------------------------------------------------------
   PDF.js — динамический загрузчик с fallback-CDN.

   Почему так:
   1) Плагин минификации WordPress (Remarka) переписывает <script src>
      для cdnjs и может ломать URL unpkg — видим это по ошибкам
      "Cannot load script at: /wp-content/cache/min/1/ajax/libs/..."
      и "pdfjsLib is not defined".
   2) Динамический createElement('script') в рантайме, создаваемый
      уже после минификатора, обходит его статическое сканирование.
   3) Если первый CDN не ответил — пробуем следующий; если вообще
      никак — пишем пользователю «загрузите DOCX / фото вместо PDF».

   Порядок CDN выбран так: jsdelivr даёт явный UMD-билд, где
   библиотека выставляет глобал window.pdfjsLib, в отличие от unpkg@3.x,
   который по /build/pdf.min.js редиректит на ESM-модуль и глобал не
   появляется — именно так и возникла ошибка «pdfjsLib is not defined».
   ------------------------------------------------------------ */
const PDFJS_VER = '3.11.174';
// Каждый элемент — пара { lib, worker }. Важно, чтобы lib и worker
// брались из одной и той же сборки, иначе версия не сойдётся.
const PDFJS_CDNS = [
  {
    lib:    'https://cdn.jsdelivr.net/npm/pdfjs-dist@' + PDFJS_VER + '/legacy/build/pdf.min.js',
    worker: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@' + PDFJS_VER + '/legacy/build/pdf.worker.min.js'
  },
  {
    lib:    'https://cdn.jsdelivr.net/npm/pdfjs-dist@' + PDFJS_VER + '/build/pdf.min.js',
    worker: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@' + PDFJS_VER + '/build/pdf.worker.min.js'
  },
  {
    // unpkg как последний шанс — форсируем UMD-билд из /legacy
    lib:    'https://' + 'unpkg.com' + '/pdfjs-dist@' + PDFJS_VER + '/legacy/build/pdf.min.js',
    worker: 'https://' + 'unpkg.com' + '/pdfjs-dist@' + PDFJS_VER + '/legacy/build/pdf.worker.min.js'
  }
];

// Промис, который резолвится глобалом window.pdfjsLib после первого успешного CDN.
// Кэшируем результат, чтобы повторные PDF не грузили библиотеку заново.
let _pdfjsPromise = null;

function loadScriptOnce(url, timeoutMs) {
  return new Promise((resolve, reject) => {
    const s = document.createElement('script');
    s.src = url;
    s.async = true;
    // data-minify="false" и data-no-optimize="true" — подсказки для популярных
    // WP-плагинов (Autoptimize, WP Rocket, LiteSpeed), чтобы не трогали тег.
    s.setAttribute('data-minify', 'false');
    s.setAttribute('data-no-optimize', 'true');
    s.setAttribute('data-cfasync', 'false');
    let done = false;
    const timer = setTimeout(() => {
      if (done) return; done = true;
      s.remove();
      reject(new Error('timeout: ' + url));
    }, timeoutMs || 12000);
    s.onload  = () => { if (done) return; done = true; clearTimeout(timer); resolve(url); };
    s.onerror = () => { if (done) return; done = true; clearTimeout(timer); s.remove(); reject(new Error('load failed: ' + url)); };
    document.head.appendChild(s);
  });
}

async function ensurePdfjs() {
  if (window.pdfjsLib && window.pdfjsLib.getDocument) return window.pdfjsLib;
  if (_pdfjsPromise) return _pdfjsPromise;

  _pdfjsPromise = (async () => {
    let lastErr;
    for (const cdn of PDFJS_CDNS) {
      try {
        await loadScriptOnce(cdn.lib);
        // Некоторые сборки экспортят в window.pdfjsLib, некоторые в window['pdfjs-dist/build/pdf']
        const lib = window.pdfjsLib
                 || window['pdfjs-dist/build/pdf']
                 || window['pdfjsDistBuildPdf'];
        if (!lib || !lib.getDocument) {
          throw new Error('pdfjsLib global not found after loading ' + cdn.lib);
        }
        window.pdfjsLib = lib; // нормализуем
        // Ставим worker из того же CDN. Если worker не загрузится — pdf.js
        // сам упадёт в "fake worker" (тот же main thread), но покажет warning —
        // мы это переловим в extractFile через { disableWorker: true }.
        lib.GlobalWorkerOptions.workerSrc = cdn.worker;
        return lib;
      } catch (e) {
        console.warn('PDF.js CDN не сработал:', cdn.lib, e);
        lastErr = e;
      }
    }
    _pdfjsPromise = null; // сбросим, чтобы можно было попробовать ещё раз позже
    throw new Error('Не удалось загрузить PDF.js ни с одного CDN. ' + (lastErr?.message || ''));
  })();

  return _pdfjsPromise;
}

// Цены за страницу по категориям
const PRICE = { main:800, rare:1200, exotic:1800 };

// Цены за тип заверения (фиксированные доплаты к базе)
const CERT = {
  none:    { label:'без заверения',       price:0    },
  stamp:   { label:'с печатью бюро',      price:500  },
  notary:  { label:'нотариальное',         price:1200 }
};

/**
 * Полный список языков (с сайта moscowtrans.ru/katalog-yazykov-s-kotorymi-my-rabotaem/).
 * Каждый язык относится к одной из трёх ценовых категорий:
 *   - main   (основные европейские + СНГ)     → 800 ₽/стр
 *   - rare   (редкие европейские)             → 1200 ₽/стр
 *   - exotic (восточные, с другой графикой)    → 1800 ₽/стр
 *
 * code       — ISO 639-1 (для UI)
 * franc_code — ISO 639-3 (для библиотеки franc)
 */
const LANGS = [
  // ОСНОВНЫЕ + СНГ → 800 ₽
  { code:'ru', franc_code:'rus', name:'Русский',        cat:'main' },
  { code:'en', franc_code:'eng', name:'Английский',     cat:'main' },
  { code:'de', franc_code:'deu', name:'Немецкий',       cat:'main' },
  { code:'fr', franc_code:'fra', name:'Французский',    cat:'main' },
  { code:'it', franc_code:'ita', name:'Итальянский',    cat:'main' },
  { code:'es', franc_code:'spa', name:'Испанский',      cat:'main' },
  { code:'uk', franc_code:'ukr', name:'Украинский',     cat:'main' },
  { code:'be', franc_code:'bel', name:'Белорусский',    cat:'main' },
  { code:'kz', franc_code:'kaz', name:'Казахский',      cat:'main' },
  { code:'ky', franc_code:'kir', name:'Кыргызский',     cat:'main' },
  { code:'uz', franc_code:'uzb', name:'Узбекский',      cat:'main' },
  { code:'tg', franc_code:'tgk', name:'Таджикский',     cat:'main' },
  { code:'az', franc_code:'azj', name:'Азербайджанский',cat:'main' },
  { code:'hy', franc_code:'hye', name:'Армянский',      cat:'main' },
  { code:'ka', franc_code:'kat', name:'Грузинский',     cat:'main' },
  { code:'mo', franc_code:'ron', name:'Молдавский',     cat:'main' },
  { code:'ab', franc_code:'abk', name:'Абхазский',      cat:'main' },

  // РЕДКИЕ (европейские / менее популярные) → 1200 ₽
  { code:'pl', franc_code:'pol', name:'Польский',       cat:'rare' },
  { code:'cs', franc_code:'ces', name:'Чешский',        cat:'rare' },
  { code:'sk', franc_code:'slk', name:'Словацкий',      cat:'rare' },
  { code:'sl', franc_code:'slv', name:'Словенский',     cat:'rare' },
  { code:'hr', franc_code:'hrv', name:'Хорватский',     cat:'rare' },
  { code:'sr', franc_code:'srp', name:'Сербский',       cat:'rare' },
  { code:'bs', franc_code:'bos', name:'Боснийский',     cat:'rare' },
  { code:'bg', franc_code:'bul', name:'Болгарский',     cat:'rare' },
  { code:'ro', franc_code:'ron', name:'Румынский',      cat:'rare' },
  { code:'hu', franc_code:'hun', name:'Венгерский',     cat:'rare' },
  { code:'pt', franc_code:'por', name:'Португальский',  cat:'rare' },
  { code:'nl', franc_code:'nld', name:'Нидерландский',  cat:'rare' },
  { code:'sv', franc_code:'swe', name:'Шведский',       cat:'rare' },
  { code:'da', franc_code:'dan', name:'Датский',        cat:'rare' },
  { code:'no', franc_code:'nob', name:'Норвежский',     cat:'rare' },
  { code:'fi', franc_code:'fin', name:'Финский',        cat:'rare' },
  { code:'et', franc_code:'ekk', name:'Эстонский',      cat:'rare' },
  { code:'lv', franc_code:'lav', name:'Латышский',      cat:'rare' },
  { code:'lt', franc_code:'lit', name:'Литовский',      cat:'rare' },
  { code:'el', franc_code:'ell', name:'Греческий',      cat:'rare' },
  { code:'tr', franc_code:'tur', name:'Турецкий',       cat:'rare' },
  { code:'tk', franc_code:'tuk', name:'Туркменский',    cat:'rare' },

  // ЭКЗОТИЧЕСКИЕ → 1800 ₽
  { code:'ar', franc_code:'arb', name:'Арабский',           cat:'exotic' },
  { code:'he', franc_code:'heb', name:'Иврит',              cat:'exotic' },
  { code:'fa', franc_code:'pes', name:'Персидский (фарси)', cat:'exotic' },
  { code:'hi', franc_code:'hin', name:'Хинди',              cat:'exotic' },
  { code:'zh', franc_code:'cmn', name:'Китайский',          cat:'exotic' },
  { code:'ja', franc_code:'jpn', name:'Японский',           cat:'exotic' },
  { code:'ko', franc_code:'kor', name:'Корейский',          cat:'exotic' },
  { code:'vi', franc_code:'vie', name:'Вьетнамский',        cat:'exotic' },
  { code:'th', franc_code:'tha', name:'Тайский',            cat:'exotic' },
  { code:'id', franc_code:'ind', name:'Индонезийский',      cat:'exotic' },
  { code:'ms', franc_code:'zsm', name:'Малайский',          cat:'exotic' },
  { code:'mn', franc_code:'khk', name:'Монгольский',        cat:'exotic' },
];

// Быстрый поиск по ISO-3 (для franc) и по code (для UI)
const LANG_BY_FRANC = {};
const LANG_BY_CODE  = {};
LANGS.forEach(l => {
  LANG_BY_FRANC[l.franc_code] = l;
  LANG_BY_CODE[l.code] = l;
});

/* ---------- 2. СОСТОЯНИЕ ---------- */

// Каждый документ — отдельный объект, живущий в этом массиве.
// Это источник правды, UI рендерится по нему.
let documents = []; // {id, name, size, files:File[], chars, pages, srcLang, tgtLang, cert, status, error}
let urgency   = 'standard'; // standard | urgent | express
let nextId    = 1;

/* ---------- 3. DOM-хелперы ---------- */

const $  = (s, root) => (root || document).querySelector(s);
const $$ = (s, root) => Array.from((root || document).querySelectorAll(s));

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c =>
    ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function notif(msg, type) {
  const el = $('#rem-notif');
  el.textContent = msg;
  el.className = 'rem-notif show ' + (type || '');
  clearTimeout(notif._t);
  notif._t = setTimeout(() => el.classList.remove('show'), 4500);
}

/* ---------- 4. ДАТЫ ДЛЯ БЛОКА СРОЧНОСТИ ---------- */

// Форматирует дату в «20.04.2026»
function fmtDate(d) {
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  return `${dd}.${mm}.${d.getFullYear()}`;
}

/**
 * Считаем срок выполнения (в днях от сегодня) в зависимости от режима и объёма.
 *   standard: базово +2 дня; если >8 стр — добавляем по 1 дню за каждые 8 стр сверх.
 *   urgent:   базово +1 день (завтра). Если >10 стр — срок = ceil(pages/10) дней.
 *   express:  базово сегодня. Если >15 стр — срок = ceil(pages/15) дней.
 * Возвращает { days, time }
 *   days = сколько целых суток от сегодня нужно прибавить
 *   time = время готовности ('16:00' / '19:00')
 */
function calcDuration(urgency, pages) {
  pages = Math.max(1, pages || 1);
  if (urgency === 'standard') {
    // +2 дня + 1 день за каждые полные 8 страниц сверх первых 8
    const extra = pages > 8 ? Math.floor((pages - 1) / 8) : 0;
    return { days: 2 + extra, time: '16:00' };
  }
  if (urgency === 'urgent') {
    const days = pages > 10 ? Math.ceil(pages / 10) : 1;
    return { days, time: '16:00' };
  }
  // express
  const days = pages > 15 ? Math.ceil(pages / 15) : 0;
  return { days, time: '19:00' };
}

/**
 * Множитель к стандартной цене в зависимости от режима и объёма.
 *   standard: ×1
 *   urgent:   ×1.5 если >10 страниц, иначе ×1
 *   express:  ×2 всегда
 */
function urgMultiplier(urgency, pages) {
  if (urgency === 'urgent')  return pages > 10 ? 1.5 : 1;
  if (urgency === 'express') return 2;
  return 1;
}

// Форматированная метка срочности для UI («20.04.2026 до 16:00 МСК»)
function urgLabel(urgency, pages) {
  const { days, time } = calcDuration(urgency, pages);
  const d = new Date();
  d.setDate(d.getDate() + days);
  return `${fmtDate(d)} до ${time} МСК`;
}

// Перерисовывает подписи и ценники на кнопках срочности — срок и наценка
// зависят от суммарного объёма заказа, поэтому дергаем при каждом recalc.
function renderUrgDates() {
  const totalPages = documents
    .filter(d => d.status === 'ready')
    .reduce((s,d) => s + d.pages, 0) || 1;

  // Стандарт
  const std = calcDuration('standard', totalPages);
  const stdD = new Date(); stdD.setDate(stdD.getDate() + std.days);
  $('#rem-urg-d-standard').textContent = `${fmtDate(stdD)} до ${std.time}`;

  // Срочно
  const urg = calcDuration('urgent', totalPages);
  const urgD = new Date(); urgD.setDate(urgD.getDate() + urg.days);
  $('#rem-urg-d-urgent').textContent   = `${fmtDate(urgD)} до ${urg.time}`;
  const urgMult = urgMultiplier('urgent', totalPages);
  $('#rem-urg-p-urgent').textContent   = urgMult === 1 ? 'без доплаты' : `цена ×${urgMult}`;

  // Экспресс
  const exp = calcDuration('express', totalPages);
  const expD = new Date(); expD.setDate(expD.getDate() + exp.days);
  $('#rem-urg-d-express').textContent  = `${fmtDate(expD)} до ${exp.time}`;
}

/* ---------- 5. ОПРЕДЕЛЕНИЕ ЯЗЫКА ---------- */

function detectLang(text) {
  if (!text || text.trim().length < 20) return 'ru'; // fallback
  try {
    // franc-min возвращает ISO 639-3 код
    const fr = (typeof franc !== 'undefined') ? franc(text, { minLength: 10 }) : 'und';
    if (fr === 'und') return 'ru';
    const lang = LANG_BY_FRANC[fr];
    return lang ? lang.code : 'ru';
  } catch(e) {
    return 'ru';
  }
}

/* ---------- 6. ИЗВЛЕЧЕНИЕ ТЕКСТА ИЗ ФАЙЛОВ ---------- */

// Универсальный ридер → вернёт { text, pageHint } для одного файла.
// pageHint — приблизительное кол-во «листов» в файле (для PDF/картинок),
// НО итоговый подсчёт страниц всё равно ведётся по символам (1800 зн. = 1 стр).
async function extractFile(file, onProgress) {
  const ext = (file.name.split('.').pop() || '').toLowerCase();
  onProgress && onProgress(`Обработка: ${file.name}`, 0);

  // Plain text
  if (ext === 'txt' || ext === 'rtf' || ext === 'odt') {
    const txt = await file.text();
    // rtf/odt без спец. парсера — хотя бы грубо очистим от тегов
    const clean = ext === 'rtf'
      ? txt.replace(/\\[a-z]+-?\d* ?|[{}]/gi,' ').replace(/\s+/g,' ')
      : txt;
    return { text: clean };
  }

  // DOCX
  if (ext === 'docx' || ext === 'doc') {
    if (ext === 'doc') {
      // старый .doc — mammoth не умеет; сообщим, что нужна ручная оценка
      throw new Error('Старый формат .doc не поддерживается для авто-анализа. Сохраните файл как .docx');
    }
    const ab = await file.arrayBuffer();
    const res = await mammoth.extractRawText({ arrayBuffer: ab });
    return { text: res.value || '' };
  }

  // PDF
  if (ext === 'pdf') {
    onProgress && onProgress('Загружаем модуль PDF...', 0);
    // Ленивая загрузка pdf.js — только при первом PDF-файле, с fallback по CDN.
    const lib = await ensurePdfjs();
    const ab = await file.arrayBuffer();
    let pdf;
    try {
      // Первая попытка — с worker (быстрее, в отдельном потоке)
      pdf = await lib.getDocument({ data: ab }).promise;
    } catch (err) {
      // Типичные ошибки wp-минификатора: "Setting up fake worker failed",
      // "Cannot load script at...". Падаем на disableWorker — работает в main thread.
      console.warn('PDF worker недоступен, переключаемся на no-worker режим:', err);
      onProgress && onProgress('PDF: загрузка без worker...', 0);
      pdf = await lib.getDocument({ data: ab, disableWorker: true }).promise;
    }
    let fullText = '';
    for (let i = 1; i <= pdf.numPages; i++) {
      onProgress && onProgress(`PDF: страница ${i} / ${pdf.numPages}`,
                              Math.round(i / pdf.numPages * 100));
      const page = await pdf.getPage(i);
      const content = await page.getTextContent();
      fullText += content.items.map(it => it.str).join(' ') + '\n';
    }
    // Если PDF «сканерный» (текста почти нет) — запустим OCR первой страницы для оценки
    if (fullText.replace(/\s/g,'').length < 50) {
      onProgress && onProgress('PDF без текста — запускаем OCR...', 0);
      const ocrText = await ocrPdfAsImages(pdf, onProgress);
      return { text: ocrText };
    }
    return { text: fullText };
  }

  // Изображения → Tesseract
  if (['jpg','jpeg','png'].includes(ext)) {
    onProgress && onProgress('Распознаём текст (OCR)...', 0);
    const { data } = await Tesseract.recognize(file, 'rus+eng', {
      logger: m => {
        if (m.status === 'recognizing text') {
          onProgress && onProgress('OCR: распознавание', Math.round((m.progress||0)*100));
        }
      }
    });
    return { text: data.text || '' };
  }

  // XLSX / PPTX и прочее — без полного парсера, оценка по размеру
  // (грубая эвристика: ~0.5 символа на байт для офисных форматов)
  if (['xlsx','xls','pptx','ppt'].includes(ext)) {
    const estChars = Math.round(file.size * 0.3);
    return { text: '', estimatedChars: estChars };
  }

  throw new Error('Формат не поддерживается: .' + ext);
}

// OCR сканерного PDF: рендерим каждую страницу в canvas и прогоняем через Tesseract.
// Чтобы не перегружать браузер, обрабатываем максимум 5 первых страниц, остальные
// оцениваем по среднему количеству символов на странице.
async function ocrPdfAsImages(pdf, onProgress) {
  const limit = Math.min(pdf.numPages, 5);
  let text = '';
  for (let i = 1; i <= limit; i++) {
    onProgress && onProgress(`OCR PDF: стр. ${i} / ${limit}`,
                             Math.round(i / limit * 100));
    const page = await pdf.getPage(i);
    const viewport = page.getViewport({ scale: 1.5 });
    const canvas = document.createElement('canvas');
    canvas.width  = viewport.width;
    canvas.height = viewport.height;
    const ctx = canvas.getContext('2d');
    await page.render({ canvasContext: ctx, viewport }).promise;
    const { data } = await Tesseract.recognize(canvas, 'rus+eng');
    text += (data.text || '') + '\n';
  }
  // экстраполируем объём на все страницы
  if (pdf.numPages > limit && text.length > 0) {
    const avg = text.length / limit;
    const extra = Math.round(avg * (pdf.numPages - limit));
    // добавим пустой текст нужного размера — только для счётчика символов
    text += ' '.repeat(Math.max(0, extra));
  }
  return text;
}

/* ---------- 7. АНАЛИЗ ОДНОГО ДОКУМЕНТА ---------- */

// Документ может состоять из нескольких файлов (многостраничное фото/скан).
// Обрабатываем их последовательно и суммируем символы.
async function analyzeDoc(doc, uiUpdate) {
  doc.status = 'processing';
  uiUpdate();

  let totalText = '';
  let totalEstChars = 0;

  try {
    for (let i = 0; i < doc.files.length; i++) {
      const f = doc.files[i];
      const res = await extractFile(f, (msg, pct) => {
        doc._progressMsg = `${msg} ${pct ? '('+pct+'%)' : ''}`.trim();
        uiUpdate();
      });
      if (res.text) totalText += res.text + '\n';
      if (res.estimatedChars) totalEstChars += res.estimatedChars;
    }

    const chars = totalText.replace(/\s+/g,' ').trim().length || totalEstChars;
    if (!chars) throw new Error('Не удалось извлечь текст из файла.');

    doc.chars   = chars;
    doc.pages   = Math.max(1, Math.ceil(chars / 1800));
    doc.srcLang = detectLang(totalText);
    if (!doc.tgtLang || doc.tgtLang === doc.srcLang) {
      // если исходный — русский, по умолчанию переводим на английский, иначе на русский
      doc.tgtLang = (doc.srcLang === 'ru') ? 'en' : 'ru';
    }
    doc.cert   = doc.cert || 'none';
    doc.status = 'ready';
  } catch(err) {
    console.error(err);
    doc.status = 'error';
    doc.error  = err.message || 'Ошибка обработки файла';
  }

  uiUpdate();
  recalcAll();
}

/* ---------- 8. ЦЕНЫ ---------- */

function pricePerPage(tgtCode) {
  const lang = LANG_BY_CODE[tgtCode];
  if (!lang) return PRICE.main;
  return PRICE[lang.cat] || PRICE.main;
}

// Расчёт стоимости перевода одного документа (без срочности, срочность — множитель к итогу).
function calcDoc(doc) {
  if (!doc.pages || !doc.tgtLang) return 0;
  const base = doc.pages * pricePerPage(doc.tgtLang);
  const certPrice = CERT[doc.cert || 'none'].price;
  return base + certPrice;
}

// Итоговая сумма по всем документам с учётом срочности.
function calcTotal() {
  const ready = documents.filter(d => d.status === 'ready');
  const sum = ready.reduce((s, d) => s + calcDoc(d), 0);
  const totalPages = ready.reduce((s, d) => s + d.pages, 0);
  return Math.round(sum * urgMultiplier(urgency, totalPages));
}

/* ---------- 9. РЕНДЕР ---------- */

function renderLangOptions(selected) {
  return LANGS.map(l =>
    `<option value="${l.code}"${l.code === selected ? ' selected' : ''}>${l.name}</option>`
  ).join('');
}

function renderDocs() {
  const list = $('#rem-docs-list');
  list.innerHTML = '';

  documents.forEach(doc => {
    const el = document.createElement('div');
    el.className = 'doc-card';
    el.dataset.id = doc.id;

    const headHtml = `
      <div class="doc-head">
        <div class="d-name">
          <span class="d-ico">📄</span>
          <span class="d-nm" title="${escapeHtml(doc.name)}">${escapeHtml(doc.name)}</span>
        </div>
        <span class="d-size">${(doc.size/1024).toFixed(0)} КБ${
          doc.files.length>1 ? ` · ${doc.files.length} стр.` : ''
        }</span>
        <button type="button" class="d-rm" data-act="remove" data-id="${doc.id}" aria-label="Удалить">✕</button>
      </div>`;

    if (doc.status === 'processing') {
      el.innerHTML = headHtml + `
        <div class="doc-processing">
          <div class="spinner"></div>
          <div>Анализируем документ…</div>
          <div class="p-progress">${escapeHtml(doc._progressMsg || '')}</div>
        </div>`;
    } else if (doc.status === 'error') {
      el.innerHTML = headHtml + `
        <div class="doc-error">⚠ ${escapeHtml(doc.error || 'Ошибка')}</div>`;
    } else if (doc.status === 'ready') {
      const pricePP = pricePerPage(doc.tgtLang);
      const subtotal = calcDoc(doc);
      el.innerHTML = headHtml + `
        <div class="doc-body">
          <div class="doc-stats">
            Документ содержит: <strong>${doc.chars.toLocaleString('ru')}</strong> символов с пробелами =
            <strong>${doc.pages}</strong> стандартных ${plural(doc.pages,'страница','страницы','страниц')}
            <div class="st-note">1 страница = 1 800 знаков с пробелами, минимальный заказ — 1 страница.</div>
          </div>

          <div class="doc-row">
            <div class="field">
              <label>Язык оригинала <span class="auto-mark">(определён автоматически)</span></label>
              <select data-act="src" data-id="${doc.id}">${renderLangOptions(doc.srcLang)}</select>
            </div>
            <div class="field">
              <label>Язык перевода</label>
              <select data-act="tgt" data-id="${doc.id}">${renderLangOptions(doc.tgtLang)}</select>
            </div>
          </div>

          <div class="cert-row">
            ${['none','stamp','notary'].map(k => `
              <button type="button" class="cert-btn${doc.cert === k ? ' sel' : ''}"
                      data-act="cert" data-id="${doc.id}" data-cert="${k}">
                <div class="c-title">${
                  k==='none'  ? 'Без заверения' :
                  k==='stamp' ? 'С печатью бюро' :
                                'Нотариальный'
                }</div>
                <div class="c-price">${
                  CERT[k].price === 0 ? '0 ₽' : '+' + CERT[k].price + ' ₽'
                }</div>
              </button>
            `).join('')}
          </div>

          <div class="doc-subtotal">
            <span>${doc.pages} ${plural(doc.pages,'стр.','стр.','стр.')} × ${pricePP.toLocaleString('ru')} ₽
            ${CERT[doc.cert].price ? `+ ${CERT[doc.cert].price} ₽ (${CERT[doc.cert].label})` : ''}</span>
            <strong>${subtotal.toLocaleString('ru')} ₽</strong>
          </div>
        </div>`;
    }

    list.appendChild(el);
  });

  // Показать шаги срочности / итого, если хоть один документ готов
  const anyReady = documents.some(d => d.status === 'ready');
  $('#rem-step-urg').classList.toggle('hidden', !anyReady);
  $('#rem-step-total').classList.toggle('hidden', !anyReady);
  $('#rem-step-docs').classList.toggle('hidden', documents.length === 0);
}

// простая функция «склонения» русских слов
function plural(n, one, few, many) {
  n = Math.abs(n) % 100;
  const n1 = n % 10;
  if (n > 10 && n < 20) return many;
  if (n1 > 1 && n1 < 5)  return few;
  if (n1 === 1)           return one;
  return many;
}

function renderTotal() {
  const total = calcTotal();
  const ready = documents.filter(d => d.status === 'ready');
  $('#rem-tp-val').textContent = total.toLocaleString('ru');

  if (!ready.length) {
    $('#rem-tp-detail').textContent = 'Загрузите документы для расчёта';
    $('#rem-tp-right').textContent = '—';
    return;
  }

  const totalPages = ready.reduce((s,d) => s + d.pages, 0);
  const mult = urgMultiplier(urgency, totalPages);
  const multLabel = mult === 1 ? '' :
    (urgency === 'express' ? ' · ×2 (экспресс)' : ` · ×${mult} (срочно, >10 стр)`);
  $('#rem-tp-detail').textContent =
    `${ready.length} ${plural(ready.length,'документ','документа','документов')} · ` +
    `${totalPages} ${plural(totalPages,'страница','страницы','страниц')}` +
    multLabel;

  $('#rem-tp-right').innerHTML = `
    <div>Готово: <strong style="color:#fff">${urgLabel(urgency, totalPages)}</strong></div>
  `;
}

function recalcAll() {
  renderDocs();
  // сроки и наценки зависят от суммарного объёма — обновляем при каждом пересчёте
  renderUrgDates();
  renderTotal();
}

/* ---------- 10. ОБРАБОТЧИКИ ---------- */

// Группировка «каждый File = отдельный документ», но если пользователь
// выбрал за раз несколько картинок — считаем, что это страницы одного документа
// (имя формируем общее). Можно было бы делать «один файл = один документ» —
// но ТЗ специально разрешает «несколько страниц одного документа вместе».
function addFilesAsDoc(fileList) {
  const files = Array.from(fileList);
  if (!files.length) return;

  // Картинки, выбранные пачкой, объединяем в один документ
  const images = files.filter(f => /\.(jpe?g|png)$/i.test(f.name));
  const rest   = files.filter(f => !/\.(jpe?g|png)$/i.test(f.name));

  if (images.length > 1) {
    const doc = createDoc(images, `Скан из ${images.length} страниц`);
    documents.push(doc);
    analyzeDoc(doc, recalcAll);
  } else if (images.length === 1) {
    const doc = createDoc(images, images[0].name);
    documents.push(doc);
    analyzeDoc(doc, recalcAll);
  }

  // Остальные файлы — каждый отдельный документ
  rest.forEach(f => {
    const doc = createDoc([f], f.name);
    documents.push(doc);
    analyzeDoc(doc, recalcAll);
  });

  // Раскрываем шаги
  $('#rem-step-docs').classList.remove('hidden');
  recalcAll();
}

function createDoc(files, name) {
  return {
    id: nextId++,
    name,
    size: files.reduce((s,f) => s + f.size, 0),
    files,
    chars: 0, pages: 0,
    srcLang: 'ru', tgtLang: 'en',
    cert: 'none',
    status: 'processing',
    error: null,
    _progressMsg: ''
  };
}

// Upload zone (шаг 1): клик + drag&drop
const uz = $('#rem-uz');
const finp = $('#rem-finp');
uz.addEventListener('click', (e) => {
  if (e.target.tagName !== 'INPUT') finp.click();
});
finp.addEventListener('change', () => {
  addFilesAsDoc(finp.files);
  finp.value = '';
});
['dragenter','dragover'].forEach(ev => uz.addEventListener(ev, (e) => {
  e.preventDefault(); uz.classList.add('drag');
}));
['dragleave','drop'].forEach(ev => uz.addEventListener(ev, (e) => {
  e.preventDefault(); uz.classList.remove('drag');
}));
uz.addEventListener('drop', (e) => {
  if (e.dataTransfer.files.length) addFilesAsDoc(e.dataTransfer.files);
});

// Добавить ещё документ (шаг 2)
const finpMore = $('#rem-finp-more');
finpMore.addEventListener('change', () => {
  addFilesAsDoc(finpMore.files);
  finpMore.value = '';
});

// Делегирование кликов внутри списка документов
$('#rem-docs-list').addEventListener('click', (e) => {
  const btn = e.target.closest('[data-act]');
  if (!btn) return;
  const id = +btn.dataset.id;
  const doc = documents.find(d => d.id === id);
  if (!doc) return;
  const act = btn.dataset.act;

  if (act === 'remove') {
    documents = documents.filter(d => d.id !== id);
    recalcAll();
    if (!documents.length) $('#rem-step-docs').classList.add('hidden');
  } else if (act === 'cert') {
    doc.cert = btn.dataset.cert;
    recalcAll();
  }
});
$('#rem-docs-list').addEventListener('change', (e) => {
  const sel = e.target.closest('select[data-act]');
  if (!sel) return;
  const id = +sel.dataset.id;
  const doc = documents.find(d => d.id === id);
  if (!doc) return;
  if (sel.dataset.act === 'src') doc.srcLang = sel.value;
  if (sel.dataset.act === 'tgt') doc.tgtLang = sel.value;
  recalcAll();
});

// Срочность
$('#rem-urg-row').addEventListener('click', (e) => {
  const btn = e.target.closest('.urg-btn');
  if (!btn) return;
  $$('#rem-urg-row .urg-btn').forEach(b => b.classList.remove('sel'));
  btn.classList.add('sel');
  urgency = btn.dataset.urg;
  renderTotal();
});

// Начать заново
$('#rem-btn-reset').addEventListener('click', () => {
  if (!confirm('Удалить все документы и начать заново?')) return;
  documents = [];
  urgency = 'standard';
  $$('#rem-urg-row .urg-btn').forEach(b => b.classList.toggle('sel', b.dataset.urg === 'standard'));
  recalcAll();
  $('#rem-step-docs').classList.add('hidden');
  $('#rem-step-urg').classList.add('hidden');
  $('#rem-step-total').classList.add('hidden');
});

// Оформить заказ — открыть модалку
$('#rem-btn-order').addEventListener('click', () => {
  const ready = documents.filter(d => d.status === 'ready');
  if (!ready.length) { notif('Загрузите хотя бы один документ', 'error'); return; }
  openOrderModal();
});

/* ---------- 11. МОДАЛКА ЗАКАЗА ---------- */

let orderDelivery = { type:'none', price:0, city:'' };

function buildSummaryHtml() {
  const ready = documents.filter(d => d.status === 'ready');
  const totalPages = ready.reduce((s,d) => s + d.pages, 0);
  const urgMult = urgMultiplier(urgency, totalPages);
  const urgL = urgLabel(urgency, totalPages);
  let html = '';
  ready.forEach((doc, idx) => {
    const src = LANG_BY_CODE[doc.srcLang]?.name || '—';
    const tgt = LANG_BY_CODE[doc.tgtLang]?.name || '—';
    const certText = doc.cert === 'none' ? 'без нотариального заверения'
                   : doc.cert === 'stamp' ? 'с печатью бюро'
                   : 'нотариальный перевод';
    const price = Math.round(calcDoc(doc) * urgMult);
    html += `
      <div class="ms-item">
        <div><strong>Документ ${idx+1}:</strong> ${escapeHtml(doc.name)}</div>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-top:2px">
          <div style="flex:1;color:#555">
            Перевод с ${src} на ${tgt} на ${urgL}, ${certText}
          </div>
          <div class="ms-price">${price.toLocaleString('ru')} ₽</div>
        </div>
      </div>`;
  });

  html += `<div class="ms-total">
    <span>Сумма:</span>
    <span class="ms-price">${calcTotal().toLocaleString('ru')} ₽</span>
  </div>`;
  return html;
}

function renderModalFinal() {
  const subTotal = calcTotal();
  const total = subTotal + (orderDelivery.price || 0);
  const note = orderDelivery.price ? `+ доставка ${orderDelivery.price} ₽` : '';
  $('#rem-m-final').textContent = total.toLocaleString('ru');
  $('#rem-m-final-note').textContent = note;
}

function openOrderModal() {
  orderDelivery = { type:'none', price:0, city:'' };
  $('#rem-m-summary').innerHTML = buildSummaryHtml();
  $$('#rem-m-delivery .del-opt').forEach(o => o.classList.toggle('sel', o.dataset.del === 'none'));
  renderModalFinal();
  $('#rem-modal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeOrderModal() {
  $('#rem-modal').classList.remove('show');
  document.body.style.overflow = '';
}

$('#rem-m-close').addEventListener('click', closeOrderModal);
$('#rem-modal').addEventListener('click', (e) => {
  if (e.target.id === 'rem-modal') closeOrderModal();
});

// Выбор способа доставки
$('#rem-m-delivery').addEventListener('click', (e) => {
  const opt = e.target.closest('.del-opt');
  if (!opt) return;
  $$('#rem-m-delivery .del-opt').forEach(o => o.classList.remove('sel'));
  opt.classList.add('sel');
  orderDelivery.type  = opt.dataset.del;
  orderDelivery.price = +opt.dataset.price || 0;
  $('#rem-m-summary').innerHTML = buildSummaryHtml();
  renderModalFinal();
});

// Отправка заявки
$('#rem-m-submit').addEventListener('click', async () => {
  const name    = $('#rem-m-name').value.trim();
  const phone   = $('#rem-m-phone').value.trim();
  const email   = $('#rem-m-email').value.trim();
  const company = $('#rem-m-company').value.trim();
  const fio     = $('#rem-m-fio').value.trim();
  const city    = $('#rem-m-city').value.trim();
  const comment = $('#rem-m-comment').value.trim();

  if (!name)  return notif('Укажите ваше имя', 'error');
  if (!phone) return notif('Укажите телефон', 'error');
  if (!email || !email.includes('@')) return notif('Укажите корректный e-mail', 'error');

  const ready      = documents.filter(d => d.status === 'ready');
  const totalPages = ready.reduce((s,d) => s + d.pages, 0);
  const urgMult    = urgMultiplier(urgency, totalPages);
  const urgL       = urgLabel(urgency, totalPages);

  const docsDesc = ready.map((doc, i) => {
    const src      = LANG_BY_CODE[doc.srcLang]?.name || '—';
    const tgt      = LANG_BY_CODE[doc.tgtLang]?.name || '—';
    const certText = CERT[doc.cert].label;
    const price    = Math.round(calcDoc(doc) * urgMult);
    return `Документ ${i+1}: ${doc.name}\n  ${doc.pages} стр. · ${src} → ${tgt} · ${certText}\n  Готово: ${urgL} · ${price.toLocaleString('ru')} ₽`;
  }).join('\n\n');

  const subTotal   = calcTotal();
  const finalTotal = subTotal + (orderDelivery.price || 0);

  const btn = $('#rem-m-submit');
  btn.disabled = true;

  // Убираем старый errBox
  btn.parentNode.querySelector('.rem-err-box')?.remove();

  /* ── ШАГ 1: загрузка файлов на сервер ── */
  let fileLinks = '';
  const allFiles = ready.flatMap(doc => doc.files);

  try {
    btn.textContent = `Загружаем файлы (${allFiles.length})…`;
    const { files: uploaded, errors: upErrors } = await uploadFilesToServer(allFiles);

    if (uploaded.length) {
      fileLinks = '\n\n─── ПРИКРЕПЛЁННЫЕ ФАЙЛЫ ───\n' +
        uploaded.map(f => `📎 ${f.name} (${f.size_fmt})\n   ${f.url}`).join('\n\n');
    }
    if (upErrors.length) {
      fileLinks += '\n\n⚠ Не загружены:\n' + upErrors.map(e => '  ' + e).join('\n');
    }
  } catch (upErr) {
    // Загрузка файлов не критична — логируем и едем дальше без ссылок
    console.warn('Ошибка загрузки файлов на сервер:', upErr);
    fileLinks = '\n\n⚠ Файлы не удалось загрузить автоматически: ' + (upErr.message || upErr);
  }

  /* ── ШАГ 2: формируем тело письма с учётом ссылок ── */
  const details = docsDesc +
    `\n\nСумма за перевод: ${subTotal.toLocaleString('ru')} ₽` +
    (orderDelivery.price ? `\nДоставка: +${orderDelivery.price} ₽` : '\nДоставка: без доставки / самовывоз') +
    (city    ? `\nГород: ${city}`                 : '') +
    (fio     ? `\nФИО на языке перевода: ${fio}`  : '') +
    `\nИТОГО: ${finalTotal.toLocaleString('ru')} ₽` +
    fileLinks;

  // Полный текст для mailto-fallback
  const orderText = [
    `ИМЯ: ${name}`,
    `ТЕЛЕФОН: ${phone}`,
    `EMAIL: ${email}`,
    company ? `КОМПАНИЯ: ${company}` : '',
    fio     ? `ФИО (язык перевода): ${fio}` : '',
    city    ? `ГОРОД: ${city}` : '',
    `ДОСТАВКА: ${orderDelivery.price ? `Курьер +${orderDelivery.price} ₽` : 'Без доставки / на e-mail'}`,
    '',
    details,
    comment ? `КОММЕНТАРИЙ: ${comment}` : ''
  ].filter(Boolean).join('\n');

  /* ── ШАГ 3: отправка через EmailJS ── */
  try {
    btn.textContent = 'Отправляем заявку…';
    ensureEmailjs();

    // Используем sendForm со скрытой формой.
    // sendForm умеет отправлять файлы-вложения — оставляем как запасной механизм,
    // но основные файлы уже ушли на сервер и ссылки включены в details.
    const form = document.createElement('form');
    form.style.cssText = 'display:none;position:absolute;left:-9999px;';

    const addField = (name, value) => {
      const inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = name; inp.value = value || '';
      form.appendChild(inp);
    };

    addField('to_email',   'alefcom1@gmail.com');
    addField('from_name',  name);
    addField('from_email', email);
    addField('reply_to',   email);
    addField('phone',      phone);
    addField('company',    company || '—');
    addField('calc_type',  'Перевод документов · ' + SITE_SOURCE);
    addField('price_est',  finalTotal.toLocaleString('ru') + ' ₽');
    addField('details',    details);
    addField('comment',    comment || 'нет');

    document.body.appendChild(form);
    try {
      await emailjs.sendForm(EJS_SERVICE_ID, EJS_TEMPLATE_ID, form);
    } finally {
      document.body.removeChild(form);
    }

    notif('✓ Заявка отправлена! Свяжемся в течение 30 минут.', 'success');
    btn.textContent = '✓ Заявка отправлена';
    setTimeout(() => {
      closeOrderModal();
      btn.disabled = false;
      btn.textContent = 'Отправить заявку';
    }, 1800);

  } catch(err) {
    console.error('EmailJS error:', err);
    const errMsg = err?.text || err?.message || String(err);
    const mailtoLink = `mailto:alefcom1@gmail.com?subject=${encodeURIComponent('Заявка на перевод с сайта')}&body=${encodeURIComponent(orderText)}`;

    const errBox = document.createElement('div');
    errBox.className = 'rem-err-box';
    errBox.style.cssText = 'margin-top:10px;padding:12px 14px;background:#FFF5F5;border:1px solid #F5C6CB;border-radius:8px;font-size:13px;color:#721c24;line-height:1.5;';
    errBox.innerHTML = `
      <strong>Не удалось отправить автоматически.</strong><br>
      Причина: <em>${escapeHtml(errMsg)}</em><br><br>
      <a href="${mailtoLink}" style="display:inline-block;margin-top:4px;padding:8px 16px;background:#C0392B;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;font-size:13px;">
        📧 Отправить письмо вручную
      </a>
      <span style="margin-left:10px;font-size:12px;color:#888;">или напишите на alefcom1@gmail.com</span>`;
    btn.insertAdjacentElement('afterend', errBox);
    btn.disabled = false;
    btn.textContent = 'Повторить отправку';
  }
});

/* ---------- 12. ИНИЦИАЛИЗАЦИЯ ---------- */

renderUrgDates();
recalcAll();

})();
