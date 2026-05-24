import React from 'react';
import { createRoot } from 'react-dom/client';
import './styles/themes.css';
import './styles/quiz.css';
import App from './App';
import QuestionOfWeek from './components/QuestionOfWeek';
import VerifyCert from './components/VerifyCert';
import { detectTheme } from './hooks/useTheme';

function applyTheme(el) {
  el.setAttribute('data-rtap-theme', detectTheme());
  const obs = new MutationObserver(() => el.setAttribute('data-rtap-theme', detectTheme()));
  obs.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme', 'class', 'style'] });
  obs.observe(document.body, { attributes: true, attributeFilter: ['data-theme', 'class', 'style'] });
}

// Mount quiz
const quizEl = document.getElementById('rtap-root');
if (quizEl) {
  const topic = quizEl.dataset.topic || '';
  const lang  = quizEl.dataset.lang  || 'en';
  createRoot(quizEl).render(<App defaultTopic={topic} defaultLang={lang} />);
}

// Mount QoW widget
const qowEl = document.getElementById('rtap-qow-root');
if (qowEl) {
  applyTheme(qowEl);
  createRoot(qowEl).render(<QuestionOfWeek />);
}

// Mount certificate verifier
const verEl = document.getElementById('rtap-verify-root');
if (verEl) {
  applyTheme(verEl);
  const certId = verEl.dataset.certId || new URLSearchParams(window.location.search).get('cert_id') || '';
  createRoot(verEl).render(<VerifyCert certId={certId} />);
}
