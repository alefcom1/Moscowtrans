import { useState, useEffect, useRef, useCallback } from 'react';

export function useTimer(seconds, onExpire) {
  const [left, setLeft] = useState(seconds);
  const intervalRef = useRef(null);
  const onExpireRef = useRef(onExpire);

  useEffect(() => { onExpireRef.current = onExpire; }, [onExpire]);

  const start = useCallback(() => {
    setLeft(seconds);
    clearInterval(intervalRef.current);
    intervalRef.current = setInterval(() => {
      setLeft(prev => {
        if (prev <= 1) {
          clearInterval(intervalRef.current);
          onExpireRef.current?.();
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
  }, [seconds]);

  const stop = useCallback(() => clearInterval(intervalRef.current), []);
  const reset = useCallback(() => { stop(); setLeft(seconds); }, [stop, seconds]);

  useEffect(() => () => clearInterval(intervalRef.current), []);

  const color = left > 15 ? '#5ECB8F' : left > 7 ? '#F59E0B' : '#E85555';
  const dash  = 138.2 * (left / seconds);

  return { left, color, dash, start, stop, reset };
}
