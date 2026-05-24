import { useState } from 'react';

const LETTERS = ['А', 'Б', 'В', 'Г'];

export default function QuestionFB({ question, payload, onAnswer, disabled, result }) {
  const [selected, setSelected] = useState(null);
  const { sentence = '', options = [], correct } = payload || {};

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

  // Render sentence replacing ___ with a styled blank slot
  function renderSentence() {
    const filledWord = result
      ? options[correct]
      : selected !== null
      ? options[selected]
      : null;

    const parts = sentence.split('___');
    if (parts.length < 2) return <span>{sentence}</span>;

    const blankClass = result
      ? 'rtap-fb-blank rtap-fb-blank--correct'
      : selected !== null
      ? 'rtap-fb-blank rtap-fb-blank--selected'
      : 'rtap-fb-blank';

    return (
      <>
        {parts[0]}
        <span className={blankClass}>
          {filledWord || '___'}
        </span>
        {parts[1]}
      </>
    );
  }

  return (
    <div className="rtap-question-enter">
      <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>

      <div className="rtap-card mb-5 text-base leading-relaxed rtap-source-block rtap-source-block--purple">
        {renderSentence()}
      </div>

      <p className="text-sm mb-3 rtap-hint-text">Выберите подходящий вариант:</p>
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
