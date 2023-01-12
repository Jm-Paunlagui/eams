<?php
require 'config/config.php';
require GLOBAL_FUNC;
require CL_SESSION_PATH;
require CONNECT_PATH;

$csrf = new CSRF($session_class);
$token_1 = $csrf->validate('token_login_admin_form', $_POST['token_login_admin_form']);
if ($token_1) {
} else {
    $error = true;
    $msg_response = array();
    $msg_response['status'] = "error";
    $msg_response['msg'] = "Invalid Auth-Token";
    $session_class->setValue('error', $msg_response['msg']);
    header('Location: ' . BASE_URL . 'index.php' . $reset_password);
    exit();
}

if (isset($_POST['user_login'])) {
    if (isset($_POST['username']) and isset($_POST['password'])) {

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $agent = isset($_POST['agents']) ? json_decode($_POST['agents'], true) : array();
        //var_dump($agent);
        //exit();
        if (!empty($username) and !empty($password)) {

            $password = set_password($password);
            $query = "SELECT `user_id`,`username`,`password`,`f_name`,`m_name`,`l_name`,`suffix`,`img`,`user_role`,`status`,`locked`,`position` FROM `users` WHERE `username`='$username' LIMIT 1";
            $result = mysqli_query($db_connect, $query);
            $num_row = mysqli_num_rows($result);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

            $session_class->incValue('browser_attempt_login', 1);
            $error_login = false;

            if ($num_row == 0) {
                $session_class->setValue('msg_error', 'Please Check your username');
            } else if ($num_row > 0) {
                $f_name = $row['f_name'] != null || $row['f_name'] != ''  ? $row['f_name'] : '';
                $m_name = $row['m_name'] != null || $row['m_name'] != '' ? $row['m_name'] : '';
                $l_name = $row['l_name'] != null || $row['l_name'] != '' ? $row['l_name'] : '';
                $suffix = $row['suffix'] != null || $row['suffix'] != '' ? $row['suffix'] : '';
                $fullname = $f_name  . " " . ($m_name != '' ? substr($m_name, 0, 1) . '. ' : '') . $l_name . " " . $suffix;
                $position = $row['position'] != null ? $row['position'] : '';

                if ($row['status'] == 1) {
                    $session_class->setValue('msg_error', 'Deactivated Account');
                } else {

                    $row['img']  = empty(trim($row['img'])) ? "" : BASE_URL . "assets/img/" . $row['img'];

                    $login_attempt = 0;
                    if ($row['password'] != $password) {
                        $session_class->setValue('last_user', sha1($username));
                        $session_class->incValue("login_attempt_" . sha1($username), 1);
                        $error_login = true;
                        $session_class->setValue('msg_error', 'Please check Username and Password!');

                        $login_attempt =  $session_class->getValue('login_attempt_' . sha1($username));
                    }
                    if ($row['locked'] == 1) {
                        $session_class->setValue('msg_error', 'Account ' . var_html($username) . ' has been locked. Please reset password or Contact System Admin.!');
                    } else if (!empty($login_attempt) and $login_attempt >= 5) {
                        $session_class->setValue('msg_error', 'Account ' . var_html($username) . ' has been locked. Please reset password or Contact System Admin.!');
                        $locked_query = "UPDATE users SET locked = '1' WHERE user_id = '" . $row['user_id'] . "'";
                        if (mysqli_query($db_connect, $locked_query)) {
                        }
                    } elseif ($row['user_role'] == 1 and !$error_login) { //admin
                        $session_class->setValue('user_id', $row['user_id']);
                        $session_class->setValue('employee_id', $row['employee_id']);
                        $session_class->setValue('role_id', 'ADMIN');
                        $session_class->setValue('photo', $row['img']);
                        $session_class->setValue('fullname', $fullname);
                        $session_class->setValue('f_name', $f_name);
                        $session_class->setValue('m_name', $m_name);
                        $session_class->setValue('l_name', $l_name);
                        $session_class->setValue('suffix', $suffix);
                        $session_class->setValue('position', $position);
                        $session_class->setValue('agent_browser', $agent);
                        $fingerprint = $session_class->getValue('fingerprint');
                        $session_class->setValue('browser_fingerprint', $fingerprint);
                        $session_class->setValue('msg_success', "Welcome to EAMS");
                        $session_class->dropValue('browser_attempt_login');
                        user_log("LOGIN", $agent);
                        header("location: " . BASE_URL . "app_main/index.php"); // location to admin page
                        exit();
                    } elseif ($row['user_role'] == 2 and !$error_login) { //admin staff
                        $session_class->setValue('user_id', $row['user_id']);
                        $session_class->setValue('role_id', 'ADMIN_STAFF');
                        $session_class->setValue('photo', $row['img']);
                        $session_class->setValue('fullname', $fullname);
                        $session_class->setValue('f_name', $f_name);
                        $session_class->setValue('m_name', $m_name);
                        $session_class->setValue('l_name', $l_name);
                        $session_class->setValue('suffix', $suffix);
                        $session_class->setValue('position', $position);
                        $session_class->setValue('agent_browser', $agent);
                        $fingerprint = $session_class->getValue('fingerprint');
                        $session_class->setValue('browser_fingerprint', $fingerprint);
                        $session_class->setValue('msg_success', "Welcome to EAMS");
                        $session_class->dropValue('browser_attempt_login');
                        user_log("LOGIN", $agent);
                        header("location: " . BASE_URL . "app_admin/index.php");
                        exit();
                    } elseif ($row['user_role'] == 3 and !$error_login) { //onsite
                        $session_class->setValue('user_id', $row['user_id']);
                        $session_class->setValue('employee_id', $row['employee_id']);
                        $session_class->setValue('role_id', 'ONSITE');
                        $session_class->setValue('photo', $row['img']);
                        $session_class->setValue('fullname', $fullname);
                        $session_class->setValue('f_name', $f_name);
                        $session_class->setValue('m_name', $m_name);
                        $session_class->setValue('l_name', $l_name);
                        $session_class->setValue('suffix', $suffix);
                        $session_class->setValue('position', $position);
                        $session_class->setValue('agent_browser', $agent);
                        $fingerprint = $session_class->getValue('fingerprint');
                        $session_class->setValue('browser_fingerprint', $fingerprint);
                        $session_class->setValue('msg_success', "Welcome to EAMS");
                        $session_class->dropValue('browser_attempt_login');
                        user_log("LOGIN", $agent);
                        $session_class->setValue('onsite_title', 'Welcome to EAMS');
                        $session_class->setValue('onsite_msg', $name);
                        header("location: " . BASE_URL . "app_onsite/index.php");
                        exit();
                    } elseif ($row['user_role'] == 4 and !$error_login) { //enduser
                        $session_class->setValue('user_id', $row['user_id']);
                        $session_class->setValue('employee_id', $row['employee_id']);
                        $session_class->setValue('role_id', 'END_USER');
                        $session_class->setValue('photo', $row['img']);
                        $session_class->setValue('fullname', $fullname);
                        $session_class->setValue('f_name', $f_name);
                        $session_class->setValue('m_name', $m_name);
                        $session_class->setValue('l_name', $l_name);
                        $session_class->setValue('suffix', $suffix);
                        $session_class->setValue('position', $position);
                        $session_class->setValue('agent_browser', $agent);
                        $fingerprint = $session_class->getValue('fingerprint');
                        $session_class->setValue('browser_fingerprint', $fingerprint);
                        $session_class->setValue('msg_success', "Welcome to EAMS");
                        $session_class->dropValue('browser_attempt_login');
                        user_log("LOGIN", $agent);
                        header("location: " . BASE_URL . "app_user/index.php");
                        exit();
                    }
                }
            }
        } else {
            $session_class->setValue('msg_error', 'Invalid Input');
            header("location: " . BASE_URL . "index.php");
            exit();
        }
    }
    header("location: " . BASE_URL . "index.php");
    exit();
}
