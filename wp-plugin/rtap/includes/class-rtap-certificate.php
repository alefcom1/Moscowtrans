<?php
defined('ABSPATH') || exit;

class RTAP_Certificate {

    private static array $topic_abbr = [
        'technical' => 'TECH',
        'legal'     => 'LEG',
        'medical'   => 'MED',
        'it'        => 'IT',
    ];

    private static array $level_abbr = [
        'beginner'     => 'BEG',
        'intermediate' => 'INT',
        'advanced'     => 'ADV',
    ];

    public static function generate_id(string $topic, string $level): string {
        global $wpdb;
        $t   = self::$topic_abbr[$topic] ?? strtoupper(substr($topic, 0, 4));
        $l   = self::$level_abbr[$level] ?? strtoupper(substr($level, 0, 3));
        $yr  = date('Y');
        $num = (int)$wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}rtap_certificates WHERE YEAR(issued_at)=$yr"
        ) + 1;
        return sprintf('CERT-%s-%s-%s-%05d', $t, $l, $yr, $num);
    }
}
