<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/group.php';
  
// instantiate database and group object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$group = new Group($db);
  
// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";
  
// query groups
$stmt = $group->search($keywords);
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // groups array
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
  
    // show groups data
    echo json_encode($groups_arr);
}
  
else{
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no groups found
    echo json_encode(
        array("message" => "No groups found.")
    );
}
?>
