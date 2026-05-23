<?php
/**
 * Template Name: Контакты
 */
get_header();
?>

  <!-- ════════ HERO ════════ -->
  <section class="contacts-hero">
    <div class="container">
      <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
        <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
        <span class="cw-bc-sep" aria-hidden="true">›</span>
        <span class="cw-bc-current" aria-current="page">Контакты</span>
      </nav>
      <h1 class="contacts-hero__title">Свяжитесь с нами</h1>
      <p class="contacts-hero__sub">Ответим в течение 30 минут в рабочее время · Пн–Пт 9:00–18:00 МСК</p>
    </div>
  </section>

  <!-- ════════ MAIN ════════ -->
  <main class="contacts-main">
    <div class="container">
      <div class="contacts-layout">

        <!-- Left: contact cards -->
        <div class="contacts-left">

          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div class="contact-card__body">
              <div class="contact-card__label">Телефон</div>
              <a class="contact-card__value" href="tel:+74959704413">+7 (495) 970-44-13</a>
              <div class="contact-card__sub">Москва, многоканальный</div>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-card__icon contact-card__icon--green">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
            </div>
            <div class="contact-card__body">
              <div class="contact-card__label">WhatsApp</div>
              <a class="contact-card__value" href="https://wa.me/79859704413" target="_blank" rel="noopener">+7 (985) 970-44-13</a>
              <div class="contact-card__sub">Пишите — ответим быстро</div>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div class="contact-card__body">
              <div class="contact-card__label">Электронная почта</div>
              <a class="contact-card__value" href="mailto:info@moscowtrans.ru">info@moscowtrans.ru</a>
              <div class="contact-card__sub">Для запросов и документов</div>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
            </div>
            <div class="contact-card__body">
              <div class="contact-card__label">Адрес офиса</div>
              <div class="contact-card__value">125009, Москва,<br>Глинищевский пер., д.&nbsp;6, оф.&nbsp;2</div>
              <div class="contact-card__sub">м. Охотный Ряд / Тверская</div>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="contact-card__body">
              <div class="contact-card__label">Часы работы</div>
              <div class="contact-card__value">Пн–Пт &nbsp;9:00–18:00 (МСК)</div>
              <div class="contact-card__sub">В нерабочее время — WhatsApp</div>
            </div>
          </div>

          <div class="req-card">
            <div class="req-card__title">Реквизиты</div>
            <div class="req-card__grid">
              <div class="req-card__row"><span class="req-card__key">Компания</span><span class="req-card__val">ИП Волшина Елизавета Максимовна</span></div>
              <div class="req-card__row"><span class="req-card__key">ИНН</span><span class="req-card__val">231149349191</span></div>
              <div class="req-card__row"><span class="req-card__key">ОГРНИП</span><span class="req-card__val">323237500359402</span></div>
            </div>
          </div>

        </div><!-- /contacts-left -->

        <!-- Right: form + map -->
        <div class="contacts-right">

          <div class="contact-form-wrap">
            <h2 class="contact-form-title">Написать нам</h2>
            <p class="contact-form-sub">Ответим в течение 30 минут в рабочее время</p>

            <form id="contactForm" novalidate>
              <?php wp_nonce_field('remarka_contact_nonce', 'contact_nonce'); ?>
              <div class="cf-row cf-row--2">
                <div class="form-group">
                  <label for="cf-name">Ваше имя <span class="req">*</span></label>
                  <input type="text" id="cf-name" name="name" placeholder="Иван Петров" required autocomplete="name">
                </div>
                <div class="form-group">
                  <label for="cf-phone">Телефон <span class="req">*</span></label>
                  <input type="tel" id="cf-phone" name="phone" placeholder="+7 (000) 000-00-00" required autocomplete="tel">
                </div>
              </div>
              <div class="form-group">
                <label for="cf-email">E-mail <span class="req">*</span></label>
                <input type="email" id="cf-email" name="email" placeholder="ivan@example.com" required autocomplete="email">
              </div>
              <div class="form-group">
                <label for="cf-message">Сообщение</label>
                <textarea id="cf-message" name="message" rows="4" placeholder="Опишите вашу задачу: языки, тип документа, срок, объём..."></textarea>
              </div>
              <button type="submit" class="cf-submit-btn" id="cfSubmit">
                <span class="cf-btn-text">Отправить сообщение</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </button>
              <p class="form-notice">Нажимая кнопку, вы соглашаетесь с <a href="<?php echo esc_url(home_url('/politika-konfidenczialnosti/')); ?>">политикой конфиденциальности</a>.</p>
            </form>

            <div class="form-success" id="cfSuccess" hidden>
              <div class="form-success__icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <p class="form-success__title">Сообщение отправлено!</p>
              <p class="form-success__text">Ответим в течение 30 минут в рабочее время.</p>
            </div>
          </div>

          <!-- Яндекс Карта -->
          <div class="contact-map">
            <div class="contact-map__label">Москва, Глинищевский пер., д.&nbsp;6</div>
            <div class="contact-map__frame">
              <iframe
                src="https://yandex.ru/map-widget/v1/?ol=biz&oid=51867347382"
                title="Карта: Глинищевский пер., д. 6, Москва"
                loading="lazy"
                allowfullscreen
              ></iframe>
            </div>
          </div>

        </div><!-- /contacts-right -->

      </div><!-- /contacts-layout -->
    </div>
  </main>

<script>
(function () {
  var form    = document.getElementById('contactForm');
  var success = document.getElementById('cfSuccess');
  var btn     = document.getElementById('cfSubmit');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var name  = document.getElementById('cf-name').value.trim();
    var phone = document.getElementById('cf-phone').value.trim();
    var email = document.getElementById('cf-email').value.trim();
    if (!name || !phone || !email) {
      [['cf-name', name], ['cf-phone', phone], ['cf-email', email]].forEach(function (pair) {
        var el = document.getElementById(pair[0]);
        el.style.borderColor = pair[1] ? '' : '#ef4444';
      });
      return;
    }

    btn.disabled = true;
    btn.querySelector('.cf-btn-text').textContent = 'Отправляем…';

    var data = new FormData();
    data.append('action',  'remarka_contact');
    data.append('nonce',   document.querySelector('[name="contact_nonce"]').value);
    data.append('name',    name);
    data.append('phone',   phone);
    data.append('email',   email);
    data.append('message', document.getElementById('cf-message').value.trim());

    fetch('<?php echo esc_url(admin_url("admin-ajax.php")); ?>', { method: 'POST', body: data })
      .then(function (r) { return r.json(); })
      .catch(function () { return { success: true }; })
      .finally(function () {
        form.style.display = 'none';
        success.hidden = false;
      });
  });
}());
</script>

<?php get_footer();
