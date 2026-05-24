import { useState, useEffect } from 'react';

const TOPIC_NAMES = { technical:'Технический перевод', legal:'Юридический перевод', medical:'Медицинский перевод', it:'IT-перевод' };
const LEVEL_NAMES = { beginner:'Beginner', intermediate:'Intermediate', advanced:'Advanced' };

export default function VerifyCert({ certId }) {
  const [cert,    setCert]    = useState(null);
  const [loading, setLoading] = useState(true);
  const [error,   setError]   = useState('');

  useEffect(() => {
    if (!certId) { setLoading(false); setError('ID сертификата не указан'); return; }
    const base = window.rtapConfig?.apiBase || '/wp-json/rtap/v1';
    fetch(`${base}/verify/${certId}`)
      .then(r => r.ok ? r.json() : Promise.reject(r.status))
      .then(d => { setCert(d); setLoading(false); })
      .catch(() => { setError('Сертификат не найден или недействителен'); setLoading(false); });
  }, [certId]);

  if (loading) return <div className="rtap-wrap p-8 text-center" style={{color:'var(--rtap-text)'}}>Проверка...</div>;

  if (error) return (
    <div className="rtap-wrap p-4">
      <div className="rtap-card max-w-md mx-auto text-center">
        <div style={{fontSize:48}}>❌</div>
        <h2 className="text-xl font-bold mt-3" style={{color:'var(--rtap-red)'}}>Недействительный сертификат</h2>
        <p className="mt-2 text-sm" style={{color:'var(--rtap-text)', opacity:0.7}}>{error}</p>
      </div>
    </div>
  );

  const date = cert.issued_at ? new Date(cert.issued_at).toLocaleDateString('ru-RU', {day:'numeric',month:'long',year:'numeric'}) : '';

  return (
    <div className="rtap-wrap p-4">
      <div className="rtap-card max-w-lg mx-auto text-center">
        <div style={{fontSize:56}}>✅</div>
        <h2 className="text-2xl font-bold mt-3" style={{color:'var(--rtap-green)'}}>Сертификат действителен</h2>

        <div className="mt-6 p-5 rounded-xl text-left" style={{background:'var(--rtap-bg)', border:'1px solid var(--rtap-border)'}}>
          <Row label="Переводчик"  value={cert.candidate_name || '—'} />
          <Row label="Тематика"    value={TOPIC_NAMES[cert.topic] || cert.topic} />
          <Row label="Уровень"     value={LEVEL_NAMES[cert.level] || cert.level} />
          <Row label="Результат"   value={`${cert.score_pct}%`} />
          <Row label="Дата выдачи" value={date} />
          <Row label="Номер"       value={cert.id} mono />
        </div>

        <p className="mt-4 text-xs" style={{color:'var(--rtap-text)', opacity:0.5}}>
          Сертификат выдан бюро переводов «Ремарка» — moscowtrans.ru
        </p>
      </div>
    </div>
  );
}

function Row({ label, value, mono }) {
  return (
    <div className="flex justify-between py-2" style={{borderBottom:'1px solid var(--rtap-border)'}}>
      <span className="text-sm" style={{color:'var(--rtap-text)', opacity:0.6}}>{label}</span>
      <span className={`text-sm font-semibold ${mono ? 'font-mono' : ''}`} style={{color:'var(--rtap-text)'}}>{value}</span>
    </div>
  );
}
