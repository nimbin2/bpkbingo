<?php
// statements get
$servername = "localhost";
$username = "bpkbingo";
$password = "DAJZ2wd34uadu14sUia5wd";
$dbname = "bpkbingo_db";o


$conn = new mysqli($servername, $username, $password, $dbname);

statements= [];
if($statements_set = $conn->query("SELECT * FROM statements")) {
    while ($row = $statements_set->fetch_assoc()) {
        $groupId = $row['group_id'];
        $groupPosition = array_search("$groupId",array_column($groups, "id"));
        $groupName = $groups["$groupPosition"]['nameS'];
//      $groupName = $groups[$row['group_id']]['nameS'];
        $statements[] = [ 'id'=> $row['id'], 'statement'=> $row['statement'], 'group'=> $groupName, 'rank_
up'=> $row['rank_up'], 'rank_down'=> $row['rank_down']];
    }
    $statements_length = count($statements);
}

?>
