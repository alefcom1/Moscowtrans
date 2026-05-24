import { useState, useEffect } from 'react';

function Countdown({ targetTs }) {
  const [left, setLeft] = useState(Math.max(0, targetTs * 1000 - Date.now()));

  useEffect(() => {
    const id = setInterval(() => setLeft(Math.max(0, targetTs * 1000 - Date.now())), 1000);
    return () => clearInterval(id);
  }, [targetTs]);

  const h = Math.floor(left / 3600000);
  const m = Math.floor((left % 3600000) / 60000);
  const s = Math.floor((left % 60000) / 1000);
  return <span className="font-mono">{h}:{String(m).padStart(2,'0')}:{String(s).padStart(2,'0')}</span>;
}

export default function QuestionOfWeek() {
  const [qow,      setQow]      = useState(null);
  const [loading,  setLoading]  = useState(true);
  const [selected, setSelected] = useState(null);
  const [result,   setResult]   = useState(null);
  const [answered, setAnswered] = useState(false);

  const base = window.rtapConfig?.apiBase || '/wp-json/rtap/v1';

  useEffect(() => {
    // Check localStorage if already answered this week
    const key = 'rtap_qow_answered';
    const stored = localStorage.getItem(key);
    if (stored) {
      const { weekStart, answer } = JSON.parse(stored);
      const thisMonday = getThisMonday();
      if (weekStart === thisMonday) setAnswered(true);
    }

    fetch(`${base}/qow`)
      .then(r => r.ok ? r.json() : null)
      .then(d => { if (d) setQow(d); setLoading(false); })
      .catch(() => setLoading(false));
  }, []);

  function getThisMonday() {
    const d = new Date();
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    d.setDate(diff);
    return d.toISOString().split('T')[0];
  }

  async function handleAnswer(option) {
    if (answered || result) return;
    setSelected(option);

    const res = await fetch(`${base}/qow/answer`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ qow_id: qow.qow_id, option: String(option) }),
    });
    const data = await res.json();
    setResult(data);

    localStorage.setItem('rtap_qow_answered', JSON.stringify({
      weekStart: getThisMonday(),
      answer: option,
    }));
  }

  if (loading) return (
    <div className="rtap-qow rtap-card" style={{textAlign:'center', padding:20, color:'var(--rtap-text)'}}>
      Загрузка вопроса недели...
    </div>
  );

  if (!qow) return null;

  const payload = qow.payload || {};
  const options = payload.options || [];
  const stats   = qow.stats || {};

  return (
    <div className="rtap-qow rtap-card">
      <div className="flex items-center justify-between mb-4">
        <h3 className="font-bold" style={{color:'var(--rtap-accent)'}}>Вопрос недели</h3>
        {qow.next_reset && (
          <span className="text-xs" style={{color:'var(--rtap-text)', opacity:0.55}}>
            Смена через <Countdown targetTs={qow.next_reset} />
          </span>
        )}
      </div>

      <p className="mb-4 font-medium" style={{color:'var(--rtap-text)'}}>{qow.question}</p>

      {payload.source && (
        <div className="mb-4 p-3 rounded-lg text-sm italic" style={{background:'var(--rtap-bg)', border:'1px solid var(--rtap-border)', color:'var(--rtap-text)'}}>
          {payload.source}
        </div>
      )}

      <div className="flex flex-col gap-2">
        {options.map((opt, i) => {
          let cls = 'rtap-option';
          if (result) {
            if (String(i) === String(result.correct_val)) cls += ' rtap-option--correct';
            else if (String(i) === String(selected))      cls += ' rtap-option--wrong';
          } else if (String(i) === String(selected)) {
            cls += ' rtap-option--selected';
          }

          return (
            <button key={i} className={cls} onClick={() => handleAnswer(i)}
              disabled={!!result || answered}>
              <span className="rtap-option__letter">{String.fromCharCode(65 + i)}</span>
              <span>{opt}</span>
            </button>
          );
        })}
      </div>

      {answered && !result && (
        <p className="mt-4 text-sm text-center" style={{color:'var(--rtap-text)', opacity:0.6}}>
          Вы уже отвечали на этой неделе
        </p>
      )}

      {result && (
        <div className="mt-4 p-3 rounded-lg text-sm"
          style={{background: result.correct ? 'color-mix(in srgb, var(--rtap-green) 12%, transparent)' : 'color-mix(in srgb, var(--rtap-red) 12%, transparent)',
                  border:`1px solid ${result.correct ? 'var(--rtap-green)' : 'var(--rtap-red)'}`,
                  color:'var(--rtap-text)'}}>
          <strong>{result.correct ? '✅ Верно!' : '❌ Неверно'}</strong>
          {result.explanation && <p className="mt-1 opacity-80">{result.explanation}</p>}
        </div>
      )}

      {(result || answered) && stats.total_answers > 0 && (
        <p className="mt-3 text-xs text-center" style={{color:'var(--rtap-text)', opacity:0.55}}>
          {stats.correct_pct}% из {stats.total_answers} ответивших ответили правильно
        </p>
      )}

      <div className="mt-4 text-center">
        <a href="/test-perevodchika/"
          className="text-sm underline" style={{color:'var(--rtap-accent)'}}>
          Пройти полный тест →
        </a>
      </div>
    </div>
  );
}
