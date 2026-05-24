import { useState, useEffect } from 'react';

const LS_KEY = 'rtap_candidate_submitted';

const LANG_PAIR_OPTIONS = [
  { value: 'en-ru', label: 'EN↔RU' },
  { value: 'de-ru', label: 'DE↔RU' },
  { value: 'fr-ru', label: 'FR↔RU' },
  { value: 'zh-ru', label: 'ZH↔RU' },
  { value: 'es-ru', label: 'ES↔RU' },
  { value: 'ar-ru', label: 'AR↔RU' },
  { value: 'other', label: 'Другие' },
];

const TOPIC_OPTIONS = [
  { value: 'technical', label: 'Технический' },
  { value: 'legal',     label: 'Юридический' },
  { value: 'medical',   label: 'Медицинский' },
  { value: 'it',        label: 'IT/Программное обеспечение' },
];

const EXPERIENCE_OPTIONS = [
  { value: '0-1', label: 'Менее 1 года' },
  { value: '1-3', label: '1–3 года' },
  { value: '3-7', label: '3–7 лет' },
  { value: '7+',  label: 'Более 7 лет' },
];

const EMPLOYMENT_OPTIONS = [
  { value: 'full',     label: 'Полная занятость' },
  { value: 'part',     label: 'Частичная занятость' },
  { value: 'project',  label: 'Проектная работа' },
];

// ─── Checkbox group helper ────────────────────────────────────────────────────
function CheckboxGroup({ options, value, onChange, disabled }) {
  function toggle(v) {
    if (disabled) return;
    onChange(
      value.includes(v) ? value.filter((x) => x !== v) : [...value, v]
    );
  }

  return (
    <div className="flex flex-wrap gap-2">
      {options.map((opt) => {
        const checked = value.includes(opt.value);
        return (
          <label
            key={opt.value}
            className="rtap-checkbox-label"
            style={{
              display: 'inline-flex',
              alignItems: 'center',
              gap: '6px',
              padding: '6px 12px',
              borderRadius: '8px',
              border: `2px solid ${checked ? 'var(--rtap-accent)' : 'var(--rtap-border)'}`,
              background: checked
                ? 'color-mix(in srgb, var(--rtap-accent) 10%, transparent)'
                : 'var(--rtap-bg)',
              cursor: disabled ? 'default' : 'pointer',
              fontSize: '14px',
              fontWeight: checked ? 600 : 400,
              color: 'var(--rtap-text)',
              userSelect: 'none',
              transition: 'border-color 0.15s, background 0.15s',
            }}
          >
            <input
              type="checkbox"
              checked={checked}
              onChange={() => toggle(opt.value)}
              disabled={disabled}
              style={{ display: 'none' }}
            />
            {checked && (
              <span style={{ color: 'var(--rtap-accent)', fontSize: '12px' }}>✓</span>
            )}
            {opt.label}
          </label>
        );
      })}
    </div>
  );
}

// ─── Radio group helper ───────────────────────────────────────────────────────
function RadioGroup({ options, value, onChange, disabled }) {
  return (
    <div className="flex flex-col gap-2">
      {options.map((opt) => {
        const selected = value === opt.value;
        return (
          <label
            key={opt.value}
            className="rtap-option"
            style={{
              cursor: disabled ? 'default' : 'pointer',
              borderColor: selected ? 'var(--rtap-accent)' : 'var(--rtap-border)',
              background: selected
                ? 'color-mix(in srgb, var(--rtap-accent) 10%, transparent)'
                : 'var(--rtap-bg)',
            }}
          >
            <input
              type="radio"
              name="employment"
              value={opt.value}
              checked={selected}
              onChange={() => !disabled && onChange(opt.value)}
              disabled={disabled}
              style={{ display: 'none' }}
            />
            <span
              className="rtap-option__letter flex-shrink-0"
              style={{
                borderColor: selected ? 'var(--rtap-accent)' : undefined,
                background: selected ? 'var(--rtap-accent)' : undefined,
                color: selected ? '#fff' : undefined,
                fontSize: '10px',
              }}
            >
              {selected ? '●' : '○'}
            </span>
            <span style={{ fontSize: '14px', color: 'var(--rtap-text)' }}>
              {opt.label}
            </span>
          </label>
        );
      })}
    </div>
  );
}

