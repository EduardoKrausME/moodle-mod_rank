<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * manager.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_rank;

/**
 * Class manager.
 */
class manager {
    /**
     * Method add_instance.
     *
     * @param \stdClass $data Parameter data.
     * @return int Return value.
     */
    public static function add_instance(\stdClass $data): int {
        global $DB;

        $record = self::prepare_record($data);
        $record->timecreated = time();
        $record->timemodified = $record->timecreated;

        $transaction = $DB->start_delegated_transaction();
        $id = $DB->insert_record("rank", $record);
        self::replace_items($id, self::extract_items($data));
        $transaction->allow_commit();

        return $id;
    }

    /**
     * Method update_instance.
     *
     * @param \stdClass $data Parameter data.
     * @return bool Return value.
     */
    public static function update_instance(\stdClass $data): bool {
        global $DB;

        $record = self::prepare_record($data);
        $record->id = $data->instance;
        $record->timemodified = time();

        $transaction = $DB->start_delegated_transaction();
        $DB->update_record("rank", $record);
        self::replace_items($record->id, self::extract_items($data));
        $transaction->allow_commit();

        return true;
    }

    /**
     * Method delete_instance.
     *
     * @param int $id Parameter id.
     * @return bool Return value.
     */
    public static function delete_instance(int $id): bool {
        global $DB;

        if (!$DB->record_exists("rank", ["id" => $id])) {
            return false;
        }

        $transaction = $DB->start_delegated_transaction();
        $responseids = $DB->get_fieldset_select("rank_responses", "id", "rankid = :rankid", ["rankid" => $id]);
        if ($responseids) {
            [$insql, $params] = $DB->get_in_or_equal($responseids, SQL_PARAMS_NAMED, "response");
            $DB->delete_records_select("rank_response_items", "responseid {$insql}", $params);
        }
        $DB->delete_records("rank_responses", ["rankid" => $id]);
        $DB->delete_records("rank_items", ["rankid" => $id]);
        $DB->delete_records("rank", ["id" => $id]);
        $transaction->allow_commit();

        return true;
    }

    /**
     * Method prepare_record.
     *
     * @param \stdClass $data Parameter data.
     * @return \stdClass Return value.
     */
    private static function prepare_record(\stdClass $data): \stdClass {
        $record = new \stdClass();
        $record->course = (int)$data->course;
        $record->name = $data->name;
        $record->intro = $data->intro ?? "";
        $record->introformat = $data->introformat ?? FORMAT_HTML;
        $record->instructions = $data->instructions_editor["text"] ?? "";
        $record->instructionsformat = $data->instructions_editor["format"] ?? FORMAT_HTML;
        $record->allowresubmit = empty($data->allowresubmit) ? 0 : 1;

        return $record;
    }

    /**
     * Method extract_items.
     *
     * @param \stdClass $data Parameter data.
     * @return array Return value.
     */
    private static function extract_items(\stdClass $data): array {
        $items = [];
        for ($i = 1; $i <= 10; $i++) {
            $value = trim((string)($data->{"rankitem{$i}"} ?? ""));
            if ($value !== "") {
                $items[] = $value;
            }
        }
        return $items;
    }

    /**
     * Method replace_items.
     *
     * @param int $rankid Parameter rankid.
     * @param array $items Parameter items.
     * @return void Return value.
     */
    private static function replace_items(int $rankid, array $items): void {
        global $DB;

        if (count($items) < 5 || count($items) > 10) {
            throw new \moodle_exception("invaliditemcount", "rank");
        }

        $hasresponses = $DB->record_exists("rank_responses", ["rankid" => $rankid]);
        $existing = array_values($DB->get_records("rank_items", ["rankid" => $rankid], "position ASC"));

        if ($hasresponses && count($existing) === count($items)) {
            foreach ($existing as $index => $item) {
                $item->content = $items[$index];
                $item->position = $index + 1;
                $DB->update_record("rank_items", $item);
            }
            return;
        }

        if ($hasresponses) {
            throw new \moodle_exception("cannotchangeitemcount", "rank");
        }

        $DB->delete_records("rank_items", ["rankid" => $rankid]);
        foreach ($items as $index => $content) {
            $DB->insert_record("rank_items", (object)[
                "rankid" => $rankid,
                "content" => $content,
                "position" => $index + 1,
            ]);
        }
    }
}
