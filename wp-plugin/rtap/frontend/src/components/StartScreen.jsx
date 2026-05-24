import { useState } from 'react';

const TOPICS = [
  { id: 'technical', label: 'Технический',  icon: '⚙️', desc: 'Машиностроение, химия, стандарты' },
  { id: 'legal',     label: 'Юридический',  icon: '⚖️', desc: 'Договоры, арбитраж, законодательство' },
  { id: 'medical',   label: 'Медицинский',  icon: '🏥', desc: 'Клиническая документация, фармация' },
  { id: 'it',        label: 'IT-перевод',   icon: '💻', desc: 'Документация, интерфейсы, API' },
];

// Языки: основные (активны) + вторичные (скоро)
const LANGUAGES_PRIMARY = [
  { code: 'en', flag: '🇬🇧', name: 'Английский', active: true },
  { code: 'de', flag: '🇩🇪', name: 'Немецкий',   active: false, soon: true },
  { code: 'fr', flag: '🇫🇷', name: 'Французский',active: false, soon: true },
  { code: 'es', flag: '🇪🇸', name: 'Испанский',  active: false, soon: true },
  { code: 'zh', flag: '🇨🇳', name: 'Китайский',  active: false, soon: true },
  { code: 'ar', flag: '🇸🇦', name: 'Арабский',   active: false, soon: true },
  { code: 'it', flag: '🇮🇹', name: 'Итальянский',active: false, soon: true },
  { code: 'pt', flag: '🇵🇹', name: 'Португальский', active: false, soon: true },
];

const LANGUAGES_SECONDARY = [
  { code: 'ja', flag: '🇯🇵', name: 'Японский' },
  { code: 'ko', flag: '🇰🇷', name: 'Корейский' },
  { code: 'nl', flag: '🇳🇱', name: 'Нидерландский' },
  { code: 'pl', flag: '🇵🇱', name: 'Польский' },
  { code: 'cs', flag: '🇨🇿', name: 'Чешский' },
  { code: 'sv', flag: '🇸🇪', name: 'Шведский' },
  { code: 'tr', flag: '🇹🇷', name: 'Турецкий' },
  { code: 'uk', flag: '🇺🇦', name: 'Украинский' },
  { code: 'ro', flag: '🇷🇴', name: 'Румынский' },
  { code: 'hu', flag: '🇭🇺', name: 'Венгерский' },
  { code: 'el', flag: '🇬🇷', name: 'Греческий' },
  { code: 'fi', flag: '🇫🇮', name: 'Финский' },
  { code: 'da', flag: '🇩🇰', name: 'Датский' },
  { code: 'no', flag: '🇳🇴', name: 'Норвежский' },
  { code: 'he', flag: '🇮🇱', name: 'Иврит' },
  { code: 'fa', flag: '🇮🇷', name: 'Персидский' },
  { code: 'hi', flag: '🇮🇳', name: 'Хинди' },
  { code: 'vi', flag: '🇻🇳', name: 'Вьетнамский' },
  { code: 'th', flag: '🇹🇭', name: 'Тайский' },
  { code: 'id', flag: '🇮🇩', name: 'Индонезийский' },
  { code: 'ms', flag: '🇲🇾', name: 'Малайский' },
  { code: 'bg', flag: '🇧🇬', name: 'Болгарский' },
  { code: 'hr', flag: '🇭🇷', name: 'Хорватский' },
  { code: 'sk', flag: '🇸🇰', name: 'Словацкий' },
  { code: 'sl', flag: '🇸🇮', name: 'Словенский' },
  { code: 'sr', flag: '🇷🇸', name: 'Сербский' },
  { code: 'lt', flag: '🇱🇹', name: 'Литовский' },
  { code: 'lv', flag: '🇱🇻', name: 'Латышский' },
  { code: 'et', flag: '🇪🇪', name: 'Эстонский' },
  { code: 'ka', flag: '🇬🇪', name: 'Грузинский' },
  { code: 'az', flag: '🇦🇿', name: 'Азербайджанский' },
  { code: 'kk', flag: '🇰🇿', name: 'Казахский' },
];

