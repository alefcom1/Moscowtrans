<?php

class RmqNavWalker extends Walker_Nav_Menu {

    private $arrow_svg = '<svg viewBox="0 0 10 6" style="width:9px;height:9px;fill:rgba(255,255,255,.4);margin-left:4px;vertical-align:middle;transition:transform .18s"><path d="M0 0l5 6 5-6z"/></svg>';

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '<div class="rmq-sub">';
        }
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</div>';
        }
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes   = empty( $item->classes ) ? [] : (array) $item->classes;
        $has_child = in_array( 'menu-item-has-children', $classes );
        $is_active = in_array( 'current-menu-item', $classes )
                  || in_array( 'current_page_item', $classes )
                  || in_array( 'current-menu-ancestor', $classes );

        $url   = esc_url( $item->url );
        $title = esc_html( $item->title );

        if ( $depth === 0 ) {
            $wrap_class = 'rmq-nav-item' . ( $has_child ? ' rmq-has-sub' : '' );
            $output .= '<div class="' . $wrap_class . '">';
            $active_attr = $is_active ? ' class="active"' : '';
            $arrow = $has_child ? $this->arrow_svg : '';
            $output .= '<a href="' . $url . '"' . $active_attr . '>' . $title . $arrow . '</a>';
        } else {
            $output .= '<a href="' . $url . '">' . $title . '</a>';
        }
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</div>';
        }
    }
}

class RmqMobWalker extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="rmq-mob-sub">';
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes   = empty( $item->classes ) ? [] : (array) $item->classes;
        $has_child = in_array( 'menu-item-has-children', $classes );
        $is_active = in_array( 'current-menu-item', $classes )
                  || in_array( 'current_page_item', $classes )
                  || in_array( 'current-menu-ancestor', $classes );

        $url   = esc_url( $item->url );
        $title = esc_html( $item->title );

        $li_class = 'rmq-mob-item';
        if ( $has_child ) $li_class .= ' rmq-mob-has-sub';
        if ( $is_active )  $li_class .= ' active';

        $output .= '<li class="' . $li_class . '">';

        if ( $has_child ) {
            $output .= '<div class="rmq-mob-row">';
            $output .= '<a href="' . $url . '">' . $title . '</a>';
            $output .= '<button class="rmq-mob-toggle" aria-label="Открыть подменю">'
                     . '<svg viewBox="0 0 10 6"><path d="M0 0l5 6 5-6z"/></svg>'
                     . '</button>';
            $output .= '</div>';
        } else {
            $output .= '<a href="' . $url . '">' . $title . '</a>';
        }
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="initial-scale=1.0, width=device-width">
<link href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon.svg" rel="icon" type="image/svg+xml">
<meta name="robots" content="index, follow">
<meta name="ai" content="allow">
<meta name="uri" content="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>">
<?php if ( is_front_page() ) : ?>
<meta name="robots" content="noyaca">
<?php endif; ?>
<?php wp_head(); ?>
<title><?php echo wp_get_document_title(); ?></title>
</head>
<body <?php body_class(); ?>>

<style>

.rmq-hdr *, .rmq-hdr *::before, .rmq-hdr *::after { box-sizing: border-box; margin: 0; padding: 0; }

.rmq-hdr {
  font-family: Tahoma, Arial, sans-serif;
  width: 100%;
  background: #fff;
  border-bottom: 3px solid #393185;
  position: relative;
  z-index: 1000;
}

