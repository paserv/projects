<?php
require_once 'autoload.php';
GeoLocation_autoload();

class DBModel {
	function getConnection() {
		$conn = @new mysqli ( DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE );
		if (isset ($conn) && $conn->connect_error) {
			throw new Exception($conn->connect_error, 200);
		}
		return $conn;
	}
	function getQuizData($quizId) {
		$conn = $this->getConnection();
		$sql = "SELECT * FROM quiz WHERE quiz.id = " . $quizId;
		$result = $conn->query ( $sql );
		if (!$result) {
			$conn->close ();
			throw new Exception("Impossible to reach DB for Quiz ID: " . $quizId, 200);
		} else if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$quiz = new QuizDTO($row["id"], $row["name"], $row["threshold"], $row["counter"], $row["solution"]);
			$conn->close ();
			return $quiz;
		} else {
			$conn->close ();
			return null;
		}
	}
	function incrementQuizCounter($quizId) {
		$result = $this->getQuizData($quizId);
		$counter = $result->counter;
		$counter = $counter + 1;
		$conn = $this->getConnection ();
		$sql = "UPDATE quiz SET counter = '$counter' WHERE 1";
		if ($conn->query ( $sql ) === FALSE) {
			$error = $conn->error;
			$conn->close ();
			throw new Exception ( "Error increment quiz counter " . $quizId, 200 );
		}
		$conn->close ();
	}
	function deleteUser($idUser, $sn) {
		$conn = $this->getConnection ();
		$sql = "DELETE FROM user WHERE user.socialId like " . $idUser . " AND user.socialNetwork like '" . $sn . "'";
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception("Impossible to delete User " . $idUser, 200);
		}
		$conn->close ();
	}
	function isUserRegistered($idUser, $sn) {
		$conn = $this->getConnection ();
		$sql = "SELECT * FROM user WHERE user.socialId = " . $idUser . " AND user.socialNetwork like '" . $sn . "'";
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception("Impossible check User is registered " . $idUser, 200);
		} else if ($result->num_rows > 0) {
			$conn->close ();
			return TRUE;
			}
		$conn->close ();
		return FALSE;
	}
	function searchByName($name) {
		$res = array();
		$conn = $this->getConnection ();
		$sql = "SELECT * FROM user WHERE user.name like '%" . $name . "%' limit " . DB_SEARCH_LIMIT;
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception("Impossible search User " . $name, 200);
		} else if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$currUser = new DBUser($row["socialId"], $row["name"], $row["email"], $row["lat"], $row["lng"], $row["description"], $row["socialPageUrl"], $row["avatarUrl"], $row["timestamp"], $row["socialNetwork"]);
				array_push($res, $currUser);
			}
			return $res;
		} else {
			return null;
		}
		$conn->close ();
	}
	function searchById($socialId, $sn) {
		$conn = $this->getConnection ();
		$sql = "SELECT * FROM user WHERE user.socialId = " . $socialId . " AND user.socialNetwork like '" . $sn . "'";
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception("Impossible search by ID " . $socialId . "Query: " . $sql, 200);
		} else if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$currUser = new DBUser($row["socialId"], $row["name"], $row["email"], $row["lat"], $row["lng"], $row["description"], $row["socialPageUrl"], $row["avatarUrl"], $row["timestamp"], $row["socialNetwork"]);
			$conn->close ();
			return $currUser;
		} else {
			return null;
		}
	}
	function searchByNameAndCoordsSpatial($name, $lat, $lng, $ray) {
		$res = array();
		
		if ($ray > 100) {
			$ray = 100;
		}
		
		$latDegree = $ray/100;
		$lngDegree = $ray/100;//(111*cos($lat));
		
		$conn = $this->getConnection ();
		$sql = "SELECT * FROM user WHERE Contains(GeomFromText(CONCAT('POLYGON((',X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $lngDegree .", '))')), location) HAVING user.name like '%" . $name . "%' limit " . DB_SEARCH_LIMIT;
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception("Impossible search by Name and Coords " . $name . " " . $lat . " " . $lng . " " . $ray, 200);
		} else if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$currUser = new DBUser($row["socialId"], $row["name"], $row["email"], $row["lat"], $row["lng"], $row["description"], $row["socialPageUrl"], $row["avatarUrl"], $row["timestamp"], $row["socialNetwork"]);
				array_push($res, $currUser);
			}
			return $res;
		} else {
			return null;
			//throw new Exception("No Result found in search by Name And Coords", 700);
		}
		$conn->close ();
	}
	function searchByNameAndCoords($name, $lat, $lng, $ray) {
		$res = array();
		
		if ($ray > 100) {
			$ray = 100;
		}
		
		$unit_of_measurement = 'kilometers';
		$gl = GeoLocation::fromDegrees($lat, $lng);
		$result = $gl->boundingCoordinates($ray, $unit_of_measurement);
		
		$minLat = $result[0]->getLatitudeInDegrees();
		$minLng = $result[0]->getLongitudeInDegrees();
		
		$maxLat = $result[1]->getLatitudeInDegrees();
		$maxLng = $result[1]->getLongitudeInDegrees();
		
		$conn = $this->getConnection ();
		//$sql = "SELECT * FROM user WHERE (user.lat >= " . $minLat . " AND Lat <= " . $maxLat . ") AND (user.lng >= " . $minLng . " AND user.lng <= " . $maxLng . ") HAVING user.name like '%" . $name . "%'";
		$sql = "SELECT * FROM user WHERE user.name like '%" . $name . "%' HAVING (user.lat >= " . $minLat . " AND user.lat <= " . $maxLat . ") AND (user.lng >= " . $minLng . " AND user.lng <= " . $maxLng . ") limit " . DB_SEARCH_LIMIT;
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception("Impossible search by Name and Coords " . $name . " " . $lat . " " . $lng . " " . $ray, 200);
		} else if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$currUser = new DBUser($row["socialId"], $row["name"], $row["email"], $row["lat"], $row["lng"], $row["description"], $row["socialPageUrl"], $row["avatarUrl"], $row["timestamp"], $row["socialNetwork"]);
				array_push($res, $currUser);
			}
			return $res;
		} else {
			throw new Exception("No Result found in search by Name And Coords", 700);
		}
		$conn->close ();
	}
	function addUsers($num) {
		$result = $this->countUsers();
		$result = $result + $num;
		$conn = $this->getConnection ();
		$sql = "UPDATE members SET total = '$result' WHERE 1";
		if ($conn->query ( $sql ) === FALSE) {
			$error = $conn->error;
			$conn->close ();
			throw new Exception ( "Error insert user " . $dbData->socialId, 200 );
		}
		return $result;
		$conn->close ();
	}
	function countUsers() {
		$result = 0;
		$conn = $this->getConnection ();
// 		$sql = "SELECT total FROM members";
		$sql = "SELECT count(socialId) as memberCount FROM user";
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception ( "Error Count Users", 200 );
		} else if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
