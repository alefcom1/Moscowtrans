import { useState, useEffect, useCallback } from 'react';
import {
  DndContext,
  closestCenter,
  PointerSensor,
  useSensor,
  useSensors,
  DragOverlay,
  useDroppable,
  useDraggable,
} from '@dnd-kit/core';

// ─── Draggable left item ────────────────────────────────────────────────────
function DraggableItem({ id, label, paired, disabled }) {
  const { attributes, listeners, setNodeRef, isDragging } = useDraggable({ id, disabled });

  return (
    <div
      ref={setNodeRef}
      {...listeners}
      {...attributes}
      className={`rtap-drag-item${paired ? ' rtap-drag-item--paired' : ''}`}
      style={{
        cursor: disabled ? 'default' : isDragging ? 'grabbing' : 'grab',
        opacity: isDragging ? 0.4 : 1,
        borderColor: paired ? 'var(--rtap-accent)' : undefined,
        background: paired ? 'color-mix(in srgb, var(--rtap-accent) 8%, transparent)' : undefined,
      }}
    >
      {label}
    </div>
  );
}

// ─── Droppable right slot ───────────────────────────────────────────────────
function DroppableSlot({ id, label, matchedLabel, resultState, disabled }) {
  const { setNodeRef, isOver } = useDroppable({ id, disabled });

  let borderColor = isOver ? 'var(--rtap-accent)' : 'var(--rtap-border)';
  let bg = isOver ? 'color-mix(in srgb, var(--rtap-accent) 10%, transparent)' : 'var(--rtap-bg)';

  if (resultState === 'correct') {
    borderColor = 'var(--rtap-green)';
    bg = 'color-mix(in srgb, var(--rtap-green) 12%, transparent)';
  }
  if (resultState === 'wrong') {
    borderColor = 'var(--rtap-red)';
    bg = 'color-mix(in srgb, var(--rtap-red) 12%, transparent)';
  }

  return (
    <div
      ref={setNodeRef}
      className={`rtap-drag-item${isOver ? ' rtap-drag-item--over' : ''}`}
      style={{
        minHeight: '44px',
        borderColor,
        background: bg,
        display: 'flex',
        flexDirection: 'column',
        gap: 4,
      }}
    >
      <span
        className="text-xs font-semibold"
        style={{ color: 'var(--rtap-text)', opacity: 0.6 }}
      >
        {label}
      </span>
      {matchedLabel && (
        <span
          className="text-sm font-medium"
          style={{
            color:
              resultState === 'correct'
                ? 'var(--rtap-green)'
                : resultState === 'wrong'
                ? 'var(--rtap-red)'
                : 'var(--rtap-accent)',
          }}
        >
          {matchedLabel}
        </span>
      )}
    </div>
  );
}

