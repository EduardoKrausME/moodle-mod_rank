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
 * backup_rank_stepslib.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_rank_activity_structure_step extends backup_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return mixed Return value.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value("userinfo");

        $rank = new backup_nested_element("rank", ["id"], [
            "name", "intro", "introformat", "instructions", "instructionsformat",
            "allowresubmit", "timecreated", "timemodified",
        ]);
        $items = new backup_nested_element("items");
        $item = new backup_nested_element("item", ["id"], ["content", "position"]);
        $responses = new backup_nested_element("responses");
        $response = new backup_nested_element("response", ["id"], ["userid", "timecreated", "timemodified"]);
        $responseitems = new backup_nested_element("response_items");
        $responseitem = new backup_nested_element("response_item", ["id"], ["itemid", "position"]);

        $rank->add_child($items);
        $items->add_child($item);
        $rank->add_child($responses);
        $responses->add_child($response);
        $response->add_child($responseitems);
        $responseitems->add_child($responseitem);

        $rank->set_source_table("rank", ["id" => backup::VAR_ACTIVITYID]);
        $item->set_source_table("rank_items", ["rankid" => backup::VAR_PARENTID]);

        if ($userinfo) {
            $response->set_source_table("rank_responses", ["rankid" => backup::VAR_PARENTID]);
            $responseitem->set_source_table("rank_response_items", ["responseid" => backup::VAR_PARENTID]);
            $response->annotate_ids("user", "userid");
        }

        $rank->annotate_files("mod_rank", "intro", null);

        return $this->prepare_activity_structure($rank);
    }
}
