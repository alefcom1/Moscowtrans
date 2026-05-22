#!/usr/bin/env python3
"""
Extracts content from prototype subpage HTML files and generates
wp-theme/remarka/inc/setup-subpages.php with all 147 subpages.
"""

import os
import re
from pathlib import Path

ROOT = Path(__file__).parent.parent
PROTOTYPE = ROOT / 'prototype'
OUTPUT = ROOT / 'wp-theme' / 'remarka' / 'inc' / 'setup-subpages.php'

# service slug → parent WP page slug + greeting
SERVICES = {
    'yuridicheskiy-perevod': {
        'parent': 'yuridicheskiy-perevod',
        'g1': 'Здравствуйте! 👋 Помогу с переводом юридических документов.',
        'g2': 'Загрузите договор, иск или доверенность — рассчитаем стоимость.',
        'g3': 'Переводчики с юридическим образованием. NDA до передачи файлов.',
    },
    'tekhnicheskiy-perevod': {
        'parent': 'tekhnicheskiy-perevod',
        'g1': 'Здравствуйте! 👋 Переводим техническую документацию любой сложности.',
        'g2': 'Загрузите файл — подберём профильного переводчика за 30 минут.',
        'g3': 'Инженерная экспертиза + отраслевой глоссарий. Конфиденциально.',
    },
    'meditsinskiy-perevod': {
        'parent': 'meditsinskiy-perevod',
        'g1': 'Здравствуйте! 👋 Переводим медицинские документы с точностью врача.',
        'g2': 'Загрузите документ — переводчик с медобразованием возьмётся за работу.',
        'g3': 'Строгая конфиденциальность медданных. NDA обязателен.',
    },
    'it-perevod': {
        'parent': 'it-perevod',
        'g1': 'Здравствуйте! 👋 Локализуем ПО, API и техническую документацию.',
        'g2': 'Загрузите файлы — XLIFF, JSON, строки интерфейса — рассчитаем стоимость.',
        'g3': 'Поддержка Lokalise, Phrase, Crowdin. CI/CD по запросу.',
    },
}


def extract_content(html: str) -> str:
    """Extract sections between sec-intro and sec-calculator."""
    # Find start of sec-intro
    start = html.find('<section class="sec sec-intro">')
    if start == -1:
        start = html.find('<section class="sec sec-intro ')
    if start == -1:
        return ''

    # Find start of sec-calculator
    end = html.find('id="calc-section"')
    if end == -1:
        end = html.find('sec-calculator')
    if end != -1:
        # Walk back to find the opening <section tag
        end = html.rfind('<section', 0, end)
    else:
        # Fallback: stop before footer
        end = html.find('<footer')

    if end == -1 or end <= start:
        return ''

    content = html[start:end].strip()
    return content


def extract_title(html: str) -> str:
    """Extract page title from <title> tag."""
    m = re.search(r'<title>(.+?)</title>', html)
    if m:
        # Remove site suffix like " — Ремарка Москва"
        title = m.group(1)
        title = re.sub(r'\s*[—–-]+\s*Ремарка.*$', '', title).strip()
        title = re.sub(r'\s*[—–-]+\s*Москва.*$', '', title).strip()
        return title
    return ''


def extract_h1(html: str) -> str:
    """Extract H1 text."""
    m = re.search(r'<h1[^>]*>(.+?)</h1>', html, re.DOTALL)
    if m:
        return re.sub(r'<[^>]+>', '', m.group(1)).strip()
    return ''


def php_escape(s: str) -> str:
    """Escape string for PHP single-quoted string."""
    return s.replace('\\', '\\\\').replace("'", "\\'")


def process_service(service_dir: str, service_info: dict) -> list:
    pages = []
    service_path = PROTOTYPE / service_dir

    if not service_path.exists():
        return pages

    for html_file in sorted(service_path.glob('*.html')):
        slug = html_file.stem
        html = html_file.read_text(encoding='utf-8')

        title = extract_title(html) or extract_h1(html) or slug
        content = extract_content(html)

        if not content:
            print(f'  WARNING: no content extracted from {html_file.name}')

        pages.append({
            'slug': slug,
            'title': title,
            'parent': service_info['parent'],
            'g1': service_info['g1'],
            'g2': service_info['g2'],
            'g3': service_info['g3'],
            'content': content,
        })
        print(f'  ✅ {slug} — {title[:60]}')

    return pages


