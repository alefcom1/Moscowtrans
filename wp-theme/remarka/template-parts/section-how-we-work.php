<?php
/**
 * "Как мы работаем" section — reusable on any page.
 * Usage: get_template_part('template-parts/section-how-we-work');
 *
 * Optional $args:
 *   lang_acc — accusative form of language name in Russian (e.g. "английский").
 *              When set, heading and sub are adapted for language-specific pages.
 */
$lang_acc = $args['lang_acc'] ?? null;

if ( $lang_acc ) {
	$heading = 'Как мы переводим на ' . esc_html( $lang_acc ) . ' язык';
	$sub     = 'Шесть шагов вашего заказа — с контролем качества на каждом этапе';
} else {
	$heading = 'Как мы работаем';
	$sub     = 'Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе';
}
?>
  <!-- ════════════════ HOW WE WORK ════════════════ -->
  <section class="sec sec-how">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title"><?php echo $heading; ?></h2>
        <p class="sec-sub"><?php echo $sub; ?></p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item"><div class="step-num">01</div><h3>Отправьте файл</h3><p>Загрузите документ через сайт, мессенджер, email или принесите в офис</p></div>
        <div class="step-item"><div class="step-num">02</div><h3>Анализ и расчёт</h3><p>Оцениваем объём, тематику и сложность — сообщаем стоимость и срок за несколько минут</p></div>
        <div class="step-item"><div class="step-num">03</div><h3>Подбор специалиста</h3><p>Выбираем переводчика с профильным образованием именно в вашей области</p></div>
        <div class="step-item"><div class="step-num">04</div><h3>Перевод</h3><p>Специалист работает с соблюдением отраслевой терминологии и стиля оригинала</p></div>
        <div class="step-item"><div class="step-num">05</div><h3>Корректура</h3><p>Независимый редактор проверяет точность, стиль и соответствие заданию</p></div>
        <div class="step-item"><div class="step-num">06</div><h3>Готовый перевод</h3><p>Отправляем файл или вы забираете из офиса — точно в оговорённый срок</p></div>
      </div>
    </div>
  </section>
