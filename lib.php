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

 function get_template_context($data, $instanceid) {
    global $USER, $COURSE;

    $urlparams = array('blockid' => $instanceid, 'courseid' => $COURSE->id);

    $data = ['attendancebasedonrm' => new moodle_url('/blocks/block_attendance_report/view.php', $urlparams)];
    
    return $data;
}

/**
 * Call to the SP Class_Attendance_By_Term
 */
function get_attendance_by_term() {
    global $USER;

    try {

        $config = get_config('block_attendance_report');

        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbyterm . ' :id';

        $params = array(
            'id' => $USER->username,
        );

        $attendancedata = $externalDB->get_records_sql($sql, $params);
        return $attendancedata;
        
    } catch (Exception $ex) {
        //TODO: DO something with the error.
    }
}

function get_attendance_by_class() {
    global $USER;

    try {
        $config = get_config('block_attendance_report');

        $externalDB = moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbyclass . ' :id';

        $params = array(
            'id' => $USER->username,
        );

        $attendancedata = $externalDB->get_records_sql($sql, $params);
      
        return $attendancedata;
    } catch (Exception $ex) {
        //TODO: DO something with the error.
    }
}

// Full Class attendance current Term based on roll marking
function get_student_attendance_based_on_rollmarking() {
    global $USER;
    try {
        $config = get_config('block_attendance_report');
        $externalDB = moodle_database::get_driver_instance($config->dbtype, 'native', true);

        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbattbytermbyid . ' :id';

        $params = array(
            'id' => $USER->username,
        );
        $attendancedata = $externalDB->get_recordset_sql($sql, $params);

        $attdata = [];
        $days = [];
        $monthsdata = [];
        $days = [];
        $monthlabel = [];
        $monthdates = [];
        $classcodes = [];
     
        foreach ($attendancedata as $data) {
            $createDate = new DateTime($data->attendancedate);
            $day = $createDate->format("d-m-Y");
            $month = $createDate->format("F");
            $swipedt = (new DateTime($data->swipedt))->format('h:i A');

            $monthsdata['months'][$month.'_'.$day][] = ['attendancedate' => $day,
                'attendanceperiod' => $data->attendanceperiod,
                'ttclasscode' => $data->ttclasscode,
                'housesignin' => $swipedt,
                'attendedflag' => $data->attendedflag,
                'latearrivalflag' => $data->latearrivalflag,
                'month' => $month,
                'classdescription' => $data->classdescription
            ];

        }


        $attendancedata->close();


        array_walk($monthsdata, function($months) use (&$monthsdata, &$days, $createDate,
            $monthlabel, $classcodes, $monthdates) {

            foreach ($months as $key => $month) {
                $daydetails = new \stdClass();
                $classcode = [];

                list($daydetails->month, $daydetails->attendancedate) = explode('_', $key);

                foreach ($month as $i => $m) {
                    foreach ($m as $j => $q) {

                        switch ($j) {
                            case 'housesignin':
                                $daydetails->housesignin = $q;
                                break;
                            case 'classdescription':

                                $classcode['codes'][] = ['code' => $q];
                                break;                           
                        }
                    }
                }
                $daydetails->classcode = $classcode;
                $days['months']['details']['det'][] = $daydetails;
            }
        });
        
        return $days;
    } catch (Exception $ex) {

    }
}

// Collect all the data related to attendance
function get_data($instanceid) {   
    global $COURSE;

    $attendacebyclass = get_attendance_by_class();  
    
    $classes = [];  

    foreach ($attendacebyclass as $class) {
        $c = new \stdClass();
        $c->classcode = $class->classcode;
        $c->attended = $class->attended;
        $c->notattended = $class->notattended;
        $c->totalclasses = $class->totalclasses;
        $c->percentageattended = $class->percentageattended;
        $c->nooflateclasses = $nooflateclasses;
        $c->percentagelate = $class->percentagelate;
        $c->lessthan = $class->percentageattended < 90;
        $classes [] = $c;
    }
 
    $attendacebyterm = get_attendance_by_term();
    $terms = array();

    foreach ($attendacebyterm as $term) {
        $data = new \stdClass();
        $data->totalpercentageattended = $term->totalpercentageattended;
        $data->filesemester = $term->filesemester;
        $data->currentterm = $term->currentterm;
        $terms [] = $data;
    }

    $urlparams = array('blockid' => $instanceid, 'courseid' => $COURSE->id);
    
    $result = ['terms' => $terms, 'classes' => $classes,'attendancebasedonrm' => new moodle_url('/blocks/attendance_report/view.php', $urlparams) ]; 
  
    return $result;
}
