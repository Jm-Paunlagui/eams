<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CL_SESSION_PATH;
require CONNECT_PATH;
require ISLOGIN;

$date_time = DATE_TIME;

// No limit on the number of records to be displayed
$query_limit = "";
$field_query = '*';
$pages = 0;
$start = 0;
$size = 0;

// 1, 2, 3, 4, 5, 6 is equal to Monday, Tuesday, Wednesday, Thursday, Friday, Saturday and get the current day of the week

if (isset($_GET['table']) && $_GET['table'] == 'physical_checking') { // Physical Checking



    $date = isset($_GET['day']) ? date('Y-m-d', strtotime($_GET['day'])) : '';
    $day = date('w', strtotime($date));

    $sorters = array();
    $orderby = "start_time";
    $sql_where_schedule = "";
    $sql_where_makeup = "";
    $sql_where_check = "";
    $sql_conds_schedule = "";
    $sql_conds_makeup = "";
    $sql_conds_check = "";
    $sql_where_array_schedule = array();
    $to_encode = array();
    $output = "";
    $total_query = 0;

    $initial_schedule = array();
    $data_schedule = array();

    // Schedule
    $table_schedule = "schedule";
    $sql_where_array_schedule[] = "day = '" . $day . "'";
    $dbfield_schedule = array('id', 'CONCAT(userInfo.f_name," ",userInfo.l_name) as name', 'tableInfo.employee_id', 'day', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'semester', 'active', 'start_date', 'end_date');
    $dborig_schedule = array('id', 'name', 'employee_id', 'day', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'semester', 'active', 'start_date', 'end_date', 'check_status', 'series_num');

    $field_query_schedule = implode(", ", $dbfield_schedule);
    if (count($sql_where_array_schedule) > 0) {
        $sql_where_schedule = implode(" AND ", $sql_where_array_schedule);
    }
    $sql_conds_schedule = (empty($sql_where_schedule)) ? '' : 'WHERE ' . $sql_where_schedule;

    $default_query_schedule = "SELECT " . $field_query_schedule . " FROM " . $table_schedule . " as tableInfo LEFT JOIN (SELECT f_name,l_name,employee_id FROM users) as userInfo ON tableInfo.employee_id = userInfo.employee_id " . $sql_conds_schedule . " ORDER BY $orderby";

    if ($sched_query = call_mysql_query($default_query_schedule)) {
        if ($num = call_mysql_num_rows($sched_query)) {
            while ($data = call_mysql_fetch_array($sched_query)) {
                $data = array_html($data);
                $initial_schedule[] = $data;
            }
        }
    }

    foreach ($initial_schedule as $value) {
        $employee_id = $value['employee_id'];
        $section = $value['section'];
        $subject_code = $value['subject_code'];
        $sched_day = $value['day'];

        // Physical Checking
        $table_physicalChecking = "physical_checking";
        $sql_where_array_check = array("date = '" . $date . "'", "employee_id = '" . $employee_id . "'", "section = '" . $section . "'", "subject_code = '" . $subject_code . "'");
        $dbfield_physicalCheck = array('id', 'employee_id', 'room', 'section', 'subject_code', 'date', 'date_check', 'remarks', 'check_status');
        $dborig_physicalCheck = array('id', 'employee_id', 'room', 'date', 'date_check', 'remarks', 'check_status');

        $field_query_makeup = implode(", ", $dbfield_physicalCheck);
        if (count($sql_where_array_check) > 0) {
            $sql_where_check = implode(" AND ", $sql_where_array_check);
        }
        $sql_conds_check = (empty($sql_where_check)) ? '' : 'WHERE ' . $sql_where_check;

        $default_query_schedule = "SELECT " . $field_query_makeup . " FROM " . $table_physicalChecking . " " . $sql_conds_check . "";
        $check_status = '';
        if ($query = call_mysql_query($default_query_schedule)) {
            if ($num = call_mysql_num_rows($query)) {
                $data = call_mysql_fetch_array($query);
                $check_status = $data['check_status'];
                $date_check = date('m/d/Y h:i A', strtotime($data['date_check']));
            } else {
                $check_status = '3';
                $date_check = '-';
            }
        }

        // MakeUp Class
        $table_makeup = "makeup_class";
        $sql_where_array_makeup = array("tableInfo.employee_id = '" . $employee_id . "'", "section = '" . $section . "'", "subject_code = '" . $subject_code . "'", "date = '" . $date . "'");
        $dbfield_makeup = array('id', 'CONCAT(userInfo.f_name," ",userInfo.l_name) as name', 'tableInfo.employee_id', 'date', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'check_remark');
        $dborig_makeup = array('id', 'employee_id', 'date', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'check_remark', 'check_status', 'series_num');
        $field_query_makeup = implode(", ", $dbfield_makeup);
        if (count($sql_where_array_makeup) > 0) {
            $sql_where_makeup = implode(" AND ", $sql_where_array_makeup);
        }
        $sql_conds_makeup = (empty($sql_where_makeup)) ? '' : 'WHERE ' . $sql_where_makeup;

        $default_query_makeup = "SELECT " . $field_query_makeup . " FROM " . $table_makeup . " as tableInfo LEFT JOIN (SELECT f_name,l_name,employee_id FROM users) as userInfo ON tableInfo.employee_id = userInfo.employee_id " . $sql_conds_makeup . " ORDER BY $orderby";
        if ($query = call_mysql_query($default_query_makeup)) {
            if ($num = call_mysql_num_rows($query)) {
                while ($data = call_mysql_fetch_array($query)) {
                    $data = array_html($data);
                    $data['remarks'] = 'Make-up Class';
                    $data['check_status'] = $check_status;
                    $data['date_check'] = $date_check;
                    $data_schedule[] = $data;
                }
            } else {
                $value['remarks'] = 'Scheduled';
                $value['check_status'] = $check_status;
                $value['date_check'] = $date_check;
                $data_schedule[] = $value;
            }
        }
    }
    $output = json_encode(["data" => $data_schedule]);
    echo $output;
    exit();
}

