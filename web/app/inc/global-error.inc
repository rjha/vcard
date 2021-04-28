<?php

	
	function webgloo_error_handler($error_no, $error_message, $file, $line) {
	
	    // do nothing for silenced errors
	    if(error_reporting() == 0) { 
            return true ; 
        }
	
	    switch($error_no) {
	        case E_STRICT:
	        case E_NOTICE:
	        case E_USER_NOTICE:
	            $message = sprintf("[NOTICE] %s:%d [%d] %s",$file, $line, $error_no, $error_message);
	            \com\indigloo\Logger::getInstance()->error($message);
	            break ;
	               
	        // E_USER_ERROR may not necessarily be a well defined error
	        // Hence E_USER_ERRORS should not bubble back to the user
	        case E_USER_ERROR:
	        default:
	            throw new ErrorException($error_message, 0, $error_no, $file, $line);
	    }
	}
	
	function webgloo_exception_handler($ex) {
	    
		$message = $ex->getMessage();
		$log_message = sprintf("%s:%d %s",$ex->getFile(), $ex->getLine(), $message);
		\com\indigloo\Logger::getInstance()->error($log_message);
		\com\indigloo\Logger::getInstance()->backtrace($ex->getTrace());
		
		header('Location: /app/500.php?message='. urlencode($message));
		exit(1);
            
	    
    }
	
	function webgloo_ajax_exception_handler($ex) {
	    
		$code = $ex->getCode();
		$message = $ex->getMessage();

		$log_message = sprintf("%s:%d %s \n", $ex->getFile(), $ex->getLine(), $message);
	    \com\indigloo\Logger::getInstance()->error($log_message);
	    \com\indigloo\Logger::getInstance()->backtrace($ex->getTrace());
		
		// translate database and other errors 
		if($ex instanceof \com\indigloo\exception\DBException) {
			$message = "database error happened" ;
		} else if ($ex instanceof \com\indigloo\exception\APIException){
			$message = $ex->getMessage();
		} else {
			$message = "unknown server error happened";
		}
		
		$response = array("code" => $code, "error" => $message);
        echo  json_encode($response);
	    exit(1);

	}
	
	// set default error handler. Without error_types mask our error_handler will be called 
	// for every error regardless of the error_reporting setting
	// mixed set_error_handler($callable, int error_types)
	
	ob_start();
	set_error_handler('webgloo_error_handler');
	set_exception_handler('webgloo_exception_handler');

?>