<?php
    
  $error_message = "500 error";
  if(array_key_exists("message", $_GET)) {
    $error_message = $_GET["message"];
  }
  
  
?>

<!DOCTYPE html>
 <html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <head>
        <meta charset="utf-8">
        <title>Error Page</title>
        <link href="/site/error.css?r=7" rel="stylesheet" media="screen">
        
    </head>

    <body>

        <div id="error" class="error_code_500">
            <div class="error_message">
                We had a problem processing that request. <br>
                <a href="/">Go to the Homepage</a> »
                
            </div>
            
            <div class="error_bubble">
                <div class="error_code">500<br><span>ERROR</span></div>
                <div class="error_quote">Internal server error.</div>
            </div>
            
            <div class="error_arrow"></div>
            <div class="error_attrib"> <span>What does the code say?</span>  </div>
            <div class="trace_message"> <?php echo $error_message; ?> </div>
            <div class="clear"></div>
        </div>
        

    </body>
</html>
