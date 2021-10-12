<?php
class Statement {
	
	private $conn;
	private $table_name = "statements";

	public $id;
	public $statement;
	public $group_id;
	public $rank_up;
	public $rank_down;

	//
	// database connection and table name
	public function __construct($db) {
		$this->conn = $db;
	}

	// read statements
	function read(){
	  
		// select all query
		$query = "SELECT *
				FROM
					" . $this->table_name . "";
	  
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	  
		// execute query
		$stmt->execute();
	  
		return $stmt;
	}

	// used when filling up the update statement form
	function readOne(){

		// query to read single record
		$query = "SELECT *
				FROM
					" . $this->table_name . " 
				WHERE
					id = ?
				LIMIT
					0,1";
	  
		// prepare query statement
		$stmt = $this->conn->prepare( $query );
	  
		// bind id of statement to be updated
		$stmt->bindParam(1, $this->id);
	  
		// execute query
		$stmt->execute();
	  
		// get retrieved row
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	  
		// set values to object properties
		$this->statement = $row['statement'];
		$this->group_id = $row['group_id'];
		$this->rank_up = $row['rank_up'];
		$this->rank_down = $row['rank_down'];
	}

	// create statement
	function create() {
		$query = "INSERT INTO 
					" . $this->table_name . "
					SET
						statement=:statement, group_id=:group_id, rank_up=:rank_up, rank_down=:rank_down";
		$stmt = $this->conn->prepare($query);

		// sanitize
			$this->statement=htmlspecialchars(strip_tags($this->statement));
			$this->group_id=htmlspecialchars(strip_tags($this->group_id));
			$this->rank_up=htmlspecialchars(strip_tags($this->rank_up));
			$this->rank_down=htmlspecialchars(strip_tags($this->rank_down));

		//
		// post values
		$this->statement=htmlspecialchars(strip_tags($this->statement));
		$this->group_id=htmlspecialchars(strip_tags($this->group_id));
		$this->rank_up=htmlspecialchars(strip_tags($this->rank_up));
		$this->rank_down=htmlspecialchars(strip_tags($this->rank_down));

		//
		//bind values
		$stmt->bindParam(":statement", $this->statement);
		$stmt->bindParam(":group_id", $this->group_id);
		$stmt->bindParam(":rank_up", $this->rank_up);
		$stmt->bindParam(":rank_down", $this->rank_down);

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
	}

	// update the statement
	function update(){
	  
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					statement= :statement,
					group_id = :group_id,
					rank_up = :rank_up,
					rank_down = :rank_down
				WHERE
					id = :id";
	  
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	  
		// sanitize
		$this->statement=htmlspecialchars(strip_tags($this->statement));
		$this->group_id=htmlspecialchars(strip_tags($this->group_id));
		$this->rank_up=htmlspecialchars(strip_tags($this->rank_up));
		$this->rank_down=htmlspecialchars(strip_tags($this->rank_down));
		$this->id=htmlspecialchars(strip_tags($this->id));
	  
		// bind new values
		$stmt->bindParam(':statement', $this->statement);
		$stmt->bindParam(':group_id', $this->group_id);
		$stmt->bindParam(':rank_up', $this->rank_up);
		$stmt->bindParam(':rank_down', $this->rank_down);
		$stmt->bindParam(':id', $this->id);
	  
		// execute the query
		if($stmt->execute()){
			return true;
		}
	  
		return false;
	}


	// rank up
	function rankUp() {

		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					rank_up = :rank_up
				WHERE
					id = :id";
	  
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	  
		// sanitize
		$this->id=htmlspecialchars(strip_tags($this->id));
		$this->rank_up=htmlspecialchars(strip_tags($this->rank_up));
	  
		// bind new values
		$stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':rank_up', $this->rank_up);
	  
		// execute the query
		if($stmt->execute()){
			return true;
		}
	  
		return false;
	}

	// rank down
	function rankDown() {

		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					rank_down = :rank_down
				WHERE
					id = :id";
	  
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	  
		// sanitize
		$this->id=htmlspecialchars(strip_tags($this->id));
		$this->rank_down=htmlspecialchars(strip_tags($this->rank_down));
	  
		// bind new values
		$stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':rank_down', $this->rank_down);
	  
		// execute the query
		if($stmt->execute()){
			return true;
		}
	  
		return false;
	}

	// search products
	function search($keywords){
	  
		// select all query
		$query = "SELECT *
				FROM
					" . $this->table_name . " p
				WHERE
					statement LIKE ? OR group_id LIKE ?
				ORDER BY
					id DESC";
	  
		// prepare query statement
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
