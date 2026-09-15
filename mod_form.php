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
 * mod_form.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/course/moodleform_mod.php");

/**
 * Class mod_rank_mod_form.
 */
class mod_rank_mod_form extends moodleform_mod {
    /**
     * Method definition.
     *
     * @return mixed Return value.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement("header", "general", get_string("general", "form"));
        $mform->addElement("text", "name", get_string("rankname", "rank"), ["size" => 64]);
        $mform->setType("name", PARAM_TEXT);
        $mform->addRule("name", null, "required", null, "client");
        $mform->addRule("name", get_string("maximumchars", "", 255), "maxlength", 255, "client");

        $this->standard_intro_elements();

        $mform->addElement("header", "ranksettings", get_string("ranksettings", "rank"));
        $mform->addElement("editor", "instructions_editor", get_string("instructions", "rank"), null,
            ["maxfiles" => 0, "context" => $this->get_context()]);
        $mform->setType("instructions_editor", PARAM_RAW);
        $mform->addHelpButton("instructions_editor", "instructions", "rank");

        $mform->addElement("static", "itemshelp", "", get_string("itemshelp", "rank"));
        for ($i = 1; $i <= 10; $i++) {
            $label = get_string("itemnumber", "rank", $i);
            $mform->addElement("text", "rankitem{$i}", $label, ["size" => 64]);
            $mform->setType("rankitem{$i}", PARAM_TEXT);
            $mform->addRule("rankitem{$i}", get_string("maximumchars", "", 1000), "maxlength", 1000, "client");
        }

        $mform->addElement("advcheckbox", "allowresubmit", get_string("allowresubmit", "rank"));
        $mform->setDefault("allowresubmit", 1);
        $mform->addHelpButton("allowresubmit", "allowresubmit", "rank");

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Method data_preprocessing.
     *
     * @param mixed $defaultvalues Parameter defaultvalues.
     * @return mixed Return value.
     */
    public function data_preprocessing(&$defaultvalues) {
        global $DB;

        parent::data_preprocessing($defaultvalues);

        if (!empty($defaultvalues["id"])) {
            $rank = $DB->get_record("rank", ["id" => $defaultvalues["id"]], "instructions, instructionsformat", IGNORE_MISSING);
            if ($rank) {
                $defaultvalues["instructions_editor"] = [
                    "text" => $rank->instructions,
                    "format" => $rank->instructionsformat,
                ];
            }

            $items = $DB->get_records("rank_items", ["rankid" => $defaultvalues["id"]], "position ASC");
            $i = 1;
            foreach ($items as $item) {
                if ($i > 10) {
                    break;
                }
                $defaultvalues["rankitem{$i}"] = $item->content;
                $i++;
            }
        }
    }

    /**
     * Method validation.
     *
     * @param mixed $data Parameter data.
     * @param mixed $files Parameter files.
     * @return mixed Return value.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $items = [];
        $hasgap = false;
        $seenempty = false;

        for ($i = 1; $i <= 10; $i++) {
            $value = trim($data["rankitem{$i}"] ?? "");
            if ($value === "") {
                $seenempty = true;
                continue;
            }
            if ($seenempty) {
                $hasgap = true;
            }
            $items[] = $value;
        }

        if (count($items) < 5) {
            $errors["rankitem1"] = get_string("minimumitems", "rank");
        }
        if ($hasgap) {
            $errors["rankitem1"] = get_string("itemsgap", "rank");
        }
        if (count($items) !== count(array_unique(array_map("core_text::strtolower", $items)))) {
            $errors["rankitem1"] = get_string("duplicateitems", "rank");
        }

        return $errors;
    }
}