/* ── ВЕРХНЯЯ ПОЛОСА ── */
.rmq-top { background: #1a1a2e; padding: 0; }
.rmq-top-inner {
  max-width: 1100px; margin: 0 auto; padding: 6px 28px;
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.rmq-top a { font-size: 12px; color: rgba(255,255,255,.5); text-decoration: none; transition: color .18s; }
.rmq-top a:hover { color: #fff; }
.rmq-top-left  { display: flex; align-items: center; gap: 18px; }
.rmq-top-right { display: flex; align-items: center; gap: 14px; }

.rmq-social { display: flex; align-items: center; gap: 8px; }
.rmq-social a {
  display: flex; align-items: center; justify-content: center;
  width: 26px; height: 26px; border-radius: 5px;
  background: rgba(255,255,255,.07); text-decoration: none; transition: background .18s;
}
.rmq-social a:hover { background: rgba(255,255,255,.17); }
.rmq-social svg { width: 13px; height: 13px; fill: rgba(255,255,255,.6); }

.rmq-sep { width: 1px; height: 16px; background: rgba(255,255,255,.12); flex-shrink: 0; }

/* Выбор языка */
.rmq-lang-wrap { position: relative; }
.rmq-lang-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 4px 9px; border-radius: 5px;
  border: 1px solid rgba(255,255,255,.15);
  background: rgba(255,255,255,.05);
  cursor: pointer; font-size: 12px;
  color: rgba(255,255,255,.75);
  font-family: Tahoma, Arial, sans-serif;
  user-select: none; transition: all .18s;
}
.rmq-lang-btn:hover { border-color: rgba(255,255,255,.35); color: #fff; }
.rmq-lang-arrow { width: 8px; height: 8px; fill: rgba(255,255,255,.4); transition: transform .18s; }
.rmq-lang-drop {
  position: absolute; top: calc(100% + 6px); right: 0;
  background: #1e2240; border: 1px solid rgba(255,255,255,.15);
  border-radius: 8px; padding: 5px 0; min-width: 160px;
  z-index: 9999; display: none;
  box-shadow: 0 8px 24px rgba(0,0,0,.4);
}
.rmq-lang-drop.open { display: block; }
.rmq-lang-item {
  display: flex; align-items: center; gap: 9px;
  padding: 8px 14px; font-size: 13px;
  color: rgba(255,255,255,.7); text-decoration: none;
  font-family: Tahoma, Arial, sans-serif;
  transition: background .15s;
}
.rmq-lang-item.link:hover { background: rgba(255,255,255,.08); color: #fff; }
.rmq-lang-item .lname { flex: 1; }
.rmq-lang-item .lcheck { width: 14px; height: 14px; fill: #C0392B; }
.rmq-flag { width: 18px; height: 12px; border-radius: 2px; flex-shrink: 0; display: inline-block; }
.rmq-flag-ru { background: linear-gradient(180deg,#fff 33%,#003087 33%,#003087 66%,#C0392B 66%); }
.rmq-flag-it { background: linear-gradient(90deg,#009246 33%,#fff 33%,#fff 66%,#CE2B37 66%); }
.rmq-flag-en { width:18px;height:12px;border-radius:2px;flex-shrink:0;display:inline-block;position:relative;overflow:hidden;background:#012169; }
.rmq-flag-en span { position: absolute; }
.rmq-flag-en .h  { top:50%;left:0;width:100%;height:3px;background:#fff;transform:translateY(-50%); }
.rmq-flag-en .v  { top:0;left:50%;height:100%;width:3px;background:#fff;transform:translateX(-50%); }
.rmq-flag-en .hc { top:50%;left:0;width:100%;height:1.5px;background:#C0392B;transform:translateY(-50%); }
.rmq-flag-en .vc { top:0;left:50%;height:100%;width:1.5px;background:#C0392B;transform:translateX(-50%); }

/* Войти */
.rmq-login {
  font-size: 12px; color: rgba(255,255,255,.55); text-decoration: none;
  padding: 4px 11px; border: 1px solid rgba(255,255,255,.15);
  border-radius: 5px; transition: all .18s;
  display: flex; align-items: center; gap: 5px;
}
.rmq-login:hover { color: #fff; border-color: rgba(255,255,255,.4); background: rgba(255,255,255,.07); }
.rmq-login svg { width: 12px; height: 12px; fill: currentColor; }

/* ── ОСНОВНОЙ БЛОК ── */
.rmq-main {
  max-width: 1100px; margin: 0 auto;
  padding: 12px 28px;
  display: flex; align-items: center; gap: 20px;
}
.rmq-logo-area { flex-shrink: 0; text-decoration: none; display: block; }
.rmq-logo-area img { height: 52px; width: auto; display: block; }

.rmq-vline { width: 1px; height: 46px; background: #e8e6e2; flex-shrink: 0; }

.rmq-contacts { flex: 1; display: flex; align-items: stretch; gap: 0; min-width: 0; }

.rmq-city-block {
  padding-right: 16px; border-right: 1px solid #e8e6e2;
  margin-right: 16px; cursor: pointer; flex-shrink: 0;
  display: flex; flex-direction: column; justify-content: center;
  position: relative;
}
.rmq-city-name { font-size: 13px; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 4px; white-space: nowrap; }
.rmq-city-name svg { width: 9px; height: 9px; fill: #aaa; transition: transform .18s; }
.rmq-city-block.open .rmq-city-name svg { transform: rotate(180deg); }
.rmq-city-sub { font-size: 11px; color: #bbb; margin-top: 2px; }

.rmq-city-drop {
  position: absolute; top: calc(100% + 8px); left: 0;
  background: #fff; border: 1px solid #e0ddd9;
  border-radius: 8px; padding: 5px 0;
  min-width: 190px; z-index: 9999;
  display: none;
  box-shadow: 0 8px 20px rgba(0,0,0,.12);
}
.rmq-city-block.open .rmq-city-drop { display: block; }
.rmq-city-drop a {
  display: flex; align-items: center; gap: 8px;
  padding: 9px 15px; font-size: 13px; color: #333;
  text-decoration: none; font-family: Tahoma, Arial, sans-serif;
  transition: background .15s; white-space: nowrap;
}
.rmq-city-drop a:hover { background: #f5f4ff; color: #393185; }
.rmq-city-drop a.active { color: #393185; font-weight: 700; }
.rmq-city-drop a .cdot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #e8e6e2; flex-shrink: 0;
}
.rmq-city-drop a.active .cdot { background: #C0392B; }

.rmq-phone-block {
  display: flex; flex-direction: column; justify-content: center;
  gap: 2px; min-width: 0;
  margin-right: 16px; padding-right: 16px;
  border-right: 1px solid #e8e6e2;
}
.rmq-phone { font-size: 15px; font-weight: 700; color: #1a1a2e; text-decoration: none; letter-spacing: -.2px; white-space: nowrap; transition: color .18s; }
.rmq-phone:hover { color: #C0392B; }
.rmq-addr { font-size: 11px; color: #999; line-height: 1.5; }
.rmq-addr a { color: #aaa; text-decoration: none; transition: color .15s; }
.rmq-addr a:hover { color: #C0392B; }
.rmq-phone-item { display: none; }
.rmq-phone-item.active { display: block; }
.rmq-addr-item { display: none; }
.rmq-addr-item.active { display: block; }

.rmq-hours-block { display: flex; flex-direction: column; justify-content: center; flex-shrink: 0; }
.rmq-hours-label { font-size: 10px; color: #bbb; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 2px; }
.rmq-hours-val { font-size: 12px; font-weight: 600; color: #444; }
.rmq-hours-sat { font-size: 11px; color: #aaa; margin-top: 1px; }
.rmq-hours-item { display: none; }
.rmq-hours-item.active { display: block; }

.rmq-actions { display: flex; flex-direction: column; gap: 6px; flex-shrink: 0; margin-left: 16px; }
.rmq-btn {
  display: flex; align-items: center; gap: 8px;
  padding: 9px 16px; border-radius: 7px;
  font-size: 12px; font-weight: 700; text-decoration: none;
  white-space: nowrap; transition: all .18s; line-height: 1.3;
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
.rmq-btn-lbl { display: flex; flex-direction: column; }
.rmq-btn-lbl small { font-size: 10px; font-weight: 400; opacity: .7; display: block; margin-bottom: 1px; }
.rmq-btn-lbl span { font-size: 13px; font-weight: 700; }
.rmq-btn-primary { background: #393185; color: #fff; }
.rmq-btn-primary:hover { background: #2c2668; }
.rmq-btn-red { background: #C0392B; color: #fff; }
.rmq-btn-red:hover { background: #a93226; }

/* ── ДЕСКТОПНАЯ НАВИГАЦИЯ ── */
.rmq-nav { background: #2c2568; padding: 0; }
.rmq-nav-inner {
  max-width: 1100px; margin: 0 auto; padding: 0 28px;
  display: flex; align-items: stretch;
}
.rmq-nav ul,
.rmq-nav-inner ul { display: flex; align-items: stretch; list-style: none; margin: 0; padding: 0; }

.rmq-nav-item { position: relative; list-style: none; }
.rmq-nav-item > a {
  display: flex; align-items: center; gap: 4px;
  font-size: 13px; font-weight: 600;
  color: rgba(255,255,255,.75); text-decoration: none;
  padding: 10px 13px;
  border-bottom: 2px solid transparent;
  transition: all .18s; white-space: nowrap;
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-nav-item > a:hover,
.rmq-nav-item:hover > a { color: #fff; border-bottom-color: rgba(255,255,255,.35); }
.rmq-nav-item > a.active { color: #fff; border-bottom-color: #C0392B; }
.rmq-nav-item:hover > a svg { transform: rotate(180deg); }

.rmq-sub {
  position: absolute; top: 100%; left: 0;
  background: #fff; border: 1px solid #e0ddd9;
  border-radius: 0 0 8px 8px;
  padding: 6px 0; min-width: 240px;
  z-index: 9998; display: none;
  box-shadow: 0 8px 20px rgba(0,0,0,.12);
}
.rmq-nav-item:hover .rmq-sub { display: block; }
.rmq-sub a {
  display: block; font-size: 13px; color: #333;
  text-decoration: none; padding: 8px 16px;
  transition: background .15s;
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-sub a:hover { background: #f5f4ff; color: #393185; }

/* ── МОБИЛЬНАЯ ШАПКА ── */
.rmq-mob-hdr { display: none; }
.rmq-mob-nav { display: none; }

.rmq-burger {
  display: flex; flex-direction: column; justify-content: center;
  gap: 5px; width: 34px; height: 34px;
  padding: 6px; cursor: pointer;
  background: none; border: none;
  flex-shrink: 0;
}
.rmq-burger span {
  display: block; height: 2px;
  background: #1a1a2e; border-radius: 2px;
  transition: all .25s;
}
.rmq-burger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.rmq-burger.open span:nth-child(2) { opacity: 0; }
.rmq-burger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

.rmq-mob-nav {
  background: #1a1a2e;
  overflow: hidden;
  max-height: 0;
  transition: max-height .35s ease;
}
.rmq-mob-nav.open { max-height: 100vh; }
.rmq-mob-nav ul  { list-style: none; margin: 0; padding: 8px 0 12px; }
.rmq-mob-item > a,
.rmq-mob-item .rmq-mob-row a {
  display: block; font-size: 15px; font-weight: 600;
  color: rgba(255,255,255,.8); text-decoration: none;
  padding: 11px 20px; transition: color .15s;
  font-family: Tahoma, Arial, sans-serif;
}
.rmq-mob-item > a:hover,
.rmq-mob-item .rmq-mob-row a:hover { color: #fff; }
.rmq-mob-item.active > a,
.rmq-mob-item.active .rmq-mob-row a { color: #fff; }

.rmq-mob-row { display: flex; align-items: center; }
.rmq-mob-row a { flex: 1; }
.rmq-mob-toggle {
  background: none; border: none; cursor: pointer;
  padding: 11px 20px 11px 4px;
  display: flex; align-items: center; justify-content: center;
}
.rmq-mob-toggle svg { width: 12px; height: 8px; fill: rgba(255,255,255,.5); transition: transform .2s; }
.rmq-mob-item.sub-open > .rmq-mob-row .rmq-mob-toggle svg { transform: rotate(180deg); }

.rmq-mob-sub { display: none; padding: 0; background: rgba(0,0,0,.2); }
.rmq-mob-item.sub-open > .rmq-mob-sub { display: block; }
.rmq-mob-sub a { font-size: 13px; font-weight: 400; padding: 9px 20px 9px 34px; color: rgba(255,255,255,.6); }
.rmq-mob-sub a:hover { color: #fff; }

.rmq-mob-divider { height: 1px; background: rgba(255,255,255,.08); margin: 6px 20px; }

.rmq-mob-btns {
  display: flex; flex-direction: column; gap: 8px;
  padding: 12px 20px 16px;
}
.rmq-mob-btns .rmq-btn { justify-content: center; font-size: 13px; padding: 11px 16px; }
.rmq-mob-btns .rmq-btn-lbl small { font-size: 10px; }
.rmq-mob-btns .rmq-btn-lbl span  { font-size: 13px; }

/* ── АДАПТИВ ── */
@media (max-width: 960px) {
  .rmq-main { padding: 10px 16px; gap: 12px; }
  .rmq-top  { padding: 6px 16px; }
  .rmq-hours-block { display: none; }
}
@media (max-width: 700px) {
  .rmq-main    { display: none; }
  .rmq-top     { display: none; }
  .rmq-nav     { display: none; }
  .rmq-mob-hdr {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    background: #fff;
    border-bottom: 2px solid #393185;
    gap: 10px;
  }
  .rmq-mob-hdr img { height: 38px; width: auto; }
  .rmq-mob-hdr .rmq-mob-phone {
    font-size: 14px; font-weight: 700; color: #1a1a2e;
    text-decoration: none; flex: 1; text-align: center;
  }
  .rmq-mob-nav { display: block; }
}
</style>

<div class="rmq-hdr">

  <!-- ── ВЕРХНЯЯ ПОЛОСА ── -->
  <div class="rmq-top">
   <div class="rmq-top-inner">
    <div class="rmq-top-left">
      <a href="<?php echo esc_url( home_url('/kontakty/') ); ?>">Контакты</a>
      <a href="<?php echo esc_url( home_url('/o-nas/') ); ?>">О компании</a>
    </div>
    <div class="rmq-top-right">

      <div class="rmq-social">
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

      <div class="rmq-sep"></div>

      <div class="rmq-lang-wrap" id="rmq-lang-wrap">
        <div class="rmq-lang-btn" id="rmq-lang-btn">
          <span class="rmq-flag rmq-flag-ru"></span>
          <span id="rmq-lang-label">RU</span>
          <svg class="rmq-lang-arrow" id="rmq-lang-arrow" viewBox="0 0 10 6"><path d="M0 0l5 6 5-6z"/></svg>
        </div>
        <div class="rmq-lang-drop" id="rmq-lang-drop">
          <div class="rmq-lang-item">
            <span class="rmq-flag rmq-flag-ru"></span>
            <span class="lname">Русский</span>
            <svg class="lcheck" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          </div>
          <a class="rmq-lang-item link" href="https://1russian.com/" target="_blank" rel="noopener">
            <span class="rmq-flag-en"><span class="h"></span><span class="v"></span><span class="hc"></span><span class="vc"></span></span>
            <span class="lname">English</span>
          </a>
          <a class="rmq-lang-item link" href="https://traduzione.tech/" target="_blank" rel="noopener">
            <span class="rmq-flag rmq-flag-it"></span>
            <span class="lname">Italiano</span>
          </a>
        </div>
      </div>

      <div class="rmq-sep"></div>

      <a href="/login/" class="rmq-login">
        <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
        Войти
      </a>
    </div>
   </div>
  </div>

  <!-- ── ОСНОВНОЙ БЛОК ── -->
  <div class="rmq-main">
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="rmq-logo-area">
      <img src="<?php bloginfo('template_directory'); ?>/img/logo.png" width="210" height="58" alt="<?php bloginfo('name'); ?>">
    </a>
    <div class="rmq-vline"></div>
    <div class="rmq-contacts">
      <div class="rmq-city-block" id="rmq-city-block">
        <div class="rmq-city-name" id="rmq-city-label">
          Москва Центр
          <svg viewBox="0 0 10 6"><path d="M0 0l5 6 5-6z"/></svg>
        </div>
        <div class="rmq-city-sub">выбрать офис</div>
        <div class="rmq-city-drop" id="rmq-city-drop">
          <a href="#" class="active" data-cont="1"><span class="cdot"></span>Москва Центр</a>
          <a href="#" data-cont="2"><span class="cdot"></span>Москва Некрасовка</a>
        </div>
      </div>
      <div class="rmq-phone-block">
        <a contact="1" href="tel:+74959704413" class="rmq-phone rmq-phone-item active">+7 (495) 970-44-13</a>
        <a contact="2" href="tel:+79773174158" class="rmq-phone rmq-phone-item">+7 (977) 317-41-58</a>
        <div contact="1" class="rmq-addr rmq-addr-item active">Глинищевский пер., 6, оф. 2<br><a href="mailto:alefcom1@gmail.com">alefcom1@gmail.com</a></div>
        <div contact="2" class="rmq-addr rmq-addr-item">ул. Лавриненко, 1<br><a href="mailto:mira584@mail.ru">mira584@mail.ru</a></div>
      </div>
      <div class="rmq-hours-block">
        <div class="rmq-hours-label">Время работы</div>
        <div contact="1" class="rmq-hours-item active"><div class="rmq-hours-val">Пн–Пт: 9:00–17:00</div></div>
        <div contact="2" class="rmq-hours-item"><div class="rmq-hours-val">Пн–Пт: 9:00–20:00</div><div class="rmq-hours-sat">Сб: 10:00–16:00</div></div>
      </div>
    </div>
    <div class="rmq-actions">
      <a href="/#qa-form" class="rmq-btn rmq-btn-primary">
        <svg viewBox="0 0 24 24" style="fill:#fff"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        <div class="rmq-btn-lbl"><small>бесплатно онлайн</small><span>Оценить качество перевода</span></div>
      </a>
      <a href="/#calc-docs" class="rmq-btn rmq-btn-red">
        <svg viewBox="0 0 24 24" style="fill:#fff"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        <div class="rmq-btn-lbl"><small>калькулятор стоимости</small><span>Оценить стоимость перевода</span></div>
      </a>
    </div>
  </div>

  <!-- ── МОБИЛЬНАЯ ШАПКА (≤700px) ── -->
  <div class="rmq-mob-hdr">
    <a href="<?php echo esc_url( home_url('/') ); ?>">
      <img src="<?php bloginfo('template_directory'); ?>/img/logo.png" height="38" alt="<?php bloginfo('name'); ?>">
    </a>
    <a href="tel:+79773174158" class="rmq-mob-phone">+7 (977) 317-41-58</a>
    <button class="rmq-burger" id="rmq-burger" aria-label="Меню">
      <span></span><span></span><span></span>
    </button>
  </div>

  <!-- ── МОБИЛЬНОЕ МЕНЮ ── -->
  <div class="rmq-mob-nav" id="rmq-mob-nav">
    <?php wp_nav_menu( [
      'menu'            => 'MenuTop',
      'menu_class'      => 'rmq-mob-menu',
      'container'       => false,
      'fallback_cb'     => false,
      'walker'          => new RmqMobWalker(),
    ] ); ?>
    <div class="rmq-mob-divider"></div>
    <div class="rmq-mob-btns">
      <a href="#qa-form" class="rmq-btn rmq-btn-primary">
        <svg viewBox="0 0 24 24" style="fill:#fff"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        <div class="rmq-btn-lbl"><small>бесплатно онлайн</small><span>Оценить качество перевода</span></div>
      </a>
      <a href="#calc-docs" class="rmq-btn rmq-btn-red">
        <svg viewBox="0 0 24 24" style="fill:#fff"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        <div class="rmq-btn-lbl"><small>калькулятор стоимости</small><span>Оценить стоимость перевода</span></div>
      </a>
    </div>
  </div>

  <!-- ── ДЕСКТОПНАЯ НАВИГАЦИЯ ── -->
  <nav class="rmq-nav">
    <div class="rmq-nav-inner">
    <?php wp_nav_menu( [
      'menu'            => 'MenuTop',
      'menu_class'      => 'rmq-nav-list',
      'container'       => false,
      'fallback_cb'     => false,
      'walker'          => new RmqNavWalker(),
    ] ); ?>
    </div>
  </nav>

</div><!-- /rmq-hdr -->

<script>
(function(){
  'use strict';

  /* ── Выбор языка ── */
  var langBtn   = document.getElementById('rmq-lang-btn');
  var langDrop  = document.getElementById('rmq-lang-drop');
  var langArrow = document.getElementById('rmq-lang-arrow');
  if (langBtn) {
    langBtn.addEventListener('click', function(e){
      e.stopPropagation();
      var open = langDrop.classList.toggle('open');
      langArrow.style.transform = open ? 'rotate(180deg)' : '';
    });
    document.addEventListener('click', function(){
      langDrop.classList.remove('open');
      langArrow.style.transform = '';
    });
  }

  /* ── Бургер / мобильное меню ── */
  var burger  = document.getElementById('rmq-burger');
  var mobNav  = document.getElementById('rmq-mob-nav');
  if (burger && mobNav) {
    burger.addEventListener('click', function(){
      burger.classList.toggle('open');
      mobNav.classList.toggle('open');
    });
  }

  /* ── Подменю в мобильном меню ── */
  document.querySelectorAll('.rmq-mob-toggle').forEach(function(btn){
    btn.addEventListener('click', function(){
      var li = btn.closest('.rmq-mob-item');
      if (li) li.classList.toggle('sub-open');
    });
  });

  /* ── Выпадающий список офисов ── */
  var cityBlock = document.getElementById('rmq-city-block');
  var cityDrop  = document.getElementById('rmq-city-drop');

  if (cityBlock) {
    cityBlock.addEventListener('click', function(e) {
      var link = e.target.closest('.rmq-city-drop a');
      if (link) {
        e.preventDefault();
        var n = link.getAttribute('data-cont');
        switchOffice(n);
        cityBlock.classList.remove('open');
        return;
      }
      e.stopPropagation();
      cityBlock.classList.toggle('open');
    });
    document.addEventListener('click', function() {
      cityBlock.classList.remove('open');
    });
  }

  /* ── Переключение офисов ── */
  var cityNames = {'1':'Москва Центр','2':'Москва Некрасовка'};

  function switchOffice(n) {
    document.querySelectorAll('.rmq-phone-item').forEach(function(el){ el.classList.toggle('active', el.getAttribute('contact') === n); });
    document.querySelectorAll('.rmq-addr-item').forEach(function(el){ el.classList.toggle('active', el.getAttribute('contact') === n); });
    document.querySelectorAll('.rmq-hours-item').forEach(function(el){ el.classList.toggle('active', el.getAttribute('contact') === n); });
    var lbl = document.getElementById('rmq-city-label');
    if (lbl && cityNames[n]) lbl.childNodes[0].textContent = cityNames[n] + ' ';
    if (cityDrop) {
      cityDrop.querySelectorAll('a').forEach(function(a){ a.classList.toggle('active', a.getAttribute('data-cont') === n); });
    }
  }

})();
</script>
