<?php
defined('ABSPATH') || exit;

class RTAP_Importer {

    public static function from_json(string $json): array {
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return ['error' => 'Invalid JSON', 'imported' => 0, 'errors' => 1];
        }
        return RTAP_DB::import_questions($data);
    }

    public static function from_csv(string $csv): array {
        $lines = explode("\n", trim($csv));
        $headers = str_getcsv(array_shift($lines));
        $questions = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $row = str_getcsv($line);
            $map = array_combine($headers, $row);

            $options = array_filter([
                $map['option_1'] ?? '',
                $map['option_2'] ?? '',
                $map['option_3'] ?? '',
                $map['option_4'] ?? '',
            ]);

            $payload = [
                'options'  => array_values($options),
                'correct'  => max(0, (int)($map['correct'] ?? 1) - 1),
            ];
            if (!empty($map['source'])) {
                $payload['source'] = $map['source'];
            }

            $questions[] = [
                'topic'       => $map['topic']       ?? '',
                'level'       => $map['level']       ?? '',
                'type'        => $map['type']        ?? 'mc',
                'lang'        => $map['lang']        ?? 'en',
                'question'    => $map['question']    ?? '',
                'payload'     => $payload,
                'explanation' => $map['explanation'] ?? '',
                'difficulty'  => (int)($map['difficulty'] ?? 3),
            ];
        }

        return RTAP_DB::import_questions($questions);
    }
}
