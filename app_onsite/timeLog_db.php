<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CL_SESSION_PATH;
require CONNECT_PATH;
require ISLOGIN;

function getDataFromDatabase() {


    //check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // sql query to fetch data
    $query = "SELECT * FROM time_log";

    // execute the query
    $result = $conn->query($query);

    // array to hold the data
    $data = array();

    //loop through the data
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // close the connection
    $conn->close();

    // return the data
    return $data;
}

if (isset($_POST['download-csv'])) {
    // filename for download
    $filename = "data_" . date('Ymd') . ".csv";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/csv; ");

    // get data from the database
    $data = getDataFromDatabase();

    // open the file "data.csv" for writing
    $file = fopen('php://output', 'w');

    // save the column headers
    fputcsv($file, array('Column 1', 'Column 2', 'Column 3'));

    // save each row of the data
    foreach ($data as $row) {
        fputcsv($file, $row);
    }

    // Close the file
    fclose($file);
    exit;
}


$session_class->session_close();
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')){
    include HTTP_404;
    exit();
}

$query_limit = QUERY_LIMIT;

$table_name= "time_log";
$field_query ='*';
$pages =0;
$start = 0;
$size = 0;

$sorters =array();
$orderby ="id DESC";
$sql_where="";
$sql_conds="";
$sql_where_array=array();
$to_encode=array();
$output="";
$total_query = 0;

$dbfield = array('id', 'employee_id', 'card_id', 'time_in', 'time_out', 'break_in', 'break_out', 'building', 'way', 'date_log'); // need iset based sa table columns

//$dborig = array('user_id','name','firstname','lastname','id','time_in','time_out','break_in','break_out','building','way');
$dborig = array('id', 'employee_id', 'card_id', 'time_in', 'time_out', 'break_in', 'break_out', 'building', 'way', 'date_log');

if(isset($_GET['filters'])){
    /**
     * Filters the data based on the filter value and field name
     * @return array $sql_where_array - array of the filtered data
     */
    $filters =array();
    $sort_filters =array();
    $filters = $_GET['filters'];
    // This loop will filter the data based on the filter value and field name
    foreach($filters as $filter){
        if(isset($filter['field'])){
            if($filter['field'] == 'user_role'){
                if(is_array($filter['value'])){
                    $filter['value'] = $filter['value'][0];
                }
                if($filter['value']==0){
                    continue;
                }
            }
            $id = $filter['field'];
            $sort_filters[$id] = $filter['value'];
        }
    }
    foreach($dborig as $id){
        if(isset($sort_filters[$id])){
            $value =escape($db_connect,$sort_filters[$id]);
            if($id == "name"){
                $sql_where_array[] = 'employee_id LIKE \'%' . $value . '%\'';
                continue;
            }
            $sql_where_array[] = $id . ' LIKE \'%' . $value . '%\'';
        }
    }

    // This will create the where clause for the query based on the filtered data array
    if(count($sql_where_array)>0){
        $sql_where = implode(" AND ",$sql_where_array);
    }
}

// filter status
// array_push($sql_where_array,'status !=1');
// if(!empty($sql_where_array)){
// 	$temp_arr = implode(' AND ',$sql_where_array);
// 	$sql_where = (empty($temp_arr)) ? '' : $temp_arr;
// }

if(isset($_GET['sorters'])){
    $sorters = $_GET['sorters'];
    $tag =array('asc','desc');
    if(in_array($sorters[0]['field'],$dborig) AND in_array($sorters[0]['dir'],$tag)){
        $orderby = $sorters[0]['field'].' '.$sorters[0]['dir'];
    }
}


if(isset($_GET['size']) AND is_digit($_GET['size'])){
    $query_limit = ($_GET['size'] > $query_limit) ? $_GET['size'] : $query_limit;
}

//total query counter
$field_query ='COUNT(DISTINCT id) as count'; // baguhin based sa need
$sql_conds = (empty($sql_where)) ? '' : 'WHERE '.$sql_where;
$default_query ="SELECT ".$field_query." FROM ".$table_name." ".$sql_conds;
if($query = call_mysql_query($default_query)){
    if($num = call_mysql_num_rows($query)){
        while($data = call_mysql_fetch_array($query)){
            $total_query = $data['count'];
        }
    }
}
// page button
 $pages= ($total_query===0) ? 1 : ceil($total_query/$query_limit);
 if(isset($_GET['page']) AND is_digit($_GET['page'])){
 	$page_no = $_GET['page'] - 1;
 	$start = $page_no * $query_limit;
 }

$start_no = ($start >= $total_query) ? $total_query : $start;

$field_query = implode(',',$dbfield);
$sql_conds = (empty($sql_where)) ? '' : 'WHERE '.$sql_where; // ichange based sa need
$default_query ="SELECT ".$field_query." FROM ".$table_name." ".$sql_conds." ORDER BY ".$orderby;
$limit=" LIMIT ". $start_no.",".$query_limit;
$sql_limit=$default_query.' '.$limit;
if($query = call_mysql_query($sql_limit)){
    if($num = call_mysql_num_rows($query)){
        while($data = call_mysql_fetch_array($query)){
            $data = array_html($data);
            $data['id'] = $data['id'];
            $to_encode[] = $data;
        }
    }
    $output = json_encode(["last_page"=>$pages, "data"=>$to_encode,"total_record"=>$total_query]);
}else{
    $output =  json_encode(["last_page"=>0, "data"=>"","total_record"=>0]);
}

echo $output; //output
