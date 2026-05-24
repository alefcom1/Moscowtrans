<?php
defined('ABSPATH') || exit;

class RTAP_Candidate {

    public static function try_tms_sync(int $candidate_id, array $data): void {
        $tms_url = get_option('rtap_tms_url', '');
        $tms_key = get_option('rtap_tms_key', '');

        if (!$tms_url || !$tms_key) return;

        $response = wp_remote_post($tms_url, [
            'timeout' => 10,
            'headers' => [
                'X-API-Key'    => $tms_key,
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode([
                'name'       => $data['name'] ?? '',
                'email'      => $data['email'] ?? '',
                'phone'      => $data['phone'] ?? '',
                'lang_pairs' => $data['lang_pairs'] ?? [],
                'topics'     => $data['topics'] ?? [],
                'source'     => 'rtap',
            ]),
        ]);

        if (is_wp_error($response)) return;

        $body  = json_decode(wp_remote_retrieve_body($response), true);
        $tms_id = $body['id'] ?? '';

        if ($tms_id) {
            RTAP_DB::mark_tms_synced($candidate_id, $tms_id);
        }
    }

    // WP Cron: retry un-synced candidates
    public static function sync_pending(): void {
        $pending = RTAP_DB::get_unsync_candidates();
        foreach ($pending as $candidate) {
            self::try_tms_sync($candidate['id'], $candidate);
        }
    }
}
