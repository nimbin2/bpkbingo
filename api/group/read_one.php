<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/group.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare group object
$group = new Group($db);
  
// set ID property of record to read
$group->id = isset($_GET['id']) ? $_GET['id'] : die();
  
// read the details of group to be edited
$group->readOne();
  
if($group->id!=null){
    // create array
    $group_arr = array(
        "id" =>  $group->id,
        "name_short" => $group->name_short,
        "name_long" => $group->name_long
    );
  
    // set response code - 200 OK
    http_response_code(200);
  
    // make it json format
    echo json_encode($group_arr);
}
  
else{
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user group does not exist
    echo json_encode(array("message" => "Group does not exist. $group->id"));
}
?>
