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
	
    $dao = new CardDao();
	$dao->searchInMainTable($postObj->token);
	
	$responseObj->message = "success";
		$responseObj->code = 200;
	// standard JSON response
	echo json_encode($responseObj);

?>