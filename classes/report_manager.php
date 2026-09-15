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
 * report_manager.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_rank;

/**
 * Class report_manager.
 */
class report_manager {
    /**
     * Property rank.
     *
     * @var mixed
     */
    private $rank;

    /**
     * Method __construct.
     *
     * @param \stdClass $rank Parameter rank.
     */
    public function __construct(\stdClass $rank) {
        $this->rank = $rank;
    }

    /**
     * Method get_average_positions.
     *
     * @return array Return value.
     */
    public function get_average_positions(): array {
        global $DB;

        $sql = "SELECT i.id,
                       i.content,
                       AVG(ri.position) AS averageposition,
                       COUNT(ri.id) AS responsecount
                  FROM {rank_items} i
             LEFT JOIN {rank_response_items} ri ON ri.itemid = i.id
                 WHERE i.rankid = :rankid
              GROUP BY i.id, i.content
                HAVING COUNT(ri.id) > 0
              ORDER BY averageposition ASC, i.position ASC";

        return array_values($DB->get_records_sql($sql, ["rankid" => $this->rank->id]));
    }
}
