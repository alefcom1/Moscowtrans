/* ============================================================
   REMARKA — PRICING ENGINE v2.0
   Модуль: расчёт стоимости + чтение файлов
   1 стандартная страница = 1800 знаков с пробелами
   ============================================================ */

const PricingEngine = (() => {

  // ── КОНСТАНТЫ ───────────────────────────────────────────────
  const PAGE_SIZE = 1800; // знаков с пробелами

  // Базовые ставки за стандартную страницу (₽)
  const BASE_RATES = {
    mtpe:    350,   // Machine Translation Post-Editing
    human:   750,   // Профессиональный переводчик
    premium: 1350,  // Эксперт + носитель языка
  };

  // Мультипликаторы по предметной области
  const DOMAIN_MULT = {
    general:   1.00,
    technical: 1.30,
    legal:     1.45,
    medical:   1.45,
    it:        1.40,
    finance:   1.25,
    marketing: 1.15,
    literary:  1.20,
    patent:    1.55,
    certified: 1.60,
  };

  // Мультипликаторы срочности
  const URGENCY_MULT = {
    flexible: 0.90,   // гибкие сроки
    standard: 1.00,   // 3–7 дней
    urgent:   1.35,   // 1–2 дня
    express:  1.70,   // 24 часа
    superexp: 2.20,   // 4–6 часов
  };

  // Мультипликаторы языковой пары
  const LANG_MULT = {
    'ru-en': 1.00, 'en-ru': 1.00,
    'ru-de': 1.10, 'de-ru': 1.10,
    'ru-fr': 1.10, 'fr-ru': 1.10,
    'ru-it': 1.10, 'it-ru': 1.10,
    'ru-es': 1.10, 'es-ru': 1.10,
    'ru-zh': 1.55, 'zh-ru': 1.55,
    'ru-ja': 1.55, 'ja-ru': 1.55,
    'ru-ar': 1.50, 'ar-ru': 1.50,
    'ru-ko': 1.50, 'ko-ru': 1.50,
    'en-de': 1.05, 'de-en': 1.05,
    'en-fr': 1.05, 'fr-en': 1.05,
    'en-it': 1.05, 'it-en': 1.05,
    'en-zh': 1.50, 'zh-en': 1.50,
    'en-ja': 1.50, 'ja-en': 1.50,
    'default': 1.30,
  };

  // Доп. мультипликаторы
  const SEO_MULT        = { none: 1.00, basic: 1.15, full: 1.30 };
  const FORMATTING_MULT = { none: 1.00, basic: 1.10, full: 1.20, complex: 1.35 };
  const COMPLEXITY_MULT = { simple: 0.90, standard: 1.00, complex: 1.20, highly_complex: 1.40 };

  // Скидки за объём
  const VOLUME_DISCOUNTS = [
    { minPages: 100, discount: 0.15 },
    { minPages:  50, discount: 0.12 },
    { minPages:  20, discount: 0.08 },
    { minPages:  10, discount: 0.05 },
    { minPages:   1, discount: 0.00 },
  ];

  // Сроки выполнения (стр/день) по тарифу
  const PAGES_PER_DAY = { mtpe: 30, human: 10, premium: 6 };

  // ── РАСЧЁТ СТОИМОСТИ ────────────────────────────────────────

  /**
   * Рассчитать стоимость для одного тарифа
   * @param {string} tariff — 'mtpe' | 'human' | 'premium'
   * @param {object} slots  — все параметры заказа
   * @returns {object}
   */
  function calcOne(tariff, slots) {
    const {
      chars     = 0,
      pages     = 0,
      langPair  = 'ru-en',
      domain    = 'general',
      urgency   = 'standard',
      seo       = 'none',
      formatting = 'none',
      complexity = 'standard',
    } = slots;

    // Определяем количество страниц
    const totalPages = pages > 0 ? pages
      : chars > 0 ? Math.ceil(chars / PAGE_SIZE)
      : 1;

    const base   = BASE_RATES[tariff];
    const lm     = LANG_MULT[langPair] ?? LANG_MULT['default'];
    const dm     = DOMAIN_MULT[domain]     ?? 1.00;
    const um     = URGENCY_MULT[urgency]   ?? 1.00;
    const sm     = SEO_MULT[seo]           ?? 1.00;
    const fm     = FORMATTING_MULT[formatting] ?? 1.00;
    const cm     = COMPLEXITY_MULT[complexity] ?? 1.00;

    // Скидка за объём
    const vd = VOLUME_DISCOUNTS.find(v => totalPages >= v.minPages)?.discount ?? 0;
    const volumeCoeff = 1 - vd;

    const perPage = Math.round(base * lm * dm * um * sm * fm * cm * volumeCoeff);
    const total   = perPage * totalPages;

    // Срок выполнения
    const ppd = PAGES_PER_DAY[tariff];
    let days  = Math.ceil(totalPages / ppd);
    if (urgency === 'express')  days = Math.max(days, 1);
    if (urgency === 'urgent')   days = Math.max(days, 2);
    if (urgency === 'standard') days = Math.max(days, 3);

    // Дедлайн
    const deadline = new Date();
    deadline.setDate(deadline.getDate() + days);
    const deadlineStr = deadline.toLocaleDateString('ru-RU', { day:'numeric', month:'long' });

    return {
      tariff,
      totalPages,
      totalChars: chars || totalPages * PAGE_SIZE,
      perPage,
      total,
      days,
      deadlineStr,
      volumeDiscount: vd > 0 ? `−${(vd * 100).toFixed(0)}%` : null,
      multipliers: { lm, dm, um, sm, fm, cm },
    };
  }

  /**
   * Рассчитать все три тарифа
   */
  function calculate(slots) {
    return {
      mtpe:    calcOne('mtpe',    slots),
      human:   calcOne('human',   slots),
      premium: calcOne('premium', slots),
    };
  }

  /**
   * Достаточно слотов для расчёта?
   */
  function isReady(slots) {
    return !!(
      (slots.chars > 0 || slots.pages > 0) &&
      slots.langPair &&
      slots.domain
    );
  }

  // ── FILE READER ─────────────────────────────────────────────

  /**
   * Прочитать файл и вернуть { text, chars, pages, fileName, fileType }
   */
  async function readFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    const fileName = file.name;

    let text = '';
    let method = '';

    try {
      if (['txt', 'md', 'csv', 'html', 'htm', 'xml', 'json', 'srt'].includes(ext)) {
        text = await readAsText(file);
        method = 'text';
      } else if (['doc', 'docx', 'odt', 'rtf'].includes(ext)) {
        text = await readDocx(file);
        method = 'docx';
      } else if (ext === 'pdf') {
        text = await readPdf(file);
        method = 'pdf';
      } else if (['xlsx', 'xls', 'ods'].includes(ext)) {
        text = await readXlsx(file);
        method = 'xlsx';
      } else if (['pptx', 'ppt'].includes(ext)) {
        text = `[Презентация: ${fileName}. Подсчёт по слайдам недоступен в браузере. Укажите количество страниц вручную.]`;
        method = 'manual';
      } else {
        text = `[Файл: ${fileName}. Формат не поддерживается для автоподсчёта. Укажите объём вручную.]`;
        method = 'manual';
      }
    } catch (e) {
      text = '';
      method = 'error';
    }

    const chars = countChars(text);
    const pages = Math.ceil(chars / PAGE_SIZE) || 0;

    return { text, chars, pages, fileName, fileType: ext, method };
  }

  // Читает файл как plain text
  function readAsText(file) {
    return new Promise((res, rej) => {
      const r = new FileReader();
      r.onload  = e => res(e.target.result);
      r.onerror = () => rej(new Error('Read error'));
      r.readAsText(file, 'UTF-8');
    });
  }

  // Читает .docx через mammoth (CDN)
  async function readDocx(file) {
    if (typeof mammoth === 'undefined') {
      await loadScript('https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js');
    }
    const ab = await readAsArrayBuffer(file);
    const result = await mammoth.extractRawText({ arrayBuffer: ab });
    return result.value || '';
  }

  // Читает .pdf через pdf.js (CDN)
  async function readPdf(file) {
    if (typeof pdfjsLib === 'undefined') {
      await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
      pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }
    const ab  = await readAsArrayBuffer(file);
    const pdf = await pdfjsLib.getDocument({ data: ab }).promise;
    let text  = '';
    for (let i = 1; i <= pdf.numPages; i++) {
      const page    = await pdf.getPage(i);
      const content = await page.getTextContent();
      text += content.items.map(s => s.str).join(' ') + '\n';
    }
    return text;
  }

  // Читает .xlsx через SheetJS (CDN)
  async function readXlsx(file) {
    if (typeof XLSX === 'undefined') {
      await loadScript('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js');
    }
    const ab   = await readAsArrayBuffer(file);
    const wb   = XLSX.read(new Uint8Array(ab), { type: 'array' });
    let   text = '';
    wb.SheetNames.forEach(name => {
      const ws  = wb.Sheets[name];
      text += XLSX.utils.sheet_to_csv(ws) + '\n';
    });
    return text;
  }

  // Хелперы
  function readAsArrayBuffer(file) {
    return new Promise((res, rej) => {
      const r = new FileReader();
      r.onload  = e => res(e.target.result);
      r.onerror = () => rej(new Error('ArrayBuffer read error'));
      r.readAsArrayBuffer(file);
    });
  }

  function loadScript(src) {
    return new Promise((res, rej) => {
      const s = document.createElement('script');
      s.src = src; s.onload = res; s.onerror = rej;
      document.head.appendChild(s);
    });
  }

  /**
   * Подсчёт знаков с пробелами
   */
  function countChars(text) {
    if (!text) return 0;
    // Убираем лишние переносы, считаем всё включая пробелы
    return text.replace(/[\r\n]+/g, '\n').length;
  }

  /**
   * Форматирование числа в читаемый вид
   */
  function fmt(n) {
    return n.toLocaleString('ru-RU');
  }

  // ── ПУБЛИЧНЫЙ API ───────────────────────────────────────────
  return {
    calculate,
    calcOne,
    isReady,
    readFile,
    countChars,
    fmt,
    PAGE_SIZE,
    DOMAIN_MULT,
    URGENCY_MULT,
    LANG_MULT,
    BASE_RATES,
  };

})();

/* ── ЭКСПОРТ для модульной среды ── */
if (typeof module !== 'undefined') module.exports = PricingEngine;
