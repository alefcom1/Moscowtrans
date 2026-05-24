<?php
defined('ABSPATH') || exit;

class RTAP_QOW {

    public static function set_question(int $question_id): bool {
        global $wpdb;

        // Deactivate current
        $wpdb->update(
            $wpdb->prefix . 'rtap_question_of_week',
            ['active' => 0],
            ['active' => 1]
        );

        $week_start = date('Y-m-d', strtotime('Monday this week'));

        $inserted = $wpdb->insert($wpdb->prefix . 'rtap_question_of_week', [
            'question_id' => $question_id,
            'week_start'  => $week_start,
            'stats_json'  => wp_json_encode([
                'total_answers'      => 0,
                'correct_pct'        => 0,
                'correct_count'      => 0,
                'option_distribution' => [],
            ]),
            'active'      => 1,
        ]);

        return (bool)$inserted;
    }

    public static function auto_rotate(): void {
        global $wpdb;

        $current = $wpdb->get_var(
            "SELECT week_start FROM {$wpdb->prefix}rtap_question_of_week WHERE active=1"
        );

        $this_monday = date('Y-m-d', strtotime('Monday this week'));
        if ($current === $this_monday) return; // already rotated

        // Pick random eligible question (MC, BT or FB)
        $q = $wpdb->get_row(
            "SELECT id FROM {$wpdb->prefix}rtap_questions
             WHERE type IN ('mc','bt','fb') AND active=1
             ORDER BY RAND() LIMIT 1"
        );

        if ($q) self::set_question($q->id);
    }
}
