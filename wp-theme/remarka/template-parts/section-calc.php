<?php
/**
 * Calculator section — reusable on any page.
 * Usage: get_template_part('template-parts/section-calc');
 *
 * Optional $args:
 *   heading  — override heading text
 *   sub      — override sub text
 */
$heading = $args['heading'] ?? 'Рассчитайте стоимость перевода за&nbsp;30&nbsp;минут';
$sub     = $args['sub']     ?? 'Загрузите документ, укажите языковую пару — получите прозрачный расчёт без скрытых комиссий.';
?>
  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->
  <section class="sec sec-calculator-hero" id="calc-section">
    <div class="container">
      <div class="calc-hero-layout">

        <div class="calc-hero-left">
          <span class="calc-badge">БЕСПЛАТНЫЙ РАСЧЁТ</span>
          <h2 class="calc-hero-h" id="calc-hero-heading"><?php echo $heading; ?></h2>
          <p class="calc-hero-sub" id="calc-hero-sub"><?php echo esc_html($sub); ?></p>
          <ul class="calc-features">
            <li class="cf-item">
              <span class="cf-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span>
              <div><strong>Быстрый ответ</strong><span>Расчёт за 30 минут. Срочное выполнение в тот же день.</span></div>
            </li>
            <li class="cf-item">
              <span class="cf-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
              <div><strong>Прозрачные цены</strong><span>Оплата за страницу или за слово. Никаких скрытых доплат.</span></div>
            </li>
            <li class="cf-item">
              <span class="cf-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
              <div><strong>100% конфиденциально</strong><span>Файлы в защищённом хранилище. NDA по запросу.</span></div>
            </li>
            <li class="cf-item">
              <span class="cf-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
              <div><strong>Гарантия качества</strong><span>Двухэтапная редактура. Бесплатные правки 30 дней.</span></div>
            </li>
          </ul>
          <div class="calc-trust-row">
            <span>✓ Без предоплаты</span>
            <span>✓ Дипломированные переводчики</span>
            <span>✓ 4.98/5 рейтинг</span>
            <span>✓ 2 400+ заказов</span>
          </div>
        </div>

        <div class="calc-hero-right">
          <div id="calc-docs" class="rem-calc">
            <div class="calc-head">
              <h2>Расчёт по загруженному документу</h2>
              <p class="sub">Перетащите файл или выберите с компьютера — подсчитаем объём, определим язык и рассчитаем стоимость автоматически.</p>
            </div>
            <div class="calc-body">
              <div class="step" id="rem-step-upload">
                <div class="step-title"><span class="step-num">1</span>Загрузка документа</div>
                <div class="upload-zone" id="rem-uz">
                  <input type="file" id="rem-finp" multiple accept=".pdf,.doc,.docx,.txt,.rtf,.odt,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                  <span class="u-ico">📄</span>
                  <div class="u-main">Перетащите файлы сюда</div>
                  <div class="u-sub">или нажмите кнопку, чтобы выбрать с компьютера</div>
                  <span class="u-btn">Выбрать файлы</span>
                  <div class="u-formats">PDF · DOC · DOCX · TXT · RTF · ODT · XLS · XLSX · PPT · PPTX · JPG · PNG</div>
                </div>
              </div>
              <div class="step hidden" id="rem-step-docs">
                <div class="step-title"><span class="step-num">2</span>Ваши документы</div>
                <div class="docs-list" id="rem-docs-list"></div>
                <label class="upload-mini" id="rem-add-more">
                  <input type="file" id="rem-finp-more" multiple accept=".pdf,.doc,.docx,.txt,.rtf,.odt,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                  <span>＋</span><span>Добавить ещё документ</span>
                </label>
              </div>
              <div class="step hidden" id="rem-step-urg">
                <div class="step-title"><span class="step-num">3</span>Выберите срок готовности</div>
                <div class="urg-row" id="rem-urg-row">
                  <button type="button" class="urg-btn sel" data-urg="standard" data-mult="1">
                    <div class="u-h">Стандарт</div><div class="u-d" id="rem-urg-d-standard">—</div><div class="u-p">без доплаты</div>
                  </button>
                  <button type="button" class="urg-btn" data-urg="urgent" data-mult="1">
                    <div class="u-h">Срочно</div><div class="u-d" id="rem-urg-d-urgent">—</div><div class="u-p" id="rem-urg-p-urgent">без доплаты</div>
                  </button>
                  <button type="button" class="urg-btn" data-urg="express" data-mult="2">
                    <div class="u-h">Экспресс</div><div class="u-d" id="rem-urg-d-express">—</div><div class="u-p">цена ×2</div>
                  </button>
                </div>
              </div>
              <div class="step hidden" id="rem-step-total">
                <div class="total-box">
                  <div>
                    <div class="tl">Итого к оплате</div>
                    <div class="tp"><span id="rem-tp-val">0</span><small>₽</small></div>
                    <div class="td" id="rem-tp-detail">—</div>
                  </div>
                  <div class="tr" id="rem-tp-right">—</div>
                </div>
                <p class="result-note">* Стоимость рассчитана автоматически и может быть скорректирована менеджером после проверки документов.</p>
                <div class="btn-row">
                  <button type="button" class="btn btn-secondary" id="rem-btn-reset">Начать заново</button>
                  <button type="button" class="btn btn-primary" id="rem-btn-order">Оформить заказ<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
                </div>
                <div class="trust-row">
                  <div class="trust-item"><span class="trust-dot"></span>Ответим за 30 минут</div>
                  <div class="trust-item"><span class="trust-dot"></span>Бесплатная оценка</div>
                  <div class="trust-item"><span class="trust-dot"></span>С 2001 года</div>
                  <div class="trust-item"><span class="trust-dot"></span>Конфиденциально</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <div id="rem-notif" class="rem-notif"></div>
  <div class="rem-modal-overlay" id="rem-modal">
    <div class="rem-modal" role="dialog" aria-modal="true">
      <div class="m-head">
        <h3>Оформление заказа</h3>
        <div class="m-sub">Проверьте состав заказа и заполните данные для связи</div>
        <button type="button" class="m-close" id="rem-m-close" aria-label="Закрыть">×</button>
      </div>
      <div class="m-body">
        <div class="step"><div class="step-title"><span class="step-num">1</span>Ваш заказ</div><div class="m-summary" id="rem-m-summary"></div><div class="m-note">* Стоимость рассчитана автоматически и может быть изменена после проверки.</div></div>
        <div class="step">
          <div class="step-title"><span class="step-num">2</span>Контактные данные</div>
          <div class="m-row">
            <div class="field"><label>Ваше имя <span class="req">*</span></label><input type="text" id="rem-m-name" placeholder="Иван Петров"></div>
            <div class="field"><label>Телефон <span class="req">*</span></label><input type="tel" id="rem-m-phone" placeholder="+7 (000) 000-00-00"></div>
          </div>
          <div class="m-row">
            <div class="field"><label>E-mail <span class="req">*</span></label><input type="email" id="rem-m-email" placeholder="ivan@example.com"></div>
            <div class="field"><label>Компания</label><input type="text" id="rem-m-company" placeholder="Название организации"></div>
          </div>
          <div class="m-row-full field" style="margin-bottom:0"><label>Комментарий</label><textarea id="rem-m-comment" placeholder="Особые требования, пожелания..."></textarea></div>
        </div>
        <div class="step">
          <div class="step-title"><span class="step-num">3</span>Получение перевода</div>
          <div class="field" style="margin-bottom:12px"><label>Город</label><input type="text" id="rem-m-city" placeholder="Москва"></div>
          <div class="delivery-row" id="rem-m-delivery">
            <div class="del-opt sel" data-del="none" data-price="0"><div class="d-t">На e-mail (электронная версия)</div><div class="d-s">Бесплатно · мгновенно после готовности</div></div>
            <div class="del-opt" data-del="courier" data-price="490"><div class="d-t">Курьер (СДЭК, OZON, Яндекс)</div><div class="d-s">от 3 дней · от 490 ₽</div></div>
          </div>
          <div class="del-note">Сроки курьерской доставки указаны без учёта срока выполнения перевода.</div>
        </div>
        <div class="m-final">
          <div><div class="mf-l">Итоговая сумма</div><div class="mf-p"><span id="rem-m-final">0</span><small> ₽</small></div></div>
          <div class="mf-note" id="rem-m-final-note"></div>
        </div>
        <button type="button" id="rem-m-submit">
          <span class="rem-submit-text">Отправить заявку</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
        <div class="m-agree">Нажимая на кнопку, вы соглашаетесь с <a href="/politika-konfidenczialnosti/" target="_blank">политикой конфиденциальности</a>.</div>
      </div>
    </div>
  </div>
