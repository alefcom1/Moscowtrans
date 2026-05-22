<?php
/**
 * Hero chat window — reusable across all pages.
 *
 * $args:
 *   greeting_1  — first Olga message
 *   greeting_2  — second Olga message
 *   greeting_3  — third Olga message
 *   breadcrumb  — current page name for breadcrumb (empty on homepage)
 *   home_dots   — true for homepage (shows colored dots), false for service pages (shows breadcrumb)
 */
$g1         = $args['greeting_1'] ?? 'Здравствуйте! 👋 Я Ольга, менеджер бюро переводов «Ремарка». Чем могу вам помочь сегодня?';
$g2         = $args['greeting_2'] ?? 'Вы можете общаться со мной голосовыми сообщениями.';
$g3         = $args['greeting_3'] ?? 'Кроме русского я понимаю английский и итальянский. 🇷🇺&nbsp;🇬🇧&nbsp;🇮🇹';
$breadcrumb = $args['breadcrumb'] ?? '';
$home_dots  = $args['home_dots']  ?? false;
$img_uri    = get_template_directory_uri() . '/assets/images/olga.jpg';
?>

  <div class="hero-bg-block">
  <div class="hero-page">

    <?php get_template_part('template-parts/sidebar-chat'); ?>

    <div class="hero-main">
      <div class="chat-window">

        <div class="cw-top-bar">
          <?php if ($home_dots): ?>
            <div class="cw-dots">
              <span class="cw-dot cw-dot-red"></span>
              <span class="cw-dot cw-dot-yellow"></span>
              <span class="cw-dot cw-dot-green"></span>
            </div>
          <?php else: ?>
            <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
              <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
              <span class="cw-bc-sep" aria-hidden="true">›</span>
              <span class="cw-bc-current" aria-current="page"><?php echo esc_html($breadcrumb); ?></span>
            </nav>
          <?php endif; ?>
          <button class="cw-close" aria-label="Закрыть">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="cw-body">
          <div class="cw-agent-panel">
            <div class="agent-avatar-wrap">
              <div class="agent-avatar"><img src="<?php echo esc_url($img_uri); ?>" alt="Ольга"></div>
              <div class="agent-online-dot"></div>
            </div>
            <div class="agent-name">Ольга</div>
            <div class="agent-role">Менеджер<br>бюро переводов</div>
            <div class="agent-langs">
              <button class="lang-flag lang-flag--active" data-lang="ru-RU" title="Русский">🇷🇺</button>
              <button class="lang-flag" data-lang="en-US" title="English">🇬🇧</button>
              <button class="lang-flag" data-lang="it-IT" title="Italiano">🇮🇹</button>
            </div>
            <hr class="agent-sep">
            <ul class="agent-stats">
              <li><span class="asl">Ответ</span><span class="asv">~1 мин</span></li>
              <li><span class="asl">Рейтинг</span><span class="asv">4.98 ★</span></li>
              <li><span class="asl">Заказов</span><span class="asv">2 400+</span></li>
              <li><span class="asl">Языков</span><span class="asv">20+</span></li>
            </ul>
            <hr class="agent-sep">
            <svg class="waveform-svg" id="waveformSvg" viewBox="0 0 220 120" xmlns="http://www.w3.org/2000/svg">
              <defs><linearGradient id="wg" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#00A0F0" stop-opacity="0.6"/><stop offset="50%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0" stop-opacity="0.6"/></linearGradient></defs>
              <path class="wp wp1" stroke="url(#wg)" stroke-width="2.5" fill="none" d="M0,60 C18,35 36,85 54,60 C72,35 90,15 108,60 C126,105 144,25 162,60 C180,95 200,40 220,60"/>
              <path class="wp wp2" stroke="url(#wg)" stroke-width="1.5" fill="none" opacity="0.45" d="M0,60 C20,45 40,75 60,60 C80,45 100,28 120,60 C140,92 160,32 180,60 C200,88 210,48 220,60"/>
              <path class="wp wp3" stroke="url(#wg)" stroke-width="1" fill="none" opacity="0.25" d="M0,60 C15,50 30,70 45,60 C60,50 75,38 90,60 C105,82 120,42 135,60 C150,78 165,50 180,60 C195,70 208,54 220,60"/>
            </svg>
            <button class="cw-mic-btn" id="cwMicBtn" aria-label="Голосовой ввод">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
              <span class="mic-tooltip">Голосовой ввод</span>
            </button>
          </div>

          <div class="cw-right-col">
            <div class="cw-msgs-col">
              <div class="cw-msgs">
                <div class="msg-bub" data-greeting="1"><p><?php echo wp_kses_post($g1); ?></p><span class="msg-ts">10:30</span></div>
                <div class="msg-bub" data-greeting="2"><p><?php echo wp_kses_post($g2); ?></p><span class="msg-ts">10:30</span></div>
                <div class="msg-bub" data-greeting="3"><p><?php echo wp_kses_post($g3); ?></p><span class="msg-ts">10:30</span></div>
              </div>
            </div>
            <div class="cw-input-bar">
              <input class="cw-input" type="text" placeholder="Опишите задачу или загрузите файл..." id="cwInput">
              <div class="cw-btns">
                <button class="cw-btn-icon" title="Прикрепить файл"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg></button>
                <button class="cw-btn-icon cw-btn-voice" id="cwVoiceBtn" title="Голосовой ввод"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg></button>
                <button class="cw-btn-send" title="Отправить"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg></button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div><!-- /hero-page -->
