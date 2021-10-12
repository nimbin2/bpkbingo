<?php

$servername = "localhost";
$username = "bpkbingo";
$password = "DAJZ2wd34uadu14sUia5wd";
$dbname = "bpkbingo_db";

$statement = $_POST['statement'];
$group = $_POST['group'];

// Create conn
$conn = new mysqli($servername, $username, $password, $dbname);

// statement submit
$sql = "INSERT INTO statements (group_id, statement, rank_up, rank_down)
	VALUES ('$group', '$statement', 10, 0)";
if ($conn->query($sql) === TRUE) {
	echo "New record created successfully";
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}


echo json_encode(array("abc"=>'successfuly registered'));
?>
