import { useState, useEffect, useRef, useCallback } from 'react';
import { useTimer } from '../hooks/useTimer';
import QuestionMC from './questions/QuestionMC';
import QuestionBT from './questions/QuestionBT';
import QuestionFE from './questions/QuestionFE';
import QuestionFB from './questions/QuestionFB';
import QuestionTM from './questions/QuestionTM';
import QuestionRO from './questions/QuestionRO';

const QUESTION_DURATION = 30;
const EXPLANATION_DELAY = 2000; // ms to show feedback before next question
const AUTO_NEXT_DELAY   = 1500; // ms after timer expire before advancing

// Map question type string → component
const TYPE_MAP = {
  mc: QuestionMC,
  bt: QuestionBT,
  fe: QuestionFE,
  fb: QuestionFB,
  tm: QuestionTM,
  ro: QuestionRO,
};

// ─── Timer ring SVG ──────────────────────────────────────────────────────────
function TimerRing({ left, total, color, dash }) {
  const urgent = left <= 7;
  return (
    <div className="rtap-timer" aria-label={`Осталось ${left} секунд`}>
      <svg viewBox="0 0 48 48" width="56" height="56">
        <circle
          className="rtap-timer__track"
          cx="24" cy="24" r="22"
          fill="none"
          strokeWidth="4"
        />
        <circle
          className="rtap-timer__fill"
          cx="24" cy="24" r="22"
          fill="none"
          strokeWidth="4"
          stroke={color}
          strokeDasharray="138.2"
          strokeDashoffset={138.2 - dash}
        />
      </svg>
      <div className={`rtap-timer__num${urgent ? ' rtap-timer__num--urgent' : ''}`}>
        {left}
      </div>
    </div>
  );
}

// ─── Progress bar ────────────────────────────────────────────────────────────
function ProgressBar({ current, total }) {
  const pct = total > 0 ? Math.round((current / total) * 100) : 0;
  return (
    <div className="rtap-progress" role="progressbar" aria-valuenow={pct} aria-valuemin={0} aria-valuemax={100}>
      <div className="rtap-progress__fill" style={{ width: `${pct}%` }} />
    </div>
  );
}

