<?php
/**
 * Category-specific split-visual illustrations for subservice pages.
 *
 * Replaces the generic checklist SVG in .split-visual via the_content filter.
 * Each of the 4 parent service categories gets a unique thematic illustration.
 * No database writes needed — purely dynamic.
 */

/* ── Content filter ──────────────────────────────────────────────────────────── */
add_filter( 'the_content', function ( string $content ): string {
    if ( ! is_page_template( 'page-templates/template-subservice.php' ) ) return $content;

    global $post;
    if ( ! $post || ! $post->post_parent ) return $content;

    $parent_slug = get_post_field( 'post_name', $post->post_parent );
    $uid         = sanitize_html_class( $post->post_name );
    $svg         = remarka_subservice_svg( $parent_slug, $uid );
    if ( ! $svg ) return $content;

    return preg_replace(
        '/<div class="split-visual" aria-hidden="true">[\s\S]*?<\/div>/m',
        '<div class="split-visual" aria-hidden="true">' . $svg . '</div>',
        $content,
        1
    );
} );

/* ── Dispatcher ──────────────────────────────────────────────────────────────── */
function remarka_subservice_svg( string $parent, string $uid ): string {
    switch ( $parent ) {
        case 'yuridicheskiy-perevod':  return _remarka_svg_legal( $uid );
        case 'tekhnicheskiy-perevod':  return _remarka_svg_tech( $uid );
        case 'meditsinskiy-perevod':   return _remarka_svg_medical( $uid );
        case 'it-perevod':             return _remarka_svg_it( $uid );
        default:                       return '';
    }
}

/* ── 1. LEGAL — Document stack + scales of justice ──────────────────────────── */
function _remarka_svg_legal( string $uid ): string {
    $g = 'lg-' . $uid;
    return <<<SVG
<svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
<defs>
  <linearGradient id="{$g}a" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/>
  </linearGradient>
  <linearGradient id="{$g}b" x1="0%" y1="0%" x2="100%" y2="0%">
    <stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/>
    <stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/>
  </linearGradient>
</defs>
<!-- Background glow -->
<circle cx="190" cy="155" r="130" fill="rgba(120,60,240,.07)"/>
<!-- Document stack (left) -->
<rect x="30" y="50"  width="145" height="185" rx="10" fill="rgba(120,60,240,.06)" stroke="rgba(120,60,240,.16)" stroke-width="1.2"/>
<rect x="42" y="58"  width="145" height="185" rx="10" fill="rgba(120,60,240,.09)" stroke="rgba(120,60,240,.22)" stroke-width="1.2"/>
<rect x="54" y="66"  width="145" height="185" rx="10" fill="rgba(120,60,240,.14)" stroke="rgba(120,60,240,.38)" stroke-width="1.5"/>
<!-- Text lines on front doc -->
<rect x="74" y="94"  width="105" height="7"  rx="3"   fill="rgba(167,139,250,.60)"/>
<rect x="74" y="107" width="82"  height="5"  rx="2.5" fill="rgba(167,139,250,.35)"/>
<rect x="74" y="118" width="95"  height="5"  rx="2.5" fill="rgba(167,139,250,.35)"/>
<rect x="74" y="129" width="68"  height="5"  rx="2.5" fill="rgba(167,139,250,.25)"/>
<rect x="74" y="148" width="98"  height="5"  rx="2.5" fill="rgba(167,139,250,.30)"/>
<rect x="74" y="159" width="78"  height="5"  rx="2.5" fill="rgba(167,139,250,.25)"/>
<rect x="74" y="170" width="88"  height="5"  rx="2.5" fill="rgba(167,139,250,.30)"/>
<rect x="74" y="189" width="100" height="5"  rx="2.5" fill="rgba(167,139,250,.22)"/>
<!-- Scales of justice (right side) -->
<!-- Pole -->
<line x1="288" y1="38"  x2="288" y2="175" stroke="rgba(167,139,250,.40)" stroke-width="2" stroke-linecap="round"/>
<circle cx="288" cy="36" r="5" fill="rgba(167,139,250,.55)"/>
<!-- Crossbar -->
<rect x="232" y="65" width="112" height="7" rx="3.5" fill="url({$g}a)" opacity=".45"/>
<!-- Left arm strings + pan -->
<line x1="243" y1="72" x2="233" y2="118" stroke="rgba(120,60,240,.45)" stroke-width="1.5"/>
<line x1="243" y1="72" x2="255" y2="118" stroke="rgba(120,60,240,.45)" stroke-width="1.5"/>
<path d="M229 118 Q244 126 259 118" stroke="rgba(120,60,240,.55)" stroke-width="1.8" fill="rgba(120,60,240,.12)"/>
<!-- Right arm strings + pan (slightly lower = heavier) -->
<line x1="333" y1="72" x2="322" y2="128" stroke="rgba(0,160,240,.45)" stroke-width="1.5"/>
<line x1="333" y1="72" x2="344" y2="128" stroke="rgba(0,160,240,.45)" stroke-width="1.5"/>
<path d="M318 128 Q333 136 348 128" stroke="rgba(0,160,240,.55)" stroke-width="1.8" fill="rgba(0,160,240,.12)"/>
<!-- Column base -->
<rect x="281" y="175" width="14" height="22" rx="4" fill="rgba(167,139,250,.25)"/>
<rect x="264" y="193" width="48" height="8"  rx="4" fill="rgba(167,139,250,.30)"/>
<!-- Connector line -->
<line x1="210" y1="160" x2="240" y2="160" stroke="url({$g}b)" stroke-width="2" stroke-dasharray="4 3"/>
<!-- Seal / success badge -->
<circle cx="308" cy="265" r="32" fill="rgba(0,200,100,.10)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
<circle cx="308" cy="265" r="22" fill="none" stroke="rgba(0,200,100,.25)" stroke-width="1" stroke-dasharray="3 2"/>
<path d="M297 265l9 9 15-17" stroke="rgba(0,200,100,.90)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG;
}

