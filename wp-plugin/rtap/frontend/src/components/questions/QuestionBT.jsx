import { useState } from 'react';

const LETTERS = ['А', 'Б', 'В'];

export default function QuestionBT({ question, payload, onAnswer, disabled, result }) {
  const [selected, setSelected] = useState(null);
  const { source = '', options = [], correct } = payload || {};

  function handleClick(idx) {
    if (disabled || result) return;
    setSelected(idx);
    onAnswer(idx);
  }

  function getOptionClass(idx) {
    let cls = 'rtap-option';
    if (result) {
      if (idx === correct) cls += ' rtap-option--correct';
      else if (idx === selected && idx !== correct) cls += ' rtap-option--wrong';
    } else if (idx === selected) {
      cls += ' rtap-option--selected';
    }
    return cls;
  }

  return (
    <div className="rtap-question-enter">
      <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>

      <div className="rtap-card mb-5 text-sm leading-relaxed rtap-source-block rtap-source-block--accent">
        <span className="text-xs font-semibold uppercase tracking-wide mb-2 block rtap-source-block__label">
          Исходный текст
        </span>
        {source}
      </div>

      <div className="flex flex-col gap-3">
        {options.map((opt, idx) => (
          <button
            key={idx}
            className={getOptionClass(idx)}
            onClick={() => handleClick(idx)}
            disabled={disabled || !!result}
          >
            <span className="rtap-option__letter">{LETTERS[idx]}</span>
            <span>{opt}</span>
          </button>
        ))}
      </div>
    </div>
  );
}
