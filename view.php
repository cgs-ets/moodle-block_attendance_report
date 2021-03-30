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
 * @package    block_attendance_report
 * @copyright  2021 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/attendance_report/lib.php');

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
$id = required_param('id', PARAM_INT); 

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_attendance_report', $courseid);
}

require_login($course);

$PAGE->set_url('/blocks/attendance_report/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'block_attendance_report'));
$PAGE->set_heading(get_string('attendance_report', 'block_attendance_report'));

$nav = $PAGE->navigation->add(get_string('profile', 'block_attendance_report'), $CFG->wwwroot . '/user/view.php?id=' . $id);
$reporturl = new moodle_url('/blocks/attendance_report/view.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$reportnode = $nav->add(get_string('attbasedonrollmarking', 'block_attendance_report'), $reporturl);
$reportnode->make_active();

echo $OUTPUT->header();

$data = new stdClass();

$profileuser = $DB->get_record('user', ['id' => $id]);
if (is_siteadmin($USER)) {
    $data->studentname = $profileuser->firstname . ' ' .  $profileuser->lastname;
} else {
    $data->studentname = $USER->firstname . ' ' .  $USER->lastname;
}

$data->days = get_student_attendance_based_on_rollmarking($profileuser);

echo $OUTPUT->render_from_template('block_attendance_report/attendance_based_on_rollmarking', $data);
echo $OUTPUT->footer();
