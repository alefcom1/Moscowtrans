import { useState, useEffect } from 'react';
import {
  DndContext,
  closestCenter,
  PointerSensor,
  KeyboardSensor,
  useSensor,
  useSensors,
  DragOverlay,
} from '@dnd-kit/core';
import {
  SortableContext,
  sortableKeyboardCoordinates,
  verticalListSortingStrategy,
  useSortable,
  arrayMove,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

// ─── Sortable item for desktop ──────────────────────────────────────────────
function SortableSegment({ id, label, index, disabled }) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id, disabled });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.4 : 1,
    cursor: disabled ? 'default' : isDragging ? 'grabbing' : 'grab',
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      {...attributes}
      {...listeners}
      className="rtap-drag-item flex items-center gap-3"
    >
      <span
        className="rtap-option__letter flex-shrink-0"
        style={{ fontSize: '11px' }}
      >
        {index + 1}
      </span>
      <span className="text-sm">{label}</span>
    </div>
  );
}

// ─── Result segment display ─────────────────────────────────────────────────
function ResultSegment({ label, index, isCorrectPosition }) {
  const borderColor = isCorrectPosition ? 'var(--rtap-green)' : 'var(--rtap-red)';
  const bg = isCorrectPosition
    ? 'color-mix(in srgb, var(--rtap-green) 12%, transparent)'
    : 'color-mix(in srgb, var(--rtap-red) 12%, transparent)';

  return (
    <div
      className="rtap-drag-item flex items-center gap-3"
      style={{ borderColor, background: bg }}
    >
      <span
        className="rtap-option__letter flex-shrink-0"
        style={{ fontSize: '11px' }}
      >
        {index + 1}
      </span>
      <span className="text-sm">{label}</span>
    </div>
  );
}

