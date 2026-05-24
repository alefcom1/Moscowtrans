import { useMemo } from 'react';

// ─── Rank config ──────────────────────────────────────────────────────────────
const RANKS = [
  { label: 'Мастер',      min: 85, color: '#C9A84C', emoji: '🏆' },
  { label: 'Эксперт',     min: 70, color: '#A084E8', emoji: '⭐' },
  { label: 'Специалист',  min: 50, color: '#5ECB8F', emoji: '📘' },
  { label: 'Стажёр',      min: 0,  color: '#888888', emoji: '📝' },
];

function getRank(scorePct) {
  return RANKS.find((r) => scorePct >= r.min) || RANKS[RANKS.length - 1];
}

// ─── Score circle SVG ─────────────────────────────────────────────────────────
function ScoreCircle({ scorePct }) {
  const radius    = 54;
  const circumference = 2 * Math.PI * radius;
  const filled    = circumference * (scorePct / 100);
  const rank      = getRank(scorePct);

  const stroke =
    scorePct >= 85
      ? 'var(--rtap-gold)'
      : scorePct >= 70
      ? 'var(--rtap-purple)'
      : scorePct >= 50
      ? 'var(--rtap-green)'
      : 'var(--rtap-red)';

  return (
    <div
      className="rtap-score-circle"
      style={{
        position: 'relative',
        width: '140px',
        height: '140px',
        margin: '0 auto',
      }}
    >
      <svg
        viewBox="0 0 128 128"
        width="140"
        height="140"
        style={{ transform: 'rotate(-90deg)' }}
      >
        <circle
          cx="64" cy="64" r={radius}
          fill="none"
          strokeWidth="8"
          stroke="var(--rtap-border)"
        />
        <circle
          cx="64" cy="64" r={radius}
          fill="none"
          strokeWidth="8"
          stroke={stroke}
          strokeLinecap="round"
          strokeDasharray={circumference}
          strokeDashoffset={circumference - filled}
          style={{ transition: 'stroke-dashoffset 1s cubic-bezier(.4,0,.2,1)' }}
        />
      </svg>
      <div
        style={{
          position: 'absolute',
          inset: 0,
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          justifyContent: 'center',
          gap: '2px',
        }}
      >
        <span
          style={{
            fontSize: '28px',
            fontWeight: 800,
            color: 'var(--rtap-text)',
            lineHeight: 1,
          }}
        >
          {scorePct}%
        </span>
        <span style={{ fontSize: '20px', lineHeight: 1 }}>{rank.emoji}</span>
      </div>
    </div>
  );
}

