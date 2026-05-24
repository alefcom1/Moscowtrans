import { useState, useCallback, useEffect, useRef } from 'react';
import { useTheme }  from './hooks/useTheme';
import StartScreen   from './components/StartScreen';
import LevelSelect   from './components/LevelSelect';
import QuizEngine    from './components/QuizEngine';
import ResultScreen  from './components/ResultScreen';
import Certificate   from './components/Certificate';
import CandidateForm from './components/CandidateForm';
import SocialShare   from './components/SocialShare';
import { useProgress } from './hooks/useProgress';

const SCREENS = { START:'start', LEVEL:'level', QUIZ:'quiz', RESULT:'result', CERT:'cert', FORM:'form', SHARE:'share' };

export default function App({ defaultTopic, defaultLang }) {
  // If topic is pre-set from shortcode, skip StartScreen and go straight to level select
  const [screen,    setScreen]    = useState(defaultTopic ? SCREENS.LEVEL : SCREENS.START);
  const [topic,     setTopic]     = useState(defaultTopic || '');
  const [level,     setLevel]     = useState('');
  const [lang,      setLang]      = useState(defaultLang  || 'en');
  const [quizData,  setQuizData]  = useState(null);   // { questions, sessionKey }
  const [result,    setResult]    = useState(null);   // from API after attempt
  const [certId,    setCertId]    = useState('');
  const [candidateId, setCandidateId] = useState(null);
  const [loading,   setLoading]   = useState(false);
  const [error,     setError]     = useState('');

  const { setScore, recordAttempt } = useProgress();
  const theme = useTheme();

  // Scroll widget into view on every screen transition
  useEffect(() => {
    const el = document.getElementById('rtap-root');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }, [screen]);
  const base = window.rtapConfig?.apiBase || '/wp-json/rtap/v1';

  const handleStart = useCallback(async (t, l) => {
    setTopic(t); setLang(l);
    setScreen(SCREENS.LEVEL);
  }, []);

  const handleLevelSelect = useCallback(async (lvl) => {
    setLevel(lvl);
    setLoading(true);
    setError('');

    try {
      const r = await fetch(`${base}/questions?topic=${topic}&level=${lvl}&lang=${lang}&count=10`);
      if (!r.ok) throw new Error('Не удалось загрузить вопросы');
      const d = await r.json();
      if (!d.questions?.length) throw new Error('Вопросы по этой теме ещё не добавлены');
      setQuizData({ questions: d.questions, sessionKey: d.session_key });
      setScreen(SCREENS.QUIZ);
    } catch (e) {
      setError(e.message);
    } finally {
      setLoading(false);
    }
  }, [topic, lang, base]);

  const handleQuizFinish = useCallback(async ({ answers, totalTime }) => {
    if (!quizData) return;
    recordAttempt(topic, level, lang);
    setLoading(true);

    try {
      const sessionId = `${Date.now()}-${Math.random().toString(36).slice(2)}`;
      const r = await fetch(`${base}/attempt`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.rtapConfig?.nonce || '' },
        body: JSON.stringify({
          session_id:  sessionId,
          session_key: quizData.sessionKey,
          topic, level, lang,
          answers: answers.map(a => a.given),
          time_taken: totalTime,
        }),
      });
      const d = await r.json();
      setScore(topic, level, d.score_pct, lang);
      setResult({ ...d, answers, questions: quizData.questions });
      setScreen(SCREENS.RESULT);
    } catch {
      setError('Ошибка при сохранении результата');
    } finally {
      setLoading(false);
    }
  }, [quizData, topic, level, lang, base, setScore, recordAttempt]);

  const handleCertificate = useCallback(async (name) => {
    if (!result) return;
    setLoading(true);

    try {
      const r = await fetch(`${base}/certificate`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.rtapConfig?.nonce || '' },
        body: JSON.stringify({
          topic, level, lang,
          score_pct:    result.score_pct,
          candidate_id: candidateId,
          name,
        }),
      });
      const d = await r.json();
      setCertId(d.cert_id);
      setScreen(SCREENS.CERT);
    } catch {
      setError('Ошибка генерации сертификата');
    } finally {
      setLoading(false);
    }
  }, [result, topic, level, lang, candidateId, base]);

  const handleFormSubmit = useCallback((id) => {
    setCandidateId(id);
    setScreen(SCREENS.SHARE);
  }, []);

  const handleRetry = useCallback(() => {
    setResult(null); setQuizData(null); setCertId('');
    setScreen(SCREENS.LEVEL);
  }, []);

  const handleHome = useCallback(() => {
    setResult(null); setQuizData(null); setCertId(''); setTopic(''); setLevel('');
    setScreen(SCREENS.START);
  }, []);

  return (
    <div className="rtap-wrap" data-rtap-theme={theme}>
      {error && (
        <div className="p-4 mb-4 rounded-lg text-sm text-center"
          style={{ background: 'color-mix(in srgb, var(--rtap-red) 10%, transparent)', border: '1px solid var(--rtap-red)', color: 'var(--rtap-red)' }}>
          {error}
          <button className="ml-3 underline" onClick={() => setError('')}>✕</button>
        </div>
      )}

      {loading && (
        <div className="text-center py-8" style={{ color: 'var(--rtap-text)', opacity: 0.6 }}>
          Загрузка...
        </div>
      )}

      {!loading && screen === SCREENS.START && (
        <StartScreen defaultTopic={topic} defaultLang={lang} onStart={handleStart} />
      )}

      {!loading && screen === SCREENS.LEVEL && (
        <LevelSelect topic={topic} lang={lang} onSelect={handleLevelSelect} onBack={() => setScreen(SCREENS.START)} />
      )}

      {!loading && screen === SCREENS.QUIZ && quizData && (
        <QuizEngine
          topic={topic} level={level} lang={lang}
          questions={quizData.questions}
          sessionKey={quizData.sessionKey}
          onFinish={handleQuizFinish}
        />
      )}

      {!loading && screen === SCREENS.RESULT && result && (
        <ResultScreen
          topic={topic} level={level} lang={lang}
          score={result.score}
          scorePct={result.score_pct}
          total={result.total}
          percentile={result.percentile}
          totalTakers={result.total_takers}
          answers={result.answers}
          questions={result.questions}
          onRetry={handleRetry}
          onHome={handleHome}
          onCertificate={() => setScreen(SCREENS.FORM)}
        />
      )}

      {!loading && screen === SCREENS.FORM && result && (
        <CandidateForm
          topic={topic} level={level} lang={lang}
          scorePct={result.score_pct}
          certId={certId}
          onSubmit={(id, name) => { handleCertificate(name); setCandidateId(id); }}
          onSkip={() => handleCertificate('Переводчик')}
        />
      )}

      {!loading && screen === SCREENS.CERT && certId && result && (
        <div className="p-4">
          <div className="rtap-card max-w-3xl mx-auto">
            <h2 className="text-xl font-bold mb-6 text-center" style={{ color: 'var(--rtap-gold)' }}>
              🏆 Ваш сертификат
            </h2>
            <Certificate
              name={''} topic={topic} level={level} lang={lang}
              scorePct={result.score_pct} certId={certId}
            />
            <div className="mt-8">
              <SocialShare
                scorePct={result.score_pct}
                topic={topic} level={level} lang={lang}
                certId={certId}
                siteUrl={window.rtapConfig?.siteUrl || ''}
              />
            </div>
            <div className="flex justify-center mt-6 gap-3">
              <button className="rtap-btn rtap-btn--outline" onClick={handleHome}>На главную</button>
              <button className="rtap-btn rtap-btn--primary" onClick={handleRetry}>Следующий уровень</button>
            </div>
          </div>
        </div>
      )}

      {!loading && screen === SCREENS.SHARE && (
        <div className="p-4">
          <div className="rtap-card max-w-xl mx-auto text-center">
            <div style={{ fontSize: 56 }}>🎉</div>
            <h2 className="text-xl font-bold mt-3" style={{ color: 'var(--rtap-text)' }}>
              Заявка отправлена!
            </h2>
            <p className="mt-2 text-sm" style={{ color: 'var(--rtap-text)', opacity: 0.7 }}>
              Наш менеджер свяжется с вами в течение 1 рабочего дня.
            </p>
            {result && (
              <SocialShare
                scorePct={result.score_pct}
                topic={topic} level={level} lang={lang}
                certId={certId}
                siteUrl={window.rtapConfig?.siteUrl || ''}
              />
            )}
            <button className="rtap-btn rtap-btn--outline mt-6" onClick={handleHome}>На главную</button>
          </div>
        </div>
      )}
    </div>
  );
}
