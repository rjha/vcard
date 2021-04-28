<?php

	include ('vcard.inc');
    include (APP_WEB_DIR . '/app/inc/header.inc');
	
	use \com\indigloo\Configuration as Config;
	use \com\yuktix\dao\Card as CardDao;
    use \com\indigloo\exception\APIException as APIException;

	$gWeb = \com\indigloo\core\Web::getInstance ();
	set_exception_handler('webgloo_ajax_exception_handler');
	
	// send csv file 
	$fileName = "vcards.csv";
	
	header('Content-Type: text/csv; charset=utf-8');
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header('Content-Disposition: attachment; filename=vcards.csv');
	header('Content-Description: File Transfer');
	header("Expires: 0");
	header("Pragma: public");
	
	$rows = [];
	$dao = new CardDao();
	$rows = $dao->getAllMainItems();
	
	$fp = fopen("php://output", "w");
	fputcsv($fp, array("name", "email", "source"));

	foreach($rows as $row) {
		fputcsv($fp, $row);
	}

	fclose($fp);
	exit(0);

?>