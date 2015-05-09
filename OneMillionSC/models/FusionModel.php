<?php
require_once 'autoload.php';
FusionModel_autoload();

class FusionModel {
	
	function getService() {
		$client = new Google_Client ();
		$client->setApplicationName ( "omsc" );
		
		$key = file_get_contents ( FUSION_KEY_FILE );
		$client->setAssertionCredentials ( new Google_Auth_AssertionCredentials (FUSION_SERVICE_ACCOUNT_NAME, array (
				'https://www.googleapis.com/auth/fusiontables' 
		), $key ) );
		$client->setClientId ( FUSION_CLIENT_ID );
		$service = new Google_Service_Fusiontables ( $client );
		return $service;
	}
	
	function insertUser(SocialUser $dbData) {
		
		$tableID = $this->getTableId();
		if (!$tableID) {
			throw new Exception("OMSC is full");
		}
		$service = $this->getService();
		//Insert Address???
		//Insert link to image URL and social page url
		$avatUrl = FusionModel::escapeUrl ( $dbData->avatarUrl );
		$profileUrl = FusionModel::escapeUrl ( $dbData->socialPageUrl );
		$imgSocial = $this->getImgSocial($dbData->socialNetwork);
		$insQuery = "INSERT INTO " . $tableID . " (socialId, name, avatarUrl, description, socialPageUrl, location, socialNetwork, imgSocial) VALUES ( '$dbData->socialId', '$dbData->name', '$avatUrl', '$dbData->description', '$profileUrl', '$dbData->latitude,$dbData->longitude', '$dbData->socialNetwork', '$imgSocial')";
		$res = $service->query->sql ($insQuery);
	}
	function selectRow(SocialUser $dbData, $tableId) {
		$service = $this->getService();
		$query = "SELECT ROWID FROM " . $tableId . " WHERE socialId = " . $dbData->socialId;
		$res = $service->query->sql ($query);
		if ($res->rows > 0) {
			return $res->rows;
		}
		return false;
	}
	function deleteUser(SocialUser $dbData) {
		$row = $this->selectRow($dbData, FUSION_TABLE_ID1);
		if ($row !== false) {
			$this->deleteRow(FUSION_TABLE_ID1, $row);
			return;
		}
		$row = $this->selectRow($dbData, FUSION_TABLE_ID2);
		if ($row !== false) {
			$this->deleteRow(FUSION_TABLE_ID2, $row);
			return;
		}
		$row = $this->selectRow($dbData, FUSION_TABLE_ID3);
		if ($row !== false) {
			$this->deleteRow(FUSION_TABLE_ID3, $row);
			return;
		}
		$row = $this->selectRow($dbData, FUSION_TABLE_ID4);
		if ($row !== false) {
			$this->deleteRow(FUSION_TABLE_ID4, $row);
			return;
		}
		$row = $this->selectRow($dbData, FUSION_TABLE_ID5);
		if ($row !== false) {
			$this->deleteRow(FUSION_TABLE_ID5, $row);
			return;
		}
		 
		
// 		$query = "SELECT ROWID FROM " . FUSION_TABLE_ID1 . " WHERE socialId = " . $dbData->socialId;
// 		$res = $service->query->sql ($query);
// 		if ($res->rows > 0) {
// 			$this->deleteRow($service, FUSION_TABLE_ID1, $res->rows);
// 			return;
// 		}
		
// 		$query = "SELECT socialId FROM " . FUSION_TABLE_ID2 . " WHERE socialId = " . $dbData->socialId;
// 		$res = $service->query->sql ($query);
// 		if ($res->rows > 0) {
// 			$this->deleteRow($service, FUSION_TABLE_ID2, $res->rows);
// 			return;
// 		}
		
// 		$query = "SELECT socialId FROM " . FUSION_TABLE_ID3 . " WHERE socialId = " . $dbData->socialId;
// 		$res = $service->query->sql ($query);
// 		if ($res->rows > 0) {
// 			$this->deleteRow($service, FUSION_TABLE_ID3, $res->rows);
// 			return;
// 		}
		
// 		$query = "SELECT socialId FROM " . FUSION_TABLE_ID4 . " WHERE socialId = " . $dbData->socialId;
// 		$res = $service->query->sql ($query);
// 		if ($res->rows > 0) {
// 			$this->deleteRow($service, FUSION_TABLE_ID4, $res->rows);
// 			return;
// 		}
		
// 		$query = "SELECT socialId FROM " . FUSION_TABLE_ID15 . " WHERE socialId = " . $dbData->socialId;
// 		$res = $service->query->sql ($query);
// 		if ($res->rows > 0) {
// 			$this->deleteRow($service, FUSION_TABLE_ID5, $res->rows);
// 			return;
// 		}
		
		
	}
	function deleteRow ($tableId, $rows) {
		$service = $this->getService();
		foreach ($rows as $rowid) {
			$query = "DELETE FROM " . $tableId . " WHERE ROWID = '" . $rowid[0] . "'";
			$res = $service->query->sql ($query);
		}
	}
	function updateUser(SocialUser $dbData) {
	
	}
	static function escapeUrl($url) {
		$urlEscaped = $url;
		$urlEscaped = urlencode ( $urlEscaped );
		return $urlEscaped;
	}
	function getImgSocial($socialNetwork) {
		switch ($socialNetwork) {
			case "FB" :
				return "blu_circle";
				break;
			case "TW" :
				return "ltblu_circle";
				break;
			case "PL" :
				return "red_circle";
				break;
		}
	}
	
	function getTableId() {
		if ($this->countRows(FUSION_TABLE_ID1) < 100000) {
			return FUSION_TABLE_ID1;
		} else if ($this->countRows(FUSION_TABLE_ID2) < 100000) {
			return FUSION_TABLE_ID2;
		} else if ($this->countRows(FUSION_TABLE_ID3) < 100000) {
			return FUSION_TABLE_ID3;
		} else if ($this->countRows(FUSION_TABLE_ID4) < 100000) {
			return FUSION_TABLE_ID4;
		} else if ($this->countRows(FUSION_TABLE_ID5) < 100000) {
			return FUSION_TABLE_ID5;
		}
		return false;
	}
	
	function countRows($tableId) {
		$service = $this->getService();
		$countQuery = "SELECT COUNT () FROM " . $tableId;
		$res = $service->query->sql ($countQuery);
		$result = $res->rows[0];
		return $result[0];
	}
	
	function insertUserFake(DBUSer $dbData) {
		$dbModel = new DBModel();
		$dbModel->insertFakeFusionUser($dbData);
	}
	
}

?>