// ─── Main QuizEngine ─────────────────────────────────────────────────────────
export default function QuizEngine({
  topic,
  level,
  lang,
  sessionKey,
  questions = [],
  onFinish,
}) {
  const [currentIdx, setCurrentIdx]     = useState(0);
  const [answers, setAnswers]           = useState([]);
  const [result, setResult]             = useState(null);    // null | 'correct' | 'wrong' | 'timeout'
  const [givenAnswer, setGivenAnswer]   = useState(null);
  const [phase, setPhase]               = useState('question'); // 'question' | 'explanation' | 'transitioning'
  const [animClass, setAnimClass]       = useState('rtap-question-enter');

  const startTimeRef    = useRef(Date.now());
  const qStartTimeRef   = useRef(Date.now());
  const advanceTimerRef = useRef(null);

  const totalQuestions = questions.length;
  const currentQ       = questions[currentIdx] || null;

  // ── Check answer correctness ────────────────────────────────────────────
  function checkCorrect(type, payload, given) {
    if (given === null || given === undefined) return false;
    if (!payload) return false;

    switch (type) {
      case 'mc':
      case 'bt':
      case 'fe':
      case 'fb':
        return given === payload.correct;

      case 'tm': {
        // given: [[leftIdx, rightIdx], ...]
        // payload.pairs: [[l, r], ...]
        if (!Array.isArray(given) || !Array.isArray(payload.pairs)) return false;
        const correctSet = new Set(payload.pairs.map(([l, r]) => `${l}-${r}`));
        return (
          given.length === payload.pairs.length &&
          given.every(([l, r]) => correctSet.has(`${l}-${r}`))
        );
      }

      case 'ro': {
        // given: array of original indices in user's chosen order
        // correct_order: array of original indices in correct order
        if (!Array.isArray(given) || !Array.isArray(payload.correct_order)) return false;
        return given.every((v, i) => v === payload.correct_order[i]);
      }

      default:
        return false;
    }
  }

  // ── Advance to next question or finish ──────────────────────────────────
  const advance = useCallback(() => {
    clearTimeout(advanceTimerRef.current);

    if (currentIdx + 1 >= totalQuestions) {
      const totalTime = Math.round((Date.now() - startTimeRef.current) / 1000);
      onFinish({
        answers: answers.concat(), // already includes current via setAnswers below
        totalTime,
      });
      return;
    }

    setAnimClass('rtap-question-exit');
    setTimeout(() => {
      setCurrentIdx((i) => i + 1);
      setResult(null);
      setGivenAnswer(null);
      setPhase('question');
      setAnimClass('rtap-question-enter');
      qStartTimeRef.current = Date.now();
    }, 300);
  }, [currentIdx, totalQuestions, onFinish, answers]);

  // ── Timer expiry ────────────────────────────────────────────────────────
  const handleTimerExpire = useCallback(() => {
    if (phase !== 'question') return;
    const timeSpent = Math.round((Date.now() - qStartTimeRef.current) / 1000);
    const record = { idx: currentIdx, given: null, timeSpent, timedOut: true };
    setAnswers((prev) => [...prev, record]);
    setGivenAnswer(null);
    setResult('timeout');
    setPhase('explanation');
    advanceTimerRef.current = setTimeout(advance, AUTO_NEXT_DELAY);
  }, [phase, currentIdx, advance]);

  const { left, color, dash, start, stop } = useTimer(QUESTION_DURATION, handleTimerExpire);

  // ── Start timer on each new question ───────────────────────────────────
  useEffect(() => {
    if (!currentQ) return;
    qStartTimeRef.current = Date.now();
    start();
    return () => stop();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [currentIdx]);

  // ── Handle answer submission ────────────────────────────────────────────
  function handleAnswer(given) {
    if (phase !== 'question') return;
    stop();
    clearTimeout(advanceTimerRef.current);

    const timeSpent = Math.round((Date.now() - qStartTimeRef.current) / 1000);
    const isCorrect = checkCorrect(currentQ.type, currentQ.payload, given);
    const record    = { idx: currentIdx, given, timeSpent, correct: isCorrect };

    setAnswers((prev) => [...prev, record]);
    setGivenAnswer(given);
    setResult(isCorrect ? 'correct' : 'wrong');
    setPhase('explanation');

    advanceTimerRef.current = setTimeout(advance, EXPLANATION_DELAY);
  }

  // Clean up on unmount
  useEffect(() => () => { clearTimeout(advanceTimerRef.current); }, []);

  if (!currentQ) {
    return (
      <div className="rtap-card flex items-center justify-center" style={{ minHeight: '200px' }}>
        <p className="rtap-hint-text">Загрузка вопросов…</p>
      </div>
    );
  }

  const QuestionComponent = TYPE_MAP[currentQ.type] || QuestionMC;

  // ── Explanation / feedback banner ────────────────────────────────────────
  function ExplanationBanner() {
    if (!result) return null;

    if (result === 'timeout') {
      return (
        <div className="rtap-card mt-4 rtap-feedback-banner rtap-feedback-banner--timeout">
          <span className="rtap-feedback-banner__icon">⏱</span>
          <span>Время истекло</span>
        </div>
      );
    }

    if (result === 'correct') {
      return (
        <div className="rtap-card mt-4 rtap-feedback-banner rtap-feedback-banner--correct">
          <span className="rtap-feedback-banner__icon">✓</span>
          <span>Верно!</span>
          {currentQ.explanation && (
            <p className="rtap-feedback-banner__explanation">{currentQ.explanation}</p>
          )}
        </div>
      );
    }

    return (
      <div className="rtap-card mt-4 rtap-feedback-banner rtap-feedback-banner--wrong">
        <span className="rtap-feedback-banner__icon">✗</span>
        <span>Неверно</span>
        {currentQ.explanation && (
          <p className="rtap-feedback-banner__explanation">{currentQ.explanation}</p>
        )}
      </div>
    );
  }

  return (
    <div className="rtap-wrap">
      {/* Progress bar */}
      <div className="mb-4">
        <ProgressBar current={currentIdx} total={totalQuestions} />
        <div className="flex justify-between mt-1">
          <span className="text-xs rtap-hint-text">
            Вопрос {currentIdx + 1} из {totalQuestions}
          </span>
          <span className="text-xs rtap-hint-text">
            {topic && <>{topic}</>}
            {level && <> · {level}</>}
          </span>
        </div>
      </div>

      {/* Question card */}
      <div className="rtap-card">
        {/* Card header: question type label + timer */}
        <div className="flex items-center justify-between mb-4">
          <span className="text-xs font-semibold uppercase tracking-wide rtap-type-badge">
            {currentQ.type === 'mc' && 'Выбор ответа'}
            {currentQ.type === 'bt' && 'Лучший перевод'}
            {currentQ.type === 'fe' && 'Найдите ошибку'}
            {currentQ.type === 'fb' && 'Заполните пропуск'}
            {currentQ.type === 'tm' && 'Сопоставление'}
            {currentQ.type === 'ro' && 'Расставьте порядок'}
          </span>
          <TimerRing left={left} total={QUESTION_DURATION} color={color} dash={dash} />
        </div>

        {/* Question body with enter animation */}
        <div key={currentIdx} className={animClass}>
          <QuestionComponent
            question={currentQ.question}
            payload={currentQ.payload}
            onAnswer={handleAnswer}
            disabled={phase !== 'question'}
            result={result}
            givenAnswer={givenAnswer}
          />
        </div>

        <ExplanationBanner />
      </div>
    </div>
  );
}
