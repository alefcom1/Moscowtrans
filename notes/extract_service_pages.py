#!/usr/bin/env python3
"""
Extracts content from prototype service-page HTML files and appends
a remarka_service_pages_content() function to setup-pages.php.
Also patches setup-pages.php to write content via $wpdb and always
update meta fields (not only on first create).
"""

import re
from pathlib import Path

ROOT     = Path(__file__).parent.parent
PROTO    = ROOT / 'prototype'
OUT      = ROOT / 'wp-theme' / 'remarka' / 'inc' / 'setup-pages.php'

SERVICE_SLUGS = [
    'pismennyy-perevod',
    'yuridicheskiy-perevod',
    'tekhnicheskiy-perevod',
    'meditsinskiy-perevod',
    'it-perevod',
    'perevod-saytov',
    'finansovyy-perevod',
    'marketingovyy-perevod',
    'ved-perevod',
    'patentnye-perevody',
    'nauchnyy-perevod',
    'delovaya-perepiska',
    'srochnyy-perevod',
    'khudozhestvennyy-perevod',
]


def extract_content(html: str) -> str:
    start = html.find('<section class="sec sec-intro">')
    if start == -1:
        start = html.find('<section class="sec sec-intro ')
    if start == -1:
        return ''
    end = html.find('id="calc-section"')
    if end == -1:
        end = html.find('sec-calculator')
    if end != -1:
        end = html.rfind('<section', 0, end)
    else:
        end = html.find('<footer')
    if end == -1 or end <= start:
        return ''
    return html[start:end].strip()


def php_escape(s: str) -> str:
    return s.replace('\\', '\\\\').replace("'", "\\'")


def build_content_function(contents: dict) -> str:
    lines = ['', 'function remarka_service_pages_content(): array {', '    return [']
    for slug, content in contents.items():
        lines.append(f"        '{slug}' => '{php_escape(content)}',")
    lines += ['    ];', '}', '']
    return '\n'.join(lines)


def patch_service_loop(php: str) -> str:
    """Replace the service-pages foreach to always update meta and write content via $wpdb."""
    old = r"""    foreach \(\$service_pages as \$p\) \{
        \$r = remarka_insert_page\(\[
            'slug'     => \$p\['slug'\],
            'title'    => \$p\['title'\],
            'template' => 'page-templates/template-service\.php',
        \]\);
        if \(\$r\['action'\] === 'created'\) \{
            update_post_meta\(\$r\['id'\], '_hero_greeting_1', \$p\['g1'\]\);
            update_post_meta\(\$r\['id'\], '_hero_greeting_2', \$p\['g2'\]\);
            update_post_meta\(\$r\['id'\], '_hero_greeting_3', \$p\['g3'\]\);
        \}
        \$results\[\] = \$r;
    \}"""

    new = """    $service_content = remarka_service_pages_content();

    foreach ($service_pages as $p) {
        global $wpdb;

        $r = remarka_insert_page([
            'slug'     => $p['slug'],
            'title'    => $p['title'],
            'template' => 'page-templates/template-service.php',
        ]);

        // Always update meta (not only on first create)
        update_post_meta($r['id'], '_hero_greeting_1', $p['g1']);
        update_post_meta($r['id'], '_hero_greeting_2', $p['g2']);
        update_post_meta($r['id'], '_hero_greeting_3', $p['g3']);

        // Write content directly via $wpdb to bypass KSES filters
        if (!empty($service_content[$p['slug']])) {
            $wpdb->update(
                $wpdb->posts,
                ['post_content' => $service_content[$p['slug']]],
                ['ID' => $r['id']],
                ['%s'],
                ['%d']
            );
            clean_post_cache($r['id']);
        }

        $results[] = $r;
    }"""

    patched = re.sub(old, new, php, flags=re.DOTALL)
    if patched == php:
        print('WARNING: service-pages loop not found — manual patch needed')
    return patched


def main():
    contents = {}
    for slug in SERVICE_SLUGS:
        html_file = PROTO / f'{slug}.html'
        if not html_file.exists():
            print(f'  MISSING  {slug}.html')
            continue
        html = html_file.read_text(encoding='utf-8')
        content = extract_content(html)
        if content:
            contents[slug] = content
            print(f'  ✅ {slug}: {len(content):,} chars')
        else:
            print(f'  ⚠️  {slug}: no content extracted')

    php = OUT.read_text(encoding='utf-8')

    # Remove existing content function if re-running
    php = re.sub(r'\nfunction remarka_service_pages_content\(\).*', '', php, flags=re.DOTALL)
    php = php.rstrip()

    # Patch the foreach loop
    php = patch_service_loop(php)

    # Append the content function
    php = php.rstrip() + '\n' + build_content_function(contents)

    OUT.write_text(php, encoding='utf-8')
    size = OUT.stat().st_size
    print(f'\nUpdated: {OUT}')
    print(f'File size: {size // 1024} KB')


if __name__ == '__main__':
    main()
