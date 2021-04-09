<?php

namespace com\indigloo\exception {

    class APIException extends \Exception  {

        public function __construct($code, $message) {
            parent::__construct($message, $code);
        }
        
    }

}

?>
