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
 * rank.php
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['allowresubmit'] = 'Allow students to change their ranking';
$string['allowresubmit_help'] = 'When enabled, students can reopen the activity and update their submitted order.';
$string['averageposition'] = 'Average position';
$string['averagerank'] = 'Class rank';
$string['cannotchangeitemcount'] = 'The number of items cannot be changed after students have submitted responses. You may edit the item text without changing the quantity.';
$string['draghelp'] = 'Drag items to reorder them. With the keyboard, focus an item and use Alt + Up/Down Arrow.';
$string['duplicateitems'] = 'Each item must be unique.';
$string['eventresponsecreated'] = 'Ranking response submitted';
$string['eventresponseupdated'] = 'Ranking response updated';
$string['instructions'] = 'Instructions';
$string['instructions_help'] = 'Explain the criterion students should use to order the items, such as priority, chronology, importance, or process sequence.';
$string['invaliditemcount'] = 'A ranking activity must contain between 5 and 10 items.';
$string['invalidranking'] = 'The submitted ranking is invalid. Every item must appear exactly once.';
$string['item'] = 'Item';
$string['itemnumber'] = 'Item ';
$string['itemsgap'] = 'Do not leave blank fields between items. Put blank fields only after the last item.';
$string['itemshelp'] = 'Enter between 5 and 10 items. Leave unused fields at the end blank.';
$string['minimumitems'] = 'Enter at least 5 items.';
$string['modulename'] = 'Ranking';
$string['modulenameplural'] = 'Rankings';
$string['noresponses'] = 'No responses have been submitted yet.';
$string['pluginadministration'] = 'Ranking administration';
$string['pluginname'] = 'Ranking';
$string['privacy:export:response'] = 'Ranking response';
$string['privacy:metadata:rank_response_items'] = 'Stores each item position in a ranking response.';
$string['privacy:metadata:rank_response_items:itemid'] = 'The ranked item identifier.';
$string['privacy:metadata:rank_response_items:position'] = 'The position assigned to the item by the user.';
$string['privacy:metadata:rank_response_items:responseid'] = 'The response identifier.';
$string['privacy:metadata:rank_responses'] = 'Stores the response submitted by each user.';
$string['privacy:metadata:rank_responses:rankid'] = 'The ranking activity identifier.';
$string['privacy:metadata:rank_responses:timecreated'] = 'When the response was first submitted.';
$string['privacy:metadata:rank_responses:timemodified'] = 'When the response was last updated.';
$string['privacy:metadata:rank_responses:userid'] = 'The user who submitted the response.';
$string['rank:addinstance'] = 'Add a new ranking activity';
$string['rank:submit'] = 'Submit a ranking';
$string['rank:view'] = 'View ranking activities';
$string['rank:viewreport'] = 'View ranking reports';
$string['rankname'] = 'Ranking name';
$string['ranksettings'] = 'Ranking settings';
$string['report'] = 'Ranking report';
$string['reportfor'] = 'Ranking report: ';
$string['reportsummary'] = 'Responses received: ';
$string['responses'] = 'Responses';
$string['responsesaved'] = 'Your ranking has been saved.';
$string['resubmitdisabled'] = 'This ranking has already been submitted and changes are not allowed.';
$string['submitranking'] = 'Submit ranking';
$string['updateranking'] = 'Update ranking';
$string['viewreport'] = 'View class report';
$string['yourranking'] = 'Your current ranking';