// ─── Field wrapper ────────────────────────────────────────────────────────────
function Field({ label, required, children }) {
  return (
    <div className="mb-4">
      <label
        className="block text-sm font-semibold mb-2"
        style={{ color: 'var(--rtap-text)' }}
      >
        {label}
        {required && (
          <span style={{ color: 'var(--rtap-red)', marginLeft: '4px' }}>*</span>
        )}
      </label>
      {children}
    </div>
  );
}

function InputField({ type = 'text', value, onChange, placeholder, disabled, required }) {
  return (
    <input
      type={type}
      value={value}
      onChange={(e) => onChange(e.target.value)}
      placeholder={placeholder}
      disabled={disabled}
      required={required}
      className="w-full"
      style={{
        padding: '10px 14px',
        borderRadius: '8px',
        border: '2px solid var(--rtap-border)',
        background: 'var(--rtap-bg)',
        color: 'var(--rtap-text)',
        fontSize: '14px',
        outline: 'none',
        transition: 'border-color 0.15s',
      }}
      onFocus={(e) => (e.target.style.borderColor = 'var(--rtap-accent)')}
      onBlur={(e) => (e.target.style.borderColor = 'var(--rtap-border)')}
    />
  );
}

function SelectField({ value, onChange, options, disabled }) {
  return (
    <select
      value={value}
      onChange={(e) => onChange(e.target.value)}
      disabled={disabled}
      style={{
        width: '100%',
        padding: '10px 14px',
        borderRadius: '8px',
        border: '2px solid var(--rtap-border)',
        background: 'var(--rtap-bg)',
        color: 'var(--rtap-text)',
        fontSize: '14px',
        outline: 'none',
        cursor: disabled ? 'default' : 'pointer',
      }}
    >
      <option value="">— Выберите —</option>
      {options.map((opt) => (
        <option key={opt.value} value={opt.value}>
          {opt.label}
        </option>
      ))}
    </select>
  );
}

function TextareaField({ value, onChange, placeholder, disabled }) {
  return (
    <textarea
      value={value}
      onChange={(e) => onChange(e.target.value)}
      placeholder={placeholder}
      disabled={disabled}
      rows={4}
      style={{
        width: '100%',
        padding: '10px 14px',
        borderRadius: '8px',
        border: '2px solid var(--rtap-border)',
        background: 'var(--rtap-bg)',
        color: 'var(--rtap-text)',
        fontSize: '14px',
        outline: 'none',
        resize: 'vertical',
        transition: 'border-color 0.15s',
        fontFamily: 'inherit',
      }}
      onFocus={(e) => (e.target.style.borderColor = 'var(--rtap-accent)')}
      onBlur={(e) => (e.target.style.borderColor = 'var(--rtap-border)')}
    />
  );
}