if (isset($_GET['table']) && $_GET['table'] == 'schedule') { // Schedule
    $date = isset($_GET['day']) ? date('Y-m-d', strtotime($_GET['day'])) : '';
    $day = date('w', strtotime($date));

    $sorters = array();
    $orderby = "start_time";
    $sql_where_schedule = "";
    $sql_where_makeup = "";
    $sql_where_check = "";
    $sql_conds_schedule = "";
    $sql_conds_makeup = "";
    $sql_conds_check = "";
    $sql_where_array_schedule = array();
    $to_encode = array();
    $output = "";
    $total_query = 0;

    $initial_schedule = array();
    $data_schedule = array();

    $start_time = 'DATE_FORMAT(start_time, "%h:%i %p")';
    $end_time = 'DATE_FORMAT(end_time, "%h:%i %p")';
    $timeFrame = 'CONCAT(' . $start_time . '," - ",' . $end_time . ') as time_frame';

    // Schedule
    $table_schedule = "schedule";
    $sql_where_array_schedule[] = "day = '" . $day . "'";
    $dbfield_schedule = array('id', 'CONCAT(userInfo.f_name," ",userInfo.l_name) as name', 'tableInfo.employee_id', 'day', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'semester', 'active', 'start_date', 'end_date');
    $dborig_schedule = array('id', 'name', 'employee_id', 'day', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'semester', 'active', 'start_date', 'end_date', 'check_status', 'series_num');
    $field_query_schedule = implode(", ", $dbfield_schedule);
    if (count($sql_where_array_schedule) > 0) {
        $sql_where_schedule = implode(" AND ", $sql_where_array_schedule);
    }
    $sql_conds_schedule = (empty($sql_where_schedule)) ? '' : 'WHERE ' . $sql_where_schedule;

    $default_query_schedule = "SELECT " . $field_query_schedule . ", " . $timeFrame . " FROM " . $table_schedule . " as tableInfo LEFT JOIN (SELECT f_name,l_name,employee_id FROM users) as userInfo ON tableInfo.employee_id = userInfo.employee_id " . $sql_conds_schedule . " ORDER BY $orderby";

    if ($sched_query = call_mysql_query($default_query_schedule)) {
        if ($num = call_mysql_num_rows($sched_query)) {
            while ($data = call_mysql_fetch_array($sched_query)) {
                $data = array_html($data);
                $initial_schedule[] = $data;
            }
        }
    }

    foreach ($initial_schedule as $value) {
        $employee_id = $value['employee_id'];
        $section = $value['section'];
        $subject_code = $value['subject_code'];
        $sched_day = $value['day'];

        // Physical Checking
        $table_physicalChecking = "physical_checking";
        $sql_where_array_check = array("date = '" . $date . "'", "employee_id = '" . $employee_id . "'", "section = '" . $section . "'", "subject_code = '" . $subject_code . "'");
        $dbfield_physicalCheck = array('id', 'employee_id', 'room', 'section', 'subject_code', 'date', 'date_check', 'remarks', 'check_status', 'series_num');
        $dborig_physicalCheck = array('id', 'employee_id', 'room', 'date', 'date_check', 'remarks', 'check_status');
        $field_query_makeup = implode(", ", $dbfield_physicalCheck);
        if (count($sql_where_array_check) > 0) {
            $sql_where_check = implode(" AND ", $sql_where_array_check);
        }
        $sql_conds_check = (empty($sql_where_check)) ? '' : 'WHERE ' . $sql_where_check;

        $default_query_check = "SELECT " . $field_query_makeup . " FROM " . $table_physicalChecking . " " . $sql_conds_check . "";
        $check_status = '';
        $series_num = '';
        if ($query = call_mysql_query($default_query_check)) {
            if ($num = call_mysql_num_rows($query)) {
                $data = call_mysql_fetch_array($query);
                $check_status = $data['check_status'];
                $date_check = $data['date_check'];
                $series_num = $data['series_num'] != null ? $data['series_num'] : '';
            } else {
                $check_status = '0';
                $date_check = '-';
            }
        }

        // MakeUp Class
        $table_makeup = "makeup_class";
        $sql_where_array_makeup = array("tableInfo.employee_id = '" . $employee_id . "'", "section = '" . $section . "'", "subject_code = '" . $subject_code . "'", "date = '" . $date . "'");
        $dbfield_makeup = array('id', 'CONCAT(userInfo.f_name," ",userInfo.l_name) as name', 'tableInfo.employee_id', 'date', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'check_remark');
        $dborig_makeup = array('id', 'employee_id', 'date', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'check_remark', 'check_status', 'series_num');
        $field_query_makeup = implode(", ", $dbfield_makeup);
        if (count($sql_where_array_makeup) > 0) {
            $sql_where_makeup = implode(" AND ", $sql_where_array_makeup);
        }
        $sql_conds_makeup = (empty($sql_where_makeup)) ? '' : 'WHERE ' . $sql_where_makeup;

        $default_query_makeup = "SELECT " . $field_query_makeup . ", " . $timeFrame . " FROM " . $table_makeup . " as tableInfo LEFT JOIN (SELECT f_name,l_name,employee_id FROM users) as userInfo ON tableInfo.employee_id = userInfo.employee_id " . $sql_conds_makeup . " ORDER BY $orderby";
        if ($query = call_mysql_query($default_query_makeup)) {
            if ($num = call_mysql_num_rows($query)) {
                while ($data = call_mysql_fetch_array($query)) {
                    $data = array_html($data);
                    $data['remarks'] = 'Make-up Class';
                    $data['check_status'] = $check_status;
                    $data['date_check'] = $date_check;
                    $data['series_num'] = $series_num;
                    $data_schedule[] = $data;
                }
            } else {
                $value['remarks'] = 'Scheduled';
                $value['check_status'] = $check_status;
                $value['date_check'] = $date_check;
                $value['series_num'] = $series_num;
                $data_schedule[] = $value;
            }
        }
    }
    $output = json_encode(["data" => $data_schedule]);
    echo $output;
    exit();


    // $date = isset($_GET['day']) ? date('Y-m-d', strtotime($_GET['day'])) : '';
    // $day = date('w', strtotime($date));
    // $sorters = array();
    // $orderby = "start_time ASC";
    // $sql_where_schedule = "";
    // $sql_where_makeup = "";
    // $sql_conds_sched = "";
    // $sql_conds_makeup = "";
    // $sql_where_array_schedule = array("day = '" . $day . "'", "active = '1'");
    // $sql_where_array_makeup = array("date = '" . $date . "'");
    // $to_encode = array();
    // $to_encode_schedule = array(); //not use
    // $to_encode_makeup = array(); //not use
    // $output = "";
    // $total_query = 0;

    // // Schedule
    // $table_schedule = "schedule";
    // $dbfield_schedule = array('id', 'CONCAT(userInfo.f_name," ",userInfo.l_name) as name', 'tableInfo.employee_id', 'day', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'semester', 'active', 'start_date', 'end_date', 'check_status', 'series_num');
    // $dborig_schedule = array('id', 'name', 'employee_id', 'day', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'semester', 'active', 'start_date', 'end_date', 'check_status', 'series_num');

    // // MakeUp Class
    // $table_makeup = "makeup_class";
    // $dbfield_makeup = array('id', 'CONCAT(userInfo.f_name," ",userInfo.l_name) as name', 'tableInfo.employee_id', 'date', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'check_remark', 'check_status', 'series_num');
    // $dborig_makeup = array('id', 'name', 'employee_id', 'date', 'start_time', 'end_time', 'section', 'room', 'subject_name', 'subject_code', 'check_remark', 'check_status', 'series_num');
    // $field_query_makeup = implode(", ", $dbfield_makeup);


    // if (count($sql_where_array_schedule) > 0) {
    //     $sql_where_schedule = implode(" AND ", $sql_where_array_schedule);
    // }
    // if (count($sql_where_array_makeup) > 0) {
    //     $sql_where_makeup = implode(" AND ", $sql_where_array_makeup);
    // }
    // // echo $sql_where_makeup;
    // $start_time = 'DATE_FORMAT(start_time, "%h:%i %p")';
    // $end_time = 'DATE_FORMAT(end_time, "%h:%i %p")';
    // $timeFrame = 'CONCAT(' . $start_time . '," - ",' . $end_time . ') as time_frame';
    // $field_query_schedule = implode(", ", $dbfield_schedule);
    // $sql_conds_schedule = (empty($sql_where_schedule)) ? '' : 'WHERE ' . $sql_where_schedule;
    // $sql_conds_makeup = (empty($sql_where_makeup)) ? '' : 'WHERE ' . $sql_where_makeup;


    // $default_query_schedule = "SELECT " . $field_query_schedule . ", " . $timeFrame . " FROM " . $table_schedule . " as tableInfo LEFT JOIN (SELECT f_name,l_name,employee_id FROM users) as userInfo ON tableInfo.employee_id = userInfo.employee_id " . $sql_conds_schedule . " ORDER BY $orderby ";

    // $default_query_makeup = "SELECT " . $field_query_makeup . ", " . $timeFrame . " FROM " . $table_makeup . " as tableInfo LEFT JOIN (SELECT f_name,l_name,employee_id FROM users) as userInfo ON tableInfo.employee_id = userInfo.employee_id " . $sql_conds_makeup . " ORDER BY $orderby ";

    // if (($query_schedule = call_mysql_query($default_query_schedule)) && ($query_makeup = call_mysql_query($default_query_makeup))) {
    //     if ($num_schedule = call_mysql_num_rows($query_schedule)) {
    //         while ($data_schedule = call_mysql_fetch_array($query_schedule)) {
    //             $data_schedule = array_html($data_schedule);
    //             $data_schedule['remarks'] = 'Scheduled';
    //             $to_encode[] = $data_schedule;
    //             // echo $data_schedule;
    //         }
    //     }
    //     if ($num_makeup = call_mysql_num_rows($query_makeup)) {
    //         while ($data_makeup = call_mysql_fetch_array($query_makeup)) {
    //             $data_makeup = array_html($data_makeup);
    //             $data_makeup['remarks'] = 'Make-Up Class';
    //             $to_encode[] = $data_makeup;
    //         }
    //     }

    //     // sort($to_encode);
    //     $output = json_encode(["data" => $to_encode]);
    // } else {
    //     $output = json_encode(["data" => []]);
    // }

    // echo $output;
    // exit();
}


