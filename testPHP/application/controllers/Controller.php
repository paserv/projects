<?php
#include_once '../models/FBModel.php';
#include_once '../models/DBModel.php';
#include_once '../models/FusionModel.php';
#include_once '../models/DummyModel.php';
#include_once '../dto/SocialUser.php';
#include_once '../dto/DBUser.php';

require_once $_SERVER["DOCUMENT_ROOT"] . '/application/models/FBModel.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/models/DBModel.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/models/FusionModel.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/models/DummyModel.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/dto/SocialUser.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/dto/DBUser.php';

class Controller {
	function getLoggedUser($socialNetwork) {
		switch ($socialNetwork) {
			case "FB" :
				$model = new FBModel ();
				break;
			case "TW" :
				echo "TWITTER NOT PRESENT";
				$model = new DummyModel ();
				break;
			case "PL" :
				echo "PLUS NOT PRESENT";
				$model = new DummyModel ();
				break;
			case "LI" :
				echo "LINKEDIN NOT PRESENT";
				$model = new DummyModel ();
				break;
			case "PI" :
				echo "PINTEREST NOT PRESENT";
				$model = new DummyModel ();
				break;
			case "TU" :
				echo "TUMBLR NOT PRESENT";
				$model = new DummyModel ();
				break;
			case "YT" :
				echo "YOUTUBE NOT PRESENT";
				$model = new DummyModel ();
				break;
			case "IN" :
				echo "INSTAGRAM NOT PRESENT";
				$model = new DummyModel ();
				break;
		}
		
		$user = $model->getUser ();
		$user->socialNetwork = $socialNetwork;
		return $user;
	}
	function register(DBUser $dbData) {
		try {
			$this->registerUserIntoDB ( $dbData );
		} catch ( Exception $e ) {
			return "Unable to register in DB, Retry Later!";
		}
		try {
		$this->registerFakeUserIntoFusionTable ( $dbData );
		} catch ( Exception $e ) {
			$model = new DBModel ();
			$model->deleteUser($dbData->socialId);
			return "Unable to register in Table, Retry Later!";
		}
		return "Successfully registered!";
	}
	function searchByName($name) {
		$model = new DBModel();
		$result = $model->searchByName($name);
		return $result;
	}
	function registerUserIntoDB(DBUser $dbData) {
		$model = new DBModel ();
		$model->insertUser ( $dbData );
	}
	function registerUserIntoFusionTable(SocialUser $dbData) {
		$model = new FusionModel ();
		$model->insertUser ( $dbData );
	}
	function registerFakeUserIntoFusionTable(DBUser $dbData) {
		$model = new FusionModel ();
		$model->insertUserFake ( $dbData );
	}
}
?>