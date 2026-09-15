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
 * view.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

$id = optional_param("id", 0, PARAM_INT);
$r = optional_param("r", 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id("rank", $id, 0, false, MUST_EXIST);
    $course = get_course($cm->course);
    $rank = $DB->get_record("rank", ["id" => $cm->instance], "*", MUST_EXIST);
} else {
    $rank = $DB->get_record("rank", ["id" => $r], "*", MUST_EXIST);
    $course = get_course($rank->course);
    $cm = get_coursemodule_from_instance("rank", $rank->id, $course->id, false, MUST_EXIST);
}

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/rank:view", $context);

$PAGE->set_url(new moodle_url("/mod/rank/view.php", ["id" => $cm->id]));
$PAGE->set_title(format_string($rank->name));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

\mod_rank\event\course_module_viewed::create([
    "objectid" => $rank->id,
    "context" => $context,
])->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$manager = new \mod_rank\response_manager($rank, $cm, $context);
$message = null;

if (data_submitted()) {
    require_sesskey();
    require_capability("mod/rank:submit", $context);
    $order = required_param("order", PARAM_SEQUENCE);
    $manager->save_response($USER->id, $order);
    $message = get_string("responsesaved", "rank");
}

$items = $manager->get_items_for_user($USER->id);
$response = $manager->get_response($USER->id);
$canedit = has_capability("mod/rank:submit", $context) && (!$response || !empty($rank->allowresubmit));

$PAGE->requires->js_call_amd("mod_rank/ranking", "init", ["rank-list", "rank-order"]);

$templatecontext = [
    "instructions" => format_text($rank->instructions, $rank->instructionsformat, ["context" => $context]),
    "items" => array_values(array_map(static function($item) {
        return [
            "id" => $item->id,
            "content" => format_string($item->content),
        ];
    }, $items)),
    "canedit" => $canedit,
    "hasresponse" => (bool)$response,
    "action" => (new moodle_url("/mod/rank/view.php", ["id" => $cm->id]))->out(false),
    "sesskey" => sesskey(),
    "order" => implode(",", array_map(static function($item) {
        return $item->id;
    }, $items)),
    "submitlabel" => $response ? get_string("updateranking", "rank") : get_string("submitranking", "rank"),
];

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($rank->name));

if (!empty($rank->intro)) {
    echo $OUTPUT->box(format_module_intro("rank", $rank, $cm->id), "generalbox mod_introbox");
}

if ($message) {
    echo $OUTPUT->notification($message, "notifysuccess");
}

if (has_capability("mod/rank:viewreport", $context)) {
    echo html_writer::div(
        html_writer::link(new moodle_url("/mod/rank/report.php", ["id" => $cm->id]),
            get_string("viewreport", "rank"), ["class" => "btn btn-secondary"]),
        "mb-3"
    );
}

echo $OUTPUT->render_from_template("mod_rank/ranking", $templatecontext);
echo $OUTPUT->footer();
