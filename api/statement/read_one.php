<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/statement.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare statement object
$statement = new Statement($db);
  
// set ID property of record to read
$statement->id = isset($_GET['id']) ? $_GET['id'] : die();
  
// read the details of statement to be edited
$statement->readOne();
  
if($statement->statement!=null){
    // create array
    $statement_arr = array(
        "id" =>  $statement->id,
        "statement" => $statement->statement,
        "group_id" => $statement->group_id,
        "rank_up" => $statement->rank_up,
        "rank_down" => $statement->rank_down
  
    );
  
    // set response code - 200 OK
    http_response_code(200);
  
    // make it json format
    echo json_encode($statement_arr);
}
  
else{
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user statement does not exist
    echo json_encode(array("message" => "Statement does not exist."));
}
?>
