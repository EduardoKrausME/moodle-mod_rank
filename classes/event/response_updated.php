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
 * response_updated.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_rank\event;

/**
 * Class response_updated.
 */
class response_updated extends \core\event\base {
    /**
     * Method init.
     *
     * @return mixed Return value.
     */
    protected function init() {
        $this->data["crud"] = "u";
        $this->data["edulevel"] = self::LEVEL_PARTICIPATING;
        $this->data["objecttable"] = "rank_responses";
    }

    /**
     * Method get_name.
     *
     * @return mixed Return value.
     */
    public static function get_name() {
        return get_string("eventresponseupdated", "rank");
    }

    /**
     * Method get_description.
     *
     * @return mixed Return value.
     */
    public function get_description() {
        return "The user with id '{$this->userid}' updated ranking response " .
            "'{$this->objectid}' in rank activity '{$this->other['rankid']}'.";
    }

    /**
     * Method get_url.
     *
     * @return mixed Return value.
     */
    public function get_url() {
        return new \moodle_url("/mod/rank/view.php", ["id" => $this->contextinstanceid]);
    }

    /**
     * Method get_objectid_mapping.
     *
     * @return mixed Return value.
     */
    public static function get_objectid_mapping() {
        return ["db" => "rank_responses", "restore" => "rank_response"];
    }

    /**
     * Method get_other_mapping.
     *
     * @return mixed Return value.
     */
    public static function get_other_mapping() {
        return ["rankid" => ["db" => "rank", "restore" => "rank"]];
    }
}
