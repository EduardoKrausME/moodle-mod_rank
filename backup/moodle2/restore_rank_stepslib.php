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
 * restore_rank_stepslib.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_rank_activity_structure_step extends restore_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return mixed Return value.
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element("rank", "/activity/rank");
        $paths[] = new restore_path_element("rank_item", "/activity/rank/items/item");

        if ($this->get_setting_value("userinfo")) {
            $paths[] = new restore_path_element("rank_response", "/activity/rank/responses/response");
            $paths[] = new restore_path_element("rank_response_item",
                "/activity/rank/responses/response/response_items/response_item");
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Method process_rank.
     *
     * @param mixed $data Parameter data.
     * @return mixed Return value.
     */
    protected function process_rank($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();
        $newitemid = $DB->insert_record("rank", $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Method process_rank_item.
     *
     * @param mixed $data Parameter data.
     * @return mixed Return value.
     */
    protected function process_rank_item($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->rankid = $this->get_new_parentid("rank");
        $newid = $DB->insert_record("rank_items", $data);
        $this->set_mapping("rank_item", $oldid, $newid);
    }

    /**
     * Method process_rank_response.
     *
     * @param mixed $data Parameter data.
     * @return mixed Return value.
     */
    protected function process_rank_response($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->rankid = $this->get_new_parentid("rank");
        $data->userid = $this->get_mappingid("user", $data->userid);
        $newid = $DB->insert_record("rank_responses", $data);
        $this->set_mapping("rank_response", $oldid, $newid);
    }

    /**
     * Method process_rank_response_item.
     *
     * @param mixed $data Parameter data.
     * @return mixed Return value.
     */
    protected function process_rank_response_item($data) {
        global $DB;

        $data = (object)$data;
        $data->responseid = $this->get_new_parentid("rank_response");
        $data->itemid = $this->get_mappingid("rank_item", $data->itemid);
        $DB->insert_record("rank_response_items", $data);
    }

    /**
     * Method after_execute.
     *
     * @return mixed Return value.
     */
    protected function after_execute() {
        $this->add_related_files("mod_rank", "intro", null);
    }
}