if (isset($_POST['actionStatus']) && $_POST['actionStatus'] == 'checkStatus') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $employeeId = isset($_POST['employeeId']) ? $_POST['employeeId'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
    $section = isset($_POST['section']) ? $_POST['section'] : '';
    $subject_code = isset($_POST['subject_code']) ? $_POST['subject_code'] : '';
    $num = 1;
    $series_number = serial_number();
    for ($i = 0; $i < $num; $i++) {
        $check_sql = "SELECT series_num FROM physical_checking WHERE series_num='$series_number'";
        if ($check_query = call_mysql_query($check_sql)) {
            if (mysqli_num_rows($check_query) > 0) {
                $num++;
                $series_number = serial_number();
            } else {
                $series_num =  $series_number;
            }
        }
    }

    $insert_sql = "INSERT INTO physical_checking (employee_id,room,section,subject_code,remarks,check_status,series_num) VALUES ('" . $employeeId . "','" . $room . "','" . $section . "','" . $subject_code . "','" . $remarks . "','" . $status . "','" . $series_num . "')";
    if ($insert_query = call_mysql_query($insert_sql)) {
        // if ($remarks == 0) {
        //     $update_sql = "UPDATE schedule SET check_status='$status',series_num='$series_num' WHERE id='$id'";
        //     if ($update_query = call_mysql_query($update_sql)) {
        //     } else {
        //     }
        // } elseif ($remarks == 1) {
        //     $update_sql = "UPDATE makeup_class SET check_remark='1',check_status='$status',series_num='$series_num' WHERE id='$id'";
        //     if ($update_query = call_mysql_query($update_sql)) {
        //     } else {
        //     }
        // }
    } else {
    }
    header("location: " . BASE_URL . "app_admin/physical_checking.php");
    exit();
}

