import { useEffect, useRef } from 'react';
import { useTheme } from '../hooks/useTheme';

const TOPIC_NAMES = {
  technical: 'Технический перевод',
  legal:     'Юридический перевод',
  medical:   'Медицинский перевод',
  it:        'IT-перевод',
};

const LEVEL_NAMES = {
  beginner:     'Beginner',
  intermediate: 'Intermediate',
  advanced:     'Advanced',
};

const LANG_NAMES = {
  en: 'Английский → Русский',
  de: 'Немецкий → Русский',
  fr: 'Французский → Русский',
  es: 'Испанский → Русский',
  zh: 'Китайский → Русский',
  ar: 'Арабский → Русский',
  it: 'Итальянский → Русский',
  pt: 'Португальский → Русский',
};

export default function Certificate({ name, topic, level, lang = 'en', scorePct, certId, onDownloadPng, onDownloadPdf }) {
  const canvasRef = useRef(null);
  const theme     = useTheme();

  useEffect(() => {
    drawCertificate();
  }, [name, topic, level, scorePct, certId, theme]);

  function drawCertificate() {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = 1200, H = 840;
    canvas.width = W; canvas.height = H;

    const dark = theme === 'dark';
    const bg     = dark ? '#0D0D1A' : '#FFFFFF';
    const navy   = dark ? '#A084E8' : '#1A3C6E';
    const gold   = '#C9A84C';
    const text   = dark ? '#E8E8E8' : '#1A1A2E';
    const subtle = dark ? '#2A2A4E' : '#DDDDDD';

    // Background
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, W, H);

    // Border decoration
    ctx.strokeStyle = gold;
    ctx.lineWidth = 6;
    ctx.strokeRect(30, 30, W - 60, H - 60);
    ctx.lineWidth = 2;
    ctx.strokeStyle = subtle;
    ctx.strokeRect(44, 44, W - 88, H - 88);

    // Corner ornaments
    drawCorner(ctx, 30, 30, gold, 1, 1);
    drawCorner(ctx, W - 30, 30, gold, -1, 1);
    drawCorner(ctx, 30, H - 30, gold, 1, -1);
    drawCorner(ctx, W - 30, H - 30, gold, -1, -1);

    // Header bar
    ctx.fillStyle = navy;
    ctx.fillRect(0, 0, W, 100);

    // Company name
    ctx.fillStyle = '#FFFFFF';
    ctx.font = 'bold 24px Georgia, serif';
    ctx.textAlign = 'center';
    ctx.fillText('БЮРО ПЕРЕВОДОВ «РЕМАРКА»  ·  MOSCOWTRANS.RU', W / 2, 62);

    // СЕРТИФИКАТ title
    ctx.fillStyle = gold;
    ctx.font = 'bold 52px Georgia, serif';
    ctx.fillText('СЕРТИФИКАТ', W / 2, 200);

    // Subtitle
    ctx.fillStyle = text;
    ctx.font = '22px Georgia, serif';
    ctx.fillText('о прохождении профессионального теста', W / 2, 240);

    // Name
    ctx.fillStyle = navy;
    ctx.font = 'bold 48px Georgia, serif';
    ctx.fillText(name || 'Переводчик', W / 2, 330);

    // Divider
    ctx.strokeStyle = gold;
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(160, 360); ctx.lineTo(W - 160, 360);
    ctx.stroke();

    // Topic & level
    ctx.fillStyle = text;
    ctx.font = '28px Georgia, serif';
    ctx.fillText(`${TOPIC_NAMES[topic] || topic}  ·  ${LEVEL_NAMES[level] || level}`, W / 2, 420);

    // Lang pair
    ctx.font = '20px Georgia, serif';
    ctx.fillStyle = dark ? '#A084E8' : '#1A3C6E';
    ctx.fillText(LANG_NAMES[lang] || lang, W / 2, 460);

    // Score
    ctx.fillStyle = gold;
    ctx.font = 'bold 72px Georgia, serif';
    ctx.fillText(`${scorePct}%`, W / 2, 570);

    ctx.fillStyle = text;
    ctx.font = '18px Georgia, serif';
    ctx.fillText('результат теста', W / 2, 600);

    // Date & cert id
    const date = new Date().toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
    ctx.font = '16px monospace';
    ctx.textAlign = 'left';
    ctx.fillStyle = subtle;
    ctx.fillText(date, 80, H - 70);
    ctx.textAlign = 'right';
    ctx.fillText(certId || '', W - 80, H - 70);

    // Verify URL
    ctx.textAlign = 'center';
    ctx.fillStyle = dark ? '#A084E8' : '#1A3C6E';
    ctx.font = '14px monospace';
    ctx.fillText(`moscowtrans.ru/verify/${certId}`, W / 2, H - 70);

    // QR placeholder (simple square)
    ctx.strokeStyle = gold;
    ctx.lineWidth = 2;
    ctx.strokeRect(W / 2 - 30, H - 130, 60, 60);
    ctx.fillStyle = gold;
    ctx.font = '10px sans-serif';
    ctx.fillText('QR', W / 2, H - 94);
  }

  function drawCorner(ctx, x, y, color, sx, sy) {
    ctx.strokeStyle = color;
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.moveTo(x + sx * 60, y);
    ctx.lineTo(x, y);
    ctx.lineTo(x, y + sy * 60);
    ctx.stroke();
  }

  function handleDownloadPng() {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const a = document.createElement('a');
    a.download = `certificate-${certId}.png`;
    a.href = canvas.toDataURL('image/png');
    a.click();
    onDownloadPng?.();
  }

  async function handleDownloadPdf() {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const { jsPDF } = await import('jspdf');
    const pdf = new jsPDF({ orientation: 'landscape', unit: 'px', format: [1200, 840] });
    pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, 1200, 840);
    pdf.save(`certificate-${certId}.pdf`);
    onDownloadPdf?.();
  }

  return (
    <div className="rtap-cert-wrap">
      <canvas ref={canvasRef} style={{ display: 'block' }} />
      <div className="flex gap-3 justify-center mt-4 flex-wrap">
        <button className="rtap-btn rtap-btn--primary" onClick={handleDownloadPng}>
          ⬇ Скачать PNG
        </button>
        <button className="rtap-btn rtap-btn--outline" onClick={handleDownloadPdf}>
          ⬇ Скачать PDF
        </button>
      </div>
    </div>
  );
}
