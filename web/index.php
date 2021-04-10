<?php

    include ('vcard.inc');
    include (APP_WEB_DIR . '/app/inc/header.inc');
	
    use \com\indigloo\Url as Url;
	use \com\indigloo\Configuration as Config;
	use \com\yuktix\dao\Card as CardDao;
    use \com\indigloo\exception\APIException as APIException;

    $page = Url::tryQueryParam("page");
    $page = (empty($page)) ? 0 : $page;
    $dao = new CardDao();
    $result = $dao->get($page);

    $nextPage = $page + 1;
    $previousPage = $page - 1;
    $previousPage = ($previousPage < 0) ? 0 : $previousPage;

    $num_records = sizeof($result);
    $i = 0;


?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.rawgit.com/Chalarangelo/mini.css/v3.0.1/dist/mini-default.min.css">
 
</head>

<body>


<div class="container">
    <header class="sticky">
        <h1> visiting cards database </h1>
    </header>

    <?php for($i =0 ; $i < $num_records; $i++) { ?>

    <div class="row">
        <div class="col-sm-4"> <?php echo $result[$i]["name"]; ?></div>  
        <div class="col-sm-4"><?php echo $result[$i]["email"]; ?></div>
    </div>

    <?php } ?>

    <footer class="sticky">
        <a href="/index.php?page=<?php echo $previousPage; ?>">&lt;&nbsp;previous</a>
        &nbsp;&nbsp;
        <a href="/index.php?page=<?php echo $nextPage; ?>">next&nbsp;&gt;</a>
    </footer>

  </div>


</body>



</html>