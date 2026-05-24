<?php
defined('ABSPATH') || exit;

class RTAP_Stats {

    public static function dashboard(): array {
        global $wpdb;

        $week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));

        $attempts_week = (int)$wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}rtap_attempts WHERE created_at >= '$week_ago'"
        );

        $avg_score = (float)$wpdb->get_var(
            "SELECT AVG(score_pct) FROM {$wpdb->prefix}rtap_attempts"
        );

        $new_candidates = (int)$wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}rtap_candidates WHERE created_at >= '$week_ago'"
        );

        $by_topic = $wpdb->get_results(
            "SELECT topic, COUNT(*) as cnt FROM {$wpdb->prefix}rtap_attempts GROUP BY topic ORDER BY cnt DESC",
            ARRAY_A
        );

        return [
            'attempts_week'  => $attempts_week,
            'avg_score'      => round($avg_score),
            'new_candidates' => $new_candidates,
            'by_topic'       => $by_topic,
        ];
    }
}
