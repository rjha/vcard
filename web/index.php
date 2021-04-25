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

    $gparams = new \stdClass ;
    $gparams->cards = $result;
    


?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.rawgit.com/Chalarangelo/mini.css/v3.0.1/dist/mini-default.min.css">
    
    <style>

        .strike {
            background-color: coral;
        }

    </style>

</head>

<body>


<div class="container" id="container">
    <header class="sticky">
        <h1> visiting cards database </h1>
    </header>

    <div id="cards">
        <div class="row" v-for="card in cards" v-bind:class="{strike: card.hide}">
            <div class="col-sm-4"> {{card.name}} </div>  
            <div class="col-sm-4"> {{card.email}} </div>
            <div class="col-sm-4"> <a href="#" v-on:click="removeEmail(card)">Remove</a> </div>
        </div>
    </div>

    

    <footer class="sticky">
        <div class="row">
            <div class="col-sm-6"> 
                <a href="/index.php?page=<?php echo $previousPage; ?>">&lt;&nbsp;previous</a>
                &nbsp;&nbsp;
                <a href="/index.php?page=<?php echo $nextPage; ?>">next&nbsp;&gt;</a>
            </div>
            <div class="col-sm-6"> 
                <span> Removed {{trash.length}}</span>
            </div>
        </div>

        
    </footer>

  </div>

  <script src="https://unpkg.com/vue@next"></script>
  <script>
    
    var gparams = <?php echo json_encode($gparams, JSON_PRETTY_PRINT); ?>;
    
    const Contact = {
        data() {
            return {
                cards: gparams.cards,
                trash: []
            }
        },
        methods: {
            removeEmail(card) {
                this.trash.push(card.email);
                card.hide = true;
                console.log("clicked %s, trash %O", card.email, this.trash.length);
            }
        }
    };

    Vue.createApp(Contact).mount('#container')
    </script>

</body>



</html>