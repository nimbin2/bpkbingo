<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/statement.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare statement object
$statement = new Statement($db);
  
// get id of statement to be edited
$data = json_decode(file_get_contents("php://input"));
  
// set ID property of statement to be edited
$statement->id = $data->id;
  
// set statement property values
$statement->statement = $data->statement;
$statement->group_id = $data->group_id;
$statement->rank_up = $data->rank_up;
$statement->rank_down = $data->rank_down;
  
// update the statement
if($statement->update()){
  
    // set response code - 200 ok
    http_response_code(200);
  
    // tell the user
    echo json_encode(array("message" => "Statement was updated."));
}
  
// if unable to update the statement, tell the user
else{
  
    // set response code - 503 service unavailable
    http_response_code(503);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update statement."));
}
?>