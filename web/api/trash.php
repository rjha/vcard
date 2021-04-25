<?php

	include ('vcard.inc');
    include (APP_WEB_DIR . '/app/inc/header.inc');
	
	use \com\indigloo\Configuration as Config;
	use \com\yuktix\dao\Card as CardDao;
    use \com\indigloo\exception\APIException as APIException;
	use \com\indigloo\Logger as Logger;

	$gWeb = \com\indigloo\core\Web::getInstance ();
	set_exception_handler('webgloo_ajax_exception_handler');
	$responseObj = new \stdClass;
	
	$postData = file_get_contents("php://input");
	Logger::getInstance()->info("trash api got -> ".$postData);
	$postObj = json_decode($postData);
	
	$dao = new CardDao();
    $dao->trash($postObj->emails);
	
	// standard JSON response 
    $responseObj->code = 200;
	$responseObj->message = "success";
	echo json_encode($responseObj);
    
?>