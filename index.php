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
 * index.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

$id = required_param("id", PARAM_INT);
$course = get_course($id);
require_course_login($course);

$PAGE->set_url(new moodle_url("/mod/rank/", ["id" => $id]));
$PAGE->set_pagelayout("incourse");
$PAGE->set_title(get_string("modulenameplural", "rank"));
$PAGE->set_heading($course->fullname);

$instances = get_all_instances_in_course("rank", $course);

$table = new html_table();
$table->head = [get_string("name"), get_string("responses", "rank")];
$table->data = [];

foreach ($instances as $instance) {
    $context = context_module::instance($instance->coursemodule);
    if (!has_capability("mod/rank:view", $context)) {
        continue;
    }
    $count = $DB->count_records("rank_responses", ["rankid" => $instance->id]);
    $table->data[] = [
        html_writer::link(new moodle_url("/mod/rank/view.php", ["id" => $instance->coursemodule]), format_string($instance->name)),
        $count,
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("modulenameplural", "rank"));
echo html_writer::table($table);
echo $OUTPUT->footer();
