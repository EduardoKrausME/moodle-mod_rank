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
 * provider.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_rank\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Class provider.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Method get_metadata.
     *
     * @param collection $collection Parameter collection.
     * @return collection Return value.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table("rank_responses", [
            "rankid" => "privacy:metadata:rank_responses:rankid",
            "userid" => "privacy:metadata:rank_responses:userid",
            "timecreated" => "privacy:metadata:rank_responses:timecreated",
            "timemodified" => "privacy:metadata:rank_responses:timemodified",
        ], "privacy:metadata:rank_responses");
        $collection->add_database_table("rank_response_items", [
            "responseid" => "privacy:metadata:rank_response_items:responseid",
            "itemid" => "privacy:metadata:rank_response_items:itemid",
            "position" => "privacy:metadata:rank_response_items:position",
        ], "privacy:metadata:rank_response_items");
        return $collection;
    }

    /**
     * Method get_contexts_for_userid.
     *
     * @param int $userid Parameter userid.
     * @return contextlist Return value.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {rank} r ON r.id = cm.instance
                  JOIN {rank_responses} rr ON rr.rankid = r.id
                 WHERE rr.userid = :userid";
        $params = [
            "contextlevel" => CONTEXT_MODULE,
            "modname" => "rank",
            "userid" => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Method get_users_in_context.
     *
     * @param userlist $userlist Parameter userlist.
     * @return void Return value.
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }

        $sql = "SELECT rr.userid
                  FROM {rank_responses} rr
                  JOIN {rank} r ON r.id = rr.rankid
                  JOIN {course_modules} cm ON cm.instance = r.id
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                 WHERE cm.id = :cmid";
        $userlist->add_from_sql("userid", $sql, ["modname" => "rank", "cmid" => $context->instanceid]);
    }

    /**
     * Method export_user_data.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }

            $cm = get_coursemodule_from_id("rank", $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }

            $response = $DB->get_record("rank_responses", ["rankid" => $cm->instance, "userid" => $userid]);
            if (!$response) {
                continue;
            }

            $sql = "SELECT ri.id, i.content, ri.position
                      FROM {rank_response_items} ri
                      JOIN {rank_items} i ON i.id = ri.itemid
                     WHERE ri.responseid = :responseid
                  ORDER BY ri.position ASC";
            $items = $DB->get_records_sql($sql, ["responseid" => $response->id]);
            $data = (object)[
                "timecreated" => transform::datetime($response->timecreated),
                "timemodified" => transform::datetime($response->timemodified),
                "ranking" => array_values(array_map(static function($item) {
                    return (object)["position" => $item->position, "item" => $item->content];
                }, $items)),
            ];
            writer::with_context($context)->export_data([get_string("privacy:export:response", "rank")], $data);
        }
    }

    /**
     * Method delete_data_for_all_users_in_context.
     *
     * @param \context $context Parameter context.
     * @return void Return value.
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }
        $cm = get_coursemodule_from_id("rank", $context->instanceid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }
        self::delete_responses($cm->instance, null);
    }

    /**
     * Method delete_data_for_user.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id("rank", $context->instanceid, 0, false, IGNORE_MISSING);
            if ($cm) {
                self::delete_responses($cm->instance, $contextlist->get_user()->id);
            }
        }
    }

    /**
     * Method delete_data_for_users.
     *
     * @param approved_userlist $userlist Parameter userlist.
     * @return void Return value.
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }
        $cm = get_coursemodule_from_id("rank", $context->instanceid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }

        $userids = $userlist->get_userids();
        if (!$userids) {
            return;
        }

        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, "userid");
        $params["rankid"] = $cm->instance;
        $responseids = $DB->get_fieldset_select("rank_responses", "id", "rankid = :rankid AND userid {$insql}", $params);
        self::delete_response_ids($responseids);
    }

    /**
     * Method delete_responses.
     *
     * @param int $rankid Parameter rankid.
     * @param ?int $userid Parameter userid.
     * @return void Return value.
     */
    private static function delete_responses(int $rankid, ?int $userid): void {
        global $DB;

        $params = ["rankid" => $rankid];
        $where = "rankid = :rankid";
        if ($userid !== null) {
            $where .= " AND userid = :userid";
            $params["userid"] = $userid;
        }
        $responseids = $DB->get_fieldset_select("rank_responses", "id", $where, $params);
        self::delete_response_ids($responseids);
    }

    /**
     * Method delete_response_ids.
     *
     * @param array $responseids Parameter responseids.
     * @return void Return value.
     */
    private static function delete_response_ids(array $responseids): void {
        global $DB;

        if (!$responseids) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($responseids, SQL_PARAMS_NAMED, "response");
        $DB->delete_records_select("rank_response_items", "responseid {$insql}", $params);
        $DB->delete_records_select("rank_responses", "id {$insql}", $params);
    }
}
