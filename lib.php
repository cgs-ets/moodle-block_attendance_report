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
 *  Attendance report block
 *
 * @package    block_attendance_report
 * @copyright 2021 Veronica Bermegui
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the context for the template
 * @return string
 */
namespace attendance_report;

 function get_template_context($data, $instanceid) {
    global  $COURSE;

    $urlparams = array('blockid' => $instanceid, 'courseid' => $COURSE->id);

    $data = ['attendancebasedonrm' => new \moodle_url('/blocks/block_attendance_report/view.php', $urlparams)];
    
    return $data;
}

/**
 * Call to the SP Class_Attendance_By_Term
 */
function get_attendance_by_term($profileuser) {

    try {

        $config = get_config('block_attendance_report');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbyterm . ' :id';

        $params = array(
            'id' => $profileuser->username,
        );
        $attendancedata = $externalDB->get_records_sql($sql, $params);
       
        return $attendancedata;
        
    } catch (\Exception $ex) {
       throw $ex;
    }
}

function get_attendance_by_class($profileuser) {
   
    try {
        $config = get_config('block_attendance_report');

        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB.
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbyclass . ' :id';

        $params = array(
            'id' =>$profileuser->username,
        );

        $attendancedata = $externalDB->get_records_sql($sql, $params);
       
        return $attendancedata;

    } catch (\Exception $ex) {

        throw $ex;
    }
}

// Full Class attendance current Term based on roll marking.
function get_student_attendance_based_on_rollmarking($profileuser) {
    try {
       
        $config = get_config('block_attendance_report');
        $externalDB = \moodle_database::get_driver_instance($config->dbtype, 'native', true);       
        
        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbytermbyid . ' :id';

        $params = array(
            'id' => $profileuser->username,
        );
        $attendancedata = $externalDB->get_recordset_sql($sql, $params);

        $days = [];
        $monthsdata = [];
        $days = [];
     
     
        foreach ($attendancedata as $data) {
            $createDate = new \DateTime($data->attendancedate);
            $day = $createDate->format("d-m-Y");
            $month = $createDate->format("F");
            $swipedt = (new \DateTime($data->swipedt))->format('h:i A');

            $monthsdata['months'][$month.'_'.$day][] = ['attendancedate' => $day,
                'attendanceperiod' => $data->attendanceperiod,
                'ttclasscode' => $data->ttclasscode,
                'housesignin' => $swipedt,
                'nosignin' => $data->swipedt == null,
                'attendedflag' => $data->attendedflag,
                'latearrivalflag' => $data->latearrivalflag,
                'month' => $month,
                'classdescription' => $data->classdescription
            ];

        }

        $attendancedata->close();

        array_walk($monthsdata, function($months) use (&$monthsdata, &$days) {

            foreach ($months as $key => $month) {
             
                $daydetails = new \stdClass();
                $classcode = [];
                $time = "06:00 AM";

                list($daydetails->month, $daydetails->attendancedate) = explode('_', $key);

                foreach ($month as $i => $m) {
                    foreach ($m as $j => $q) {

                        switch ($j) {
                            case 'housesignin':
                                $daydetails->housesignin = $q;
                                $daydetails->late  = strtotime($q) < strtotime($time);
                                break;
                            case 'classdescription':
                                $classcode['codes'][] = ['code' => $q];
                                break;         
                            case 'nosignin': 
                                $daydetails->nosignin = $q;
                                $daydetails->late  =$q;
                                break;                  
                        }
                    }
                }
                $daydetails->classcode = $classcode;
                $days['months']['details']['det'][] = $daydetails;
            }
        });
        return $days;
    } catch (\Exception $ex) {
        throw $ex;
    }
}

// Collect all the data related to attendance.
function get_data($instanceid, $profileuser) {   
    global $COURSE;

    $attendacebyclass = get_attendance_by_class($profileuser);  
    
    $classes = [];  

    foreach ($attendacebyclass as $class) {
        $c = new \stdClass();
        $c->classcode = $class->classcode;
        $c->attended = $class->attended;
        $c->notattended = $class->notattended;
        $c->totalclasses = $class->totalclasses;
        $c->percentageattended = $class->percentageattended;
        $c->nooflateclasses = $class->nooflateclasses;
        $c->nooflateshow = $class->nooflateclasses > 0;
        $c->percentagelate = $class->percentagelate;
        $c->lessthan = $class->percentageattended < 90;
        $c->islate = $class->percentagelate != .00;
        $classes [] = $c;
    }
 
    $attendacebyterm = get_attendance_by_term($profileuser);
    $terms = array();

    foreach ($attendacebyterm as $term) {
        $data = new \stdClass();
        $data->totalpercentageattended = $term->totalpercentageattended;
        $data->filesemester = $term->filesemester;
        $data->currentterm = $term->currentterm;
        $terms [] = $data;
    }

    $urlparams = array('blockid' => $instanceid, 'courseid' => $COURSE->id, 'id' => $profileuser->id);
    
    $result = ['terms' => $terms, 'classes' => $classes,'attendancebasedonrm' => new \moodle_url('/blocks/attendance_report/view.php', $urlparams) ]; 
   
    return $result;
}


// Parent view of own child's activity functionality.
function can_view_on_profile()
{
    global $DB, $USER, $PAGE;
     
    if ($PAGE->url->get_path() ==  '/cgs/moodle/user/profile.php') {
        $profileuser = $DB->get_record('user', ['id' => $PAGE->url->get_param('id')]);
        // Admin is allowed.
     
        
        if (is_siteadmin($USER) && $USER->username != $profileuser->username) {
            return true;
        }
        
        // Students are allowed to see timetables in their own profiles.
        if ($profileuser->username == $USER->username && !is_siteadmin($USER)) {
            return true;
        }

        // Parents are allowed to view timetables in their mentee profiles.
        $mentorrole = $DB->get_record('role', array('shortname' => 'parent'));

        if ($mentorrole) {

            $sql = "SELECT ra.*, r.name, r.shortname
                FROM {role_assignments} ra
                INNER JOIN {role} r ON ra.roleid = r.id
                INNER JOIN {user} u ON ra.userid = u.id
                WHERE ra.userid = ?
                AND ra.roleid = ?
                AND ra.contextid IN (SELECT c.id
                    FROM {context} c
                    WHERE c.contextlevel = ?
                    AND c.instanceid = ?)";
            $params = array(
                $USER->id, //Where current user
                $mentorrole->id, // is a mentor
                CONTEXT_USER,
                $profileuser->id, // of the prfile user
            );
    
            $mentor = $DB->get_records_sql($sql, $params);
            if (!empty($mentor)) {
                return true;
            }
        }
    }

    return false;
}