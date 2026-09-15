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
 * report.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

$id = required_param("id", PARAM_INT);
$cm = get_coursemodule_from_id("rank", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$rank = $DB->get_record("rank", ["id" => $cm->instance], "*", MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/rank:viewreport", $context);

$PAGE->set_url(new moodle_url("/mod/rank/report.php", ["id" => $cm->id]));
$PAGE->set_title(get_string("report", "rank"));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

$report = new \mod_rank\report_manager($rank);
$rows = $report->get_average_positions();
$responsecount = $DB->count_records("rank_responses", ["rankid" => $rank->id]);

$table = new html_table();
$table->head = [
    get_string("averagerank", "rank"),
    get_string("item", "rank"),
    get_string("averageposition", "rank"),
    get_string("responses", "rank"),
];
$table->data = [];

$ranknumber = 1;
foreach ($rows as $row) {
    $table->data[] = [
        $ranknumber,
        format_string($row->content),
        format_float($row->averageposition, 2),
        $row->responsecount,
    ];
    $ranknumber++;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("reportfor", "rank", format_string($rank->name)));
echo $OUTPUT->notification(get_string("reportsummary", "rank", $responsecount), "info");

if ($rows) {
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string("noresponses", "rank"), "info");
}

echo $OUTPUT->footer();
