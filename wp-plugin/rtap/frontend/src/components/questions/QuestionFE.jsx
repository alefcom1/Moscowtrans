import { useState } from 'react';

export default function QuestionFE({ question, payload, onAnswer, disabled, result }) {
  const [selected, setSelected] = useState(null);
  const { source = '', translation = '', segments = [], correct } = payload || {};

  function handleSegmentClick(idx) {
    if (disabled || result) return;
    setSelected(idx);
    onAnswer(idx);
  }

  function getSegmentClass(idx) {
    if (!result) {
      return idx === selected ? 'rtap-fe-segment rtap-fe-segment--selected' : 'rtap-fe-segment';
    }
    if (idx === correct) return 'rtap-fe-segment rtap-fe-segment--correct';
    if (idx === selected && idx !== correct) return 'rtap-fe-segment rtap-fe-segment--wrong';
    return 'rtap-fe-segment rtap-fe-segment--idle';
  }

  // Inject clickable segments inline within the translation string
  function renderTranslation() {
    let remaining = translation;
    const parts = [];
    let anyFound = false;

    for (let i = 0; i < segments.length; i++) {
      const seg = segments[i];
      const pos = remaining.indexOf(seg);
      if (pos === -1) continue;
      anyFound = true;
      if (pos > 0) parts.push(<span key={`pre-${i}`}>{remaining.slice(0, pos)}</span>);
      parts.push(
        <span
          key={`seg-${i}`}
          className={getSegmentClass(i)}
          onClick={() => handleSegmentClick(i)}
          role="button"
          tabIndex={disabled || result ? -1 : 0}
          onKeyDown={e => { if (e.key === 'Enter' || e.key === ' ') handleSegmentClick(i); }}
        >
          {seg}
        </span>
      );
      remaining = remaining.slice(pos + seg.length);
    }

    if (remaining) parts.push(<span key="post">{remaining}</span>);

    if (!anyFound) {
      // Fallback: show translation as plain text, segments as separate clickable chips
      return (
        <>
          <p className="mb-3 leading-relaxed">{translation}</p>
          <p className="text-xs mb-2 rtap-hint-text">Нажмите на фрагмент с ошибкой:</p>
          <div className="flex flex-wrap gap-2">
            {segments.map((seg, idx) => (
              <span
                key={idx}
                className={`${getSegmentClass(idx)} px-2 py-1 rounded`}
                onClick={() => handleSegmentClick(idx)}
                role="button"
                tabIndex={disabled || result ? -1 : 0}
                onKeyDown={e => { if (e.key === 'Enter' || e.key === ' ') handleSegmentClick(idx); }}
              >
                {seg}
              </span>
            ))}
          </div>
        </>
      );
    }

    return <p className="leading-relaxed">{parts}</p>;
  }

  return (
    <div className="rtap-question-enter">
      <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>

      <div className="rtap-card mb-4 text-sm leading-relaxed rtap-source-block rtap-source-block--accent">
        <span className="text-xs font-semibold uppercase tracking-wide mb-2 block rtap-source-block__label">
          Исходный текст
        </span>
        {source}
      </div>

      <div className="rtap-card mb-3 text-sm rtap-source-block rtap-source-block--gold">
        <span className="text-xs font-semibold uppercase tracking-wide mb-2 block rtap-source-block__label rtap-source-block__label--gold">
          Перевод — кликните на фрагмент с ошибкой
        </span>
        {renderTranslation()}
      </div>

      {!result && selected !== null && (
        <p className="text-sm mt-2 rtap-selection-hint">
          Выбран фрагмент {selected + 1}: «{segments[selected]}»
        </p>
      )}
    </div>
  );
}
