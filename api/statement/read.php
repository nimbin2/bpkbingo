<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/statement.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$statement = new Statement($db);
  
// query products
$stmt = $statement->read();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // products array
    $statements_arr=array();
    $statements_arr["statements"]=array();
  
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $statement_item=array(
            "id" => $id,
            "statement" => $statement,
            "group_id" => $group_id,
            "rank_up" => $rank_up,
            "rank_down" => $rank_down
        );
  
        array_push($statements_arr["statements"], $statement_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show products data in json format
    echo json_encode($statements_arr);
}
else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no statements found
    echo json_encode(
        array("message" => "No statements found.")
    );
}
