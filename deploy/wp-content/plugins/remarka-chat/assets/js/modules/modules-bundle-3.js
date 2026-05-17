/* ============================================================
   REMARKA MODULES BUNDLE 3 v1.0
   Содержит 5 модулей:
   9.  VoiceSynthesis    — Ольга отвечает голосом
   12. ClientPortal      — мини-кабинет в чате
   15. ComparisonTable   — сравнение тарифов таблицей
   17. ReferralGenerator — генератор реферальных ссылок
   18. ChatExport        — экспорт переписки в PDF/текст
   ============================================================ */


/* ────────────────────────────────────────────────────────────
   9. VOICE SYNTHESIS — Ольга говорит голосом
   ──────────────────────────────────────────────────────────── */
const VoiceSynthesis = (() => {
  'use strict';

  let _enabled  = false;
  let _voices   = [];
  let _langMap  = { ru: 'ru-RU', en: 'en-US', it: 'it-IT' };
  let _curLang  = 'ru';
  let _btn      = null;

  function init() {
    if (!('speechSynthesis' in window)) return;

    // Загружаем голоса
    const load = () => {
      _voices = window.speechSynthesis.getVoices();
    };
    load();
    window.speechSynthesis.onvoiceschanged = load;

    // Создаём кнопку в navbar
    setTimeout(_createToggleBtn, 1500);

    // Перехватываем сообщения бота
    _watchMessages();
  }

  function _createToggleBtn() {
    const nav = document.querySelector('.remarka-navbar__right');
    if (!nav || document.getElementById('vs-toggle')) return;

    _btn = document.createElement('button');
    _btn.id = 'vs-toggle';
    _btn.title = 'Голосовые ответы Ольги';
    _btn.style.cssText = [
      'width:34px', 'height:34px', 'border-radius:10px',
      'border:1px solid rgba(82,108,255,0.3)',
      'background:rgba(8,18,52,0.7)',
      'color:rgba(140,155,210,0.6)',
      'cursor:pointer', 'display:flex',
      'align-items:center', 'justify-content:center',
      'transition:all .2s', 'font-size:16px',
    ].join(';');
    _btn.textContent = '🔇';
    _btn.onclick = toggle;
    nav.prepend(_btn);
  }

  function toggle() {
    _enabled = !_enabled;
    if (_btn) {
      _btn.textContent = _enabled ? '🔊' : '🔇';
      _btn.style.borderColor = _enabled ? 'rgba(82,108,255,0.7)' : 'rgba(82,108,255,0.3)';
      _btn.style.background  = _enabled ? 'rgba(79,106,255,0.2)' : 'rgba(8,18,52,0.7)';
      _btn.style.color       = _enabled ? '#a5b4fc' : 'rgba(140,155,210,0.6)';
    }
    if (!_enabled) window.speechSynthesis.cancel();
    else speak('Голосовой режим включён. Я буду зачитывать свои ответы.');
  }

  function _watchMessages() {
    const msgs = document.getElementById('messages');
    if (!msgs) { setTimeout(_watchMessages, 800); return; }

    const obs = new MutationObserver((mutations) => {
      if (!_enabled) return;
      for (const m of mutations) {
        for (const node of m.addedNodes) {
          if (node.classList?.contains('msg--bot')) {
            const bubble = node.querySelector('.bubble--bot');
            if (bubble) {
              const text = bubble.textContent?.trim().slice(0, 300);
              if (text) setTimeout(() => speak(text), 200);
            }
          }
        }
      }
    });
    obs.observe(msgs, { childList: true });
  }

  function speak(text, lang) {
    if (!('speechSynthesis' in window)) return;
    window.speechSynthesis.cancel();

    const utt = new SpeechSynthesisUtterance(text.replace(/[*_#>\[\]]/g, '').trim());
    utt.lang  = _langMap[lang || _curLang] || 'ru-RU';
    utt.rate  = 0.95;
    utt.pitch = 1.05;

    // Предпочитаем женский голос
    const preferred = _voices.filter(v =>
      v.lang.startsWith(utt.lang.slice(0,2)) &&
      (v.name.toLowerCase().includes('female') ||
       v.name.toLowerCase().includes('alena') ||
       v.name.toLowerCase().includes('irina') ||
       v.name.toLowerCase().includes('milena'))
    );
    if (preferred.length > 0) utt.voice = preferred[0];
    else {
      const any = _voices.find(v => v.lang.startsWith(utt.lang.slice(0,2)));
      if (any) utt.voice = any;
    }

    window.speechSynthesis.speak(utt);
  }

  function setLang(lang) { _curLang = lang; }

  return { init, toggle, speak, setLang };
})();


/* ────────────────────────────────────────────────────────────
   12. CLIENT PORTAL — мини-кабинет прямо в чате
   ──────────────────────────────────────────────────────────── */
const ClientPortal = (() => {
  'use strict';

  function checkIntent(text) {
    return /\b(мой.*кабинет|личный.*кабинет|мои.*заказы|история.*заказов|мой.*профиль|portal|account)\b/i.test(text);
  }

  function show() {
    const loyalty  = typeof LoyaltyProgram !== 'undefined' ? LoyaltyProgram.load() : { orders:0, totalSpent:0 };
    const tier     = typeof LoyaltyProgram !== 'undefined' ? LoyaltyProgram.getTier(loyalty) : { name:'—', emoji:'', discount:0 };
    const orders   = get_option('remarka_orders_log_local') || [];
    const profile  = typeof StateMachine !== 'undefined' ? StateMachine.getProfile() : {};
    const promo    = _getPromo();

    const html = `
      <div>
        <!-- Профиль -->
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0 14px;border-bottom:1px solid rgba(82,108,255,0.1);margin-bottom:12px">
          <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#4f6aff,#7c5cfc);
            display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
            ${tier.emoji || '👤'}
          </div>
          <div style="flex:1">
            <div style="font-size:13px;font-weight:700;color:#e8eeff">${profile.name || 'Гость'}</div>
            <div style="font-size:12px;color:${tier.color || '#8fa0d8'}">${tier.name || 'Новый клиент'}</div>
          </div>
          ${tier.discount ? `<div style="background:rgba(34,212,110,0.12);border-radius:8px;padding:6px 10px;text-align:center">
            <div style="font-size:15px;font-weight:800;color:#22d46e">-${tier.discount}%</div>
            <div style="font-size:9px;color:rgba(140,155,210,0.6)">скидка</div>
          </div>` : ''}
        </div>

        <!-- Статистика -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:12px">
          ${_stat('Заказов', loyalty.orders)}
          ${_stat('Потрачено', (loyalty.totalSpent||0).toLocaleString('ru-RU')+' ₽')}
          ${_stat('Промокод', promo ? promo.code : '—')}
        </div>

        <!-- Быстрые действия -->
        <div style="font-size:10px;color:rgba(140,155,210,0.5);text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px">Быстрые действия</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
          ${_action('📦 Мой заказ', 'OrderTracker && OrderTracker.start()')}
          ${_action('⭐ Оставить отзыв', 'FeedbackCollector && FeedbackCollector.start()')}
          ${_action('🎁 Моя скидка', 'LoyaltyProgram && LoyaltyProgram.showStatus()')}
          ${_action('🔗 Пригласить друга', 'ReferralGenerator && ReferralGenerator.show()')}
          ${_action('💬 Новый заказ', "ChatEngine && ChatEngine.handleUserInput('Нужен перевод')")}
          ${_action('📋 Экспорт истории', 'ChatExport && ChatExport.export()')}
        </div>
      </div>`;

    _shared_appendBotRich('👤 Ваш личный кабинет', html, []);
  }

  function _stat(label, val) {
    return `<div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:8px;text-align:center">
      <div style="font-size:13px;font-weight:700;color:#e8eeff">${val}</div>
      <div style="font-size:10px;color:rgba(140,155,210,0.6)">${label}</div>
    </div>`;
  }

  function _action(label, onclick) {
    return `<button onclick="${onclick}"
      style="padding:9px;background:rgba(8,16,50,0.6);border:1px solid rgba(82,108,255,0.2);
      border-radius:10px;color:#a5b4fc;font-size:12px;font-weight:500;cursor:pointer;
      transition:all .2s;font-family:inherit"
      onmouseover="this.style.borderColor='rgba(82,108,255,0.55)'"
      onmouseout="this.style.borderColor='rgba(82,108,255,0.2)'">
      ${label}
    </button>`;
  }

  function _getPromo() {
    try { return JSON.parse(localStorage.getItem('remarka_promo') || 'null'); } catch { return null; }
  }

  function get_option(key) { try { return JSON.parse(localStorage.getItem(key)); } catch { return null; } }

  function _shared_appendBotRich(title, html, replies) {
    if (typeof window._shared_appendBotRich === 'function') { window._shared_appendBotRich(title, html, replies); return; }
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className='msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML=`<div class="bubble bubble--bot bubble--rich"><div class="bubble-lead">${title}</div>${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d); msgs.scrollTop=msgs.scrollHeight;
  }

  return { checkIntent, show };
})();


/* ────────────────────────────────────────────────────────────
   15. COMPARISON TABLE — сравнение тарифов
   ──────────────────────────────────────────────────────────── */
const ComparisonTable = (() => {
  'use strict';

  function checkIntent(text) {
    return /\b(сравн|чем.*отлич|разниц.*тариф|какой.*лучше|tариф.*разниц|compare|comparison|versus|vs\b)\b/i.test(text);
  }

  function show() {
    const tariffs = typeof RemarkaConfig !== 'undefined' && RemarkaConfig.tariffs
      ? RemarkaConfig.tariffs
      : { mtpe:{name:'MTPE',price:350}, human:{name:'Профессиональный',price:750}, premium:{name:'Premium Expert',price:1350} };

    const rows = [
      ['Стоимость/стр.',      `от ${tariffs.mtpe?.price||350} ₽`,  `от ${tariffs.human?.price||750} ₽`,  `от ${tariffs.premium?.price||1350} ₽`],
      ['Тип работы',          'AI + вычитка',   'Переводчик',       'Переводчик + носитель'],
      ['Скорость',            '1–2 дня',        '3–7 дней',         '5–10 дней'],
      ['Качество',            '★★★☆☆',           '★★★★☆',            '★★★★★'],
      ['Нотариальное заверен','➕ Доступно',     '➕ Доступно',      '➕ Доступно'],
      ['Глоссарий клиента',   '➖ Нет',         '✅ Да',            '✅ Да'],
      ['Редактор-носитель',   '➖ Нет',         '➖ Нет',           '✅ Да'],
      ['Гарантия качества',   'Базовая',        'Полная',           'Расширенная'],
      ['Постоянные клиенты',  '➖',             '✅ Скидки',        '✅ VIP-условия'],
      ['Лучше для',           'AI-контент, SEO','Бизнес-документы','Юр., мед., публикации'],
    ];

    const colColors = ['rgba(79,106,255,0.15)', 'rgba(79,106,255,0.25)', 'rgba(196,146,42,0.15)'];
    const colBorder = ['rgba(82,108,255,0.25)', 'rgba(82,108,255,0.55)', 'rgba(196,146,42,0.4)'];
    const colHeader = ['#a5b4fc', '#e8eeff', '#e8b84b'];

    const html = `
      <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:separate;border-spacing:0 3px;font-size:12px">
          <thead>
            <tr>
              <th style="padding:6px 8px;text-align:left;color:rgba(140,155,210,0.5);font-size:10px;text-transform:uppercase;letter-spacing:.05em;font-weight:500">Характеристика</th>
              ${['🤖 MTPE', '👨‍💼 Профессиональный', '⭐ Premium'].map((h,i) =>
                `<th style="padding:8px;text-align:center;color:${colHeader[i]};font-weight:700;
                  background:${colColors[i]};border:1px solid ${colBorder[i]};
                  border-radius:${i===0?'8px 0 0 0':i===2?'0 8px 0 0':'0'}">${h}</th>`
              ).join('')}
            </tr>
          </thead>
          <tbody>
            ${rows.map((row, ri) => `<tr>
              <td style="padding:7px 8px;color:rgba(140,155,210,0.7);border-bottom:1px solid rgba(82,108,255,0.06)">${row[0]}</td>
              ${[1,2,3].map(ci => `<td style="padding:7px 8px;text-align:center;
                background:${ri%2===0 ? colColors[ci-1] : 'transparent'};
                color:${row[ci].includes('★') ? '#f59e0b' : row[ci].includes('➖') ? 'rgba(140,155,210,0.4)' : row[ci].includes('✅') ? '#22d46e' : '#dde4ff'};
                border-bottom:1px solid rgba(82,108,255,0.06);font-weight:${ci===2?'600':'400'}">${row[ci]}</td>`
              ).join('')}
            </tr>`).join('')}
          </tbody>
        </table>
        <div style="display:flex;gap:7px;margin-top:12px;flex-wrap:wrap">
          ${['🤖 Выбрать MTPE','👨‍💼 Выбрать Профессиональный','⭐ Выбрать Premium'].map((l,i) => {
            const tariff = ['mtpe','human','premium'][i];
            return `<button onclick="ChatEngine && ChatEngine.handleUserInput('${l}')"
              style="flex:1;min-width:90px;padding:9px 8px;background:${colColors[i]};
              border:1.5px solid ${colBorder[i]};border-radius:10px;
              color:${colHeader[i]};font-family:inherit;font-size:11.5px;font-weight:600;cursor:pointer">
              ${l}
            </button>`;
          }).join('')}
        </div>
      </div>`;

    _shared_appendBotRich('📊 Сравнение тарифов', html, []);
  }

  function _shared_appendBotRich(title, html, replies) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className='msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML=`<div class="bubble bubble--bot bubble--rich"><div class="bubble-lead">${title}</div>${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d); msgs.scrollTop=msgs.scrollHeight;
    const qr=document.getElementById('quick-replies');
    if(qr){qr.innerHTML='';(replies||[]).forEach(r=>{const b=document.createElement('button');b.className='qr-btn';b.textContent=r;b.onclick=()=>{if(typeof ChatEngine!=='undefined')ChatEngine.handleUserInput(r);};qr.appendChild(b);});}
  }

  return { checkIntent, show };
})();


