/* ============================================================
   REMARKA MODULE: quality-checker.js v1.0
   Оценка качества готового перевода:
   • Клиент вставляет оригинал + перевод
   • AI анализирует по 6 критериям
   • Выдаёт детальный отчёт с примерами ошибок
   • Предлагает услугу вычитки если качество низкое
   ============================================================ */

const QualityChecker = (() => {
  'use strict';

  let state = { active: false, step: null, original: '', translation: '' };

  const CRITERIA = [
    { key: 'accuracy',     label: 'Точность',         icon: '🎯', desc: 'Соответствие смыслу оригинала' },
    { key: 'fluency',      label: 'Читаемость',       icon: '📖', desc: 'Естественность языка перевода' },
    { key: 'terminology',  label: 'Терминология',      icon: '📚', desc: 'Корректность специальных терминов' },
    { key: 'style',        label: 'Стиль',             icon: '✍️', desc: 'Соответствие стилю документа' },
    { key: 'completeness', label: 'Полнота',           icon: '✅', desc: 'Отсутствие пропусков и добавлений' },
    { key: 'formatting',   label: 'Форматирование',    icon: '📐', desc: 'Сохранение структуры текста' },
  ];

  // ── ТРИГГЕР ───────────────────────────────────────────────
  function checkIntent(text) {
    return /провер|качество|вычитк|оцен.*перевод|проверить.*перевод|качество.*перевод|check.*quality|revision/i.test(text);
  }

  function start() {
    state = { active: true, step: 'get_original', original: '', translation: '' };
    _appendBotRich(
      '✅ Проверю качество перевода!\n\nЭто займёт около 30 секунд. Я проанализирую:\n🎯 Точность • 📖 Читаемость • 📚 Терминологию • ✍️ Стиль • ✅ Полноту • 📐 Форматирование',
      _buildInputBlock('original'),
      ['❌ Отмена']
    );
    _overrideSend();
  }

  function _buildInputBlock(type) {
    if (type === 'original') {
      return `<div style="margin-top:8px">
        <div style="font-size:12px;color:rgba(140,155,210,0.7);margin-bottom:6px">Шаг 1/2 — Вставьте исходный текст (оригинал):</div>
        <textarea id="qc-original" placeholder="Вставьте оригинальный текст здесь…"
          style="width:100%;min-height:100px;background:rgba(8,18,52,0.7);border:1.5px solid rgba(82,108,255,0.3);
          border-radius:10px;color:#dde4ff;padding:10px 14px;font-size:13px;resize:vertical;
          outline:none;font-family:inherit;line-height:1.5;box-sizing:border-box"></textarea>
        <button onclick="QualityChecker.submitOriginal()"
          style="margin-top:8px;padding:9px 20px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);
          border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:pointer">
          Далее →
        </button>
      </div>`;
    }
    return `<div style="margin-top:8px">
      <div style="font-size:12px;color:rgba(140,155,210,0.7);margin-bottom:6px">Шаг 2/2 — Вставьте перевод для проверки:</div>
      <textarea id="qc-translation" placeholder="Вставьте текст перевода здесь…"
        style="width:100%;min-height:100px;background:rgba(8,18,52,0.7);border:1.5px solid rgba(82,108,255,0.3);
        border-radius:10px;color:#dde4ff;padding:10px 14px;font-size:13px;resize:vertical;
        outline:none;font-family:inherit;line-height:1.5;box-sizing:border-box"></textarea>
      <button onclick="QualityChecker.submitTranslation()"
        style="margin-top:8px;padding:9px 20px;background:linear-gradient(135deg,#22d46e,#1aaa55);
        border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:pointer">
        🔍 Проверить качество
      </button>
    </div>`;
  }

  function submitOriginal() {
    const ta = document.getElementById('qc-original');
    if (!ta || !ta.value.trim()) { _toast('⚠️ Вставьте оригинальный текст'); return; }
    state.original = ta.value.trim();
    state.step = 'get_translation';
    _appendBotRich('Отлично! Теперь вставьте перевод.', _buildInputBlock('translation'), []);
  }

  function submitTranslation() {
    const ta = document.getElementById('qc-translation');
    if (!ta || !ta.value.trim()) { _toast('⚠️ Вставьте текст перевода'); return; }
    state.translation = ta.value.trim();
    state.step = 'analyzing';
    _analyze();
  }

  async function _analyze() {
    _appendBotRich('Анализирую качество перевода…',
      `<div style="text-align:center;padding:16px 0">
        <div style="width:40px;height:40px;border:3px solid rgba(82,108,255,0.2);border-top-color:#4f6aff;border-radius:50%;animation:rc-spin 0.7s linear infinite;margin:0 auto 10px"></div>
        <div style="color:rgba(160,170,220,0.7);font-size:13px">GPT-4o проверяет все критерии…</div>
      </div>`, []);

    const prompt =
      `Проверь качество перевода по 6 критериям. Верни ТОЛЬКО JSON без markdown.\n\n` +
      `ОРИГИНАЛ:\n"""\n${state.original.slice(0, 3000)}\n"""\n\n` +
      `ПЕРЕВОД:\n"""\n${state.translation.slice(0, 3000)}\n"""\n\n` +
      `JSON-структура:\n{\n` +
      `  "overall": 0-100,\n` +
      `  "grade": "A+|A|B|C|D",\n` +
      `  "verdict": "Отлично|Хорошо|Удовлетворительно|Требует правки|Плохое качество",\n` +
      `  "criteria": {\n` +
      `    "accuracy":{"score":0-100,"comment":"1 предложение","examples":["пример ошибки если есть"]},\n` +
      `    "fluency":{"score":0-100,"comment":"...","examples":[]},\n` +
      `    "terminology":{"score":0-100,"comment":"...","examples":[]},\n` +
      `    "style":{"score":0-100,"comment":"...","examples":[]},\n` +
      `    "completeness":{"score":0-100,"comment":"...","examples":[]},\n` +
      `    "formatting":{"score":0-100,"comment":"...","examples":[]}\n` +
      `  },\n` +
      `  "strengths": ["что сделано хорошо"],\n` +
      `  "issues": ["основные проблемы"],\n` +
      `  "recommendation": "что рекомендуется сделать",\n` +
      `  "needsProofreading": true|false\n` +
      `}`;

    try {
      const text = await _callProxy(prompt,
        'Ты опытный редактор переводческого бюро. Оценивай строго и честно. Отвечай ТОЛЬКО валидным JSON.');
      const result = JSON.parse(text.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim());
      _showReport(result);
    } catch (e) {
      _appendBot('❌ Не удалось проанализировать. Попробуйте ещё раз или обратитесь к менеджеру.', ['🔄 Повторить', '💬 К менеджеру']);
    } finally {
      state.active = false;
      _restoreSend();
    }
  }

  function _showReport(r) {
    document.querySelectorAll('.msg--bot').forEach(m => {
      if (m.querySelector('#qc-original') || m.querySelector('#qc-translation') || m.textContent.includes('Анализирую')) {
        // не удаляем
      }
    });

    const gradeColor = { 'A+':'#22d46e','A':'#22d46e','B':'#4f6aff','C':'#c4922a','D':'#ef4444' };
    const color = gradeColor[r.grade] || '#4f6aff';
    const fmt = n => typeof n === 'number' ? n : 0;

    const criteriaRows = CRITERIA.map(c => {
      const cr = r.criteria?.[c.key] || { score: 0, comment: '', examples: [] };
      const s  = fmt(cr.score);
      const sc = s >= 80 ? '#22d46e' : s >= 60 ? '#4f6aff' : s >= 40 ? '#c4922a' : '#ef4444';
      return `<div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
          <span style="font-size:12.5px;font-weight:600;color:#e8eeff">${c.icon} ${c.label}</span>
          <span style="font-size:13px;font-weight:800;color:${sc}">${s}/100</span>
        </div>
        <div style="height:5px;background:rgba(82,108,255,0.12);border-radius:3px;overflow:hidden;margin-bottom:4px">
          <div style="height:100%;width:${s}%;background:${sc};border-radius:3px;transition:width .5s"></div>
        </div>
        <div style="font-size:11.5px;color:rgba(160,170,220,0.8)">${cr.comment || ''}</div>
        ${cr.examples?.length ? `<div style="font-size:11px;color:rgba(239,68,68,0.8);margin-top:3px">${cr.examples.map(e=>'• '+e).join('<br>')}</div>` : ''}
      </div>`;
    }).join('');

    const html = `
      <!-- Общая оценка -->
      <div style="text-align:center;padding:14px 0 16px;border-bottom:1px solid rgba(82,108,255,0.1);margin-bottom:14px">
        <div style="font-size:3.5rem;font-weight:900;color:${color};line-height:1">${r.grade}</div>
        <div style="font-size:1.4rem;font-weight:700;color:#e8eeff;margin:4px 0">${fmt(r.overall)}/100</div>
        <div style="font-size:14px;color:${color};font-weight:600">${r.verdict || ''}</div>
      </div>
      <!-- Полоска общая -->
      <div style="height:8px;background:rgba(82,108,255,0.12);border-radius:4px;overflow:hidden;margin-bottom:14px">
        <div style="height:100%;width:${fmt(r.overall)}%;background:${color};border-radius:4px"></div>
      </div>
      <!-- Критерии -->
      ${criteriaRows}
      <!-- Сильные стороны -->
      ${r.strengths?.length ? `<div style="background:rgba(34,212,110,0.08);border-radius:8px;padding:10px 12px;margin-bottom:8px">
        <div style="font-size:11px;color:#22d46e;font-weight:700;margin-bottom:5px">✅ СИЛЬНЫЕ СТОРОНЫ</div>
        ${r.strengths.map(s=>`<div style="font-size:12px;color:#dde4ff;margin-bottom:2px">• ${s}</div>`).join('')}
      </div>` : ''}
      <!-- Проблемы -->
      ${r.issues?.length ? `<div style="background:rgba(239,68,68,0.08);border-radius:8px;padding:10px 12px;margin-bottom:8px">
        <div style="font-size:11px;color:#fca5a5;font-weight:700;margin-bottom:5px">⚠️ ТРЕБУЕТ ВНИМАНИЯ</div>
        ${r.issues.map(s=>`<div style="font-size:12px;color:#dde4ff;margin-bottom:2px">• ${s}</div>`).join('')}
      </div>` : ''}
      <!-- Рекомендация -->
      <div style="background:rgba(79,106,255,0.1);border:1px solid rgba(82,108,255,0.25);border-radius:8px;padding:10px 12px;margin-bottom:12px">
        <div style="font-size:11px;color:#a5b4fc;font-weight:700;margin-bottom:4px">💡 РЕКОМЕНДАЦИЯ</div>
        <div style="font-size:12.5px;color:#dde4ff;line-height:1.5">${r.recommendation || ''}</div>
      </div>
      <!-- CTA -->
      ${r.needsProofreading ? `<button onclick="ChatEngine && ChatEngine.handleUserInput('Нужна вычитка и исправление перевода')"
        style="width:100%;padding:11px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);border:none;border-radius:12px;
        color:#fff;font-size:13px;font-weight:700;cursor:pointer">
        ✍️ Заказать профессиональную вычитку и правку
      </button>` : `<div style="text-align:center;padding:8px;font-size:13px;color:#22d46e;font-weight:600">
        ✅ Перевод хорошего качества!
      </div>`}`;

    _appendBotRich(`Отчёт о качестве перевода`, html,
      r.needsProofreading
        ? ['✍️ Заказать вычитку', '🔄 Проверить другой текст', '💬 Задать вопрос']
        : ['✅ Отлично, спасибо', '🔄 Проверить ещё', '🔤 Нужен перевод']);
  }

  // ── HELPERS ───────────────────────────────────────────────
  let _origSend = null;
  function _overrideSend() {
    if (!_origSend && typeof ChatEngine !== 'undefined') {
      _origSend = ChatEngine.handleUserInput.bind(ChatEngine);
      ChatEngine.handleUserInput = (text) => {
        if (text === '❌ Отмена') { state.active = false; _restoreSend(); _appendBot('Хорошо, отменили.', ['🔤 Нужен перевод']); return; }
        if (state.active) return;
        _restoreSend(); _origSend(text);
      };
    }
  }
  function _restoreSend() { if (_origSend && typeof ChatEngine !== 'undefined') { ChatEngine.handleUserInput = _origSend; _origSend = null; } }

  function _appendBot(text, replies) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className = 'msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML = `<div class="bubble bubble--bot">${text.replace(/\n/g,'<br>')}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);
    _setQR(replies);
    msgs.scrollTop = msgs.scrollHeight;
  }

  function _appendBotRich(text, html, replies) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className = 'msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML = `<div class="bubble bubble--bot bubble--rich">${text?`<div class="bubble-lead">${text.replace(/\n/g,'<br>')}</div>`:''}${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);
    _setQR(replies);
    msgs.scrollTop = msgs.scrollHeight;
  }

  function _setQR(arr) {
    const qr = document.getElementById('quick-replies');
    if (!qr) return;
    qr.innerHTML = '';
    (arr||[]).forEach(text => {
      const btn = document.createElement('button');
      btn.className = 'qr-btn'; btn.textContent = text;
      btn.onclick = () => { if (typeof ChatEngine !== 'undefined') ChatEngine.handleUserInput(text); };
      qr.appendChild(btn);
    });
  }

  function _toast(msg) {
    let t = document.getElementById('remarka-toast');
    if (!t) { t = document.createElement('div'); t.id='remarka-toast'; t.style.cssText='position:fixed;bottom:100px;right:28px;background:rgba(14,26,72,0.95);color:#dde4ff;padding:10px 16px;border-radius:10px;font-size:13px;z-index:99999;border:1px solid rgba(82,108,255,0.3);display:none'; document.body.appendChild(t); }
    t.textContent = msg; t.style.display = 'block';
    clearTimeout(t._t); t._t = setTimeout(()=>t.style.display='none', 3000);
  }

  async function _callProxy(text, system) {
    if (typeof RemarkaConfig !== 'undefined') {
      const body = new URLSearchParams({ action:'remarka_chat', nonce:RemarkaConfig.nonce, text, system });
      const r = await fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() });
      const data = await r.json(); return _extractText(data.data || data);
    }
    const r = await fetch('/api/gpt.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ text, system }) });
    return _extractText(await r.json());
  }

  function _extractText(data) {
    if (data?.output) return data.output.filter(b=>b.type==='message').flatMap(b=>b.content||[]).filter(c=>c.type==='output_text').map(c=>c.text).join('');
    if (data?.choices) return data.choices[0]?.message?.content || '';
    if (typeof data==='string') return data;
    return '';
  }

  return { checkIntent, start, submitOriginal, submitTranslation };
})();

window.QualityChecker = QualityChecker;
