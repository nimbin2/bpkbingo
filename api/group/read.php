<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/group.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$group = new Group($db);
  
// query products
$stmt = $group->read();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // products array
    $groups_arr=array();
    $groups_arr["groups"]=array();
  
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $group_item=array(
            "id" => $id,
            "name_short" => $name_short,
            "name_long" => $name_long
        );
  
        array_push($groups_arr["groups"], $group_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show products data in json format
    echo json_encode($groups_arr);
}
else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no groups found
    echo json_encode(
        array("message" => "No groups found.")
    );
}
