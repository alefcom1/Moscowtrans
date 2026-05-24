import { useCallback } from 'react';

const KEY = 'rtap_progress';

function load() {
  try { return JSON.parse(localStorage.getItem(KEY) || '{}'); } catch { return {}; }
}

function save(data) {
  try { localStorage.setItem(KEY, JSON.stringify(data)); } catch {}
}

export function useProgress() {
  const getScore = useCallback((topic, level, lang = 'en') => {
    return load()[`${topic}_${level}_${lang}`] ?? null;
  }, []);

  const setScore = useCallback((topic, level, score_pct, lang = 'en') => {
    const d = load();
    const key = `${topic}_${level}_${lang}`;
    if ((d[key] ?? -1) < score_pct) { d[key] = score_pct; save(d); }
  }, []);

  const isUnlocked = useCallback((topic, level, lang = 'en') => {
    if (level === 'beginner') return true;
    const d = load();
    if (level === 'intermediate') return (d[`${topic}_beginner_${lang}`] ?? 0) >= 60;
    if (level === 'advanced')     return (d[`${topic}_intermediate_${lang}`] ?? 0) >= 70;
    return false;
  }, []);

  const getCooldown = useCallback((topic, level, lang = 'en') => {
    const d = load();
    const ts = d[`${topic}_${level}_${lang}_last`];
    if (!ts) return 0;
    const wait = 24 * 60 * 60 * 1000;
    const remaining = ts + wait - Date.now();
    return remaining > 0 ? remaining : 0;
  }, []);

  const recordAttempt = useCallback((topic, level, lang = 'en') => {
    const d = load();
    d[`${topic}_${level}_${lang}_last`] = Date.now();
    save(d);
  }, []);

  return { getScore, setScore, isUnlocked, getCooldown, recordAttempt };
}
