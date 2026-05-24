import { useProgress } from '../hooks/useProgress';

const TOPIC_NAMES = {
  technical: 'Технический перевод',
  legal:     'Юридический перевод',
  medical:   'Медицинский перевод',
  it:        'IT-перевод',
};

const LEVELS = [
  { id: 'beginner',     label: 'Beginner',     icon: '🌱', desc: '10 вопросов · 30 сек/вопрос', passReq: null,  passNext: '≥60% для разблокировки Intermediate' },
  { id: 'intermediate', label: 'Intermediate', icon: '⚡', desc: '10 вопросов · 30 сек/вопрос', passReq: 60,    passNext: '≥70% для разблокировки Advanced' },
  { id: 'advanced',     label: 'Advanced',     icon: '🏆', desc: '10 вопросов · 30 сек/вопрос', passReq: 70,    passNext: 'Высший уровень' },
];

function formatCooldown(ms) {
  const h = Math.floor(ms / 3600000);
  const m = Math.floor((ms % 3600000) / 60000);
  return h > 0 ? `${h}ч ${m}м` : `${m}м`;
}

export default function LevelSelect({ topic, lang, onSelect, onBack }) {
  const { getScore, isUnlocked, getCooldown } = useProgress();

  return (
    <div className="rtap-wrap p-4">
      <div className="rtap-card max-w-xl mx-auto">
        <button className="rtap-btn rtap-btn--outline mb-6 text-sm" onClick={onBack}>
          ← Назад
        </button>

        <h2 className="text-xl font-bold mb-1" style={{ color: 'var(--rtap-text)' }}>
          {TOPIC_NAMES[topic] || topic}
        </h2>
        <p className="text-sm mb-6" style={{ color: 'var(--rtap-text)', opacity: 0.6 }}>
          Выберите уровень сложности
        </p>

        <div className="flex flex-col gap-3">
          {LEVELS.map(lvl => {
            const unlocked = isUnlocked(topic, lvl.id, lang);
            const score    = getScore(topic, lvl.id, lang);
            const cooldown = getCooldown(topic, lvl.id, lang);
            const hasCool  = cooldown > 0;

            return (
              <button
                key={lvl.id}
                className={`rtap-level-card text-left ${!unlocked ? 'rtap-level-card--locked' : ''}`}
                onClick={() => unlocked && !hasCool && onSelect(lvl.id)}
                disabled={!unlocked || hasCool}
              >
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center gap-3">
                    <span style={{ fontSize: 28 }}>{lvl.icon}</span>
                    <div>
                      <div className="font-bold" style={{ color: 'var(--rtap-text)' }}>{lvl.label}</div>
                      <div className="text-sm" style={{ color: 'var(--rtap-text)', opacity: 0.6 }}>{lvl.desc}</div>
                    </div>
                  </div>

                  <div className="text-right">
                    {!unlocked && (
                      <span className="text-xs px-2 py-1 rounded-full" style={{ background: 'var(--rtap-border)', color: 'var(--rtap-text)' }}>
                        🔒 нужно {lvl.passReq}%
                      </span>
                    )}
                    {unlocked && score !== null && (
                      <span className="text-sm font-bold" style={{ color: score >= 70 ? 'var(--rtap-green)' : 'var(--rtap-text)' }}>
                        Лучший: {score}%
                      </span>
                    )}
                  </div>
                </div>

                {hasCool && (
                  <p className="text-xs" style={{ color: 'var(--rtap-red)' }}>
                    Повторная попытка через {formatCooldown(cooldown)}
                  </p>
                )}

                {unlocked && !hasCool && (
                  <p className="text-xs mt-1" style={{ color: 'var(--rtap-text)', opacity: 0.5 }}>
                    {lvl.passNext}
                  </p>
                )}
              </button>
            );
          })}
        </div>
      </div>
    </div>
  );
}