/* ── 2. TECHNICAL — Blueprint grid + gears ───────────────────────────────────── */
function _remarka_svg_tech( string $uid ): string {
    $g = 'tc-' . $uid;
    return <<<SVG
<svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
<defs>
  <linearGradient id="{$g}a" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="#0070D8"/><stop offset="100%" stop-color="#00C4F0"/>
  </linearGradient>
  <linearGradient id="{$g}b" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/>
  </linearGradient>
</defs>
<!-- Background circle -->
<circle cx="190" cy="155" r="130" fill="rgba(0,160,240,.07)"/>
<!-- Blueprint grid lines -->
<g stroke="rgba(0,160,240,.12)" stroke-width="1">
  <line x1="30"  y1="60"  x2="350" y2="60"/>
  <line x1="30"  y1="100" x2="350" y2="100"/>
  <line x1="30"  y1="140" x2="350" y2="140"/>
  <line x1="30"  y1="180" x2="350" y2="180"/>
  <line x1="30"  y1="220" x2="350" y2="220"/>
  <line x1="30"  y1="260" x2="350" y2="260"/>
  <line x1="60"  y1="30"  x2="60"  y2="300"/>
  <line x1="100" y1="30"  x2="100" y2="300"/>
  <line x1="140" y1="30"  x2="140" y2="300"/>
  <line x1="180" y1="30"  x2="180" y2="300"/>
  <line x1="220" y1="30"  x2="220" y2="300"/>
  <line x1="260" y1="30"  x2="260" y2="300"/>
  <line x1="300" y1="30"  x2="300" y2="300"/>
  <line x1="340" y1="30"  x2="340" y2="300"/>
</g>
<!-- Large gear (background) -->
<circle cx="200" cy="155" r="72" fill="rgba(0,160,240,.06)" stroke="rgba(0,160,240,.20)" stroke-width="1.5"/>
<circle cx="200" cy="155" r="48" fill="rgba(0,160,240,.08)" stroke="rgba(0,160,240,.25)" stroke-width="1.5"/>
<circle cx="200" cy="155" r="22" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.40)" stroke-width="1.5"/>
<!-- Gear teeth (12 teeth via rectangles) -->
<g fill="rgba(0,160,240,.25)" stroke="rgba(0,160,240,.35)" stroke-width="1">
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(0   200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(30  200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(60  200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(90  200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(120 200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(150 200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(180 200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(210 200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(240 200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(270 200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(300 200 155)"/>
  <rect x="192" y="75"  width="16" height="22" rx="4" transform="rotate(330 200 155)"/>
</g>
<!-- Small gear (linked, upper right) -->
<circle cx="298" cy="82" r="36" fill="rgba(120,60,240,.08)" stroke="rgba(120,60,240,.25)" stroke-width="1.5"/>
<circle cx="298" cy="82" r="22" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
<circle cx="298" cy="82" r="9"  fill="rgba(120,60,240,.18)" stroke="rgba(120,60,240,.45)" stroke-width="1.5"/>
<g fill="rgba(120,60,240,.20)" stroke="rgba(120,60,240,.30)" stroke-width="1">
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(0   298 82)"/>
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(45  298 82)"/>
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(90  298 82)"/>
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(135 298 82)"/>
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(180 298 82)"/>
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(225 298 82)"/>
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(270 298 82)"/>
  <rect x="293" y="42"  width="10" height="14" rx="3" transform="rotate(315 298 82)"/>
</g>
<!-- Dimension annotation lines -->
<line x1="55"  y1="250" x2="345" y2="250" stroke="rgba(0,160,240,.35)" stroke-width="1.2" stroke-dasharray="5 4"/>
<line x1="55"  y1="245" x2="55"  y2="255" stroke="rgba(0,160,240,.45)" stroke-width="1.5"/>
<line x1="345" y1="245" x2="345" y2="255" stroke="rgba(0,160,240,.45)" stroke-width="1.5"/>
<rect x="152" y="243" width="76" height="16" rx="4" fill="rgba(0,160,240,.12)"/>
<rect x="158" y="248" width="64" height="6"  rx="3" fill="rgba(0,160,240,.35)"/>
<!-- Wrench (lower left) -->
<g stroke="url({$g}b)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none">
  <path d="M68 268 c-4-4-4-10 0-14 3-3 7-4 11-2l-5 5 4 4 5-5c2 4 1 8-2 11-4 4-9 5-13 1z"/>
  <line x1="76" y1="262" x2="100" y2="238"/>
</g>
<!-- Success badge -->
<circle cx="310" cy="268" r="28" fill="rgba(0,200,100,.10)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
<path d="M300 268l8 8 14-16" stroke="rgba(0,200,100,.90)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG;
}

/* ── 3. MEDICAL — ECG pulse + medical cross ──────────────────────────────────── */
function _remarka_svg_medical( string $uid ): string {
    $g = 'md-' . $uid;
    return <<<SVG
<svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
<defs>
  <linearGradient id="{$g}a" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00C4F0"/>
  </linearGradient>
</defs>
<!-- Background -->
<circle cx="190" cy="155" r="130" fill="rgba(0,160,240,.07)"/>
<!-- ECG monitor screen -->
<rect x="40" y="60" width="300" height="165" rx="16" fill="rgba(0,160,240,.08)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
<rect x="40" y="60" width="300" height="30"  rx="16" fill="rgba(0,160,240,.15)"/>
<!-- Screen header dots -->
<circle cx="62" cy="75"  r="5" fill="rgba(255,80,80,.60)"/>
<circle cx="80" cy="75"  r="5" fill="rgba(255,200,0,.55)"/>
<circle cx="98" cy="75"  r="5" fill="rgba(0,200,100,.55)"/>
<!-- Screen label -->
<rect x="140" y="69" width="110" height="10" rx="5" fill="rgba(0,160,240,.35)"/>
<!-- ECG grid lines -->
<g stroke="rgba(0,160,240,.15)" stroke-width="1">
  <line x1="55"  y1="100" x2="325" y2="100"/>
  <line x1="55"  y1="118" x2="325" y2="118"/>
  <line x1="55"  y1="136" x2="325" y2="136"/>
  <line x1="55"  y1="154" x2="325" y2="154"/>
  <line x1="55"  y1="172" x2="325" y2="172"/>
  <line x1="55"  y1="190" x2="325" y2="190"/>
  <line x1="90"  y1="96"  x2="90"  y2="210"/>
  <line x1="130" y1="96"  x2="130" y2="210"/>
  <line x1="170" y1="96"  x2="170" y2="210"/>
  <line x1="210" y1="96"  x2="210" y2="210"/>
  <line x1="250" y1="96"  x2="250" y2="210"/>
  <line x1="290" y1="96"  x2="290" y2="210"/>
</g>
<!-- ECG pulse line -->
<polyline points="
  55,154
  90,154 100,154 106,100 113,210 120,154 130,154
  160,154 170,154 176,108 183,200 190,154 200,154
  230,154 240,154 246,112 253,196 260,154 270,154
  325,154"
  stroke="url({$g}a)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
<!-- Medical cross (right side, prominent) -->
<rect x="268" y="220" width="64" height="64" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
<rect x="288" y="236" width="24" height="32" rx="5" fill="url({$g}a)" opacity=".55"/>
<rect x="280" y="244" width="40" height="16" rx="5" fill="url({$g}a)" opacity=".55"/>
<!-- Pill capsule (lower left) -->
<ellipse cx="80" cy="260" rx="36" ry="14" rx="36" ry="14" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
<line x1="80" y1="246" x2="80" y2="274" stroke="rgba(0,160,240,.40)" stroke-width="1.5"/>
<ellipse cx="55" cy="260" rx="26" ry="14" fill="rgba(0,160,240,.18)"/>
<!-- Success badge -->
<circle cx="175" cy="268" r="28" fill="rgba(0,200,100,.10)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
<path d="M165 268l8 8 14-16" stroke="rgba(0,200,100,.90)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG;
}

/* ── 4. IT — Code terminal + API data flow ───────────────────────────────────── */
function _remarka_svg_it( string $uid ): string {
    $g = 'it-' . $uid;
    return <<<SVG
<svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
<defs>
  <linearGradient id="{$g}a" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/>
  </linearGradient>
  <linearGradient id="{$g}b" x1="0%" y1="0%" x2="100%" y2="0%">
    <stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/>
    <stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/>
  </linearGradient>
</defs>
<!-- Background -->
<circle cx="190" cy="155" r="130" fill="rgba(120,60,240,.07)"/>
<!-- Code terminal window -->
<rect x="30" y="42" width="210" height="170" rx="14" fill="rgba(20,10,40,.35)" stroke="rgba(120,60,240,.40)" stroke-width="1.5"/>
<!-- Terminal header bar -->
<rect x="30" y="42" width="210" height="32" rx="14" fill="rgba(120,60,240,.25)"/>
<rect x="30" y="58" width="210" height="16" fill="rgba(120,60,240,.25)"/>
<!-- Traffic light dots -->
<circle cx="55" cy="58"  r="5.5" fill="rgba(255,80,80,.65)"/>
<circle cx="74" cy="58"  r="5.5" fill="rgba(255,200,0,.60)"/>
<circle cx="93" cy="58"  r="5.5" fill="rgba(0,200,100,.60)"/>
<!-- Code lines -->
<rect x="50"  y="86"  width="40"  height="7" rx="3" fill="rgba(120,60,240,.55)"/>
<rect x="94"  y="86"  width="70"  height="7" rx="3" fill="rgba(167,139,250,.40)"/>
<rect x="50"  y="101" width="20"  height="7" rx="3" fill="rgba(0,160,240,.50)"/>
<rect x="74"  y="101" width="55"  height="7" rx="3" fill="rgba(167,139,250,.30)"/>
<rect x="133" y="101" width="30"  height="7" rx="3" fill="rgba(0,200,100,.40)"/>
<rect x="60"  y="116" width="80"  height="7" rx="3" fill="rgba(167,139,250,.35)"/>
<rect x="50"  y="131" width="30"  height="7" rx="3" fill="rgba(120,60,240,.50)"/>
<rect x="84"  y="131" width="50"  height="7" rx="3" fill="rgba(0,160,240,.35)"/>
<rect x="138" y="131" width="25"  height="7" rx="3" fill="rgba(167,139,250,.25)"/>
<rect x="60"  y="146" width="65"  height="7" rx="3" fill="rgba(0,200,100,.35)"/>
<rect x="50"  y="161" width="35"  height="7" rx="3" fill="rgba(120,60,240,.45)"/>
<rect x="89"  y="161" width="55"  height="7" rx="3" fill="rgba(167,139,250,.30)"/>
<!-- Cursor blink -->
<rect x="50" y="176" width="10" height="14" rx="2" fill="rgba(167,139,250,.70)"/>
<!-- API flow (right side) -->
<!-- Server blocks -->
<rect x="265" y="50"  width="90" height="56" rx="10" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.40)" stroke-width="1.5"/>
<rect x="278" y="62"  width="64" height="6"  rx="3"  fill="rgba(0,160,240,.50)"/>
<rect x="278" y="74"  width="50" height="5"  rx="2.5" fill="rgba(0,160,240,.30)"/>
<rect x="278" y="83"  width="56" height="5"  rx="2.5" fill="rgba(0,160,240,.30)"/>
<!-- Arrow down -->
<line x1="310" y1="106" x2="310" y2="128" stroke="url({$g}b)" stroke-width="2" stroke-dasharray="4 3"/>
<path d="M304 124 l6 8 6-8" stroke="url({$g}a)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
<!-- API middle block -->
<rect x="255" y="136" width="110" height="48" rx="10" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.40)" stroke-width="1.5"/>
<rect x="269" y="148" width="52"  height="7"  rx="3"  fill="rgba(120,60,240,.55)"/>
<rect x="269" y="161" width="70"  height="6"  rx="3"  fill="rgba(167,139,250,.35)"/>
<!-- Arrow down -->
<line x1="310" y1="184" x2="310" y2="206" stroke="url({$g}b)" stroke-width="2" stroke-dasharray="4 3"/>
<path d="M304 202 l6 8 6-8" stroke="url({$g}a)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
<!-- Client block -->
<rect x="265" y="214" width="90" height="48" rx="10" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.40)" stroke-width="1.5"/>
<rect x="278" y="226" width="64" height="6"  rx="3"  fill="rgba(0,160,240,.45)"/>
<rect x="278" y="237" width="48" height="5"  rx="2.5" fill="rgba(0,160,240,.28)"/>
<rect x="278" y="246" width="56" height="5"  rx="2.5" fill="rgba(0,160,240,.28)"/>
<!-- Connecting arrow terminal → API -->
<line x1="240" y1="157" x2="254" y2="157" stroke="url({$g}b)" stroke-width="2" stroke-dasharray="4 3"/>
<!-- Success badge -->
<circle cx="98"  cy="271" r="28" fill="rgba(0,200,100,.10)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
<path d="M88 271l8 8 14-16" stroke="rgba(0,200,100,.90)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG;
}
