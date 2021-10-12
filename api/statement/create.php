<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
// get database connection
include_once '../config/database.php';
  
// instantiate statement object
include_once '../objects/statement.php';
  
$database = new Database();
$db = $database->getConnection();
  
$statement = new Statement($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
if(
    !empty($data->statement) &&
    !empty($data->group_id) &&
    is_numeric($data->rank_up) &&
	is_numeric($data->rank_down)
){
  
    // set statement property values
    $statement->statement = $data->statement;
	$statement->group_id = $data->group_id;
    $statement->rank_up = $data->rank_up;
    $statement->rank_down = $data->rank_down;
  
    // create the statement
    if($statement->create()){
  
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "Statement was created."));
    }
  
    // if unable to create the statement, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create statement."));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    // echo json_encode(array("message" => "Unable to create statement. Data is incomplete."));
	echo json_encode(array("message" => "Unable to create statement."));
}
?>
