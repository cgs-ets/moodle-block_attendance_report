
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
 * @package   block_attendance_report_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/log'], function ($, Ajax, Log) {
    'use strict';

    function init() {
        Log.debug("block_attendance_report_report: initialising controls");
        const element = document.getElementById('rollmarking-container');
        const username = element.getAttribute('data-username');

        var control = new Controls(username);
        control.main();
    }

    /**
    * Controls a single block_assignmentsquizzes_report block instance contents.
    *
    * @constructor
    */
    function Controls(username) {
        let self = this;
        self.username = username;
    }

    /**
     * Run the controller.
     *
     */
    Controls.prototype.main = function () {
        let self = this;
        self.getAttendanceRollmarking();

    };

    Controls.prototype.getAttendanceRollmarking = function () {
        let self = this;
        const username = self.username;
        Ajax.call([{
            methodname: 'block_attendance_report_get_attendance_rollmarking_context',
            args: {
                username: username
            },

            done: function (response) {
                const htmlResult = response.html;
                $('[data-region="rm-table-container"]').delay(2000).fadeOut(400);
                $('[data-region="rm-table-container"]').replaceWith(htmlResult);
            },

            fail: function (reason) {
                Log.error('block_attendance_report_get_attendance_rollmarking_context: Unable to get context.');
                Log.debug(reason);
                $('[data-region="rm-table-container"]').replaceWith('<p class="alert alert-danger">Data not available. Please try later</p>');
            }
        }]);

    };


    return { init: init }
});
