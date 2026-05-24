<?php
defined('ABSPATH') || exit;

class RTAP_DB {

    public static function install(): void {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta("
            CREATE TABLE {$wpdb->prefix}rtap_questions (
                id          INT AUTO_INCREMENT PRIMARY KEY,
                topic       ENUM('technical','legal','medical','it') NOT NULL,
                level       ENUM('beginner','intermediate','advanced') NOT NULL,
                type        ENUM('mc','bt','fe','tm','ro','fb') NOT NULL,
                lang        VARCHAR(10) NOT NULL DEFAULT 'en',
                question    TEXT NOT NULL,
                payload     JSON NOT NULL,
                explanation TEXT NOT NULL,
                difficulty  TINYINT DEFAULT 3,
                active      TINYINT DEFAULT 1,
                created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_topic_level_lang (topic, level, lang),
                INDEX idx_active (active)
            ) $charset;
        ");

        dbDelta("
            CREATE TABLE {$wpdb->prefix}rtap_attempts (
                id          INT AUTO_INCREMENT PRIMARY KEY,
                session_id  VARCHAR(64) NOT NULL,
                topic       VARCHAR(20) NOT NULL,
                level       VARCHAR(20) NOT NULL,
                lang        VARCHAR(10) NOT NULL DEFAULT 'en',
                score       TINYINT NOT NULL,
                score_pct   TINYINT NOT NULL,
                time_taken  SMALLINT,
                ip_hash     VARCHAR(64),
                answers_json JSON,
                created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_topic_level (topic, level),
                INDEX idx_session (session_id)
            ) $charset;
        ");

        dbDelta("
            CREATE TABLE {$wpdb->prefix}rtap_candidates (
                id            INT AUTO_INCREMENT PRIMARY KEY,
                name          VARCHAR(255),
                email         VARCHAR(255) NOT NULL,
                phone         VARCHAR(50),
                lang_pairs    JSON,
                topics        JSON,
                best_scores   JSON,
                certif_ids    JSON,
                tms_id        VARCHAR(100),
                tms_synced    TINYINT DEFAULT 0,
                questionnaire JSON,
                created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY idx_email (email)
            ) $charset;
        ");

        dbDelta("
            CREATE TABLE {$wpdb->prefix}rtap_certificates (
                id           VARCHAR(30) PRIMARY KEY,
                candidate_id INT,
                topic        VARCHAR(20),
                level        VARCHAR(20),
                lang         VARCHAR(10) DEFAULT 'en',
                score_pct    TINYINT,
                candidate_name VARCHAR(255),
                issued_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                verify_hash  VARCHAR(64)
            ) $charset;
        ");

        dbDelta("
            CREATE TABLE {$wpdb->prefix}rtap_question_of_week (
                id          INT AUTO_INCREMENT PRIMARY KEY,
                question_id INT NOT NULL,
                week_start  DATE NOT NULL,
                stats_json  JSON,
                active      TINYINT DEFAULT 1,
                INDEX idx_week (week_start),
                INDEX idx_active (active)
            ) $charset;
        ");

        update_option('rtap_db_version', RTAP_VERSION);
    }

    public static function deactivate(): void {
        wp_clear_scheduled_hook('rtap_sync_pending');
    }

    public static function get_questions(string $topic, string $level, string $lang = 'en', int $count = 10): array {
        global $wpdb;
        $table = $wpdb->prefix . 'rtap_questions';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT id, type, question, payload, difficulty
             FROM $table
             WHERE topic = %s AND level = %s AND lang = %s AND active = 1
             ORDER BY RAND()
             LIMIT %d",
            $topic, $level, $lang, $count
        ), ARRAY_A);
    }

    public static function save_attempt(array $data): int|false {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'rtap_attempts', [
            'session_id'   => $data['session_id'],
            'topic'        => $data['topic'],
            'level'        => $data['level'],
            'lang'         => $data['lang'] ?? 'en',
            'score'        => $data['score'],
            'score_pct'    => $data['score_pct'],
            'time_taken'   => $data['time_taken'] ?? null,
            'ip_hash'      => isset($data['ip']) ? hash('sha256', $data['ip'] . AUTH_SALT) : null,
            'answers_json' => wp_json_encode($data['answers'] ?? []),
        ]);
        return $wpdb->insert_id ?: false;
    }

    public static function get_stats(string $topic, string $level = '', string $lang = 'en'): array {
        global $wpdb;
        $table = $wpdb->prefix . 'rtap_attempts';
        $where = $wpdb->prepare('WHERE topic = %s AND lang = %s', $topic, $lang);
        if ($level) {
            $where .= $wpdb->prepare(' AND level = %s', $level);
        }

        $row = $wpdb->get_row("SELECT COUNT(*) as total, AVG(score_pct) as avg_score FROM $table $where");
        return [
            'total'     => (int)($row->total ?? 0),
            'avg_score' => round((float)($row->avg_score ?? 0)),
        ];
    }

    public static function get_percentile(string $topic, string $level, int $score_pct, string $lang = 'en'): int {
        global $wpdb;
        $table = $wpdb->prefix . 'rtap_attempts';
        $below = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE topic=%s AND level=%s AND lang=%s AND score_pct < %d",
            $topic, $level, $lang, $score_pct
        ));
        $total = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE topic=%s AND level=%s AND lang=%s",
            $topic, $level, $lang
        ));
        if ($total === 0) return 100;
        return (int)round($below / $total * 100);
    }

    public static function save_candidate(array $data): int|false {
        global $wpdb;
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}rtap_candidates WHERE email=%s",
            $data['email']
        ));

        if ($exists) {
            $wpdb->update(
                $wpdb->prefix . 'rtap_candidates',
                self::candidate_row($data),
                ['id' => $exists]
            );
            return (int)$exists;
        }

        $wpdb->insert($wpdb->prefix . 'rtap_candidates', self::candidate_row($data));
        return $wpdb->insert_id ?: false;
    }

    private static function candidate_row(array $d): array {
        return [
            'name'          => sanitize_text_field($d['name'] ?? ''),
            'email'         => sanitize_email($d['email']),
            'phone'         => sanitize_text_field($d['phone'] ?? ''),
            'lang_pairs'    => wp_json_encode($d['lang_pairs'] ?? []),
            'topics'        => wp_json_encode($d['topics'] ?? []),
            'best_scores'   => wp_json_encode($d['best_scores'] ?? []),
            'questionnaire' => wp_json_encode($d['questionnaire'] ?? []),
            'tms_synced'    => 0,
        ];
    }

    public static function save_certificate(array $data): bool {
        global $wpdb;
        $rows = $wpdb->insert($wpdb->prefix . 'rtap_certificates', [
            'id'             => $data['id'],
            'candidate_id'   => $data['candidate_id'] ?? null,
            'topic'          => $data['topic'],
            'level'          => $data['level'],
            'lang'           => $data['lang'] ?? 'en',
            'score_pct'      => $data['score_pct'],
            'candidate_name' => $data['name'] ?? '',
            'verify_hash'    => hash('sha256', $data['id'] . AUTH_SALT),
        ]);
        return (bool)$rows;
    }

    public static function get_certificate(string $cert_id): ?array {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}rtap_certificates WHERE id=%s",
            $cert_id
        ), ARRAY_A);
        return $row ?: null;
    }

    public static function get_active_qow(): ?array {
        global $wpdb;
        $row = $wpdb->get_row(
            "SELECT q.*, qow.id as qow_id, qow.stats_json, qow.week_start
             FROM {$wpdb->prefix}rtap_question_of_week qow
             JOIN {$wpdb->prefix}rtap_questions q ON q.id = qow.question_id
             WHERE qow.active = 1
             ORDER BY qow.week_start DESC
             LIMIT 1",
            ARRAY_A
        );
        return $row ?: null;
    }

    public static function record_qow_answer(int $qow_id, bool $correct, string $option): void {
        global $wpdb;
        $table = $wpdb->prefix . 'rtap_question_of_week';
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT stats_json FROM $table WHERE id=%d", $qow_id
        ));

        $stats = json_decode($row->stats_json ?? '{}', true) ?: [
            'total_answers' => 0,
            'correct_pct'   => 0,
            'correct_count' => 0,
            'option_distribution' => [],
        ];

        $stats['total_answers']++;
        if ($correct) $stats['correct_count']++;
        $stats['correct_pct'] = $stats['total_answers']
            ? round($stats['correct_count'] / $stats['total_answers'] * 100)
            : 0;
        $stats['option_distribution'][$option] = ($stats['option_distribution'][$option] ?? 0) + 1;

        $wpdb->update($table, ['stats_json' => wp_json_encode($stats)], ['id' => $qow_id]);
    }

    public static function get_unsync_candidates(): array {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}rtap_candidates WHERE tms_synced=0 LIMIT 50",
            ARRAY_A
        );
    }

    public static function mark_tms_synced(int $id, string $tms_id): void {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'rtap_candidates',
            ['tms_id' => $tms_id, 'tms_synced' => 1],
            ['id' => $id]
        );
    }

    public static function import_questions(array $questions): array {
        global $wpdb;
        $ok = 0; $err = 0;

        foreach ($questions as $q) {
            if (empty($q['topic']) || empty($q['level']) || empty($q['type'])) {
                $err++;
                continue;
            }

            $inserted = $wpdb->insert($wpdb->prefix . 'rtap_questions', [
                'topic'       => sanitize_key($q['topic']),
                'level'       => sanitize_key($q['level']),
                'type'        => sanitize_key($q['type']),
                'lang'        => sanitize_key($q['lang'] ?? 'en'),
                'question'    => sanitize_text_field($q['question'] ?? ''),
                'payload'     => wp_json_encode($q['payload'] ?? []),
                'explanation' => sanitize_textarea_field($q['explanation'] ?? ''),
                'difficulty'  => (int)($q['difficulty'] ?? 3),
                'active'      => 1,
            ]);

            $inserted ? $ok++ : $err++;
        }

        return ['imported' => $ok, 'errors' => $err];
    }
}