def generate_php(all_pages: list) -> str:
    lines = []
    lines.append('<?php')
    lines.append('/**')
    lines.append(' * Auto-generated subservice page setup.')
    lines.append(' * Trigger (admin only): ?remarka_setup_subpages=1')
    lines.append(' *')
    lines.append(f' * Total pages: {len(all_pages)}')
    lines.append(' */')
    lines.append('')
    lines.append("add_action('init', function () {")
    lines.append("    if (empty($_GET['remarka_setup_subpages']) || !current_user_can('manage_options')) {")
    lines.append('        return;')
    lines.append('    }')
    lines.append('')
    lines.append('    $results = remarka_create_subpages();')
    lines.append('')
    lines.append("    echo '<pre style=\"margin:40px auto;max-width:1100px;font-family:monospace;font-size:12px\">';")
    lines.append("    echo '<strong>Remarka — subpage setup complete (' . count($results) . ' pages)</strong>' . \"\\n\\n\";")
    lines.append('    foreach ($results as $r) {')
    lines.append("        $icon = $r['action'] === 'created' ? '✅' : ($r['action'] === 'updated' ? '🔄' : '❌');")
    lines.append("        echo $icon . '  ' . str_pad($r['action'], 10) . ' /' . $r['parent'] . '/' . $r['slug'] . \"\\n\";")
    lines.append('    }')
    lines.append("    echo '</pre>';")
    lines.append('    exit;')
    lines.append('});')
    lines.append('')
    lines.append('function remarka_create_subpages(): array {')
    lines.append('    $results = [];')
    lines.append('')
    lines.append('    $pages = remarka_subpages_data();')
    lines.append('')
    lines.append('    foreach ($pages as $p) {')
    lines.append("        $parent = get_page_by_path($p['parent'], OBJECT, 'page');")
    lines.append('        $parent_id = $parent ? $parent->ID : 0;')
    lines.append('')
    lines.append("        $existing = get_page_by_path($p['parent'] . '/' . $p['slug'], OBJECT, 'page');")
    lines.append('        if (!$existing) {')
    lines.append("            $existing = get_page_by_path($p['slug'], OBJECT, 'page');")
    lines.append('        }')
    lines.append('')
    lines.append('        if ($existing) {')
    lines.append('            wp_update_post([')
    lines.append("                'ID'           => $existing->ID,")
    lines.append("                'post_title'   => $p['title'],")
    lines.append("                'post_content' => $p['content'],")
    lines.append("                'post_parent'  => $parent_id,")
    lines.append("                'post_status'  => 'publish',")
    lines.append('            ]);')
    lines.append("            update_post_meta($existing->ID, '_wp_page_template', 'page-templates/template-subservice.php');")
    lines.append("            update_post_meta($existing->ID, '_hero_greeting_1', $p['g1']);")
    lines.append("            update_post_meta($existing->ID, '_hero_greeting_2', $p['g2']);")
    lines.append("            update_post_meta($existing->ID, '_hero_greeting_3', $p['g3']);")
    lines.append("            $results[] = ['action' => 'updated', 'slug' => $p['slug'], 'parent' => $p['parent']];")
    lines.append('        } else {')
    lines.append('            $id = wp_insert_post([')
    lines.append("                'post_title'   => $p['title'],")
    lines.append("                'post_name'    => $p['slug'],")
    lines.append("                'post_type'    => 'page',")
    lines.append("                'post_status'  => 'publish',")
    lines.append("                'post_author'  => 1,")
    lines.append("                'post_parent'  => $parent_id,")
    lines.append("                'post_content' => $p['content'],")
    lines.append('            ], true);')
    lines.append('            if (!is_wp_error($id)) {')
    lines.append("                update_post_meta($id, '_wp_page_template', 'page-templates/template-subservice.php');")
    lines.append("                update_post_meta($id, '_hero_greeting_1', $p['g1']);")
    lines.append("                update_post_meta($id, '_hero_greeting_2', $p['g2']);")
    lines.append("                update_post_meta($id, '_hero_greeting_3', $p['g3']);")
    lines.append("                $results[] = ['action' => 'created', 'slug' => $p['slug'], 'parent' => $p['parent']];")
    lines.append('            } else {')
    lines.append("                $results[] = ['action' => 'error', 'slug' => $p['slug'], 'parent' => $p['parent']];")
    lines.append('            }')
    lines.append('        }')
    lines.append('    }')
    lines.append('')
    lines.append('    return $results;')
    lines.append('}')
    lines.append('')
    lines.append('function remarka_subpages_data(): array {')
    lines.append('    return [')

    for p in all_pages:
        lines.append('        [')
        lines.append(f"            'slug'    => '{php_escape(p['slug'])}',")
        lines.append(f"            'title'   => '{php_escape(p['title'])}',")
        lines.append(f"            'parent'  => '{php_escape(p['parent'])}',")
        lines.append(f"            'g1'      => '{php_escape(p['g1'])}',")
        lines.append(f"            'g2'      => '{php_escape(p['g2'])}',")
        lines.append(f"            'g3'      => '{php_escape(p['g3'])}',")
        # Escape content for PHP nowdoc-style
        content_escaped = php_escape(p['content'])
        lines.append(f"            'content' => '{content_escaped}',")
        lines.append('        ],')

    lines.append('    ];')
    lines.append('}')
    lines.append('')

    return '\n'.join(lines)


def main():
    all_pages = []

    for service_dir, service_info in SERVICES.items():
        print(f'\n{service_dir}:')
        pages = process_service(service_dir, service_info)
        all_pages.extend(pages)

    print(f'\nTotal: {len(all_pages)} pages')

    php_code = generate_php(all_pages)
    OUTPUT.write_text(php_code, encoding='utf-8')
    print(f'\nGenerated: {OUTPUT}')
    print(f'File size: {OUTPUT.stat().st_size // 1024} KB')


if __name__ == '__main__':
    main()
