<?php
    // application header
    // classes should be loaded first or we get incomplete class object errors
    // Also, do not output anything in class_loader (apart from errors ) - otherwise you will
    // get session header related warnings. Please keep this order.


    include(APP_CLASS_LOADER);

    use com\indigloo\core\Web;
    use com\indigloo\Configuration as Config;
    include(APP_WEB_DIR . '/app/inc/global-error.inc');
    $gWeb = Web::getInstance();
    $gWeb->start();
    
    //define constants
    define("G_APP_NAME", "vcard");
    
?>
