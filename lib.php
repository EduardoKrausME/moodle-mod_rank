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
 * lib.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Declares the Moodle features supported by this activity.
 *
 * @param string $feature Feature name.
 * @return mixed
 */
function rank_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_OTHER;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        default:
            return null;
    }
}

/**
 * Adds a new rank activity.
 *
 * @param stdClass $data Form data.
 * @param mod_rank_mod_form|null $mform Form object.
 * @return int
 */
function rank_add_instance($data, $mform = null) {
    return \mod_rank\manager::add_instance($data);
}

/**
 * Updates an existing rank activity.
 *
 * @param stdClass $data Form data.
 * @param mod_rank_mod_form|null $mform Form object.
 * @return bool
 */
function rank_update_instance($data, $mform = null) {
    return \mod_rank\manager::update_instance($data);
}

/**
 * Deletes a rank activity.
 *
 * @param int $id Activity id.
 * @return bool
 */
function rank_delete_instance($id) {
    return \mod_rank\manager::delete_instance($id);
}

/**
 * Returns activity information for the course page.
 *
 * @param cm_info $cm Course-module information.
 * @return cached_cm_info|null
 */
function rank_get_coursemodule_info($cm) {
    global $DB;

    $rank = $DB->get_record("rank", ["id" => $cm->instance], "id, name, intro, introformat", IGNORE_MISSING);
    if (!$rank) {
        return null;
    }

    $result = new cached_cm_info();
    $result->name = $rank->name;
    if ($cm->showdescription) {
        $result->content = format_module_intro("rank", $rank, $cm->id, false);
    }
    return $result;
}

/**
 * Serves files from the activity intro.
 *
 * @param stdClass $course Course record.
 * @param stdClass $cm Course-module record.
 * @param context $context File context.
 * @param string $filearea File area.
 * @param array $args Path arguments.
 * @param bool $forcedownload Whether to force download.
 * @param array $options Additional send options.
 * @return bool
 */
function rank_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel !== CONTEXT_MODULE || $filearea !== "intro") {
        return false;
    }

    require_login($course, true, $cm);
    require_capability("mod/rank:view", $context);

    $filename = array_pop($args);
    $filepath = "/" . implode("/", $args) . "/";
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, "mod_rank", "intro", 0, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}
