#!/usr/bin/env python3
"""
Генератор sitemap.xml для сайта moscowtrans.ru
Запуск: python3 update-sitemap.py
Включает HTML-страницы прототипа + посты WordPress (через REST API).
"""

import os
import glob
import json
import urllib.request
import urllib.error
from datetime import date

BASE_URL = 'https://moscowtrans.ru'
WP_API   = 'https://moscowtrans.ru/wp-json/wp/v2/posts?per_page=100&status=publish&_fields=link,modified&page='
PROTO_DIR = os.path.dirname(os.path.abspath(__file__))
TODAY = date.today().isoformat()

EXCLUDE = {'404.html'}

PRIORITIES = {
    'index.html':                   ('1.0', 'weekly'),
    'pismennyy-perevod.html':       ('0.9', 'monthly'),
    'yuridicheskiy-perevod.html':   ('0.9', 'monthly'),
    'tekhnicheskiy-perevod.html':   ('0.9', 'monthly'),
    'meditsinskiy-perevod.html':    ('0.9', 'monthly'),
    'it-perevod.html':              ('0.8', 'monthly'),
    'finansovyy-perevod.html':      ('0.8', 'monthly'),
    'marketingovyy-perevod.html':   ('0.8', 'monthly'),
    'ved-perevod.html':             ('0.8', 'monthly'),
    'patentnye-perevody.html':      ('0.8', 'monthly'),
    'nauchnyy-perevod.html':        ('0.8', 'monthly'),
    'delovaya-perepiska.html':      ('0.7', 'monthly'),
    'stoimost-perevoda.html':       ('0.8', 'monthly'),
    'yazyki-perevoda.html':         ('0.7', 'monthly'),
    'blog.html':                    ('0.8', 'weekly'),
    'keisy.html':                   ('0.7', 'monthly'),
    'kontakty.html':                ('0.6', 'monthly'),
    'politika-konfidenczialnosti.html': ('0.3', 'yearly'),
}

def file_to_url(fpath):
    rel = os.path.relpath(fpath, PROTO_DIR).replace(os.sep, '/')
    if rel == 'index.html':
        return BASE_URL + '/'
    url = rel.replace('.html', '')
    return BASE_URL + '/' + url + '/'

def get_priority(rel_path):
    name = os.path.basename(rel_path)
    if name in PRIORITIES:
        return PRIORITIES[name]
    if len(rel_path.split('/')) > 1:
        return ('0.6', 'monthly')
    return ('0.5', 'monthly')

def fetch_wp_posts():
    """Загружает все опубликованные посты из WordPress REST API."""
    posts = []
    page = 1
    while True:
        url = WP_API + str(page)
        try:
            req = urllib.request.Request(url, headers={'User-Agent': 'sitemap-generator/1.0'})
            with urllib.request.urlopen(req, timeout=10) as r:
                data = json.loads(r.read())
                if not data:
                    break
                for post in data:
                    link = post.get('link', '').rstrip('/')  + '/'
                    modified = post.get('modified', TODAY)[:10]
                    posts.append((link, '0.7', 'weekly', modified))
                if len(data) < 100:
                    break
                page += 1
        except urllib.error.URLError as e:
            print(f'  WordPress API недоступен (стр. {page}): {e}')
            break
    return posts

# --- HTML страницы ---
all_html = sorted(glob.glob(PROTO_DIR + '/**/*.html', recursive=True))
html_urls = []
for fpath in all_html:
    rel = os.path.relpath(fpath, PROTO_DIR).replace(os.sep, '/')
    if os.path.basename(rel) in EXCLUDE:
        continue
    priority, changefreq = get_priority(rel)
    html_urls.append((file_to_url(fpath), priority, changefreq, TODAY))

# Сортировка: главная первой, остальные по приоритету
main = [(u, p, c, d) for u, p, c, d in html_urls if u == BASE_URL + '/']
rest = sorted([(u, p, c, d) for u, p, c, d in html_urls if u != BASE_URL + '/'],
              key=lambda x: (-float(x[1]), x[0]))

# --- WordPress посты ---
print('Загружаю посты из WordPress...')
wp_posts = fetch_wp_posts()
print(f'  Найдено постов: {len(wp_posts)}')

# --- Генерация XML ---
lines = ['<?xml version="1.0" encoding="UTF-8"?>',
         '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', '']

def add_url(url, priority, changefreq, lastmod):
    lines.append('  <url>')
    lines.append(f'    <loc>{url}</loc>')
    lines.append(f'    <lastmod>{lastmod}</lastmod>')
    lines.append(f'    <changefreq>{changefreq}</changefreq>')
    lines.append(f'    <priority>{priority}</priority>')
    lines.append('  </url>')
    lines.append('')

for entry in main + rest:
    add_url(*entry)

if wp_posts:
    lines.append('  <!-- ══ Блог: WordPress посты ══ -->')
    lines.append('')
    for entry in sorted(wp_posts, key=lambda x: x[3], reverse=True):
        add_url(*entry)

lines.append('</urlset>')

sitemap_path = os.path.join(PROTO_DIR, 'sitemap.xml')
with open(sitemap_path, 'w', encoding='utf-8') as f:
    f.write('\n'.join(lines))

total = len(main) + len(rest) + len(wp_posts)
print(f'sitemap.xml обновлён: {total} URL ({len(main)+len(rest)} страниц + {len(wp_posts)} постов)')