export default function StartScreen({ defaultTopic, defaultLang, onStart }) {
  const [lang,         setLang]         = useState(defaultLang || 'en');
  const [topic,        setTopic]        = useState(defaultTopic || '');
  const [showSecondary, setShowSecondary] = useState(false);

  const canStart = lang && topic;

  return (
    <div className="rtap-wrap p-4">
      <div className="rtap-card max-w-3xl mx-auto">

        {/* Hero */}
        <div className="mb-8">
          <div className="flex items-center gap-3 mb-2">
            {window.rtapConfig?.siteIconUrl
              ? <img src={window.rtapConfig.siteIconUrl} alt="" style={{ width: 36, height: 36, borderRadius: 6, flexShrink: 0 }} />
              : <span style={{ fontSize: 32, lineHeight: 1 }}>🎯</span>
            }
            <h1 className="text-2xl font-bold" style={{ color: 'var(--rtap-text)', margin: 0 }}>
              Тест для переводчиков
            </h1>
          </div>
          <p className="text-sm" style={{ color: 'var(--rtap-text-muted)', marginLeft: 0 }}>
            Проверьте профессиональный уровень · Получите сертификат · Станьте партнёром Ремарка
          </p>
        </div>

        {/* Step 1: Language */}
        <section className="mb-8">
          <h2 className="text-base font-semibold mb-3" style={{ color: 'var(--rtap-text)' }}>
            1. Язык перевода <span style={{ color: 'var(--rtap-text)', opacity: 0.45 }}>→ Русский</span>
          </h2>

          <div className="rtap-lang-grid">
            {LANGUAGES_PRIMARY.map(l => (
              <button
                key={l.code}
                className={`rtap-lang-card ${!l.active ? 'rtap-lang-card--coming' : ''} ${lang === l.code ? 'rtap-lang-card--active' : ''}`}
                onClick={() => l.active && setLang(l.code)}
                title={l.active ? l.name : `${l.name} — скоро`}
              >
                <span className="rtap-lang-card__flag">{l.flag}</span>
                <span className="rtap-lang-card__name">{l.name}</span>
                {l.soon && <span className="rtap-lang-card__badge">скоро</span>}
              </button>
            ))}
          </div>

          {/* Secondary languages */}
          <div className="mt-3">
            <button
              className="text-xs underline cursor-pointer"
              style={{ color: 'var(--rtap-accent)', background: 'none', border: 'none' }}
              onClick={() => setShowSecondary(v => !v)}
            >
              {showSecondary ? '▲ Скрыть' : '▼ Ещё 32 языка (скоро)'}
            </button>

            {showSecondary && (
              <div className="rtap-lang-grid mt-3">
                {LANGUAGES_SECONDARY.map(l => (
                  <div key={l.code} className="rtap-lang-card rtap-lang-card--coming">
                    <span className="rtap-lang-card__flag">{l.flag}</span>
                    <span className="rtap-lang-card__name">{l.name}</span>
                    <span className="rtap-lang-card__badge">скоро</span>
                  </div>
                ))}
              </div>
            )}
          </div>
        </section>

        {/* Step 2: Topic */}
        <section className="mb-8">
          <h2 className="text-base font-semibold mb-3" style={{ color: 'var(--rtap-text)' }}>
            2. Тематика
          </h2>
          <div className="rtap-topic-grid">
            {TOPICS.map(t => (
              <button
                key={t.id}
                className={`rtap-topic-card ${topic === t.id ? 'rtap-topic-card--active' : ''}`}
                onClick={() => setTopic(t.id)}
              >
                <div className="rtap-topic-card__icon">{t.icon}</div>
                <div className="rtap-topic-card__title">{t.label}</div>
                <div className="rtap-topic-card__count">{t.desc}</div>
              </button>
            ))}
          </div>
        </section>

        {/* CTA */}
        <div className="flex justify-start" style={{ marginTop: 32, marginBottom: 20 }}>
          <button
            className="rtap-btn rtap-btn--primary"
            disabled={!canStart}
            onClick={() => canStart && onStart(topic, lang)}
            style={{ fontSize: 16, padding: '14px 40px' }}
          >
            Начать тест →
          </button>
        </div>

        {/* Info pills */}
        <div className="flex flex-wrap gap-3" style={{ marginBottom: 8 }}>
          {[
            '10 вопросов',
            '30 сек/вопрос',
            `Сертификат при ${window.rtapConfig?.minCert ?? 70}%+`,
            'Бесплатно',
          ].map(item => (
            <span key={item} className="rtap-pill">✓ {item}</span>
          ))}
        </div>
      </div>
    </div>
  );
}