if (isset($_POST['actionStatus']) && $_POST['actionStatus'] == 'updateStatus') {
    $employeeId = isset($_POST['employeeId']) ? $_POST['employeeId'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
    $series_num = isset($_POST['series_num']) ? $_POST['series_num'] : '';
    $section = isset($_POST['section']) ? $_POST['section'] : '';
    $subject_code = isset($_POST['subject_code']) ? $_POST['subject_code'] : '';
    // echo $series_num;

    $check_sql = "UPDATE physical_checking SET check_status='$status',date_check='$date_time' WHERE series_num='$series_num'";
    if ($check_query = call_mysql_query($check_sql)) {
        // if ($remarks == 0) {
        //     $update_sql = "UPDATE schedule SET check_status='$status' WHERE series_num='$series_num'";
        //     if ($update_query = call_mysql_query($update_sql)) {
        //     } else {
        //     }
        // } elseif ($remarks == 1) {
        //     $update_sql = "UPDATE makeup_class SET check_remark='1',check_status='$status' WHERE series_num='$series_num'";
        //     if ($update_query = call_mysql_query($update_sql)) {
        //     } else {
        //     }
        // }
    } else {
    }
    header("location: " . BASE_URL . "app_admin/physical_checking.php");
    exit();
}


// if (isset($_GET['filters'])) { // for filters
//     /**
//      * Filters the data based on the filter value and field name
//      * @return array $sql_where_array - array of the filtered data
//      */
//     $filters = array();
//     $sort_filters = array();
//     $filters = $_GET['filters'];
//     // This loop will filter the data based on the filter value and field name
//     foreach ($filters as $filter) {
//         if (isset($filter['field'])) {
//             $id = $filter['field'];
//             $sort_filters[$id] = $filter['value'];
//         }
//     }

//     //    foreach($dborigPhysicalCheck as $id){
//     //        if(isset($sort_filters[$id])){
//     //            $sql_where_array[] = $id." = '".$sort_filters[$id]."'";
//     //        }
//     //    }

//     foreach ($dborigSchedule as $id) { //condition sql
//         if (isset($sort_filters[$id])) {
//             if ($id == 'name') {
//                 $sql_where_array[] = $id . " LIKE '%" . $sort_filters[$id] . "%'";
//                 continue;
//             }

//             $sql_where_array[] = $id . " = '" . $sort_filters[$id] . "'";
//         }
//     }
// }