// 			$result = $row["total"];
			$result = $row["memberCount"];
		}
		$conn->close ();
		return $result;
	}
	function searchByCoordsSpatial($lat, $lng, $ray) {
		$res = array();
		
		if ($ray > 100) {
			$ray = 100;
		}
		
		$latDegree = $ray/100;
		$lngDegree = $ray/100;//(111*cos($lat));
		
		$conn = $this->getConnection ();
		//$sql = "SELECT * FROM user WHERE Contains(GeomFromText(CONCAT('POLYGON((',X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $degree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $degree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $degree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $degree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $degree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $degree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $degree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $degree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $degree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $degree .", '))')), location) limit " . DB_SEARCH_LIMIT;
		$sql = "SELECT * FROM user WHERE Contains(GeomFromText(CONCAT('POLYGON((',X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) + " . $lngDegree .", ',', X(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $latDegree .", ' ', Y(GeomFromText('POINT(" . $lat . " " . $lng . ")')) - " . $lngDegree .", '))')), location) limit " . DB_SEARCH_LIMIT;
// 		echo $lat . " " . $lng . " " . $sql;
		
		$result = $conn->query ( $sql );
		
		if (!$result) {
			throw new Exception("Impossible search by Coords " . $lat . " " . $lng . " " . $ray, 200);
		} else if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$currUser = new DBUser($row["socialId"], $row["name"], $row["email"], $row["lat"], $row["lng"], $row["description"], $row["socialPageUrl"], $row["avatarUrl"], $row["timestamp"], $row["socialNetwork"]);
				array_push($res, $currUser);
			}
			return $res;
		} else {
			return null;
			//throw new Exception("No Result found in search by Coords", 700);
		}
		$conn->close ();
	}
	
	function searchByCoords($lat, $lng, $ray) {
		$res = array();
		
		if ($ray > 100) {
			$ray = 100;
		}
		
		$unit_of_measurement = 'kilometers';
		$gl = GeoLocation::fromDegrees($lat, $lng);
		$result = $gl->boundingCoordinates($ray, $unit_of_measurement);
		
		$minLat = $result[0]->getLatitudeInDegrees();
		$minLng = $result[0]->getLongitudeInDegrees();
		
		$maxLat = $result[1]->getLatitudeInDegrees();
		$maxLng = $result[1]->getLongitudeInDegrees();
		
		$conn = $this->getConnection ();
		$sql = "SELECT * FROM user WHERE (user.lat >= " . $minLat . " AND user.lat <= " . $maxLat . ") AND (user.lng >= " . $minLng . " AND user.lng <= " . $maxLng . ") limit " . DB_SEARCH_LIMIT;
// 		echo $lat . " " . $lng . " " . $sql;
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception("Impossible search by Coords " . $lat . " " . $lng . " " . $ray, 200);
		} else if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$currUser = new DBUser($row["socialId"], $row["name"], $row["email"], $row["lat"], $row["lng"], $row["description"], $row["socialPageUrl"], $row["avatarUrl"], $row["timestamp"], $row["socialNetwork"]);
				array_push($res, $currUser);
			}
			return $res;
		} else {
			throw new Exception("No Result found in search by Coords", 700);
		}
		$conn->close ();
	}
	static function escapeUrl($conn, $url) {
		$urlEscaped = $url;
		$urlEscaped = urlencode ( $urlEscaped );
		$urlEscaped = $conn->real_escape_string ( $urlEscaped );
		return $urlEscaped;
	}
	function insertUser(DBUser $dbData) {
		if ($this->isUserRegistered ( $dbData->socialId, $dbData->socialNetwork )) {
			throw new Exception ( "User Already Registered " . $dbData->socialId, 200 );
		}
		if ($this->areUsersMoreThan ( 1000000 )) {
			throw new Exception ( "One Million Users already registered " . $dbData->socialId, 200 );
		}
		$conn = $this->getConnection ();
		$avatUrl = DBModel::escapeUrl ( $conn, $dbData->avatarUrl );
		$profileUrl = DBModel::escapeUrl ( $conn, $dbData->socialPageUrl );
		$timestamp = date("Y-m-d H:i:s");
// 		$sql = "INSERT INTO user (socialId, name, email, avatarUrl, description, socialPageUrl, lat, lng, timestamp, socialNetwork) VALUES ('$dbData->socialId', '$dbData->name', '$dbData->email', '$dbData->avatarUrl', '$dbData->description', '$dbData->socialPageUrl', '$dbData->latitude', '$dbData->longitude', '$timestamp', '$dbData->socialNetwork')";
		$sql = "INSERT INTO user (socialId, name, email, avatarUrl, description, socialPageUrl, lat, lng, timestamp, socialNetwork, location) VALUES ('$dbData->socialId', '$dbData->name', '$dbData->email', '$dbData->avatarUrl', '$dbData->description', '$dbData->socialPageUrl', '$dbData->latitude', '$dbData->longitude', '$timestamp', '$dbData->socialNetwork', GeomFromText('POINT($dbData->latitude $dbData->longitude)'))";
		if ($conn->query ( $sql ) === FALSE) {
			$error = $conn->error;
			$conn->close ();
			throw new Exception ( "Error insert user " . $dbData->socialId, 200 );
		}
		$conn->close ();
	}
	function updateUser(DBUser $dbData) {
		if (!$this->isUserRegistered ( $dbData->socialId, $dbData->socialNetwork )) {
			throw new Exception ( "Error User Not Registered " . $dbData->socialId, 200 );
		}
		$conn = $this->getConnection ();
		$avatUrl = DBModel::escapeUrl ( $conn, $dbData->avatarUrl );
		$profileUrl = DBModel::escapeUrl ( $conn, $dbData->socialPageUrl );
// 		$sql = "UPDATE user SET description = '$dbData->description', lat = '$dbData->latitude', lng = '$dbData->longitude' WHERE socialId = '$dbData->socialId' AND socialNetwork like '$dbData->socialNetwork'";
		$sql = "UPDATE user SET description = '$dbData->description', lat = '$dbData->latitude', lng = '$dbData->longitude', location = GeomFromText('POINT($dbData->latitude $dbData->longitude)') WHERE socialId = '$dbData->socialId' AND socialNetwork like '$dbData->socialNetwork'";
		if ($conn->query ( $sql ) === FALSE) {
			$error = $conn->error;
			$conn->close ();
			throw new Exception ( "Error update User " . $dbData->socialId, 200 );
		}
		$conn->close ();
	}
	function areUsersMoreThan($num) {
		$conn = $this->getConnection ();
		$sql = "SELECT COUNT(*) as total_users FROM user WHERE 1";
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception ( "Select count error for more than " . $num, 200 );
		} else if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			if ($row["total_users"] >= 1000000) {
				return true;
			} else return false;
		}
		$conn->close ();
	}
	function insertFakeFusionUser(DBUser $dbData) {
		$conn = $this->getConnection ();
		$profileUrl = DBModel::escapeUrl ( $conn, $dbData->socialPageUrl );
		$location = "'" . $dbData->latitude . "," . $dbData->longitude . "'";
		$sql = "INSERT INTO fusionuser (socialId, name, email, avatarUrl, description, socialPageUrl, lat, lng, timestamp, socialNetwork) VALUES ('$dbData->socialId', '$dbData->name', '$dbData->email', '$dbData->avatarUrl', '$dbData->description', '$dbData->socialPageUrl', '$dbData->latitude', '$dbData->longitude', '$dbData->timestamp', '$dbData->socialNetwork')";
		if ($conn->query ( $sql ) === FALSE) {
			$error = $conn->error;
			$conn->close ();
			throw new Exception ( "Error insert Fake User", 200 );
		}
		$conn->close ();
	}
	
	function isFreeUser($socialId) {
		$conn = $this->getConnection ();
		$sql = "SELECT COUNT(*) as user FROM freeuser WHERE socialId = " . $socialId;
		$result = $conn->query ( $sql );
		if (!$result) {
			throw new Exception ( "Error searching for Free User " . $socialId, 200 );
		} else if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			if ($row["user"] === "1") {
				return true;
			} else return false;
		}
		$conn->close ();
	}
}

?>
