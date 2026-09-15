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
 * ranking.js
 *
 * @package   mod_rank
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery"], function($) {
    var dragged = null;

    var updateOrder = function($list, $input) {
        var ids = [];
        $list.children("[data-itemid]").each(function() {
            ids.push($(this).data("itemid"));
        });
        $input.val(ids.join(","));
    };

    var moveByKeyboard = function($item, direction) {
        if (direction < 0) {
            var $previous = $item.prev();
            if ($previous.length) {
                $item.insertBefore($previous);
            }
        } else {
            var $next = $item.next();
            if ($next.length) {
                $item.insertAfter($next);
            }
        }
    };

    var init = function(listId, inputId) {
        var $list = $("#" + listId);
        var $input = $("#" + inputId);

        if (!$list.length || !$input.length || $list.data("editable") !== 1) {
            return;
        }

        $list.on("dragstart", "[data-itemid]", function(event) {
            dragged = this;
            event.originalEvent.dataTransfer.effectAllowed = "move";
            event.originalEvent.dataTransfer.setData("text/plain", $(this).data("itemid"));
            $(this).addClass("opacity-50");
        });

        $list.on("dragend", "[data-itemid]", function() {
            $(this).removeClass("opacity-50");
            dragged = null;
            updateOrder($list, $input);
        });

        $list.on("dragover", "[data-itemid]", function(event) {
            event.preventDefault();
            if (!dragged || dragged === this) {
                return;
            }

            var rect = this.getBoundingClientRect();
            var after = event.originalEvent.clientY > rect.top + rect.height / 2;
            if (after) {
                $(dragged).insertAfter(this);
            } else {
                $(dragged).insertBefore(this);
            }
        });

        $list.on("keydown", "[data-itemid]", function(event) {
            if (!event.altKey || (event.key !== "ArrowUp" && event.key !== "ArrowDown")) {
                return;
            }
            event.preventDefault();
            moveByKeyboard($(this), event.key === "ArrowUp" ? -1 : 1);
            updateOrder($list, $input);
            this.focus();
        });

        updateOrder($list, $input);
    };

    return {
        init: init
    };
});
