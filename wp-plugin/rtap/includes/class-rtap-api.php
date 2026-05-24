<?php
defined('ABSPATH') || exit;

class RTAP_API {

    public static function register_routes(): void {
        $ns = 'rtap/v1';

        register_rest_route($ns, '/questions', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_questions'],
            'permission_callback' => '__return_true',
            'args'                => [
                'topic' => ['required' => true,  'sanitize_callback' => 'sanitize_key'],
                'level' => ['required' => true,  'sanitize_callback' => 'sanitize_key'],
                'lang'  => ['default'  => 'en',  'sanitize_callback' => 'sanitize_key'],
                'count' => ['default'  => 10,    'sanitize_callback' => 'absint'],
            ],
        ]);

        register_rest_route($ns, '/attempt', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'save_attempt'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($ns, '/stats/(?P<topic>[a-z]+)', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_stats'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($ns, '/candidate', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'save_candidate'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($ns, '/certificate', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'issue_certificate'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($ns, '/verify/(?P<cert_id>[A-Z0-9\-]+)', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'verify_certificate'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($ns, '/qow', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_qow'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($ns, '/qow/answer', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'answer_qow'],
            'permission_callback' => '__return_true',
        ]);

        // Admin endpoints
        register_rest_route($ns, '/admin/questions', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'admin_get_questions'],
            'permission_callback' => fn() => current_user_can('manage_options'),
        ]);

        register_rest_route($ns, '/admin/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'admin_import'],
            'permission_callback' => fn() => current_user_can('manage_options'),
        ]);
    }

    public static function get_questions(WP_REST_Request $req): WP_REST_Response {
        $topic = $req->get_param('topic');
        $level = $req->get_param('level');
        $lang  = $req->get_param('lang');
        $count = min((int)$req->get_param('count'), 20);

        $valid_topics = ['technical','legal','medical','it'];
        $valid_levels = ['beginner','intermediate','advanced'];

        if (!in_array($topic, $valid_topics) || !in_array($level, $valid_levels)) {
            return new WP_REST_Response(['error' => 'Invalid topic or level'], 400);
        }

        $questions = RTAP_DB::get_questions($topic, $level, $lang, $count);

        // Strip IDs from payload — sent back only on attempt submission
        $safe = array_map(fn($q) => [
            '_idx'     => $q['id'], // temporary index, not DB id
            'type'     => $q['type'],
            'question' => $q['question'],
            'payload'  => json_decode($q['payload'], true),
        ], $questions);

        // Build question ID map in session transient (10 min TTL)
        $session_key = 'rtap_qs_' . md5($topic . $level . $lang . uniqid());
        $id_map = array_column($questions, 'id');
        set_transient($session_key, $id_map, 600);

        return new WP_REST_Response([
            'questions'   => $safe,
            'session_key' => $session_key,
            'meta'        => [
                'topic'         => $topic,
                'level'         => $level,
                'lang'          => $lang,
                'total_in_pool' => count($questions),
            ],
        ]);
    }

    public static function save_attempt(WP_REST_Request $req): WP_REST_Response {
        $body = $req->get_json_params();

        $required = ['session_id','topic','level','answers'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                return new WP_REST_Response(['error' => "Missing: $field"], 400);
            }
        }

        // Retrieve question ID map
        $session_key = sanitize_text_field($body['session_key'] ?? '');
        $id_map      = $session_key ? get_transient($session_key) : null;

        // Score calculation
        $answers  = $body['answers'];
        $score    = 0;
        $detailed = [];

        if ($id_map) {
            global $wpdb;
            foreach ($answers as $idx => $given) {
                $q_id    = $id_map[$idx] ?? null;
                if (!$q_id) continue;
                $payload = $wpdb->get_var($wpdb->prepare(
                    "SELECT payload FROM {$wpdb->prefix}rtap_questions WHERE id=%d", $q_id
                ));
                $data    = json_decode($payload, true);
                $correct = (int)($data['correct'] ?? -1);
                $is_ok   = ((int)$given === $correct);
                if ($is_ok) $score++;
                $detailed[] = ['q_id' => $q_id, 'given' => $given, 'correct' => $correct, 'ok' => $is_ok];
            }
        }

        $total     = count($answers);
        $score_pct = $total > 0 ? (int)round($score / $total * 100) : 0;

        RTAP_DB::save_attempt([
            'session_id' => sanitize_text_field($body['session_id']),
            'topic'      => sanitize_key($body['topic']),
            'level'      => sanitize_key($body['level']),
            'lang'       => sanitize_key($body['lang'] ?? 'en'),
            'score'      => $score,
            'score_pct'  => $score_pct,
            'time_taken' => absint($body['time_taken'] ?? 0),
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? '',
            'answers'    => $detailed,
        ]);

        $percentile = RTAP_DB::get_percentile(
            sanitize_key($body['topic']),
            sanitize_key($body['level']),
            $score_pct,
            sanitize_key($body['lang'] ?? 'en')
        );

        $stats = RTAP_DB::get_stats(sanitize_key($body['topic']), sanitize_key($body['level']), sanitize_key($body['lang'] ?? 'en'));

        return new WP_REST_Response([
            'score'      => $score,
            'score_pct'  => $score_pct,
            'total'      => $total,
            'percentile' => $percentile,
            'total_takers' => $stats['total'],
            'answers'    => $detailed,
        ]);
    }

    public static function get_stats(WP_REST_Request $req): WP_REST_Response {
        $topic = $req->get_param('topic');
        $level = $req->get_param('level') ?? '';
        $lang  = $req->get_param('lang')  ?? 'en';

        $cache_key = "rtap_stats_{$topic}_{$level}_{$lang}";
        $cached    = get_transient($cache_key);
        if ($cached !== false) {
            return new WP_REST_Response($cached);
        }

        $stats = RTAP_DB::get_stats($topic, $level, $lang);
        set_transient($cache_key, $stats, HOUR_IN_SECONDS);

        return new WP_REST_Response($stats);
    }

    public static function save_candidate(WP_REST_Request $req): WP_REST_Response {
        $body = $req->get_json_params();

        if (empty($body['email']) || !is_email($body['email'])) {
            return new WP_REST_Response(['error' => 'Valid email required'], 400);
        }
        if (empty($body['name'])) {
            return new WP_REST_Response(['error' => 'Name required'], 400);
        }

        $id = RTAP_DB::save_candidate($body);
        if (!$id) {
            return new WP_REST_Response(['error' => 'Save failed'], 500);
        }

        // TMS sync (non-blocking — queued via cron if fails)
        RTAP_Candidate::try_tms_sync($id, $body);

        return new WP_REST_Response(['id' => $id, 'status' => 'saved']);
    }

    public static function issue_certificate(WP_REST_Request $req): WP_REST_Response {
        $body = $req->get_json_params();

        if (empty($body['topic']) || empty($body['level']) || empty($body['score_pct'])) {
            return new WP_REST_Response(['error' => 'Missing fields'], 400);
        }
        if ((int)$body['score_pct'] < 70) {
            return new WP_REST_Response(['error' => 'Score below 70%'], 403);
        }

        $cert_id = RTAP_Certificate::generate_id($body['topic'], $body['level']);
        $saved   = RTAP_DB::save_certificate([
            'id'           => $cert_id,
            'candidate_id' => absint($body['candidate_id'] ?? 0),
            'topic'        => sanitize_key($body['topic']),
            'level'        => sanitize_key($body['level']),
            'lang'         => sanitize_key($body['lang'] ?? 'en'),
            'score_pct'    => (int)$body['score_pct'],
            'name'         => sanitize_text_field($body['name'] ?? ''),
        ]);

        if (!$saved) {
            return new WP_REST_Response(['error' => 'Certificate save failed'], 500);
        }

        return new WP_REST_Response([
            'cert_id'    => $cert_id,
            'verify_url' => get_site_url() . '/verify/' . $cert_id,
        ]);
    }

    public static function verify_certificate(WP_REST_Request $req): WP_REST_Response {
        $cert = RTAP_DB::get_certificate($req->get_param('cert_id'));
        if (!$cert) {
            return new WP_REST_Response(['error' => 'Certificate not found'], 404);
        }
        unset($cert['verify_hash']); // don't expose
        return new WP_REST_Response($cert);
    }

    public static function get_qow(): WP_REST_Response {
        $qow = RTAP_DB::get_active_qow();
        if (!$qow) {
            return new WP_REST_Response(['error' => 'No active question'], 404);
        }

        $payload = json_decode($qow['payload'], true);
        $stats   = json_decode($qow['stats_json'] ?? '{}', true) ?: [];

        // Next Monday 00:00 MSK
        $next_monday = new DateTime('next Monday midnight', new DateTimeZone('Europe/Moscow'));

        return new WP_REST_Response([
            'qow_id'     => (int)$qow['qow_id'],
            'type'       => $qow['type'],
            'question'   => $qow['question'],
            'payload'    => $payload,
            'stats'      => $stats,
            'next_reset' => $next_monday->getTimestamp(),
        ]);
    }

    public static function answer_qow(WP_REST_Request $req): WP_REST_Response {
        $body   = $req->get_json_params();
        $qow_id = absint($body['qow_id'] ?? 0);
        $option = sanitize_text_field($body['option'] ?? '');

        if (!$qow_id || $option === '') {
            return new WP_REST_Response(['error' => 'Missing params'], 400);
        }

        global $wpdb;
        $payload = $wpdb->get_var($wpdb->prepare(
            "SELECT q.payload FROM {$wpdb->prefix}rtap_question_of_week qow
             JOIN {$wpdb->prefix}rtap_questions q ON q.id=qow.question_id
             WHERE qow.id=%d",
            $qow_id
        ));
        $data    = json_decode($payload, true);
        $correct = (string)($data['correct'] ?? '');
        $is_ok   = ($option === $correct);

        RTAP_DB::record_qow_answer($qow_id, $is_ok, $option);

        return new WP_REST_Response([
            'correct'     => $is_ok,
            'correct_val' => $correct,
            'explanation' => $data['explanation'] ?? '',
        ]);
    }

    public static function admin_get_questions(WP_REST_Request $req): WP_REST_Response {
        global $wpdb;
        $topic = sanitize_key($req->get_param('topic') ?? '');
        $level = sanitize_key($req->get_param('level') ?? '');
        $lang  = sanitize_key($req->get_param('lang')  ?? 'en');
        $page  = max(1, (int)($req->get_param('page') ?? 1));
        $per   = 50;

        $where = $wpdb->prepare('WHERE lang=%s', $lang);
        if ($topic) $where .= $wpdb->prepare(' AND topic=%s', $topic);
        if ($level) $where .= $wpdb->prepare(' AND level=%s', $level);

        $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}rtap_questions $where");
        $rows  = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}rtap_questions $where ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per),
            ARRAY_A
        );

        return new WP_REST_Response(['total' => $total, 'page' => $page, 'questions' => $rows]);
    }

    public static function admin_import(WP_REST_Request $req): WP_REST_Response {
        $body      = $req->get_json_params();
        $questions = $body['questions'] ?? [];

        if (!is_array($questions) || empty($questions)) {
            return new WP_REST_Response(['error' => 'No questions provided'], 400);
        }

        $result = RTAP_DB::import_questions($questions);
        return new WP_REST_Response($result);
    }
}