// ─── Main component ─────────────────────────────────────────────────────────
export default function QuestionTM({ question, payload, onAnswer, disabled, result }) {
  const { left = [], right = [], pairs: correctPairs = [] } = payload || {};

  // userPairs: array of { leftIdx, rightIdx }
  const [userPairs, setUserPairs] = useState([]);
  const [activeId, setActiveId] = useState(null);

  // Mobile touch: tap left first, then right
  const [mobileSelected, setMobileSelected] = useState(null);
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    setIsMobile('ontouchstart' in window || navigator.maxTouchPoints > 0);
  }, []);

  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { distance: 8 } })
  );

  const pairForRight = useCallback(
    (rightIdx) => userPairs.find((p) => p.rightIdx === rightIdx),
    [userPairs]
  );
  const pairForLeft = useCallback(
    (leftIdx) => userPairs.find((p) => p.leftIdx === leftIdx),
    [userPairs]
  );

  function getResultState(rightIdx) {
    if (!result) return null;
    const matched = pairForRight(rightIdx);
    if (!matched) return null;
    const isCorrect = correctPairs.some(
      ([cl, cr]) => cl === matched.leftIdx && cr === rightIdx
    );
    return isCorrect ? 'correct' : 'wrong';
  }

  function handleDragStart({ active }) {
    setActiveId(active.id);
  }

  function handleDragEnd({ active, over }) {
    setActiveId(null);
    if (!over) return;

    const leftIdx = parseInt(active.id.replace('left-', ''), 10);
    const rightIdx = parseInt(over.id.replace('right-', ''), 10);

    if (isNaN(leftIdx) || isNaN(rightIdx)) return;

    setUserPairs((prev) => {
      const filtered = prev.filter(
        (p) => p.leftIdx !== leftIdx && p.rightIdx !== rightIdx
      );
      const next = [...filtered, { leftIdx, rightIdx }];
      if (next.length === left.length) {
        onAnswer(next.map((p) => [p.leftIdx, p.rightIdx]));
      }
      return next;
    });
  }

  // Mobile tap handler
  function handleMobileLeft(idx) {
    if (disabled || result) return;
    setMobileSelected((prev) =>
      prev?.type === 'left' && prev.idx === idx ? null : { type: 'left', idx }
    );
  }

  function handleMobileRight(idx) {
    if (disabled || result) return;
    if (mobileSelected?.type === 'left') {
      const leftIdx = mobileSelected.idx;
      const rightIdx = idx;
      setUserPairs((prev) => {
        const filtered = prev.filter(
          (p) => p.leftIdx !== leftIdx && p.rightIdx !== rightIdx
        );
        const next = [...filtered, { leftIdx, rightIdx }];
        if (next.length === left.length) {
          onAnswer(next.map((p) => [p.leftIdx, p.rightIdx]));
        }
        return next;
      });
      setMobileSelected(null);
    }
  }

  const draggingLabel = activeId
    ? left[parseInt(activeId.replace('left-', ''), 10)]
    : null;

  if (isMobile) {
    // ── Mobile layout: tap-to-match ──
    return (
      <div className="rtap-question-enter">
        <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>
        {mobileSelected?.type === 'left' && !result && (
          <p className="text-sm mb-3 rtap-selection-hint">
            Выбрано: «{left[mobileSelected.idx]}» — теперь нажмите на правую колонку
          </p>
        )}
        <div className="grid grid-cols-2 gap-3">
          <div className="flex flex-col gap-2">
            <span
              className="text-xs font-semibold uppercase tracking-wide mb-1"
              style={{ color: 'var(--rtap-text)', opacity: 0.6 }}
            >
              Термин
            </span>
            {left.map((item, idx) => {
              const paired = pairForLeft(idx);
              const isSelected =
                mobileSelected?.type === 'left' && mobileSelected.idx === idx;
              return (
                <button
                  key={idx}
                  className="rtap-drag-item text-left text-sm"
                  style={{
                    cursor: disabled || result ? 'default' : 'pointer',
                    borderColor: isSelected
                      ? 'var(--rtap-accent)'
                      : paired
                      ? 'var(--rtap-accent)'
                      : undefined,
                    background: isSelected
                      ? 'color-mix(in srgb, var(--rtap-accent) 15%, transparent)'
                      : paired
                      ? 'color-mix(in srgb, var(--rtap-accent) 8%, transparent)'
                      : undefined,
                  }}
                  onClick={() => handleMobileLeft(idx)}
                  disabled={disabled || !!result}
                >
                  {item}
                </button>
              );
            })}
          </div>
          <div className="flex flex-col gap-2">
            <span
              className="text-xs font-semibold uppercase tracking-wide mb-1"
              style={{ color: 'var(--rtap-text)', opacity: 0.6 }}
            >
              Значение
            </span>
            {right.map((item, idx) => {
              const matched = pairForRight(idx);
              const rs = getResultState(idx);
              let borderColor = 'var(--rtap-border)';
              let bg = 'var(--rtap-bg)';
              if (rs === 'correct') {
                borderColor = 'var(--rtap-green)';
                bg = 'color-mix(in srgb, var(--rtap-green) 12%, transparent)';
              }
              if (rs === 'wrong') {
                borderColor = 'var(--rtap-red)';
                bg = 'color-mix(in srgb, var(--rtap-red) 12%, transparent)';
              }
              if (!rs && matched) {
                borderColor = 'var(--rtap-accent)';
                bg = 'color-mix(in srgb, var(--rtap-accent) 8%, transparent)';
              }
              const isTarget = mobileSelected?.type === 'left';

              return (
                <button
                  key={idx}
                  className="rtap-drag-item text-left text-sm flex flex-col gap-1"
                  style={{
                    cursor:
                      disabled || result ? 'default' : isTarget ? 'pointer' : 'default',
                    borderColor,
                    background: bg,
                    minHeight: '44px',
                  }}
                  onClick={() => handleMobileRight(idx)}
                  disabled={disabled || !!result}
                >
                  <span style={{ color: 'var(--rtap-text)', opacity: 0.65, fontSize: '11px' }}>
                    {item}
                  </span>
                  {matched && (
                    <span
                      style={{
                        fontWeight: 600,
                        fontSize: '13px',
                        color:
                          rs === 'correct'
                            ? 'var(--rtap-green)'
                            : rs === 'wrong'
                            ? 'var(--rtap-red)'
                            : 'var(--rtap-accent)',
                      }}
                    >
                      ← {left[matched.leftIdx]}
                    </span>
                  )}
                </button>
              );
            })}
          </div>
        </div>
      </div>
    );
  }

  // ── Desktop: drag-and-drop ──
  return (
    <div className="rtap-question-enter">
      <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>
      <p className="text-sm mb-4 rtap-hint-text">
        Перетащите термины из левой колонки к соответствующим значениям справа
      </p>
      <DndContext
        sensors={sensors}
        collisionDetection={closestCenter}
        onDragStart={handleDragStart}
        onDragEnd={handleDragEnd}
      >
        <div className="grid grid-cols-2 gap-4">
          <div className="flex flex-col gap-2">
            <span
              className="text-xs font-semibold uppercase tracking-wide mb-1"
              style={{ color: 'var(--rtap-text)', opacity: 0.6 }}
            >
              Термины
            </span>
            {left.map((item, idx) => (
              <DraggableItem
                key={idx}
                id={`left-${idx}`}
                label={item}
                paired={!!pairForLeft(idx)}
                disabled={!!disabled || !!result}
              />
            ))}
          </div>
          <div className="flex flex-col gap-2">
            <span
              className="text-xs font-semibold uppercase tracking-wide mb-1"
              style={{ color: 'var(--rtap-text)', opacity: 0.6 }}
            >
              Значения
            </span>
            {right.map((item, idx) => {
              const matched = pairForRight(idx);
              return (
                <DroppableSlot
                  key={idx}
                  id={`right-${idx}`}
                  label={item}
                  matchedLabel={matched ? left[matched.leftIdx] : null}
                  resultState={getResultState(idx)}
                  disabled={!!disabled || !!result}
                />
              );
            })}
          </div>
        </div>
        <DragOverlay>
          {draggingLabel ? (
            <div
              className="rtap-drag-item"
              style={{
                opacity: 0.9,
                boxShadow: '0 8px 24px var(--rtap-shadow)',
                cursor: 'grabbing',
              }}
            >
              {draggingLabel}
            </div>
          ) : null}
        </DragOverlay>
      </DndContext>
    </div>
  );
}
