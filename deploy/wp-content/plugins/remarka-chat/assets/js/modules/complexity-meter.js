/* ============================================================
   REMARKA MODULE: complexity-meter.js v1.0
   Определитель сложности текста:
   • Индекс читаемости (Флеш-адаптированный)
   • Плотность терминологии через AI
   • Рекомендация тарифа и переводчика
   • Оценка времени перевода
   ============================================================ */

const ComplexityMeter = (() => {
  'use strict';

  let _waiting = false;
  let _origSend = null;

  function checkIntent(text) {
    return /\b(сложность|сложный.*текст|определ.*сложн|насколько.*сложн|complexity|difficult.*text|оцен.*текст|анализ.*текст)\b/i.test(text);
  }

  function start() {
    _waiting = true;
    _bot(
      '📊 Определю сложность текста!\n\n' +
      'Вставьте фрагмент текста (минимум 200 знаков) — я оценю:\n\n' +
      '• Индекс читаемости\n• Плотность терминов\n• Уровень специализации\n• Рекомендуемый тариф\n• Примерное время перевода',
      ['❌ Отмена']
    );
    _override();
  }

  function _override() {
    if (_origSend || typeof ChatEngine === 'undefined') return;
    _origSend = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = (text) => {
      if (!_waiting) { _restore(); _origSend(text); return; }
      if (/отмена|❌/i.test(text)) { _waiting = false; _restore(); _bot('Хорошо! Чем могу помочь?', ['🔤 Нужен перевод']); return; }
      if (text.length < 50) { _bot('⚠️ Текст слишком короткий. Вставьте минимум 200 знаков.', []); return; }
      _waiting = false;
      _restore();
      _analyze(text);
    };
  }
  function _restore() { if (_origSend && typeof ChatEngine !== 'undefined') { ChatEngine.handleUserInput = _origSend; _origSend = null; } }

  async function _analyze(text) {
    _botRich('Анализирую текст…',
      `<div style="text-align:center;padding:16px 0">
        <div style="width:38px;height:38px;border:3px solid rgba(82,108,255,0.2);border-top-color:#4f6aff;border-radius:50%;animation:rc-spin .7s linear infinite;margin:0 auto 10px"></div>
        <div style="font-size:12px;color:rgba(160,170,220,0.7)">Считаю индекс читаемости и плотность терминов…</div>
      </div>`, []);

    // Локальный анализ (без AI)
    const local = _localAnalysis(text);

    // AI анализ
    let ai = {};
    try {
      const prompt =
        `Проанализируй сложность текста для переводчика. Верни ТОЛЬКО JSON:\n` +
        `{"domain":"general|technical|legal|medical|it|finance|marketing","` +
        `termDensity":"low|medium|high|very_high","` +
        `complexity":"simple|standard|complex|highly_complex","` +
        `specialistRequired":true|false,"` +
        `domainLabel":"название отрасли на русском","` +
        `keyTerms":["список 3-5 ключевых терминов"],"` +
        `recommendation":"2 предложения рекомендации переводчику"}\n\n` +
        `ТЕКСТ:\n"""\n${text.slice(0, 2000)}\n"""`;
      const resp = await _callProxy(prompt, 'Ты лингвист-аналитик. Отвечай ТОЛЬКО валидным JSON.');
      ai = JSON.parse(resp.replace(/```json\n?/g,'').replace(/```\n?/g,'').trim());
    } catch { ai = { domain:'general', termDensity:'medium', complexity: local.complexityKey, specialistRequired: false, domainLabel:'Общий текст', keyTerms:[], recommendation:'' }; }

    _showResult(text, local, ai);
  }

  function _localAnalysis(text) {
    const words   = text.trim().split(/\s+/).length;
    const chars   = text.replace(/\s/g,'').length;
    const sents   = (text.match(/[.!?…]+/g) || []).length || 1;
    const avgWord = chars / words;
    const avgSent = words / sents;
    const pages   = Math.ceil(text.length / 1800);

    // Упрощённый индекс читаемости (0–100, выше = проще)
    const flesch  = Math.max(0, Math.min(100, Math.round(206.835 - 1.3 * avgSent - 60.1 * (avgWord / 5))));

    let complexityKey, complexityLabel, color;
    if (flesch >= 70)      { complexityKey='simple';         complexityLabel='Простой';       color='#22d46e'; }
    else if (flesch >= 50) { complexityKey='standard';       complexityLabel='Стандартный';   color='#4f6aff'; }
    else if (flesch >= 30) { complexityKey='complex';        complexityLabel='Сложный';       color='#c4922a'; }
    else                   { complexityKey='highly_complex'; complexityLabel='Очень сложный'; color='#ef4444'; }

    // Время перевода (стр/день): простой 15, стандарт 10, сложный 6, очень сложный 4
    const ppdMap = { simple:15, standard:10, complex:6, highly_complex:4 };
    const ppd    = ppdMap[complexityKey];
    const days   = Math.max(1, Math.ceil(pages / ppd));

    return { words, chars, sents, avgWord: avgWord.toFixed(1), avgSent: avgSent.toFixed(1), flesch, pages, days, complexityKey, complexityLabel, color };
  }

  function _showResult(text, local, ai) {
    document.querySelectorAll('.msg--bot').forEach(m => {
      if (m.textContent.includes('Анализирую текст')) m.remove();
    });

    const tariffMap = {
      simple:         { tariff:'mtpe',    name:'MTPE (Вычитка AI)',     price:350  },
      standard:       { tariff:'human',   name:'Профессиональный',      price:750  },
      complex:        { tariff:'human',   name:'Профессиональный',      price:750  },
      highly_complex: { tariff:'premium', name:'Premium Expert',        price:1350 },
    };
    const rec = tariffMap[ai.complexity || local.complexityKey] || tariffMap.standard;
    const estPrice = rec.price * local.pages;

    const densityLabel = { low:'Низкая 🟢', medium:'Средняя 🔵', high:'Высокая 🟡', very_high:'Очень высокая 🔴' };
    const densityColor = { low:'#22d46e', medium:'#4f6aff', high:'#c4922a', very_high:'#ef4444' };

    const html = `
      <!-- Общий балл -->
      <div style="text-align:center;padding:10px 0 14px;border-bottom:1px solid rgba(82,108,255,0.1);margin-bottom:12px">
        <div style="font-size:2.8rem;font-weight:900;color:${local.color};line-height:1">${local.flesch}</div>
        <div style="font-size:10px;color:rgba(140,155,210,0.5);text-transform:uppercase;letter-spacing:.06em;margin:2px 0">индекс читаемости</div>
        <div style="font-size:15px;font-weight:700;color:${local.color};margin-top:4px">${local.complexityLabel}</div>
        <div style="height:6px;background:rgba(82,108,255,0.12);border-radius:3px;overflow:hidden;margin:10px 0 0">
          <div style="height:100%;width:${local.flesch}%;background:${local.color};border-radius:3px"></div>
        </div>
      </div>
      <!-- Метрики -->
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:12px">
        ${_cell('📝 Слов', local.words.toLocaleString('ru-RU'))}
        ${_cell('📄 Страниц', local.pages)}
        ${_cell('📏 Предложений', local.sents)}
        ${_cell('🔤 Ср. слово', local.avgWord + ' букв')}
        ${_cell('📐 Ср. предл.', local.avgSent + ' слов')}
        ${_cell('⏱ Дней', '~' + local.days)}
      </div>
      <!-- AI анализ -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:12px">
        ${_cell('📚 Отрасль', ai.domainLabel || '—')}
        ${_cell('🔬 Терминов', `<span style="color:${densityColor[ai.termDensity] || '#4f6aff'}">${densityLabel[ai.termDensity] || '—'}</span>`)}
      </div>
      ${ai.keyTerms?.length ? `<div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:8px 12px;margin-bottom:10px">
        <div style="font-size:10px;color:rgba(140,155,210,0.6);margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em">Ключевые термины</div>
        <div style="display:flex;flex-wrap:wrap;gap:5px">
          ${ai.keyTerms.map(t=>`<span style="background:rgba(79,106,255,0.15);border:1px solid rgba(82,108,255,0.3);border-radius:20px;padding:2px 10px;font-size:11.5px;color:#a5b4fc">${t}</span>`).join('')}
        </div>
      </div>` : ''}
      ${ai.recommendation ? `<div style="background:rgba(0,0,0,0.2);border-left:3px solid #c4922a;border-radius:0 8px 8px 0;padding:8px 12px;font-size:12px;color:#dde4ff;line-height:1.55;margin-bottom:12px">${ai.recommendation}</div>` : ''}
      <!-- Рекомендация тарифа -->
      <div style="background:rgba(79,106,255,0.1);border:1.5px solid rgba(82,108,255,0.3);border-radius:12px;padding:12px;display:flex;align-items:center;justify-content:space-between;gap:10px">
        <div>
          <div style="font-size:11px;color:rgba(140,155,210,0.6);margin-bottom:2px">Рекомендуемый тариф</div>
          <div style="font-size:14px;font-weight:700;color:#e8eeff">${rec.name}</div>
          <div style="font-size:12px;color:rgba(140,155,210,0.7);margin-top:2px">≈ ${estPrice.toLocaleString('ru-RU')} ₽ за ${local.pages} стр.</div>
        </div>
        <button onclick="ChatEngine && ChatEngine.handleUserInput('Хочу заказать: ${rec.name}')"
          style="padding:9px 16px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);border:none;border-radius:20px;color:#fff;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap">
          Заказать →
        </button>
      </div>`;

    _botRich('📊 Анализ сложности текста', html,
      ['✅ Заказать перевод', '🔍 Проверить качество', '📎 Загрузить файл']);
  }

  function _cell(label, val) {
    return `<div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:7px 9px">
      <div style="font-size:10px;color:rgba(140,155,210,0.6);margin-bottom:2px">${label}</div>
      <div style="font-size:12.5px;font-weight:600;color:#dde4ff">${val}</div>
    </div>`;
  }

  async function _callProxy(text, system) {
    if (typeof RemarkaConfig !== 'undefined') {
      const body = new URLSearchParams({ action:'remarka_chat', nonce:RemarkaConfig.nonce, text, system });
      const r = await fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() });
      const d = await r.json(); return _extractText(d.data || d);
    }
    const r = await fetch('/api/gpt.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({text, system}) });
    return _extractText(await r.json());
  }
  function _extractText(d) {
    if (d?.output) return d.output.filter(b=>b.type==='message').flatMap(b=>b.content||[]).filter(c=>c.type==='output_text').map(c=>c.text).join('');
    if (d?.choices) return d.choices[0]?.message?.content||''; if (typeof d==='string') return d; return '';
  }
  function _bot(text, replies) { _shared_appendBot(text, replies); }
  function _botRich(text, html, replies) { _shared_appendBotRich(text, html, replies); }

  return { checkIntent, start };
})();

window.ComplexityMeter = ComplexityMeter;
