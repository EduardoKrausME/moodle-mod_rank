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
 * response_manager.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_rank;

/**
 * Class response_manager.
 */
class response_manager {
    /**
     * Property rank.
     *
     * @var mixed
     */
    private $rank;
    /**
     * Property cm.
     *
     * @var mixed
     */
    private $cm;
    /**
     * Property context.
     *
     * @var mixed
     */
    private $context;

    /**
     * Method __construct.
     *
     * @param \stdClass $rank Parameter rank.
     * @param mixed $cm Parameter cm.
     * @param \context_module $context Parameter context.
     */
    public function __construct(\stdClass $rank, $cm, \context_module $context) {
        $this->rank = $rank;
        $this->cm = $cm;
        $this->context = $context;
    }

    /**
     * Method get_response.
     *
     * @param int $userid Parameter userid.
     * @return ?\stdClass Return value.
     */
    public function get_response(int $userid): ?\stdClass {
        global $DB;
        $record = $DB->get_record("rank_responses", ["rankid" => $this->rank->id, "userid" => $userid]);
        return $record ?: null;
    }

    /**
     * Method get_items_for_user.
     *
     * @param int $userid Parameter userid.
     * @return array Return value.
     */
    public function get_items_for_user(int $userid): array {
        global $DB;

        $response = $this->get_response($userid);
        if (!$response) {
            $items = array_values($DB->get_records("rank_items", ["rankid" => $this->rank->id], "position ASC"));
            if (has_capability("mod/rank:submit", $this->context, $userid)) {
                usort($items, function($a, $b) use ($userid) {
                    $akey = hash("sha256", $this->rank->id . ":" . $userid . ":" . $a->id);
                    $bkey = hash("sha256", $this->rank->id . ":" . $userid . ":" . $b->id);
                    return strcmp($akey, $bkey);
                });
            }
            return $items;
        }

        $sql = "SELECT i.*
                  FROM {rank_response_items} ri
                  JOIN {rank_items} i ON i.id = ri.itemid
                 WHERE ri.responseid = :responseid
              ORDER BY ri.position ASC";
        $items = $DB->get_records_sql($sql, ["responseid" => $response->id]);
        return array_values($items);
    }

    /**
     * Method save_response.
     *
     * @param int $userid Parameter userid.
     * @param string $order Parameter order.
     * @return void Return value.
     */
    public function save_response(int $userid, string $order): void {
        global $DB;

        if (!has_capability("mod/rank:submit", $this->context, $userid)) {
            throw new \required_capability_exception($this->context, "mod/rank:submit", "nopermissions", "");
        }

        $existing = $this->get_response($userid);
        if ($existing && empty($this->rank->allowresubmit)) {
            throw new \moodle_exception("resubmitdisabled", "rank");
        }

        $itemids = array_map("intval", explode(",", $order));
        $itemids = array_values(array_filter($itemids, static function($id) {
            return $id > 0;
        }));
        $validids = array_map("intval",
            $DB->get_fieldset_select("rank_items", "id", "rankid = :rankid", ["rankid" => $this->rank->id]));

        sort($itemids);
        sort($validids);
        if ($itemids !== $validids) {
            throw new \moodle_exception("invalidranking", "rank");
        }

        $submittedids = array_map("intval", explode(",", $order));
        $transaction = $DB->start_delegated_transaction();
        $now = time();

        if ($existing) {
            $existing->timemodified = $now;
            $DB->update_record("rank_responses", $existing);
            $responseid = $existing->id;
            $DB->delete_records("rank_response_items", ["responseid" => $responseid]);
            $eventclass = "\\mod_rank\\event\\response_updated";
        } else {
            $responseid = $DB->insert_record("rank_responses", (object)[
                "rankid" => $this->rank->id,
                "userid" => $userid,
                "timecreated" => $now,
                "timemodified" => $now,
            ]);
            $eventclass = "\\mod_rank\\event\\response_created";
        }

        foreach ($submittedids as $index => $itemid) {
            $DB->insert_record("rank_response_items", (object)[
                "responseid" => $responseid,
                "itemid" => $itemid,
                "position" => $index + 1,
            ]);
        }

        $transaction->allow_commit();

        $eventclass::create([
            "objectid" => $responseid,
            "context" => $this->context,
            "relateduserid" => $userid,
            "other" => ["rankid" => $this->rank->id],
        ])->trigger();
    }
}
