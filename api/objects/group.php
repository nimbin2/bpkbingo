<?php
class Group {
	
	private $conn;
	private $table_name = "groups";

	public $id;
	public $name_short;
	public $name_long;

	//
	// database connection and table name
	public function __construct($db) {
		$this->conn = $db;
	}

	// read groups
	function read(){
	  
		// select all query
		$query = "SELECT *
				FROM
					" . $this->table_name . "";
	  
		// prepare query group
		$stmt = $this->conn->prepare($query);
	  
		// execute query
		$stmt->execute();
	  
		return $stmt;
	}

	// used when filling up the update group form
	function readOne(){
	  
		// query to read single record
		$query = "SELECT *
				FROM
					" . $this->table_name . " 
				WHERE
					id = ?
				LIMIT
					0,1";
	  
		// prepare query group
		$stmt = $this->conn->prepare( $query );
	  
		// bind id of group to be updated
		$stmt->bindParam(1, $this->id);
	  
		// execute query
		$stmt->execute();
	  
		// get retrieved row
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	  
		// set values to object properties
		$this->name_short = $row['name_short'];
		$this->name_long = $row['name_long'];
	}

	// search products
	function search($keywords){
	  
		// select all query
		$query = "SELECT *
				FROM
					" . $this->table_name . " p
				WHERE
					name_short LIKE ? OR name_long LIKE ?
				ORDER BY
					id DESC";
	  
		// prepare query group
		$stmt = $this->conn->prepare($query);
	  
		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";
	  
		// bind
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);
	  
		// execute query
		$stmt->execute();
	  
		return $stmt;
	}
}
