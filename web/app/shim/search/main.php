<?php

	include ('vcard.inc');
    include (APP_WEB_DIR . '/app/inc/header.inc');
	
	use \com\indigloo\Configuration as Config;
	use \com\yuktix\dao\Card as CardDao;
    use \com\indigloo\exception\APIException as APIException;

	$gWeb = \com\indigloo\core\Web::getInstance ();
	set_exception_handler("webgloo_ajax_exception_handler");
	$responseObj = new \stdClass;

	$postData = file_get_contents("php://input");
	$postObj = json_decode($postData);

	if(empty($postObj->token)) {
		throw new APIException(400, "empty search token");
	}

    $dao = new CardDao();
	if($postObj->tab == "main") {
		$rows = $dao->searchInMainTable($postObj->token);
	} else if($postObj->tab == "trash") {
		$rows = $dao->searchInTrashTable($postObj->token);
	} else {
		$rows = array();
	}
    
	$responseObj->rows = $rows;
	$responseObj->message = "success";
    $responseObj->code = 200;
	echo json_encode($responseObj);
	
?>