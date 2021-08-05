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
 * The graphs continuous assessment block
 *
 * @package    block_attendance_reporting
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Attendance Report';
$string['pluginname_desc'] = 'This plugin depends on DW.';
$string['attendance_report'] = 'Attendance Report';
$string['attendance_report:addinstance'] = 'Add a new attendance report block';
$string['attendance_report:myaddinstance'] = 'Add a new attendance report block to My Moodle page';

$string['dbtype'] = 'Database driver';
$string['dbtype_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhost'] = 'Database host';
$string['dbhost_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbname'] = 'Database name';
$string['dbuser'] = 'Database user';
$string['dbpass'] = 'Database password';
$string['dbattbyterm'] = 'Attendance by Term';
$string['dbattbyterm_desc'] = 'Stored procedure name to retrieve student attendance by term';
$string['dbattbyclass'] = 'Attendance by Class';
$string['dbattbyclass_desc'] = 'Stored procedure name to retrieve student attendance by class';
$string['dbattbytermbyid'] = ' Full class attendance based on roll marking. Senior students';
$string['attbasedonrollmarkinglink'] = ' Full class attendance based on roll marking';
$string['dbattbytermbyid_desc'] = 'Stored procedure name to retrieve senior student attendance this term by id';
$string['dbattbytermbyidprimary'] = ' Full class attendance based on roll marking. Primary students';
$string['dbattbytermbyidprimary_desc'] = 'Stored procedure name to retrieve primary student attendance this term by id';

$string['nodbsettings'] = 'Please configure the DB options for the plugin';
$string['profileurl'] = 'Profile URL';
$string['profileurl_desc'] =' Moodle\'s profile URL';

$string['invalidcourse'] = 'Invalid course';
$string['attendancetitle'] = 'Attendance';
$string['btnattendanceid'] = 'btnattendance';
$string['termlabel'] = 'Term';
$string['attendancereportitle'] = 'Attendance';
$string['attendancebyterm'] = date('Y') .' Attendance';
$string['classcode'] = 'Class Code';
$string['classdescription'] = 'Class Description';
$string['attendancebyclassforterm'] = 'Attendance by class for this term ';
$string['notattendancebyclassforterm'] = 'Classes not attended';
$string['totalclassforterm'] = 'Total classes';
$string['percentageattendedforterm'] = 'Percentage attended';
$string['arrivedlate'] = 'Arrived Late';
$string['percentagearrivedlate'] = 'Percentage arrived late';
$string['profile'] = 'Profile';
$string['attbasedonrollmarking'] = 'Attendance based on roll marking';
$string['attbasedonrmtitle'] = 'Full class attendance this term based on roll marking';
$string['nosignin'] = 'No sign-in';
$string['reportunavailable'] = 'Attendance report unavailable';
$string['clarification'] = 'NB this data is dependent on accurate roll marking.';
$string['norolltaken'] = 'No roll taken';
$string['markedabset'] = 'Marked absent';
$string['markedpresent'] = 'Marked present';
$string['studenthub'] = 'Student Hub';
$string['studentexcursion'] = 'Excursion';
$string['healthClinic'] = 'Health Clinic';
$string['markedlate'] = 'Red text - marked late';
$string['pastoral'] = 'Pastoral';
$string['excursion'] = 'Excursion';
$string['userprofilenotsetup'] = 'Campus role is not set';