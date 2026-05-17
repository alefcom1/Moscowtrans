<?php
?>

<style>
.rmq-footer *, .rmq-footer *::before, .rmq-footer *::after { box-sizing: border-box; margin: 0; padding: 0; }
.rmq-footer ul { list-style: none; }
.rmq-footer li { list-style: none; }
.rmq-footer li::before { display: none; }
.rmq-footer li::marker { display: none; }
.rmq-footer li { background-image: none !important; }
.rmq-footer li::before { content: none !important; display: none !important; }
.rmq-footer li::after  { content: none !important; display: none !important; }
.rmq-footer {
  font-family: Tahoma, Arial, sans-serif;
  background: #12112A;
  color: rgba(255,255,255,.75);
  font-size: 14px;
  line-height: 1.6;
}

/* ── CTA ПОЛОСА ── */
.rmq-ft-cta {
  background: #393185;
  padding: 36px 28px;
}
.rmq-ft-cta-inner {
  max-width: 1060px; margin: 0 auto;
  display: flex; align-items: center;
  justify-content: space-between; gap: 24px;
  flex-wrap: wrap;
}
.rmq-ft-cta-text h2 {
  font-size: 20px; font-weight: 700; color: #fff;
  margin-bottom: 5px; font-family: Tahoma, Arial, sans-serif;
}
.rmq-ft-cta-text p {
  font-size: 14px; color: rgba(255,255,255,.65);
}
.rmq-ft-cta-btns { display: flex; gap: 10px; flex-wrap: wrap; flex-shrink: 0; }
.rmq-ft-btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 11px 20px; border-radius: 7px;
  font-size: 13px; font-weight: 700;
  text-decoration: none; transition: all .18s;
  font-family: Tahoma, Arial, sans-serif; white-space: nowrap;
}
.rmq-ft-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
.rmq-ft-btn-white { background: #fff; color: #393185; }
.rmq-ft-btn-white:hover { background: #f0efff; }
.rmq-ft-btn-outline { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.4); }
.rmq-ft-btn-outline:hover { border-color: #fff; background: rgba(255,255,255,.08); }

/* ── ОСНОВНОЙ БЛОК ФУТЕРА ── */
.rmq-ft-main {
  padding: 52px 28px 40px;
  border-bottom: 1px solid rgba(255,255,255,.08);
}
.rmq-ft-main-inner {
  max-width: 1060px; margin: 0 auto;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr 1.4fr;
  gap: 32px;
}

/* Логотип и описание */
.rmq-ft-brand {}
.rmq-ft-logo { display: block; margin-bottom: 14px; }
.rmq-ft-logo img { height: 44px; width: auto; filter: brightness(0) invert(1); opacity: .9; }
.rmq-ft-tagline { font-size: 13px; color: rgba(255,255,255,.5); margin-bottom: 20px; line-height: 1.6; }
.rmq-ft-since {
  display: inline-flex; align-items: center; gap: 7px;
  font-size: 11px; color: rgba(255,255,255,.35);
  text-transform: uppercase; letter-spacing: .08em; margin-bottom: 20px;
}
.rmq-ft-since span { width: 20px; height: 1px; background: rgba(255,255,255,.2); display: block; }

/* Социальные сети */
.rmq-ft-social { display: flex; gap: 8px; margin-bottom: 20px; }
.rmq-ft-social a {
  display: flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 7px;
  background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
  transition: all .18s; text-decoration: none;
}
.rmq-ft-social a:hover { background: #393185; border-color: #393185; }
.rmq-ft-social svg { width: 15px; height: 15px; fill: rgba(255,255,255,.6); }
.rmq-ft-social a:hover svg { fill: #fff; }

/* Языки */
.rmq-ft-langs { display: flex; gap: 6px; }
.rmq-ft-lang {
  font-size: 11px; font-weight: 700; color: rgba(255,255,255,.4);
  text-decoration: none; padding: 4px 9px;
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 4px; transition: all .18s;
  display: flex; align-items: center; gap: 5px;
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-ft-lang:hover { color: #fff; border-color: rgba(255,255,255,.3); }
.rmq-ft-lang .flflag { width: 16px; height: 10px; border-radius: 2px; display: inline-block; flex-shrink: 0; }
.flflag-ru { background: linear-gradient(180deg,#fff 33%,#003087 33%,#003087 66%,#C0392B 66%); }
.flflag-en { background: #012169; position: relative; overflow: hidden; }
.flflag-it { background: linear-gradient(90deg,#009246 33%,#fff 33%,#fff 66%,#CE2B37 66%); }

/* Колонки навигации */
.rmq-ft-col h4 {
  font-size: 11px; font-weight: 700; color: rgba(255,255,255,.35);
  text-transform: uppercase; letter-spacing: .1em;
  margin-bottom: 16px; padding-bottom: 10px;
  border-bottom: 1px solid rgba(255,255,255,.07);
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-ft-col ul { list-style: none; }
.rmq-ft-col ul li { margin-bottom: 9px; }
.rmq-ft-col ul li a {
  font-size: 13px; color: rgba(255,255,255,.55);
  text-decoration: none; transition: color .15s;
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-ft-col ul li a:hover { color: #fff; }

/* Колонка контакты */
.rmq-ft-contacts h4 {
  font-size: 11px; font-weight: 700; color: rgba(255,255,255,.35);
  text-transform: uppercase; letter-spacing: .1em;
  margin-bottom: 16px; padding-bottom: 10px;
  border-bottom: 1px solid rgba(255,255,255,.07);
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-ft-office { margin-bottom: 14px; }
.rmq-ft-office-addr {
  font-size: 12px; color: rgba(255,255,255,.45); line-height: 1.5; margin-bottom: 3px;
}
.rmq-ft-office-phone {
  font-size: 13px; font-weight: 700; color: rgba(255,255,255,.75);
  text-decoration: none; transition: color .15s;
  display: block;
}
.rmq-ft-office-phone:hover { color: #fff; }
.rmq-ft-office-divider { height: 1px; background: rgba(255,255,255,.07); margin: 12px 0; }
.rmq-ft-tech {
  font-size: 12px; color: rgba(255,255,255,.4); line-height: 1.6;
}
.rmq-ft-tech a { color: rgba(255,255,255,.55); text-decoration: none; }
.rmq-ft-tech a:hover { color: #fff; }
.rmq-ft-tech strong { color: rgba(255,255,255,.6); display: block; margin-bottom: 3px; font-size: 11px; text-transform: uppercase; letter-spacing: .07em; }

/* ── НИЖНЯЯ ПОЛОСА ── */
.rmq-ft-bottom {
  padding: 18px 28px;
}
.rmq-ft-bottom-inner {
  max-width: 1060px; margin: 0 auto;
  display: flex; align-items: center;
  justify-content: space-between; gap: 16px;
  flex-wrap: wrap;
}
.rmq-ft-copy {
  font-size: 12px; color: rgba(255,255,255,.3);
}
.rmq-ft-copy a { color: rgba(255,255,255,.35); text-decoration: none; transition: color .15s; }
.rmq-ft-copy a:hover { color: rgba(255,255,255,.7); }
.rmq-ft-req {
  font-size: 11px; color: rgba(255,255,255,.2); text-align: center;
}
.rmq-ft-links { display: flex; gap: 20px; flex-wrap: wrap; }
.rmq-ft-links a {
  font-size: 12px; color: rgba(255,255,255,.3);
  text-decoration: none; transition: color .15s;
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-ft-links a:hover { color: rgba(255,255,255,.7); }

/* ── АДАПТИВ ── */
@media (max-width: 960px) {
  .rmq-ft-main-inner {
    grid-template-columns: 1fr 1fr 1fr;
  }
  .rmq-ft-brand { grid-column: 1 / -1; }
}
@media (max-width: 640px) {
  .rmq-ft-cta { padding: 28px 16px; }
  .rmq-ft-cta-inner { flex-direction: column; align-items: flex-start; }
  .rmq-ft-main { padding: 36px 16px 28px; }
  .rmq-ft-main-inner { grid-template-columns: 1fr 1fr; }
  .rmq-ft-brand { grid-column: 1 / -1; }
  .rmq-ft-contacts { grid-column: 1 / -1; }
  .rmq-ft-bottom { padding: 16px; }
  .rmq-ft-bottom-inner { flex-direction: column; align-items: flex-start; gap: 10px; }
  .rmq-ft-req { text-align: left; }
}
</style>

<footer class="rmq-footer">

  <!-- ── CTA ПОЛОСА ── -->
  <div class="rmq-ft-cta">
    <div class="rmq-ft-cta-inner">
      <div class="rmq-ft-cta-text">
        <h2 class="h2-style" style="text-align: center; font-weight: bold">Нужен перевод? Оценим за 15 минут</h2>
        <p>Вышлите файл или опишите задачу — назовём точную стоимость бесплатно</p>
      </div>
      <div class="rmq-ft-cta-btns">
        <a href="/#qa-form" class="rmq-ft-btn rmq-ft-btn-white">
          <svg viewBox="0 0 24 24" style="fill:#393185"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          Проверить качество перевода
        </a>
        <a href="/#calc-docs" class="rmq-ft-btn rmq-ft-btn-outline open-link">
          <svg viewBox="0 0 24 24" style="fill:currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
          Запросить стоимость
        </a>
      </div>
    </div>
  </div>

  <!-- ── ОСНОВНОЙ БЛОК ── -->
  <div class="rmq-ft-main">
    <div class="rmq-ft-main-inner">

      <!-- Бренд -->
      <div class="rmq-ft-brand">
        <a href="/" class="rmq-ft-logo">
          <img src="<?php bloginfo('template_directory'); ?>/img/logo.png" height="44" alt="Ремарка — бюро переводов">
        </a>
        <p class="rmq-ft-tagline">Профессиональное бюро переводов в Москве.<br>Нотариальное заверение, апостиль, юридические и технические переводы.</p>
        <div class="rmq-ft-since"><span></span>Работаем с 2001 года<span></span></div>
        <div class="rmq-ft-social">
          <a href="https://vk.com/bp_remarka" title="ВКонтакте" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24"><path d="M15.07 2H8.93C3.33 2 2 3.33 2 8.93v6.14C2 20.67 3.33 22 8.93 22h6.14C20.67 22 22 20.67 22 15.07V8.93C22 3.33 20.67 2 15.07 2zm3.08 13.25h-1.5c-.57 0-.74-.45-1.76-1.48-.88-.87-1.27-.99-1.49-.99-.3 0-.39.08-.39.5v1.35c0 .35-.11.56-1.03.56-1.52 0-3.2-.92-4.38-2.64C6.13 10.56 5.75 9 5.75 8.66c0-.22.08-.43.5-.43h1.5c.37 0 .51.17.65.57.72 2.07 1.92 3.88 2.42 3.88.18 0 .27-.08.27-.54V9.95c-.06-1.01-.59-1.1-.59-1.46 0-.18.15-.35.38-.35h2.36c.32 0 .43.17.43.53v2.86c0 .32.14.43.23.43.18 0 .35-.11.7-.46 1.08-1.21 1.85-3.07 1.85-3.07.1-.22.28-.43.65-.43h1.5c.45 0 .55.23.45.54-.19.87-2.02 3.46-2.02 3.46-.16.26-.22.37 0 .66.16.22.68.67 1.03 1.08.64.73 1.13 1.34 1.26 1.76.12.42-.1.63-.55.63z"/></svg>
          </a>
          <a href="https://www.youtube.com/@alefcom1" title="YouTube" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24"><path d="M23.5 6.19a3.02 3.02 0 00-2.12-2.14C19.54 3.5 12 3.5 12 3.5s-7.54 0-9.38.55A3.02 3.02 0 00.5 6.19C0 8.04 0 12 0 12s0 3.96.5 5.81a3.02 3.02 0 002.12 2.14C4.46 20.5 12 20.5 12 20.5s7.54 0 9.38-.55a3.02 3.02 0 002.12-2.14C24 15.96 24 12 24 12s0-3.96-.5-5.81zM9.75 15.5v-7l6.25 3.5-6.25 3.5z"/></svg>
          </a>
          <a href="https://wa.me/79773174158" title="WhatsApp" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24"><path d="M17.47 14.38c-.29-.15-1.71-.84-1.97-.94-.26-.1-.46-.15-.65.15-.19.29-.74.94-.91 1.13-.17.19-.34.21-.63.07-.29-.15-1.22-.45-2.32-1.43-.86-.77-1.44-1.71-1.6-2-.17-.29-.02-.45.13-.59.13-.13.29-.34.44-.51.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.51-.07-.14-.65-1.57-.89-2.15-.24-.56-.48-.49-.65-.5h-.56c-.19 0-.5.07-.77.36-.26.29-1 .98-1 2.38 0 1.41 1.03 2.77 1.17 2.96.14.19 2.02 3.09 4.9 4.33.69.3 1.22.47 1.64.6.69.22 1.31.19 1.81.12.55-.08 1.71-.7 1.95-1.37.24-.68.24-1.26.17-1.37-.07-.12-.26-.19-.55-.34zM12.05 21.8h-.04a9.73 9.73 0 01-4.96-1.36l-.36-.21-3.7.97 1-3.62-.23-.37a9.74 9.74 0 01-1.49-5.19c0-5.38 4.38-9.76 9.77-9.76 2.61 0 5.06 1.02 6.9 2.86a9.7 9.7 0 012.86 6.91c-.01 5.39-4.39 9.77-9.75 9.77zm8.31-18.07A11.8 11.8 0 0012.04 0C5.4 0 .02 5.38.02 12.01c0 2.12.55 4.19 1.6 6.01L0 24l6.13-1.61a11.97 11.97 0 005.91 1.51h.01c6.64 0 12.02-5.38 12.02-12.01 0-3.21-1.25-6.23-3.71-8.16z"/></svg>
          </a>
        </div>
        <div class="rmq-ft-langs">
          <span class="rmq-ft-lang"><span class="flflag flflag-ru"></span>RU</span>
          <a href="https://1russian.com/" class="rmq-ft-lang" target="_blank"><span class="flflag flflag-en"></span>EN</a>
          <a href="https://traduzione.tech/" class="rmq-ft-lang" target="_blank"><span class="flflag flflag-it"></span>IT</a>
        </div>
      </div>

      <!-- Услуги -->
      <div class="rmq-ft-col">
        <h4>Услуги</h4>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/notarialnyj-perevod/')); ?>">Нотариальный перевод</a></li>
          <li><a href="<?php echo esc_url(home_url('/tehnicheskij-perevod/')); ?>">Технический перевод</a></li>
          <li><a href="<?php echo esc_url(home_url('/anglijskij/yuridicheskij-perevod-anglijskogo-yazyka/')); ?>">Юридический перевод</a></li>
          <li><a href="<?php echo esc_url(home_url('/mediczinskij-perevod/')); ?>">Медицинский перевод</a></li>
          <li><a href="<?php echo esc_url(home_url('/professionalnyj-perevod/uslugi-ustnogo-perevoda/')); ?>">Устный перевод</a></li>
          <li><a href="<?php echo esc_url(home_url('/apostil/')); ?>">Апостиль</a></li>
          <li><a href="<?php echo esc_url(home_url('/konsulskaya-legalizacziya/')); ?>">Консульская легализация</a></li>
        </ul>
      </div>

      <!-- Компания -->
      <div class="rmq-ft-col">
        <h4>Компания</h4>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/o-nas/')); ?>">О нашем бюро</a></li>
          <li><a href="<?php echo esc_url(home_url('/prajs/')); ?>">Прайс и тарифы</a></li>
          <li><a href="<?php echo esc_url(home_url('/katalog-yazykov-s-kotorymi-my-rabotaem/')); ?>">Языки перевода</a></li>
          <li><a href="<?php echo esc_url(home_url('/stati-i-novosti/')); ?>">Статьи и новости</a></li>
          <li><a href="<?php echo esc_url(home_url('/sotrudnichestvo/')); ?>">Переводчикам</a></li>
          <li><a href="<?php echo esc_url(home_url('/prodazha-gotovogo-biznesa-byuro-perevodov/')); ?>">Франшиза</a></li>
          <li><a href="<?php echo esc_url(home_url('/kontakty/')); ?>">Контакты</a></li>
        </ul>
      </div>

      <!-- Инструменты -->
      <div class="rmq-ft-col">
        <h4>Онлайн-сервисы</h4>
        <ul>
          <li><a href="/#qa-form">Проверка качества перевода</a></li>
          <li><a href="/#calc-docs">Калькулятор стоимости</a></li>
          <li><a href="/#local">Заказать локализацию</a></li>
          <li><a href="/login/">Личный кабинет</a></li>
        </ul>
      </div>

      <!-- Контакты -->
      <div class="rmq-ft-contacts">
        <h4>Офисы в Москве</h4>
        <div class="rmq-ft-office">
          <div class="rmq-ft-office-addr">Глинищевский пер., 6, оф. 2</div>
          <a href="tel:+74959704413" class="rmq-ft-office-phone">+7 (495) 970-44-13</a>
        </div>
        <div class="rmq-ft-office">
          <div class="rmq-ft-office-addr">ул. Лавриненко, 1 (Некрасовка)</div>
          <a href="https://wa.me/79773174158" class="rmq-ft-office-phone">+7 (977) 317-41-58</a>
        </div>
        <div class="rmq-ft-office-divider"></div>
        <div class="rmq-ft-tech">
          <strong>Отдел технического перевода</strong>
          <a href="mailto:info@traduzione.tech">info@traduzione.tech</a><br>
          <a href="https://wa.me/79182630013">+7 (918) 263-00-13</a>
        </div>
      </div>

    </div>
  </div>

  <!-- ── НИЖНЯЯ ПОЛОСА ── -->
  <div class="rmq-ft-bottom">
    <div class="rmq-ft-bottom-inner">
      <div class="rmq-ft-copy">
        <a href="<?php echo esc_url(home_url('/')); ?>">© 2001–<?php echo date('Y'); ?> Бюро переводов «Ремарка»</a>
      </div>
      <div class="rmq-ft-req">
        ИП Капина Ольга Борисовна &nbsp;·&nbsp; ИНН 233406925261
      </div>
      <div class="rmq-ft-links">
        <a href="<?php echo esc_url(home_url('/politika-konfidenczialnosti')); ?>">Политика конфиденциальности</a>
        <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>">Карта сайта</a>
      </div>
    </div>
  </div>

</footer>

<!-- ── ПОПАПЫ ── -->
<div id="pr-popup" class="pr-popup mfp-hide" align="center">
  <form action="/formsetter" id="frm1_ignit" target="frm1_transport" method="post" enctype="multipart/form-data">
    <input type="hidden" name="act" value="form1">
    <div class="popup-title"><p>Вышлите файл и мы<br>за 15 минут назовём стоимость</p></div>
    <div class="input-line"><p><span><input size="25" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" placeholder="Ваше Имя*" value="" type="text" name="your-name"></span><br><span id="your_name_error_area"></span></p></div>
    <div class="input-line"><p><span class="wpcf7-form-control-wrap" data-name="your-email"><input size="25" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" placeholder="Ваш Email" value="" type="email" name="your-email"></span><br><span id="your_email_error_area"></span></p></div>
    <div class="input-line"><p><span class="wpcf7-form-control-wrap" data-name="tel-298"><input size="25" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel" aria-invalid="false" placeholder="Ваш телефон*" value="" type="tel" name="tel"></span><br><span id="tel_error_area"></span></p></div>
    <p><span class="wpcf7-form-control-wrap"><select class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required" aria-required="true" aria-invalid="false" name="office">
      <option value="alefcom1@gmail.com">Москва Центр (Глинищевский пер., 6)</option>
      <option value="mira584@mail.ru">Москва Некрасовка (ул. Лавриненко, 1)</option>
    </select></span><br><span id="office_error_area"></span></p>
    <div class="input-line"><p><span class="wpcf7-form-control-wrap" data-name="text-409"><input size="25" class="wpcf7-form-control wpcf7-text" aria-invalid="false" placeholder="Перевод на" value="" type="text" name="perevd"></span><br><span id="perevd_error_area"></span></p></div>
    <div class="file-upload">
      <label><input id="cur_fil" size="25" type="file" name="file-103[]" multiple onchange="amm()"><img src="/wp-content/themes/perevod/img/link.svg" alt=""><span>Загрузить файлы</span></label><br>
      <span id="filnam"></span><br><span id="file-103_error_area"></span>
    </div>
    <div class="input-line"><p><center><label><img src="/capcha/?frm=1"></label></center><input size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" placeholder="Код на картинке" value="" type="text" name="capcha"><br><span id="capcha_error_area"></span></p></div>
    <div class="btn-center" id="frm_button_area"><input type="button" onclick="frm1_start()" class="button button--small" value="Отправить"></div>
    <div style="display:none"><iframe name="frm1_transport" src="" width="0" height="0"></iframe></div>
  </form>
</div>

<div id="call-popup" class="pr-popup mfp-hide">
  <form action="/formsetter" target="frm2_transport" id="frm2_ignit" method="post">
    <input type="hidden" name="act" value="form2">
    <div class="popup-title"><p>Заказать звонок</p></div>
    <div class="input-line"><p><span class="wpcf7-form-control-wrap" data-name="your-name"><input size="30" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" placeholder="Ваше Имя*" value="" type="text" name="your-name"></span><br><span id="frm2_your_name_error_area"></span></p></div>
    <div class="input-line"><p><span class="wpcf7-form-control-wrap" data-name="tel-298"><input size="30" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-required wpcf7-validates-as-tel" aria-required="true" aria-invalid="false" placeholder="Ваш телефон*" value="" type="tel" name="tel"></span><br><span id="frm2_tel_error_area"></span></p></div>
    <div class="input-line"><p>Выберите ближайший офис</p>
      <p><span class="wpcf7-form-control-wrap" data-name="office"><select class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required" aria-required="true" aria-invalid="false" name="office">
        <option value="alefcom1@gmail.com">Москва Центр (Глинищевский пер., 6)</option>
        <option value="mira584@mail.ru">Москва Некрасовка (ул. Лавриненко, 1)</option>
      </select></span><br><span id="frm2_office_error_area"></span></p>
    </div>
    <div class="input-line"><p><center><label><img src="/capcha/?frm=2"></label></center><input size="30" class="wpcf7-form-control wpcf7-text" placeholder="Код на картинке" value="" type="text" name="capcha"><br><span id="frm2_capcha_error_area"></span></p></div>
    <div class="btn-center" id="frm2_button_area"><input type="button" onclick="frm2_start()" class="button button--small" value="Отправить"></div>
    <div style="display:none"><iframe name="frm2_transport" src="" width="0" height="0"></iframe></div>
  </form>
</div>

<link rel="preload" id="google-fonts-1-css" href="https://fonts.googleapis.com/css?family=Roboto%3A100%2C300%2C400%2C500%2C700%2C900%7CRoboto+Slab%3A100%2C300%2C400%2C500%2C700%2C900&display=swap&subset=cyrillic&ver=6.3.2" as="font" type="font/woff2" crossorigin>

<link type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/magnific-popup.min.css" rel="stylesheet">
<link type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/owl.carousel.min.css" rel="stylesheet">
<link type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/style/style.css" rel="stylesheet">
<link type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/slick.css" rel="stylesheet">
<link type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/slick-theme.css" rel="stylesheet">

<?php wp_footer(); ?>

</body>
</html>
