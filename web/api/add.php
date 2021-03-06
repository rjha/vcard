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

	if($dao->checkInTrash($cardObj->email) == 1) {

		$responseObj->message = "email is in trash";
		$responseObj->code = 409;

	} else {
		$dao->store($cardObj);
		$responseObj->message = "success";
		$responseObj->code = 200;
	}

	// standard JSON response
	echo json_encode($responseObj);

?>