// ─── Answer review item ───────────────────────────────────────────────────────
function AnswerReviewItem({ question, answerRecord, index }) {
  const isCorrect = answerRecord?.correct === true;
  const timedOut  = answerRecord?.timedOut === true;

  const borderColor = isCorrect
    ? 'var(--rtap-green)'
    : 'var(--rtap-red)';
  const bg = isCorrect
    ? 'color-mix(in srgb, var(--rtap-green) 8%, transparent)'
    : 'color-mix(in srgb, var(--rtap-red) 8%, transparent)';

  return (
    <div
      className="rtap-card"
      style={{
        borderColor,
        background: bg,
        padding: '12px 16px',
        marginBottom: '8px',
      }}
    >
      <div className="flex items-start gap-3">
        <span
          style={{
            fontSize: '16px',
            lineHeight: 1,
            flexShrink: 0,
            marginTop: '2px',
          }}
        >
          {isCorrect ? '✓' : '✗'}
        </span>
        <div style={{ flex: 1, minWidth: 0 }}>
          <p
            className="text-sm font-medium"
            style={{ color: 'var(--rtap-text)', marginBottom: '4px' }}
          >
            <span
              className="rtap-option__letter"
              style={{ marginRight: '8px', fontSize: '10px' }}
            >
              {index + 1}
            </span>
            {question?.question}
          </p>
          <div className="flex flex-wrap gap-2 text-xs" style={{ opacity: 0.75 }}>
            <span style={{ color: 'var(--rtap-text)' }}>
              Тип: <strong>{question?.type?.toUpperCase()}</strong>
            </span>
            {answerRecord?.timeSpent !== undefined && (
              <span style={{ color: 'var(--rtap-text)' }}>
                Время: <strong>{answerRecord.timeSpent}с</strong>
              </span>
            )}
            {timedOut && (
              <span style={{ color: 'var(--rtap-red)' }}>⏱ Время вышло</span>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

// ─── Main ResultScreen ────────────────────────────────────────────────────────
export default function ResultScreen({
  topic,
  level,
  lang,
  score,
  scorePct = 0,
  total,
  percentile,
  totalTakers = 0,
  answers = [],
  questions = [],
  onRetry,
  onCertificate,
  onHome,
}) {
  const rank = useMemo(() => getRank(scorePct), [scorePct]);

  // Unlock message logic
  const showUnlockMessage = useMemo(() => {
    if (level === 'beginner'      && scorePct >= 60) return true;
    if (level === 'intermediate'  && scorePct >= 70) return true;
    return false;
  }, [level, scorePct]);

  const unlockLabel = useMemo(() => {
    if (level === 'beginner')     return 'Средний уровень';
    if (level === 'intermediate') return 'Продвинутый уровень';
    return '';
  }, [level]);

  const pctLabel = useMemo(() => {
    if (scorePct >= 85) return 'Отличный результат!';
    if (scorePct >= 70) return 'Хороший результат!';
    if (scorePct >= 50) return 'Неплохо, есть куда расти';
    return 'Попробуйте ещё раз';
  }, [scorePct]);

  return (
    <div className="rtap-wrap">
      {/* Header card */}
      <div className="rtap-card mb-4 text-center">
        <h2
          className="text-xl font-bold mb-1"
          style={{ color: 'var(--rtap-text)' }}
        >
          {pctLabel}
        </h2>

        {/* Topic / level / lang tags */}
        <div className="flex flex-wrap justify-center gap-2 mb-5 text-xs">
          {topic && (
            <span className="rtap-rank" style={{ background: 'var(--rtap-border)', color: 'var(--rtap-text)' }}>
              {topic}
            </span>
          )}
          {level && (
            <span className="rtap-rank" style={{ background: 'var(--rtap-border)', color: 'var(--rtap-text)' }}>
              {level === 'beginner' ? 'Начальный' : level === 'intermediate' ? 'Средний' : 'Продвинутый'}
            </span>
          )}
          {lang && (
            <span className="rtap-rank" style={{ background: 'var(--rtap-border)', color: 'var(--rtap-text)' }}>
              {lang.toUpperCase()}
            </span>
          )}
        </div>

        {/* Score circle */}
        <ScoreCircle scorePct={scorePct} />

        {/* Score fraction */}
        <p className="mt-3 text-base" style={{ color: 'var(--rtap-text)', opacity: 0.75 }}>
          {score} из {total} правильных ответов
        </p>

        {/* Rank badge */}
        <div className="flex justify-center mt-3 mb-4">
          <span
            className="rtap-rank"
            style={{
              background: `${rank.color}22`,
              color: rank.color,
              border: `1.5px solid ${rank.color}`,
              fontSize: '15px',
              padding: '6px 18px',
            }}
          >
            {rank.emoji} {rank.label}
          </span>
        </div>

        {/* Percentile */}
        {totalTakers > 0 && percentile !== undefined && percentile !== null && (
          <p className="text-sm mt-1" style={{ color: 'var(--rtap-text)', opacity: 0.7 }}>
            Твой результат выше, чем у <strong>{percentile}%</strong> из {totalTakers.toLocaleString('ru-RU')} человек
          </p>
        )}
      </div>

      {/* Unlock message */}
      {showUnlockMessage && (
        <div
          className="rtap-card mb-4"
          style={{
            borderColor: 'var(--rtap-green)',
            background: 'color-mix(in srgb, var(--rtap-green) 8%, transparent)',
          }}
        >
          <p className="text-sm font-semibold" style={{ color: 'var(--rtap-green)' }}>
            🎉 Вы открыли уровень: <strong>{unlockLabel}</strong>
          </p>
        </div>
      )}

      {/* Action buttons */}
      <div className="flex flex-wrap gap-3 mb-6">
        {scorePct >= 70 && onCertificate && (
          <button
            className="rtap-btn rtap-btn--gold flex-1"
            onClick={onCertificate}
          >
            🏅 Получить сертификат
          </button>
        )}
        {onRetry && (
          <button
            className="rtap-btn rtap-btn--outline flex-1"
            onClick={onRetry}
          >
            Повторить
          </button>
        )}
        {onHome && (
          <button
            className="rtap-btn rtap-btn--outline flex-1"
            onClick={onHome}
          >
            На главную
          </button>
        )}
      </div>

      {/* Answer review */}
      {questions.length > 0 && (
        <div className="rtap-card">
          <h3
            className="text-base font-bold mb-4"
            style={{ color: 'var(--rtap-text)' }}
          >
            Разбор ответов
          </h3>
          {questions.map((q, idx) => (
            <AnswerReviewItem
              key={idx}
              question={q}
              answerRecord={answers.find((a) => a.idx === idx)}
              index={idx}
            />
          ))}
        </div>
      )}
    </div>
  );
}
