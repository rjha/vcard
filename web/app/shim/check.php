<?php

	include ('vcard.inc');
    include (APP_WEB_DIR . '/app/inc/header.inc');
	
	use \com\indigloo\Configuration as Config;
	use \com\yuktix\dao\Card as CardDao;
    use \com\indigloo\exception\APIException as APIException;

	$gWeb = \com\indigloo\core\Web::getInstance ();
	set_exception_handler('webgloo_ajax_exception_handler');
	$responseObj = new \stdClass;

	$email = array_key_exists("email", $_GET) ? $_GET["email"] : NULL;
	if(empty($email)) {
		throw new APIException(400, "email not found");
	}

    $dao = new CardDao();
    $flag = $dao->checkInTrash($email);

	if($flag == 1) {
		 "found in trash";
	} else {

	}
	$responseObj->message = ($flag == 1) ? "found in trash" : "not found in trash" ;
    $responseObj->code = ($flag == 1) ? 200 : 404;
	echo json_encode($responseObj);

?>