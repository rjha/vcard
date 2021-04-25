<?php

	include ('vcard.inc');
    include (APP_WEB_DIR . '/app/inc/header.inc');
	
	use \com\indigloo\Configuration as Config;
	use \com\yuktix\dao\Card as CardDao;
    use \com\indigloo\exception\APIException as APIException;

	$gWeb = \com\indigloo\core\Web::getInstance ();
	set_exception_handler('webgloo_ajax_exception_handler');
	$responseObj = new \stdClass;

	$postData = file_get_contents("php://input");
	$cardObj = json_decode($postData);
	
    if(!$cardObj) {
        throw new APIException(400, "no card object found");
    }

    $dao = new CardDao();
    $dao->store($cardObj->name, $cardObj->email);
	
	// standard JSON response 
    $responseObj->code = 200;
	$responseObj->message = "success";
	echo json_encode($responseObj);
?>