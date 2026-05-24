<?php
/**
 * Dynamically append two missing guarantee cards to every sec-guarantees section.
 *
 * Fires via the_content filter so all service/subservice pages are updated
 * without re-running the setup scripts.
 * Idempotent: skips sections that already contain «Двойная проверка».
 */

add_filter( 'the_content', function ( string $content ): string {
    if ( strpos( $content, 'sec-guarantees' ) === false ) return $content;

    $new_cards = '
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <h3>Двойная проверка</h3>
          <p>Каждый перевод проходит редактуру: сначала переводчик, затем независимый редактор с профильным образованием</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <h3>Фиксированная цена</h3>
          <p>Стоимость согласовывается до начала работы и не меняется. Никаких скрытых доплат за форматирование или срочность</p>
        </div>';

    return preg_replace_callback(
        '/<section[^>]+class="[^"]*\bsec-guarantees\b[^"]*"[^>]*>.*?<\/section>/s',
        static function ( array $m ) use ( $new_cards ): string {
            $sec = $m[0];
            if ( strpos( $sec, 'Двойная проверка' ) !== false ) return $sec;

            /*
             * Structure always ends:
             *   </div>   ← last guarantee-card
             *   </div>   ← guarantees-grid
             *   </div>   ← container
             * </section>
             *
             * This four-level closing sequence is unique in the section,
             * so we safely insert the new cards before it.
             */
            return preg_replace(
                '/(<\/div>)(\s*<\/div>\s*<\/div>\s*<\/section>)/s',
                '$1' . $new_cards . '$2',
                $sec,
                1
            );
        },
        $content
    );
}, 20 );