// ─── Main CandidateForm ───────────────────────────────────────────────────────
export default function CandidateForm({
  topic,
  level,
  lang,
  scorePct,
  certId,
  onSubmit,
  onSkip,
}) {
  const [screen, setScreen]         = useState(1); // 1 or 2
  const [loading, setLoading]       = useState(false);
  const [success, setSuccess]       = useState(false);
  const [error, setError]           = useState('');
  const [alreadySubmitted, setAlreadySubmitted] = useState(false);

  // Screen 1 fields
  const [name, setName]             = useState('');
  const [email, setEmail]           = useState('');
  const [phone, setPhone]           = useState('');
  const [langPairs, setLangPairs]   = useState([]);
  const [topics, setTopics]         = useState([]);
  const [experience, setExperience] = useState('');
  const [employment, setEmployment] = useState('');

  // Screen 2 fields
  const [portfolio, setPortfolio]   = useState('');
  const [rateRange, setRateRange]   = useState('');
  const [comments, setComments]     = useState('');

  useEffect(() => {
    try {
      const submitted = JSON.parse(localStorage.getItem(LS_KEY) || 'false');
      setAlreadySubmitted(!!submitted);
    } catch {
      setAlreadySubmitted(false);
    }
  }, []);

  // Only show when scorePct >= 70 and not submitted
  if (scorePct < 70 || alreadySubmitted) return null;

  function validateScreen1() {
    if (!name.trim())       return 'Введите ваше имя';
    if (!email.trim())      return 'Введите email';
    if (!/\S+@\S+\.\S+/.test(email)) return 'Некорректный email';
    if (langPairs.length === 0) return 'Выберите хотя бы одну языковую пару';
    if (topics.length === 0)    return 'Выберите хотя бы одну специализацию';
    if (!experience)        return 'Укажите опыт работы';
    if (!employment)        return 'Укажите тип занятости';
    return '';
  }

  function handleScreen1Next() {
    const err = validateScreen1();
    if (err) { setError(err); return; }
    setError('');
    setScreen(2);
  }

  async function handleSubmit(skipOptional = false) {
    const err = validateScreen1();
    if (err) { setError(err); setScreen(1); return; }
    setError('');
    setLoading(true);

    const payload = {
      name:       name.trim(),
      email:      email.trim(),
      phone:      phone.trim() || undefined,
      lang_pairs: langPairs,
      topics,
      experience,
      employment,
      portfolio:  skipOptional ? undefined : portfolio.trim() || undefined,
      rate_range: skipOptional ? undefined : rateRange.trim() || undefined,
      comments:   skipOptional ? undefined : comments.trim() || undefined,
      topic,
      level,
      lang,
      score_pct:  scorePct,
      cert_id:    certId,
    };

    try {
      const apiBase =
        (typeof window !== 'undefined' && window.rtapConfig?.apiBase) ||
        '/wp-json/rtap/v1';

      const res = await fetch(`${apiBase}/candidate`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(payload),
      });

      if (!res.ok) {
        const data = await res.json().catch(() => ({}));
        throw new Error(data?.message || `Ошибка сервера: ${res.status}`);
      }

      localStorage.setItem(LS_KEY, 'true');
      setSuccess(true);
      onSubmit?.(payload);
    } catch (e) {
      setError(e.message || 'Произошла ошибка. Попробуйте позже.');
    } finally {
      setLoading(false);
    }
  }

  // ── Success screen ──────────────────────────────────────────────────────────
  if (success) {
    return (
      <div className="rtap-card text-center">
        <div style={{ fontSize: '48px', marginBottom: '16px' }}>🎉</div>
        <h3
          className="text-lg font-bold mb-2"
          style={{ color: 'var(--rtap-green)' }}
        >
          Анкета отправлена!
        </h3>
        <p className="text-sm" style={{ color: 'var(--rtap-text)', opacity: 0.75 }}>
          Мы свяжемся с вами в ближайшее время по адресу <strong>{email}</strong>
        </p>
      </div>
    );
  }

  // ── Screen 1 ──────────────────────────────────────────────────────────────
  if (screen === 1) {
    return (
      <div className="rtap-card">
        <div className="flex items-center justify-between mb-5">
          <h3
            className="text-base font-bold"
            style={{ color: 'var(--rtap-text)' }}
          >
            Анкета соискателя
          </h3>
          <span className="text-xs rtap-hint-text">Шаг 1 из 2</span>
        </div>

        <p className="text-sm mb-5" style={{ color: 'var(--rtap-text)', opacity: 0.75 }}>
          Вы успешно прошли тест! Хотите, чтобы наши клиенты могли найти вас как переводчика?
          Заполните анкету — это займёт 2 минуты.
        </p>

        {error && (
          <div
            className="text-sm mb-4 p-3 rounded-lg"
            style={{
              background: 'color-mix(in srgb, var(--rtap-red) 10%, transparent)',
              color: 'var(--rtap-red)',
              border: '1px solid var(--rtap-red)',
            }}
          >
            {error}
          </div>
        )}

        <Field label="Имя и фамилия" required>
          <InputField
            value={name}
            onChange={setName}
            placeholder="Иван Иванов"
            disabled={loading}
            required
          />
        </Field>

        <Field label="Email" required>
          <InputField
            type="email"
            value={email}
            onChange={setEmail}
            placeholder="ivan@example.com"
            disabled={loading}
            required
          />
        </Field>

        <Field label="Телефон">
          <InputField
            type="tel"
            value={phone}
            onChange={setPhone}
            placeholder="+7 (999) 000-00-00"
            disabled={loading}
          />
        </Field>

        <Field label="Языковые пары" required>
          <CheckboxGroup
            options={LANG_PAIR_OPTIONS}
            value={langPairs}
            onChange={setLangPairs}
            disabled={loading}
          />
        </Field>

        <Field label="Специализация" required>
          <CheckboxGroup
            options={TOPIC_OPTIONS}
            value={topics}
            onChange={setTopics}
            disabled={loading}
          />
        </Field>

        <Field label="Опыт работы" required>
          <SelectField
            value={experience}
            onChange={setExperience}
            options={EXPERIENCE_OPTIONS}
            disabled={loading}
          />
        </Field>

        <Field label="Занятость" required>
          <RadioGroup
            options={EMPLOYMENT_OPTIONS}
            value={employment}
            onChange={setEmployment}
            disabled={loading}
          />
        </Field>

        <div className="flex gap-3 mt-5">
          <button
            className="rtap-btn rtap-btn--primary flex-1"
            onClick={handleScreen1Next}
            disabled={loading}
          >
            Далее →
          </button>
          <button
            className="rtap-btn rtap-btn--outline"
            onClick={onSkip}
            disabled={loading}
          >
            Пропустить
          </button>
        </div>
      </div>
    );
  }

  // ── Screen 2 ──────────────────────────────────────────────────────────────
  return (
    <div className="rtap-card">
      <div className="flex items-center justify-between mb-5">
        <h3
          className="text-base font-bold"
          style={{ color: 'var(--rtap-text)' }}
        >
          Дополнительно
        </h3>
        <span className="text-xs rtap-hint-text">Шаг 2 из 2</span>
      </div>

      <p className="text-sm mb-5" style={{ color: 'var(--rtap-text)', opacity: 0.75 }}>
        Эти поля необязательны — они помогут нам лучше познакомиться с вами.
      </p>

      {error && (
        <div
          className="text-sm mb-4 p-3 rounded-lg"
          style={{
            background: 'color-mix(in srgb, var(--rtap-red) 10%, transparent)',
            color: 'var(--rtap-red)',
            border: '1px solid var(--rtap-red)',
          }}
        >
          {error}
        </div>
      )}

      <Field label="Ссылка на портфолио">
        <InputField
          type="url"
          value={portfolio}
          onChange={setPortfolio}
          placeholder="https://my-portfolio.ru"
          disabled={loading}
        />
      </Field>

      <Field label="Желаемый диапазон ставки">
        <InputField
          value={rateRange}
          onChange={setRateRange}
          placeholder="Например: 500–800 руб. за стр."
          disabled={loading}
        />
      </Field>

      <Field label="Комментарии">
        <TextareaField
          value={comments}
          onChange={setComments}
          placeholder="Расскажите о себе, специализации, условиях сотрудничества…"
          disabled={loading}
        />
      </Field>

      <div className="flex gap-3 mt-5">
        <button
          className="rtap-btn rtap-btn--primary flex-1"
          onClick={() => handleSubmit(false)}
          disabled={loading}
        >
          {loading ? 'Отправка…' : 'Отправить анкету'}
        </button>
        <button
          className="rtap-btn rtap-btn--outline"
          onClick={() => handleSubmit(true)}
          disabled={loading}
        >
          Пропустить
        </button>
      </div>

      <button
        className="rtap-btn rtap-btn--outline w-full mt-2 text-sm"
        style={{ fontSize: '13px', padding: '8px' }}
        onClick={() => setScreen(1)}
        disabled={loading}
      >
        ← Назад
      </button>
    </div>
  );
}
