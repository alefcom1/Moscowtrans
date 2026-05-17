<?php
defined( 'ABSPATH' ) || exit;

/**
 * Template: chat-widget.php
 * Вставляется через шорткод или auto_inject.
 * Все настройки из WP передаются через RemarkaConfig (wp_localize_script)
 * и через data-атрибуты на корневом элементе.
 */

$agent_name   = remarka_option( 'agent_name', 'Ольга' );
$context      = $context ?? 'auto';  // Передаётся из shortcode.php
$tariffs      = remarka_get_tariffs();
$active_langs = remarka_option( 'active_languages', ['ru','en','it'] );
?>

<div
    id="remarka-app"
    class="remarka-app"
    data-page-context="<?= esc_attr( $context ) ?>"
    data-agent-name="<?= esc_attr( $agent_name ) ?>"
    role="main"
    aria-label="AI Консультант <?= esc_attr( $agent_name ) ?>"
>

  <!-- ═══ BACKGROUND ═══ -->
  <div class="remarka-bg" aria-hidden="true">
    <div class="remarka-bg__dots"></div>
    <div class="remarka-bg__worldmap">
      <svg viewBox="0 0 1200 600" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g fill="none" stroke="rgba(82,108,255,0.9)" stroke-width="0.8">
          <path d="M95,80 L85,95 L70,110 L60,130 L55,155 L65,175 L80,190 L100,200 L120,215 L135,235 L145,260 L150,285 L145,305 L135,320 L125,330 L140,335 L155,330 L165,315 L175,295 L185,275 L195,255 L205,240 L215,225 L220,205 L215,185 L205,165 L195,148 L185,132 L175,115 L162,100 L148,88 L130,80 L110,75 Z"/>
          <path d="M55,155 L40,160 L30,175 L25,195 L35,210 L50,215 L60,205 L65,185 L65,175 Z"/>
          <path d="M148,375 L138,390 L130,410 L125,435 L120,460 L118,490 L122,515 L130,535 L145,550 L165,558 L188,555 L210,545 L228,528 L238,505 L242,480 L240,452 L232,425 L220,400 L205,380 L188,368 L170,362 L155,365 Z"/>
          <path d="M490,65 L480,78 L470,95 L468,115 L475,128 L490,135 L508,138 L525,132 L535,118 L532,100 L520,85 L505,72 Z"/>
          <path d="M480,175 L465,190 L455,210 L452,235 L455,265 L462,295 L472,325 L488,352 L508,372 L532,385 L558,390 L582,385 L605,368 L620,345 L630,318 L632,288 L628,258 L618,230 L605,205 L588,185 L568,172 L545,168 L520,170 L498,175 Z"/>
          <path d="M558,35 L580,42 L610,45 L650,40 L695,38 L740,35 L780,32 L820,30 L860,32 L892,35 L920,42 L940,50 L935,62 L915,68 L888,65 L858,58 L825,52 L790,50 L755,52 L718,58 L680,62 L645,62 L610,58 L578,52 L558,45 Z"/>
          <path d="M725,80 L715,98 L712,120 L718,145 L730,168 L748,188 L770,202 L795,210 L820,208 L842,198 L858,182 L865,162 L862,140 L852,118 L835,100 L815,88 L795,82 L770,80 L748,80 Z"/>
          <path d="M748,190 L738,208 L730,230 L728,258 L735,285 L748,308 L768,322 L790,325 L810,318 L825,300 L830,278 L826,252 L815,228 L800,208 L782,195 L762,190 Z"/>
          <path d="M848,368 L835,385 L828,408 L830,435 L842,460 L862,480 L888,492 L918,496 L948,490 L972,475 L988,452 L992,425 L985,398 L968,375 L945,360 L918,352 L888,352 L865,358 Z"/>
        </g>
        <g stroke="rgba(79,106,255,0.22)" stroke-width="0.6" stroke-dasharray="4 6" fill="none">
          <path d="M150,220 Q400,150 600,200"/>
          <path d="M600,200 Q750,180 900,180"/>
          <path d="M150,220 Q300,350 500,300"/>
          <path d="M500,300 Q650,350 800,320"/>
        </g>
        <g fill="rgba(79,106,255,0.5)">
          <circle cx="150" cy="220" r="3"/><circle cx="600" cy="200" r="3"/>
          <circle cx="900" cy="180" r="3"/><circle cx="500" cy="300" r="2.5"/>
        </g>
      </svg>
    </div>
    <div class="remarka-bg__glow-bottom" aria-hidden="true"></div>
    <div class="remarka-bg__glow-left"   aria-hidden="true"></div>
    <div class="remarka-bg__glow-right"  aria-hidden="true"></div>
  </div>

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="remarka-sidebar" role="navigation" aria-label="Панель навигации">

    <div class="remarka-sidebar__logo" title="Ремарка">R</div>

    <nav class="remarka-sidebar__nav">
      <button class="remarka-sb-item remarka-sb-item--active js-sb-item" data-section="chat" title="<?= esc_attr__('Чат','remarka-chat') ?>" aria-label="Чат" aria-pressed="true">
        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="remarka-sb-item__label">Чат</span>
      </button>

      <button class="remarka-sb-item js-sb-item" data-section="upload" title="Загрузить файл" aria-label="Загрузить файл" aria-pressed="false">
        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <span class="remarka-sb-item__label">Загрузить файл</span>
      </button>

      <button class="remarka-sb-item js-sb-item" data-section="calc" title="Калькулятор" aria-label="Калькулятор стоимости" aria-pressed="false">
        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="10" y2="10"/><line x1="14" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="10" y2="14"/><line x1="14" y1="14" x2="16" y2="14"/><line x1="8" y1="18" x2="10" y2="18"/><line x1="14" y1="18" x2="16" y2="18"/></svg>
        <span class="remarka-sb-item__label">Калькулятор</span>
      </button>

      <button class="remarka-sb-item js-sb-item" data-section="history" title="История заказов" aria-label="История заказов" aria-pressed="false">
        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3h7v7H3z"/><path d="M14 3h7v7h-7z"/><path d="M14 14h7v7h-7z"/><path d="M3 14h7v7H3z"/></svg>
        <span class="remarka-sb-item__label">История заказов</span>
      </button>

      <button class="remarka-sb-item js-sb-item" data-section="quality" title="Качество перевода" aria-label="Проверить качество" aria-pressed="false">
        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M8 11h6M11 8v6"/></svg>
        <span class="remarka-sb-item__label">Качество перевода</span>
      </button>

      <button class="remarka-sb-item js-sb-item" data-section="complexity" title="Сложность текста" aria-label="Определить сложность" aria-pressed="false">
        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        <span class="remarka-sb-item__label">Сложность текста</span>
      </button>

      <button class="remarka-sb-item js-sb-item" data-section="blog" title="Блог" aria-label="Блог" aria-pressed="false">
        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="8" y1="7" x2="16" y2="7"/><line x1="8" y1="11" x2="16" y2="11"/><line x1="8" y1="15" x2="13" y2="15"/></svg>
        <span class="remarka-sb-item__label">Блог</span>
      </button>
    </nav>

  </aside>

  <!-- ═══ MAIN ═══ -->
  <main class="remarka-main" role="main">

    <!-- NAVBAR -->
    <header class="remarka-navbar" role="banner">
      <div class="remarka-navbar__brand">
        <div class="remarka-navbar__logo" aria-hidden="true">R</div>
        <div>
          <div class="remarka-navbar__name">Ремарка</div>
          <div class="remarka-navbar__sub">Бюро переводов</div>
        </div>
      </div>

      <nav class="remarka-navbar__links" aria-label="Навигация сайта">
        <a href="<?= esc_url( home_url('/uslugi') ) ?>"          class="remarka-navbar__link">Услуги</a>
        <a href="<?= esc_url( home_url('/otrasli') ) ?>"         class="remarka-navbar__link">Отрасли</a>
        <a href="<?= esc_url( home_url('/kak-my-rabotaem') ) ?>" class="remarka-navbar__link">Как мы работаем</a>
        <a href="<?= esc_url( home_url('/keys') ) ?>"            class="remarka-navbar__link">Кейсы</a>
        <a href="<?= esc_url( home_url('/o-byuro') ) ?>"         class="remarka-navbar__link">О бюро</a>
        <a href="<?= esc_url( home_url('/kontakty') ) ?>"        class="remarka-navbar__link">Контакты</a>
      </nav>

      <div class="remarka-navbar__right">

        <!-- Переключатель языков -->
        <?php if ( count( $active_langs ) > 1 ) : ?>
        <div class="remarka-lang-switcher" role="group" aria-label="Язык интерфейса">
          <?php
          $lang_meta = [ 'ru' => '🇷🇺 RU', 'en' => '🇬🇧 EN', 'it' => '🇮🇹 IT' ];
          foreach ( $active_langs as $lc ) : ?>
            <button
              class="remarka-lang-btn <?= $lc === 'ru' ? 'remarka-lang-btn--active' : '' ?>"
              data-lang="<?= esc_attr($lc) ?>"
              aria-label="<?= esc_attr($lc) ?>"
            ><?= $lang_meta[$lc] ?? $lc ?></button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( is_user_logged_in() ) : ?>
          <a href="<?= esc_url( wp_logout_url( get_permalink() ) ) ?>" class="remarka-btn-login">Выйти</a>
        <?php else : ?>
          <a href="<?= esc_url( wp_login_url( get_permalink() ) ) ?>" class="remarka-btn-login">Войти</a>
        <?php endif; ?>
      </div>
    </header>

    <!-- CHAT ZONE -->
    <div class="remarka-chat-zone">

      <!-- Glass window -->
      <div class="remarka-chat-window" role="dialog" aria-label="Чат с консультантом" aria-live="polite">

        <!-- macOS chrome -->
        <div class="remarka-window-chrome" aria-hidden="true">
          <span class="remarka-chrome-dot remarka-chrome-dot--red"></span>
          <span class="remarka-chrome-dot remarka-chrome-dot--amber"></span>
          <span class="remarka-chrome-dot remarka-chrome-dot--green"></span>
        </div>

        <div class="remarka-window-body">

          <!-- AGENT COLUMN -->
          <aside class="remarka-agent-col" aria-label="Информация об агенте">
            <div class="remarka-agent-avatar-wrap">
              <div class="remarka-agent-ring"      aria-hidden="true"></div>
              <div class="remarka-agent-ring-mask" aria-hidden="true"></div>
              <div class="remarka-agent-avatar" id="remarka-agent-avatar">
                <?php
                $avatar_url = remarka_option('agent_avatar_url', '');
                if ( $avatar_url ) : ?>
                  <img src="<?= esc_url($avatar_url) ?>" alt="<?= esc_attr($agent_name) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                <?php else : ?>
                  <!-- SVG placeholder -->
                  <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                      <linearGradient id="rh" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#5c3a1e"/><stop offset="100%" stop-color="#3a2010"/></linearGradient>
                      <radialGradient id="rs" cx="50%" cy="40%" r="50%"><stop offset="0%" stop-color="#e8c9a0"/><stop offset="100%" stop-color="#c8a070"/></radialGradient>
                      <linearGradient id="rsuit" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#1a2a5a"/><stop offset="100%" stop-color="#0d1a3a"/></linearGradient>
                    </defs>
                    <ellipse cx="40" cy="24" rx="18" ry="16" fill="url(#rh)"/>
                    <ellipse cx="40" cy="28" rx="16" ry="12" fill="url(#rs)"/>
                    <ellipse cx="40" cy="30" rx="14" ry="14" fill="url(#rs)"/>
                    <ellipse cx="26" cy="28" rx="5" ry="10" fill="url(#rh)"/>
                    <ellipse cx="54" cy="28" rx="5" ry="10" fill="url(#rh)"/>
                    <ellipse cx="34" cy="27" rx="2.5" ry="2.8" fill="#2d1a0e"/>
                    <ellipse cx="46" cy="27" rx="2.5" ry="2.8" fill="#2d1a0e"/>
                    <path d="M31 23.5 Q34 22 37 23.5" stroke="#3a2010" stroke-width="1.2" stroke-linecap="round" fill="none"/>
                    <path d="M43 23.5 Q46 22 49 23.5" stroke="#3a2010" stroke-width="1.2" stroke-linecap="round" fill="none"/>
                    <path d="M35 34 Q40 38 45 34" stroke="rgba(160,80,60,0.7)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    <rect x="35" y="42" width="10" height="8" rx="2" fill="url(#rs)"/>
                    <path d="M18 80 L18 58 Q18 52 28 50 L40 48 L52 50 Q62 52 62 58 L62 80 Z" fill="url(#rsuit)"/>
                    <path d="M35 48 L40 55 L45 48 L40 50 Z" fill="rgba(255,255,255,0.9)"/>
                    <path d="M28 50 L35 48 L38 56 L28 62 Z" fill="#0d1840"/>
                    <path d="M52 50 L45 48 L42 56 L52 62 Z" fill="#0d1840"/>
                  </svg>
                <?php endif; ?>
              </div>
              <div class="remarka-agent-online" aria-label="Онлайн"></div>
            </div>

            <div class="remarka-agent-name"><?= esc_html( $agent_name ) ?></div>
            <div class="remarka-agent-title">Персональный менеджер<br>бюро переводов</div>

            <?php if ( ! empty($active_langs) ) : ?>
            <div class="remarka-agent-flags" aria-label="Языки">
              <?php foreach ( $active_langs as $lc ) :
                $flags = ['ru'=>'🇷🇺','en'=>'🇬🇧','it'=>'🇮🇹'];
                echo $flags[$lc] ?? '';
              endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="remarka-agent-divider" aria-hidden="true"></div>

            <div class="remarka-agent-stats">
              <div class="remarka-stat-row"><span class="remarka-stat-lbl">Ответ</span><span class="remarka-stat-val">~1 мин</span></div>
              <div class="remarka-stat-row"><span class="remarka-stat-lbl">Рейтинг</span><span class="remarka-stat-val">4.98 ★</span></div>
              <div class="remarka-stat-row"><span class="remarka-stat-lbl">Заказов</span><span class="remarka-stat-val">2 400+</span></div>
              <div class="remarka-stat-row"><span class="remarka-stat-lbl">Языков</span><span class="remarka-stat-val">20+</span></div>
            </div>
          </aside>

          <!-- MESSAGES COLUMN -->
          <div class="remarka-messages-col">

            <!-- SVG Wave decoration -->
            <svg class="remarka-wave-deco" viewBox="0 0 420 170" fill="none" aria-hidden="true">
              <defs>
                <linearGradient id="rwg1" x1="0" y1="0" x2="420" y2="0" gradientUnits="userSpaceOnUse">
                  <stop offset="0%"   stop-color="#4f6aff" stop-opacity="0"/>
                  <stop offset="35%"  stop-color="#7c5cfc" stop-opacity="0.65"/>
                  <stop offset="65%"  stop-color="#4f6aff" stop-opacity="0.45"/>
                  <stop offset="100%" stop-color="#06c0c8" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="rwg2" x1="0" y1="0" x2="450" y2="0" gradientUnits="userSpaceOnUse">
                  <stop offset="0%"   stop-color="#7c5cfc" stop-opacity="0"/>
                  <stop offset="45%"  stop-color="#4f6aff" stop-opacity="0.45"/>
                  <stop offset="100%" stop-color="#06c0c8" stop-opacity="0"/>
                </linearGradient>
                <radialGradient id="rorbg"><stop offset="0%" stop-color="#7c5cfc" stop-opacity="0.2"/><stop offset="100%" stop-color="#7c5cfc" stop-opacity="0"/></radialGradient>
              </defs>
              <circle cx="300" cy="85" r="50" fill="url(#rorbg)"/>
              <path class="remarka-wave-p remarka-wp1" d="M0,85 C55,45 110,125 165,85 C220,45 275,115 330,80 C365,62 395,82 420,74" stroke="url(#rwg1)" stroke-width="2" fill="none"/>
              <path class="remarka-wave-p remarka-wp2" d="M0,78 C65,38 130,118 195,78 C260,38 325,108 390,72" stroke="url(#rwg2)" stroke-width="1.4" fill="none"/>
            </svg>

            <!-- Voice orb -->
            <button class="remarka-wave-orb" id="remarka-mic-orb" onclick="ChatEngine.toggleMic()" aria-label="Голосовой ввод" title="Голосовой ввод">
              <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                <line x1="12" y1="19" x2="12" y2="23"/>
                <line x1="8" y1="23" x2="16" y2="23"/>
              </svg>
            </button>

            <!-- Messages list -->
            <div class="remarka-messages" id="messages" role="log" aria-live="polite" aria-label="Сообщения чата"></div>

            <!-- Quick replies -->
            <div class="remarka-quick-replies" id="quick-replies" role="group" aria-label="Быстрые ответы"></div>

            <!-- Input area -->
            <div class="remarka-input-area" role="form" aria-label="Форма отправки сообщения">
              <div class="remarka-input-wrap">
                <textarea
                  class="remarka-input"
                  id="chat-input"
                  rows="1"
                  placeholder="Опишите задачу или загрузите файл..."
                  aria-label="Введите сообщение"
                  onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();ChatEngine.sendInput()}"
                  oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,110)+'px'"
                ></textarea>
                <button class="remarka-attach-btn" onclick="document.getElementById('remarka-file-inp').click()" title="Прикрепить файл" aria-label="Прикрепить файл">
                  <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                  </svg>
                </button>
                <input
                  id="remarka-file-inp"
                  type="file"
                  accept=".txt,.md,.doc,.docx,.pdf,.xlsx,.xls,.csv,.html,.srt,.odt,.rtf"
                  style="display:none"
                  aria-hidden="true"
                  onchange="if(this.files[0]) ChatEngine.handleFileUpload(this.files[0])"
                >
              </div>

              <button class="remarka-icon-btn" id="mic-btn" onclick="ChatEngine.toggleMic()" title="Голосовой ввод" aria-label="Голосовой ввод">
                <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                  <line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/>
                </svg>
              </button>

              <button class="remarka-send-btn" onclick="ChatEngine.sendInput()" title="Отправить (Enter)" aria-label="Отправить сообщение">
                <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                  <line x1="22" y1="2" x2="11" y2="13"/>
                  <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
              </button>
            </div>

          </div><!-- /messages-col -->
        </div><!-- /window-body -->
      </div><!-- /chat-window -->

      <!-- SERVICE CARDS -->
      <div class="remarka-service-strip" role="list" aria-label="Типы перевода">

        <div class="remarka-svc-card remarka-svc-card--v1" role="listitem" tabindex="0"
          onclick="ChatEngine.handleUserInput('Нужен технический перевод')"
          onkeydown="if(event.key==='Enter') ChatEngine.handleUserInput('Нужен технический перевод')">
          <div class="remarka-svc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
          <div class="remarka-svc-title">Технический перевод</div>
          <div class="remarka-svc-desc">Инструкции, руководства, спецификации, чертежи</div>
          <div class="remarka-svc-arrow" aria-hidden="true">→</div>
        </div>

        <div class="remarka-svc-card remarka-svc-card--v2" role="listitem" tabindex="0"
          onclick="ChatEngine.handleUserInput('Нужен юридический перевод')"
          onkeydown="if(event.key==='Enter') ChatEngine.handleUserInput('Нужен юридический перевод')">
          <div class="remarka-svc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
          <div class="remarka-svc-title">Юридический перевод</div>
          <div class="remarka-svc-desc">Договоры, контракты, учредительные документы</div>
          <div class="remarka-svc-arrow" aria-hidden="true">→</div>
        </div>

        <div class="remarka-svc-card remarka-svc-card--v3" role="listitem" tabindex="0"
          onclick="ChatEngine.handleUserInput('Нужен медицинский перевод')"
          onkeydown="if(event.key==='Enter') ChatEngine.handleUserInput('Нужен медицинский перевод')">
          <div class="remarka-svc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="3"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></div>
          <div class="remarka-svc-title">Медицинский перевод</div>
          <div class="remarka-svc-desc">Исследования, протоколы, заключения, инструкции</div>
          <div class="remarka-svc-arrow" aria-hidden="true">→</div>
        </div>

        <div class="remarka-svc-card remarka-svc-card--v4" role="listitem" tabindex="0"
          onclick="ChatEngine.handleUserInput('Нужен IT перевод')"
          onkeydown="if(event.key==='Enter') ChatEngine.handleUserInput('Нужен IT перевод')">
          <div class="remarka-svc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
          <div class="remarka-svc-title">IT перевод</div>
          <div class="remarka-svc-desc">Интерфейсы, документация, локализация ПО</div>
          <div class="remarka-svc-arrow" aria-hidden="true">→</div>
        </div>

        <div class="remarka-svc-card remarka-svc-card--v5" role="listitem" tabindex="0"
          onclick="ChatEngine.handleUserInput('Нужен перевод сайта')"
          onkeydown="if(event.key==='Enter') ChatEngine.handleUserInput('Нужен перевод сайта')">
          <div class="remarka-svc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
          <div class="remarka-svc-title">Перевод сайтов</div>
          <div class="remarka-svc-desc">Локализация, SEO адаптация, международный рынок</div>
          <div class="remarka-svc-arrow" aria-hidden="true">→</div>
        </div>

      </div><!-- /service-strip -->

    </div><!-- /chat-zone -->
  </main><!-- /main -->
</div><!-- /#remarka-app -->