// ─── Main component ─────────────────────────────────────────────────────────
export default function QuestionRO({ question, payload, onAnswer, disabled, result }) {
  const { segments = [], correct_order = [] } = payload || {};

  // order: array of original segment indices, representing current display order
  const [order, setOrder] = useState(() => segments.map((_, i) => i));
  const [activeId, setActiveId] = useState(null);

  // Mobile: tap to select, tap target position to swap
  const [mobileSelected, setMobileSelected] = useState(null);
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    setIsMobile('ontouchstart' in window || navigator.maxTouchPoints > 0);
  }, []);

  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { distance: 8 } }),
    useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates })
  );

  function handleDragStart({ active }) {
    setActiveId(active.id);
  }

  function handleDragEnd({ active, over }) {
    setActiveId(null);
    if (!over || active.id === over.id) return;

    setOrder((prev) => {
      const oldIndex = prev.indexOf(Number(active.id));
      const newIndex = prev.indexOf(Number(over.id));
      const next = arrayMove(prev, oldIndex, newIndex);
      onAnswer(next);
      return next;
    });
  }

  // Mobile tap: select a position, then tap target to swap
  function handleMobileTap(positionIndex) {
    if (disabled || result) return;

    if (mobileSelected === null) {
      setMobileSelected(positionIndex);
    } else {
      if (mobileSelected === positionIndex) {
        setMobileSelected(null);
        return;
      }
      setOrder((prev) => {
        const next = arrayMove(prev, mobileSelected, positionIndex);
        onAnswer(next);
        return next;
      });
      setMobileSelected(null);
    }
  }

  // Check if a position has the correct segment for result display
  function isPositionCorrect(posIdx) {
    if (!result || !correct_order.length) return false;
    return order[posIdx] === correct_order[posIdx];
  }

  const draggingSegment =
    activeId !== null ? segments[activeId] : null;

  // ── Result view ──
  if (result) {
    return (
      <div className="rtap-question-enter">
        <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>
        <p className="text-xs mb-3 rtap-hint-text">Ваш порядок:</p>
        <div className="flex flex-col gap-2">
          {order.map((segIdx, posIdx) => (
            <ResultSegment
              key={posIdx}
              label={segments[segIdx]}
              index={posIdx}
              isCorrectPosition={isPositionCorrect(posIdx)}
            />
          ))}
        </div>
        {correct_order.length > 0 && (
          <>
            <p className="text-xs mt-4 mb-3 rtap-hint-text">Правильный порядок:</p>
            <div className="flex flex-col gap-2">
              {correct_order.map((segIdx, posIdx) => (
                <div
                  key={posIdx}
                  className="rtap-drag-item flex items-center gap-3"
                  style={{
                    borderColor: 'var(--rtap-green)',
                    background: 'color-mix(in srgb, var(--rtap-green) 8%, transparent)',
                  }}
                >
                  <span
                    className="rtap-option__letter flex-shrink-0"
                    style={{ fontSize: '11px' }}
                  >
                    {posIdx + 1}
                  </span>
                  <span className="text-sm">{segments[segIdx]}</span>
                </div>
              ))}
            </div>
          </>
        )}
      </div>
    );
  }

  // ── Mobile: tap-to-select-and-move ──
  if (isMobile) {
    return (
      <div className="rtap-question-enter">
        <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>
        {mobileSelected !== null ? (
          <p className="text-sm mb-3 rtap-selection-hint">
            Выбрана позиция {mobileSelected + 1}: «{segments[order[mobileSelected]]}» — нажмите другую позицию для перестановки
          </p>
        ) : (
          <p className="text-sm mb-3 rtap-hint-text">
            Нажмите на фрагмент, затем на позицию для его перемещения
          </p>
        )}
        <div className="flex flex-col gap-2">
          {order.map((segIdx, posIdx) => {
            const isSelected = mobileSelected === posIdx;
            return (
              <button
                key={posIdx}
                className="rtap-drag-item flex items-center gap-3 text-left w-full"
                style={{
                  cursor: disabled ? 'default' : 'pointer',
                  borderColor: isSelected ? 'var(--rtap-accent)' : undefined,
                  background: isSelected
                    ? 'color-mix(in srgb, var(--rtap-accent) 15%, transparent)'
                    : undefined,
                }}
                onClick={() => handleMobileTap(posIdx)}
                disabled={!!disabled}
              >
                <span
                  className="rtap-option__letter flex-shrink-0"
                  style={{ fontSize: '11px' }}
                >
                  {posIdx + 1}
                </span>
                <span className="text-sm">{segments[segIdx]}</span>
              </button>
            );
          })}
        </div>
        <button
          className="rtap-btn rtap-btn--primary mt-4 w-full"
          onClick={() => onAnswer(order)}
          disabled={!!disabled}
        >
          Подтвердить порядок
        </button>
      </div>
    );
  }

  // ── Desktop: drag-and-drop sortable ──
  return (
    <div className="rtap-question-enter">
      <p className="text-base font-semibold mb-4 rtap-question__text">{question}</p>
      <p className="text-sm mb-4 rtap-hint-text">
        Перетащите фрагменты в правильном порядке
      </p>
      <DndContext
        sensors={sensors}
        collisionDetection={closestCenter}
        onDragStart={handleDragStart}
        onDragEnd={handleDragEnd}
      >
        <SortableContext
          items={order}
          strategy={verticalListSortingStrategy}
        >
          <div className="flex flex-col gap-2">
            {order.map((segIdx, posIdx) => (
              <SortableSegment
                key={segIdx}
                id={segIdx}
                label={segments[segIdx]}
                index={posIdx}
                disabled={!!disabled}
              />
            ))}
          </div>
        </SortableContext>
        <DragOverlay>
          {draggingSegment ? (
            <div
              className="rtap-drag-item flex items-center gap-3"
              style={{
                opacity: 0.9,
                boxShadow: '0 8px 24px var(--rtap-shadow)',
                cursor: 'grabbing',
              }}
            >
              <span className="rtap-option__letter flex-shrink-0" style={{ fontSize: '11px' }}>
                ≡
              </span>
              <span className="text-sm">{draggingSegment}</span>
            </div>
          ) : null}
        </DragOverlay>
      </DndContext>
      <button
        className="rtap-btn rtap-btn--primary mt-4 w-full"
        onClick={() => onAnswer(order)}
        disabled={!!disabled}
      >
        Подтвердить порядок
      </button>
    </div>
  );
}
