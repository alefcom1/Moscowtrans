/* ============================================================
   REMARKA MODULE: document-checker.js v1.0
   Анализ загруженного документа через AI:
   • Определяет тип документа и язык
   • Оценивает сложность и специализацию
   • Определяет нужность нотариального заверения
   • Мгновенно рассчитывает стоимость
   • Интегрируется с ChatEngine и PricingEngine
   ============================================================ */

const DocumentChecker = (() => {
  'use strict';

  // Типы документов с параметрами
  const DOC_TYPES = {
    passport:        { label: 'Паспорт / удостоверение',    domain: 'legal',     notary: true,  mult: 1.0 },
    diploma:         { label: 'Диплом / аттестат',           domain: 'legal',     notary: true,  mult: 1.0 },
    birth_cert:      { label: 'Свидетельство о рождении',    domain: 'legal',     notary: true,  mult: 1.0 },
    marriage_cert:   { label: 'Свидетельство о браке',       domain: 'legal',     notary: true,  mult: 1.0 },
    contract:        { label: 'Договор / контракт',          domain: 'legal',     notary: false, mult: 1.4 },
    court_decision:  { label: 'Судебное решение',            domain: 'legal',     notary: true,  mult: 1.55 },
    power_attorney:  { label: 'Доверенность',                domain: 'legal',     notary: true,  mult: 1.3 },
    charter:         { label: 'Устав / учредительный',       domain: 'legal',     notary: false, mult: 1.4 },
    medical_record:  { label: 'Медицинская карта / выписка', domain: 'medical',   notary: false, mult: 1.4 },
    clinical_study:  { label: 'Клиническое исследование',    domain: 'medical',   notary: false, mult: 1.5 },
    drug_manual:     { label: 'Инструкция к препарату',      domain: 'medical',   notary: false, mult: 1.45 },
    tech_manual:     { label: 'Техническое руководство',     domain: 'technical', notary: false, mult: 1.3 },
    spec_sheet:      { label: 'Технические спецификации',    domain: 'technical', notary: false, mult: 1.35 },
    patent:          { label: 'Патент',                      domain: 'patent',    notary: false, mult: 1.55 },
    financial_report:{ label: 'Финансовая отчётность',       domain: 'finance',   notary: false, mult: 1.25 },
    it_docs:         { label: 'IT-документация / интерфейс', domain: 'it',        notary: false, mult: 1.4 },
    marketing:       { label: 'Маркетинговый текст / сайт',  domain: 'marketing', notary: false, mult: 1.15 },
    general:         { label: 'Общий текст',                 domain: 'general',   notary: false, mult: 1.0 },
  };

  const COMPLEXITY_LABELS = {
    simple:         { label: 'Простой',       color: '#22d46e', emoji: '🟢' },
    standard:       { label: 'Стандартный',   color: '#4f6aff', emoji: '🔵' },
    complex:        { label: 'Сложный',       color: '#c4922a', emoji: '🟡' },
    highly_complex: { label: 'Очень сложный', color: '#ef4444', emoji: '🔴' },
  };

  // ── ГЛАВНЫЙ МЕТОД: проверить файл ────────────────────────
  async function checkFile(file) {
    // Читаем текст через PricingEngine
    let fileInfo = null;
    try {
      fileInfo = await PricingEngine.readFile(file);
    } catch (e) {
      fileInfo = { fileName: file.name, chars: 0, pages: 0, text: '', fileType: file.name.split('.').pop() };
    }

    // Показываем лоадер в чате
    _showLoader(file.name);

    // Анализируем через GPT
    const analysis = await _analyzeWithAI(fileInfo);

    // Рассчитываем цену
    const pricing = _calculatePricing(fileInfo, analysis);

    // Отображаем результат
    _showResult(fileInfo, analysis, pricing);

    // Обновляем слоты в StateMachine
    if (typeof StateMachine !== 'undefined') {
      StateMachine.updateSlots({
        chars:      fileInfo.chars,
        pages:      fileInfo.pages || pricing.pages,
        domain:     analysis.domain,
        complexity: analysis.complexity,
        lang:       analysis.sourceLang,
        langPair:   analysis.sourceLang && analysis.targetLang
                      ? `${analysis.sourceLang}-${analysis.targetLang}` : undefined,
      });
    }

    return { fileInfo, analysis, pricing };
  }

  // ── AI АНАЛИЗ ────────────────────────────────────────────
  async function _analyzeWithAI(fileInfo) {
    const snippet = (fileInfo.text || '').slice(0, 2000);
    const prompt = `Проанализируй документ и верни ТОЛЬКО JSON без markdown:
Имя файла: "${fileInfo.fileName}"
Фрагмент текста (первые 2000 знаков):
"""
${snippet || '[Текст недоступен — анализируй по имени файла]'}
"""

JSON-структура ответа:
{
  "docType": "один из: ${Object.keys(DOC_TYPES).join(', ')}",
  "docTypeLabel": "название документа на русском",
  "sourceLang": "код языка оригинала (ru/en/de/fr/it/es/zh/ja/ar/...)",
  "targetLang": "предполагаемый язык перевода (ru если не ru, иначе en)",
  "complexity": "simple|standard|complex|highly_complex",
  "domain": "general|technical|legal|medical|it|finance|marketing|patent",
  "needsNotary": true|false,
  "needsApostille": true|false,
  "termDensity": "low|medium|high",
  "summary": "2-3 предложения о документе и особенностях перевода на русском",
  "warnings": ["массив предупреждений если есть, иначе []"]
}`;

    try {
      const text = await _callProxy(prompt,
        'Ты эксперт-аналитик переводческого бюро. Анализируй документы и отвечай ТОЛЬКО валидным JSON.');
      const clean = text.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim();
      const parsed = JSON.parse(clean);
      // Валидируем и подставляем дефолты
      return {
        docType:      parsed.docType       || 'general',
        docTypeLabel: parsed.docTypeLabel  || 'Документ',
        sourceLang:   parsed.sourceLang    || 'ru',
        targetLang:   parsed.targetLang    || 'en',
        complexity:   parsed.complexity    || 'standard',
        domain:       parsed.domain        || 'general',
        needsNotary:  !!parsed.needsNotary,
        needsApostille: !!parsed.needsApostille,
        termDensity:  parsed.termDensity   || 'medium',
        summary:      parsed.summary       || '',
        warnings:     Array.isArray(parsed.warnings) ? parsed.warnings : [],
      };
    } catch (e) {
      // Fallback — базовый анализ по имени файла
      return _fallbackAnalysis(fileInfo.fileName);
    }
  }

  function _fallbackAnalysis(fileName) {
    const fn = fileName.toLowerCase();
    let docType = 'general', domain = 'general', needsNotary = false;
    if (/passport|паспорт/.test(fn))               { docType = 'passport'; domain = 'legal'; needsNotary = true; }
    else if (/diploma|diplom|диплом/.test(fn))      { docType = 'diploma';  domain = 'legal'; needsNotary = true; }
    else if (/contract|договор|контракт/.test(fn))  { docType = 'contract'; domain = 'legal'; }
    else if (/медицин|medical|выписк/.test(fn))     { docType = 'medical_record'; domain = 'medical'; }
    else if (/техни|manual|руковод/.test(fn))       { docType = 'tech_manual'; domain = 'technical'; }
    return { docType, docTypeLabel: DOC_TYPES[docType]?.label || 'Документ', sourceLang: 'ru', targetLang: 'en',
             complexity: 'standard', domain, needsNotary, needsApostille: false,
             termDensity: 'medium', summary: '', warnings: [] };
  }

  // ── РАСЧЁТ ЦЕНЫ ──────────────────────────────────────────
  function _calculatePricing(fileInfo, analysis) {
    const pages   = fileInfo.pages || Math.ceil((fileInfo.chars || 1800) / 1800) || 1;
    const docMeta = DOC_TYPES[analysis.docType] || DOC_TYPES.general;

    const complexMult = { simple: 0.9, standard: 1.0, complex: 1.2, highly_complex: 1.4 };
    const cm = complexMult[analysis.complexity] || 1.0;
    const dm = docMeta.mult || 1.0;

    const base = typeof PricingEngine !== 'undefined'
      ? PricingEngine.BASE_RATES
      : { mtpe: 350, human: 750, premium: 1350 };

    const notaryCostPerPage = analysis.needsNotary ? 400 : 0;
    const apostilleCost     = analysis.needsApostille ? 3500 : 0;

    const calc = (tariff) => {
      const pp    = Math.round(base[tariff] * dm * cm);
      const total = pp * pages + notaryCostPerPage * pages + apostilleCost;
      return { perPage: pp, total };
    };

    return {
      pages,
      chars: fileInfo.chars,
      notaryCostPerPage,
      apostilleCost,
      mtpe:    calc('mtpe'),
      human:   calc('human'),
      premium: calc('premium'),
    };
  }

  // ── UI: ЛОАДЕР ───────────────────────────────────────────
  function _showLoader(fileName) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const div = document.createElement('div');
    div.className = 'msg msg--bot';
    div.id = 'dc-loader';
    div.innerHTML = `
      <div class="bubble bubble--bot bubble--rich">
        <div style="display:flex;align-items:center;gap:12px;padding:6px 0">
          <div style="width:36px;height:36px;border:3px solid rgba(82,108,255,0.2);border-top-color:#4f6aff;border-radius:50%;animation:rc-spin 0.7s linear infinite;flex-shrink:0"></div>
          <div>
            <div style="font-size:13px;font-weight:600;color:#e8eeff;margin-bottom:3px">Анализирую документ…</div>
            <div style="font-size:11.5px;color:rgba(140,155,210,0.7)">${fileName}</div>
          </div>
        </div>
      </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
  }

  // ── UI: РЕЗУЛЬТАТ ────────────────────────────────────────
  function _showResult(fileInfo, analysis, pricing) {
    document.getElementById('dc-loader')?.remove();
    const msgs = document.getElementById('messages');
    if (!msgs) return;

    const cplx    = COMPLEXITY_LABELS[analysis.complexity] || COMPLEXITY_LABELS.standard;
    const docMeta = DOC_TYPES[analysis.docType] || DOC_TYPES.general;
    const fmt     = n => n.toLocaleString('ru-RU');
    const time    = new Date().toLocaleTimeString('ru-RU', { hour:'2-digit', minute:'2-digit' });
    const langMap = { ru:'🇷🇺', en:'🇬🇧', de:'🇩🇪', fr:'🇫🇷', it:'🇮🇹', es:'🇪🇸', zh:'🇨🇳', ja:'🇯🇵', ar:'🇸🇦', ko:'🇰🇷' };
    const srcFlag = langMap[analysis.sourceLang] || '🌍';
    const dstFlag = langMap[analysis.targetLang] || '🌍';

    const warningsHtml = analysis.warnings.length
      ? `<div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:8px;padding:8px 12px;margin-top:10px">
           ${analysis.warnings.map(w => `<div style="font-size:12px;color:#fca5a5;margin-bottom:3px">⚠️ ${w}</div>`).join('')}
         </div>` : '';

    const notaryHtml = analysis.needsNotary
      ? `<div style="background:rgba(196,146,42,0.12);border:1px solid rgba(196,146,42,0.3);border-radius:8px;padding:8px 12px;margin-top:8px;font-size:12.5px;color:#e8b84b">
           📜 Требуется нотариальное заверение (+${fmt(pricing.notaryCostPerPage)} ₽/стр.)
           ${analysis.needsApostille ? '<br>🔏 Также может потребоваться апостиль (+' + fmt(pricing.apostilleCost) + ' ₽)' : ''}
         </div>` : '';

    const div = document.createElement('div');
    div.className = 'msg msg--bot';
    div.innerHTML = `
      <div class="bubble bubble--bot bubble--rich">
        <div class="bubble-lead">📄 Документ проанализирован!</div>

        <!-- Карточка документа -->
        <div style="background:rgba(0,0,0,0.2);border-radius:12px;padding:12px 14px;margin-bottom:12px">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:10px">
            <div>
              <div style="font-size:13px;font-weight:700;color:#e8eeff;margin-bottom:4px">${analysis.docTypeLabel}</div>
              <div style="font-size:12px;color:rgba(140,155,210,0.7)">${fileInfo.fileName}</div>
            </div>
            <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(${cplx.color === '#22d46e' ? '34,212,110' : cplx.color === '#4f6aff' ? '79,106,255' : cplx.color === '#c4922a' ? '196,146,42' : '239,68,68'},0.15);border-radius:20px;padding:4px 10px;font-size:11px;font-weight:700;color:${cplx.color};white-space:nowrap;flex-shrink:0">
              ${cplx.emoji} ${cplx.label}
            </span>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
            ${_statRow('🌍 Языки', `${srcFlag} ${analysis.sourceLang.toUpperCase()} → ${dstFlag} ${analysis.targetLang.toUpperCase()}`)}
            ${_statRow('📄 Объём', `${pricing.pages} стр. / ${fmt(pricing.chars || pricing.pages * 1800)} зн.`)}
            ${_statRow('📚 Тип', analysis.domain)}
            ${_statRow('🔤 Терминов', analysis.termDensity === 'high' ? 'Много' : analysis.termDensity === 'medium' ? 'Средне' : 'Мало')}
          </div>
          ${analysis.summary ? `<div style="margin-top:10px;font-size:12px;color:rgba(160,170,220,0.8);line-height:1.5;padding-top:8px;border-top:1px solid rgba(82,108,255,0.1)">${analysis.summary}</div>` : ''}
          ${warningsHtml}
          ${notaryHtml}
        </div>

        <!-- Прайс-карточки -->
        <div style="font-size:11px;color:rgba(140,155,210,0.6);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Варианты перевода:</div>
        <div style="display:flex;gap:7px;flex-wrap:wrap">
          ${_priceCard('mtpe',    '🤖', 'MTPE',           'Вычитка AI',            pricing.mtpe,    false)}
          ${_priceCard('human',   '👨‍💼', 'Профессиональный','Отраслевой специалист', pricing.human,   true)}
          ${_priceCard('premium', '⭐', 'Premium',         'Перевод + носитель',    pricing.premium, false)}
        </div>

        <!-- CTA -->
        <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap">
          <button onclick="DocumentChecker.orderDoc('human')"
            style="padding:9px 18px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:pointer;flex:1;min-width:120px">
            Заказать перевод →
          </button>
          ${analysis.needsNotary
            ? `<button onclick="CertifiedFlow && CertifiedFlow.start('${analysis.docType}')"
                style="padding:9px 14px;background:rgba(196,146,42,0.2);border:1px solid rgba(196,146,42,0.4);border-radius:20px;color:#e8b84b;font-size:13px;font-weight:600;cursor:pointer">
                📜 С нотариусом
              </button>` : ''}
        </div>
      </div>
      <div class="msg-time">${time}</div>`;

    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;

    // Quick replies
    const qr = document.getElementById('quick-replies');
    if (qr) {
      qr.innerHTML = '';
      ['✅ Заказать профессиональный', '🤖 Выбрать MTPE (дешевле)', '📞 Нужна консультация']
        .forEach(t => {
          const btn = document.createElement('button');
          btn.className = 'qr-btn'; btn.textContent = t;
          btn.onclick = () => {
            if (typeof ChatEngine !== 'undefined') ChatEngine.handleUserInput(t);
          };
          qr.appendChild(btn);
        });
    }
  }

  function _statRow(label, val) {
    return `<div style="background:rgba(82,108,255,0.07);border-radius:6px;padding:5px 8px">
      <div style="font-size:10px;color:rgba(140,155,210,0.6);margin-bottom:1px">${label}</div>
      <div style="font-size:12px;font-weight:600;color:#dde4ff">${val}</div>
    </div>`;
  }

  function _priceCard(tariff, icon, name, desc, pr, popular) {
    const fmt = n => n.toLocaleString('ru-RU');
    return `<div onclick="DocumentChecker.orderDoc('${tariff}')"
      style="flex:1;min-width:100px;background:rgba(8,16,50,${popular ? '0.9' : '0.6'});
      border:${popular ? '1.5px solid rgba(79,106,255,0.6)' : '1px solid rgba(82,108,255,0.2)'};
      border-radius:12px;padding:11px 10px;cursor:pointer;transition:all 0.2s;position:relative"
      onmouseover="this.style.borderColor='rgba(82,108,255,0.7)'"
      onmouseout="this.style.borderColor='${popular ? 'rgba(79,106,255,0.6)' : 'rgba(82,108,255,0.2)'}'">
      ${popular ? '<div style="position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#4f6aff,#7c5cfc);color:#fff;font-size:9px;font-weight:800;padding:2px 10px;border-radius:0 0 8px 8px;letter-spacing:.05em;white-space:nowrap">ПОПУЛЯРНЫЙ</div>' : ''}
      <div style="font-size:18px;margin-bottom:4px;margin-top:${popular ? '10px' : '0'}">${icon}</div>
      <div style="font-size:11.5px;font-weight:700;color:#e8eeff;margin-bottom:2px">${name}</div>
      <div style="font-size:10px;color:rgba(140,155,210,0.6);margin-bottom:7px">${desc}</div>
      <div style="font-size:15px;font-weight:800;color:#06c0c8">${fmt(pr.total)} ₽</div>
      <div style="font-size:10px;color:rgba(140,155,210,0.5)">${fmt(pr.perPage)} ₽/стр.</div>
    </div>`;
  }

  // ── ДЕЙСТВИЯ ─────────────────────────────────────────────
  function orderDoc(tariff) {
    if (typeof StateMachine !== 'undefined') StateMachine.updateSlots({ tariff });
    if (typeof ChatEngine !== 'undefined') {
      ChatEngine.handleUserInput(`Хочу заказать тариф: ${tariff}`);
    }
  }

  // ── PROXY ВЫЗОВ ──────────────────────────────────────────
  async function _callProxy(text, system) {
    if (typeof RemarkaConfig !== 'undefined') {
      const body = new URLSearchParams({
        action: 'remarka_chat', nonce: RemarkaConfig.nonce, text, system,
      });
      const r = await fetch(RemarkaConfig.ajaxUrl, {
        method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
      });
      const data = await r.json();
      return _extractText(data.data || data);
    }
    const r = await fetch('/api/gpt.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ text, system }),
    });
    return _extractText(await r.json());
  }

  function _extractText(data) {
    if (data?.output) return data.output.filter(b=>b.type==='message').flatMap(b=>b.content||[]).filter(c=>c.type==='output_text').map(c=>c.text).join('');
    if (data?.choices) return data.choices[0]?.message?.content || '';
    if (typeof data === 'string') return data;
    return '';
  }

  return { checkFile, orderDoc };
})();

window.DocumentChecker = DocumentChecker;