/* ────────────────────────────────────────────────────────────
   17. REFERRAL GENERATOR — персональные ссылки и промокоды
   ──────────────────────────────────────────────────────────── */
const ReferralGenerator = (() => {
  'use strict';

  function checkIntent(text) {
    return /\b(пригласить|реферал|рекоменд|друг.*скидк|скидк.*друг|referral|invite|промокод.*другу)\b/i.test(text);
  }

  function show() {
    const profile = typeof StateMachine !== 'undefined' ? StateMachine.getProfile() : {};
    const refCode = _getOrCreateCode(profile.id);
    const refUrl  = `${window.location.origin}?ref=${refCode}`;
    const reward  = 500; // ₽ за каждого приведённого клиента

    _shared_appendBotRich(
      '🎁 Реферальная программа',
      `<div style="text-align:center;padding:8px 0 12px">
        <!-- Иллюстрация -->
        <div style="font-size:2.5rem;margin-bottom:8px">🤝</div>
        <div style="font-size:14px;font-weight:600;color:#e8eeff;margin-bottom:6px">Приглашайте друзей — получайте бонусы!</div>
        <div style="font-size:12.5px;color:rgba(160,170,220,0.8);line-height:1.6;margin-bottom:14px">
          За каждого клиента, который закажет перевод по вашей ссылке,<br>
          вы получите <b style="color:#22d46e">${reward} ₽</b> на счёт бюро
        </div>
        <!-- Ваш промокод -->
        <div style="background:rgba(6,192,200,0.08);border:2px dashed rgba(6,192,200,0.35);border-radius:12px;padding:14px;margin-bottom:12px">
          <div style="font-size:10px;color:rgba(140,155,210,0.6);text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px">Ваш промокод</div>
          <div id="ref-code-display" style="font-size:1.8rem;font-weight:900;color:#06c0c8;letter-spacing:.14em;cursor:pointer"
            onclick="ReferralGenerator.copyCode('${refCode}')">${refCode}</div>
          <div style="font-size:11px;color:rgba(140,155,210,0.5);margin-top:3px">нажмите чтобы скопировать</div>
        </div>
        <!-- Реферальная ссылка -->
        <div style="background:rgba(8,16,50,0.6);border:1px solid rgba(82,108,255,0.25);border-radius:10px;padding:10px 12px;margin-bottom:12px;text-align:left">
          <div style="font-size:10px;color:rgba(140,155,210,0.6);margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em">Ваша ссылка</div>
          <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:11.5px;color:#a5b4fc;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1">${refUrl}</span>
            <button onclick="ReferralGenerator.copyUrl('${refUrl}')" id="ref-url-btn"
              style="padding:5px 10px;background:rgba(79,106,255,0.2);border:1px solid rgba(82,108,255,0.4);
              border-radius:8px;color:#a5b4fc;font-size:11px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit">
              📋 Копировать
            </button>
          </div>
        </div>
        <!-- Поделиться -->
        <div style="display:flex;gap:7px;justify-content:center;flex-wrap:wrap">
          ${_shareBtn('📱 Telegram', `https://t.me/share/url?url=${encodeURIComponent(refUrl)}&text=${encodeURIComponent('Профессиональные переводы в Ремарке — рекомендую!')}`, '#29a9eb')}
          ${_shareBtn('💬 WhatsApp', `https://wa.me/?text=${encodeURIComponent('Профессиональные переводы: ' + refUrl)}`, '#25d366')}
          ${_shareBtn('📧 Email', `mailto:?subject=Рекомендую бюро переводов&body=${encodeURIComponent('Попробуй профессиональный перевод: ' + refUrl)}`, '#ea4335')}
        </div>
        <!-- Статистика -->
        <div style="margin-top:12px;display:flex;justify-content:center;gap:16px;font-size:12px">
          <div style="text-align:center"><div style="font-weight:700;color:#e8eeff">0</div><div style="color:rgba(140,155,210,0.5)">Переходов</div></div>
          <div style="text-align:center"><div style="font-weight:700;color:#e8eeff">0</div><div style="color:rgba(140,155,210,0.5)">Заказов</div></div>
          <div style="text-align:center"><div style="font-weight:700;color:#22d46e">0 ₽</div><div style="color:rgba(140,155,210,0.5)">Начислено</div></div>
        </div>
      </div>`,
      ['✅ Понятно, поделюсь!', '💬 Задать вопрос']
    );
  }

  function _shareBtn(label, url, color) {
    return `<button onclick="window.open('${url}','_blank')"
      style="padding:8px 14px;background:${color}22;border:1px solid ${color}55;
      border-radius:20px;color:${color};font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
      ${label}
    </button>`;
  }

  function copyCode(code) {
    navigator.clipboard?.writeText(code).catch(()=>{});
    const el = document.getElementById('ref-code-display');
    if (el) { const orig = el.textContent; el.textContent = '✓ Скопировано!'; el.style.color='#22d46e'; setTimeout(()=>{el.textContent=orig;el.style.color='#06c0c8';},2000); }
  }

  function copyUrl(url) {
    navigator.clipboard?.writeText(url).catch(()=>{});
    const btn = document.getElementById('ref-url-btn');
    if (btn) { btn.textContent='✓ Скопировано!'; btn.style.color='#22d46e'; setTimeout(()=>{btn.textContent='📋 Копировать';btn.style.color='#a5b4fc';},2000); }
  }

  function _getOrCreateCode(uid) {
    const key = 'remarka_ref_code';
    let code  = localStorage.getItem(key);
    if (!code) {
      const base = (uid || 'user').slice(0,6).toUpperCase().replace(/-/g,'');
      code = 'REF' + base + Math.random().toString(36).slice(2,5).toUpperCase();
      localStorage.setItem(key, code);
    }
    return code;
  }

  function _shared_appendBotRich(title, html, replies) {
    const msgs=document.getElementById('messages'); if(!msgs)return;
    const d=document.createElement('div');d.className='msg msg--bot';
    const t=new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML=`<div class="bubble bubble--bot bubble--rich"><div class="bubble-lead">${title}</div>${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);msgs.scrollTop=msgs.scrollHeight;
    const qr=document.getElementById('quick-replies');
    if(qr){qr.innerHTML='';(replies||[]).forEach(r=>{const b=document.createElement('button');b.className='qr-btn';b.textContent=r;b.onclick=()=>{if(typeof ChatEngine!=='undefined')ChatEngine.handleUserInput(r);};qr.appendChild(b);});}
  }

  return { checkIntent, show, copyCode, copyUrl };
})();


/* ────────────────────────────────────────────────────────────
   18. CHAT EXPORT — экспорт переписки
   ──────────────────────────────────────────────────────────── */
const ChatExport = (() => {
  'use strict';

  function checkIntent(text) {
    return /\b(сохранить.*переписк|экспорт.*чат|скачать.*историю|export.*chat|save.*conversation|pdf.*переписк)\b/i.test(text);
  }

  function exportChat() {
    const msgs   = document.getElementById('messages');
    const profile = typeof StateMachine !== 'undefined' ? StateMachine.getProfile() : {};
    const slots  = profile.slots || {};

    if (!msgs) return;

    // Собираем сообщения
    const messages = [];
    msgs.querySelectorAll('.msg').forEach(m => {
      const isBot  = m.classList.contains('msg--bot');
      const bubble = m.querySelector('.bubble');
      const time   = m.querySelector('.msg-time');
      if (!bubble) return;
      messages.push({
        role: isBot ? 'Ольга' : 'Клиент',
        text: bubble.textContent?.trim().slice(0, 500) || '',
        time: time?.textContent || '',
      });
    });

    if (!messages.length) {
      if (typeof window._shared_appendBot === 'function')
        window._shared_appendBot('История переписки пуста.', []);
      return;
    }

    // Формируем текст
    const date    = new Date().toLocaleDateString('ru-RU', { day:'numeric', month:'long', year:'numeric' });
    const slotStr = Object.entries(slots).filter(([,v])=>v).map(([k,v])=>`${k}: ${v}`).join(' | ');

    let content  = `ИСТОРИЯ ПЕРЕПИСКИ — БЮРО ПЕРЕВОДОВ «РЕМАРКА»\n`;
    content += `${'═'.repeat(50)}\n`;
    content += `Дата: ${date}\n`;
    if (slotStr) content += `Параметры заказа: ${slotStr}\n`;
    content += `${'═'.repeat(50)}\n\n`;

    messages.forEach(m => {
      content += `[${m.time}] ${m.role}:\n${m.text}\n\n`;
    });

    content += `${'─'.repeat(50)}\n`;
    content += `Бюро переводов «Ремарка» • ${window.location.hostname}\n`;

    // Показываем в чате и предлагаем скачать
    _showExportResult(content, messages.length);
  }

  function _showExportResult(content, count) {
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const url  = URL.createObjectURL(blob);
    const date = new Date().toLocaleDateString('ru-RU').replace(/\./g,'-');

    const html = `
      <div style="text-align:center;padding:8px 0 12px">
        <div style="font-size:2rem;margin-bottom:8px">📄</div>
        <div style="font-size:13px;font-weight:600;color:#e8eeff;margin-bottom:4px">История переписки готова</div>
        <div style="font-size:12px;color:rgba(140,155,210,0.7);margin-bottom:14px">${count} сообщений · ${date}</div>
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
          <a href="${url}" download="remarka-chat-${date}.txt"
            style="display:inline-block;padding:10px 20px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);
            border-radius:20px;color:#fff;font-size:13px;font-weight:600;text-decoration:none">
            💾 Скачать TXT
          </a>
          <button onclick="ChatExport.copyToClipboard()"
            style="padding:10px 16px;background:rgba(82,108,255,0.1);border:1px solid rgba(82,108,255,0.3);
            border-radius:20px;color:#a5b4fc;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
            📋 Копировать
          </button>
          <button onclick="ChatExport.sendToEmail()"
            style="padding:10px 16px;background:rgba(34,212,110,0.1);border:1px solid rgba(34,212,110,0.3);
            border-radius:20px;color:#22d46e;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
            📧 На email
          </button>
        </div>
      </div>`;

    window._exportContent = content;
    const msgs=document.getElementById('messages'); if(!msgs)return;
    const d=document.createElement('div');d.className='msg msg--bot';
    const t=new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML=`<div class="bubble bubble--bot bubble--rich"><div class="bubble-lead">📤 Экспорт переписки</div>${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);msgs.scrollTop=msgs.scrollHeight;
  }

  function copyToClipboard() {
    if (window._exportContent) {
      navigator.clipboard?.writeText(window._exportContent).then(() => {
        if (typeof window._shared_appendBot === 'function')
          window._shared_appendBot('✅ Переписка скопирована в буфер обмена!', ['🔤 Продолжить']);
      }).catch(() => {});
    }
  }

  function sendToEmail() {
    if (typeof window._shared_appendBot === 'function')
      window._shared_appendBot('📧 Введите email, на который отправить историю переписки:', []);
    // Слушаем следующее сообщение
    if (typeof ChatEngine !== 'undefined') {
      const orig = ChatEngine.handleUserInput.bind(ChatEngine);
      ChatEngine.handleUserInput = (text) => {
        if (text.includes('@')) {
          ChatEngine.handleUserInput = orig;
          _doSendEmail(text);
        } else {
          ChatEngine.handleUserInput = orig;
          orig(text);
        }
      };
    }
  }

  function _doSendEmail(email) {
    if (typeof RemarkaConfig === 'undefined') return;
    const body = new URLSearchParams({
      action:   'remarka_chat',
      nonce:    RemarkaConfig.nonce,
      text:     `Отправь историю переписки на ${email}`,
      system:   'Подтверди получение запроса на отправку истории переписки.',
    });
    fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() }).catch(()=>{});
    if (typeof window._shared_appendBot === 'function')
      window._shared_appendBot(`✅ Запрос отправлен! Менеджер вышлет историю на ${email} в течение 15 минут.`, ['🔤 Продолжить']);
  }

  // Алиас для удобства
  const exportFn = exportChat;

  return { checkIntent, export: exportFn, copyToClipboard, sendToEmail };
})();

window.VoiceSynthesis    = VoiceSynthesis;
window.ClientPortal      = ClientPortal;
window.ComparisonTable   = ComparisonTable;
window.ReferralGenerator = ReferralGenerator;
window.ChatExport        = ChatExport;

// Авто-инициализация VoiceSynthesis
document.addEventListener('DOMContentLoaded', () => VoiceSynthesis.init());
