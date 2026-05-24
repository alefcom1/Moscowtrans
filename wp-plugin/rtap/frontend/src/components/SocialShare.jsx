import { useState, useCallback } from 'react';

// ─── Rank label helper ────────────────────────────────────────────────────────
function getRankLabel(scorePct) {
  if (scorePct >= 85) return 'Мастер';
  if (scorePct >= 70) return 'Эксперт';
  if (scorePct >= 50) return 'Специалист';
  return 'Стажёр';
}

// ─── Topic label helper ───────────────────────────────────────────────────────
function getTopicLabel(topic) {
  const map = {
    technical: 'технический перевод',
    legal:     'юридический перевод',
    medical:   'медицинский перевод',
    it:        'IT-перевод',
  };
  return map[topic] || topic || 'перевод';
}

// ─── Level label helper ───────────────────────────────────────────────────────
function getLevelLabel(level) {
  const map = {
    beginner:     'начальный',
    intermediate: 'средний',
    advanced:     'продвинутый',
  };
  return map[level] || level || '';
}

// ─── Share text builder ───────────────────────────────────────────────────────
function buildShareText({ scorePct, rank, topic, level, lang, siteUrl }) {
  const rankLabel  = rank || getRankLabel(scorePct);
  const topicLabel = getTopicLabel(topic);
  const levelLabel = getLevelLabel(level);
  const langTag    = lang ? ` [${lang.toUpperCase()}]` : '';

  return (
    `Я прошёл тест RTAP по теме «${topicLabel}»${langTag} (${levelLabel} уровень) ` +
    `и набрал ${scorePct}%! Мой ранг: ${rankLabel}. ` +
    `Проверь свой уровень: ${siteUrl || 'https://moscowtrans.ru'}`
  );
}

// ─── Share URL builders ───────────────────────────────────────────────────────
function telegramUrl(text) {
  return `https://t.me/share/url?url=${encodeURIComponent(text)}`;
}

function vkUrl(text, url) {
  return (
    `https://vk.com/share.php?url=${encodeURIComponent(url || '')}&title=${encodeURIComponent(text)}`
  );
}

function linkedInUrl(url, title) {
  return (
    `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url || '')}&title=${encodeURIComponent(title)}`
  );
}

// ─── Share button ─────────────────────────────────────────────────────────────
function ShareButton({ label, icon, onClick, variant }) {
  const bgMap = {
    telegram:  '#2AABEE',
    vk:        '#4680C2',
    linkedin:  '#0A66C2',
    copy:      'var(--rtap-border)',
  };
  const colorMap = {
    telegram: '#fff',
    vk:       '#fff',
    linkedin: '#fff',
    copy:     'var(--rtap-text)',
  };

  return (
    <button
      className="rtap-btn flex-1"
      style={{
        background:   bgMap[variant] || 'var(--rtap-accent)',
        color:        colorMap[variant] || '#fff',
        fontSize:     '13px',
        padding:      '10px 12px',
        gap:          '6px',
        minWidth:     '80px',
      }}
      onClick={onClick}
    >
      <span style={{ fontSize: '16px', lineHeight: 1 }}>{icon}</span>
      <span>{label}</span>
    </button>
  );
}

// ─── Main SocialShare ─────────────────────────────────────────────────────────
export default function SocialShare({
  scorePct = 0,
  rank,
  topic,
  level,
  lang,
  certId,
  siteUrl,
}) {
  const [copied, setCopied] = useState(false);

  const effectiveRank = rank || getRankLabel(scorePct);
  const shareText = buildShareText({ scorePct, rank: effectiveRank, topic, level, lang, siteUrl });
  const shareUrl  = siteUrl || (typeof window !== 'undefined' ? window.location.href : 'https://moscowtrans.ru');

  const handleTelegram = useCallback(() => {
    window.open(telegramUrl(shareText), '_blank', 'noopener,noreferrer,width=600,height=500');
  }, [shareText]);

  const handleVK = useCallback(() => {
    window.open(
      vkUrl(shareText, shareUrl),
      '_blank',
      'noopener,noreferrer,width=600,height=500'
    );
  }, [shareText, shareUrl]);

  const handleLinkedIn = useCallback(() => {
    const title =
      `Тест RTAP: ${getTopicLabel(topic)} — ${scorePct}% (${effectiveRank})`;
    window.open(
      linkedInUrl(shareUrl, title),
      '_blank',
      'noopener,noreferrer,width=600,height=500'
    );
  }, [shareUrl, topic, scorePct, effectiveRank]);

  const handleCopy = useCallback(async () => {
    const textToCopy = certId
      ? `${shareText}\nСертификат: ${certId}`
      : shareText;

    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(textToCopy);
      } else {
        // Fallback for older browsers / non-secure contexts
        const textarea = document.createElement('textarea');
        textarea.value = textToCopy;
        textarea.style.position = 'fixed';
        textarea.style.opacity  = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
      }
      setCopied(true);
      setTimeout(() => setCopied(false), 2500);
    } catch {
      setCopied(false);
    }
  }, [shareText, certId]);

  return (
    <div className="rtap-card">
      <h3
        className="text-sm font-bold mb-3"
        style={{ color: 'var(--rtap-text)' }}
      >
        Поделиться результатом
      </h3>

      {/* Preview text */}
      <div
        className="text-sm mb-4 p-3 rounded-lg"
        style={{
          background:  'var(--rtap-surface)',
          border:      '1px solid var(--rtap-border)',
          color:       'var(--rtap-text)',
          opacity:     0.85,
          lineHeight:  1.5,
          fontSize:    '13px',
        }}
      >
        {shareText}
      </div>

      {/* Share buttons */}
      <div className="flex flex-wrap gap-2">
        <ShareButton
          label="Telegram"
          icon="✈"
          onClick={handleTelegram}
          variant="telegram"
        />
        <ShareButton
          label="ВКонтакте"
          icon="В"
          onClick={handleVK}
          variant="vk"
        />
        <ShareButton
          label="LinkedIn"
          icon="in"
          onClick={handleLinkedIn}
          variant="linkedin"
        />
        <ShareButton
          label={copied ? 'Скопировано!' : 'Копировать'}
          icon={copied ? '✓' : '📋'}
          onClick={handleCopy}
          variant="copy"
        />
      </div>

      {copied && (
        <p
          className="text-xs mt-2"
          style={{ color: 'var(--rtap-green)', fontWeight: 600 }}
        >
          Ссылка скопирована в буфер обмена
        </p>
      )}
    </div>
  );
}
