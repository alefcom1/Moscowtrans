import { useState } from 'react';

const LETTERS = ['А', 'Б', 'В', 'Г'];

export default function QuestionMC({ question, payload, onAnswer, disabled, result }) {
  const [selected, setSelected] = useState(null);
  const { options = [], correct } = payload || {};

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
      <p className="text-base font-semibold mb-5 rtap-question__text">{question}</p>
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
