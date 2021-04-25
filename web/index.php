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
    $gparams->base = Url::base();
    


?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.rawgit.com/Chalarangelo/mini.css/v3.0.1/dist/mini-default.min.css">
    
    <style>

        header {
            text-align: center;
        }

        #message {
            margin-bottom: 30px;
            background: azure;
        }

        input {
            width: 71px;
        }
        
        .strike {
            background-color: bisque;
        }

    </style>

</head>

<body>


<div class="container" id="container">
    <header class="sticky">
        <h1> visiting cards database </h1>
    </header>

    <div id="message"> 
        <div class="row">
            <span> &nbsp; </span>
        </div>
    </div>

    <div id="cards">
        
        <div class="row" v-for="card in cards" v-bind:class="{strike: card.trash}">
            <div class="col-sm-4"> {{card.name}} </div>  
            <div class="col-sm-4"> {{card.email}} </div>
            <div class="col-sm-4"> 
                <span v-if="!card.trash"><a href="#" v-on:click="trashCard($event, card)">Trash</a> </span>
                <span v-if="card.trash"><a href="#" v-on:click="restoreCard($event, card)">Restore</a> </span>
            </div>
        </div>
    </div>

    

    <footer class="sticky">
        
        <div class="row">
            <div class="col-sm-6" id="navigation"> 
                <a href="/index.php?page=<?php echo $previousPage; ?>">&lt;&nbsp;previous</a>
                &nbsp;
                <a href="/index.php?page=<?php echo $nextPage; ?>">next&nbsp;&gt;</a>
                &nbsp;
                <input v-model="page"/> &#47; 50
                <a href="#" v-on:click="gotoPage($event)"> jump</a> 
            </div>
            <div class="col-sm-4"> 
                <span> Trash &nbsp; {{trash.length}}</span>
            </div>

            <div class="col-sm-2"> 
                <a href="#" v-on:click="submit($event)">submit</a>
            </div>

        </div>
        

        
    </footer>

  </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="http://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


  <script>
    
    var gparams = <?php echo json_encode($gparams, JSON_PRETTY_PRINT); ?>;
    
    var app = new Vue({
        el: "#container",
        data() {
            return {
                cards: gparams.cards,
                trash: [],
                page : 1
            }
        },

        methods: {

            gotoPage(event) {

                var pageNum = parseInt(this.page, 10);
                if (isNaN(pageNum)) { 
                    pageNum =  0; 
                }

                console.log("jump to page ->  %O", pageNum);
                window.location.href = gparams.base + "/index.php?page=" + pageNum;
                
            },

            trashCard(event, card) {
                // This is to ensure that link
                // stays in the same place 
                event.preventDefault();
                this.trash.push(card);
                card.trash = true;
                console.log("clicked %s, trash size %O", card.email, this.trash.length);
                
            },

            restoreCard(event, card) {

                console.log("restore card -> email %s", card.email);
                let index =0;
                let found = true;
                let i = 0;
                event.preventDefault();
                card.trash = false;
                for(i =0; i < this.trash.length; i++) {
                    if(this.trash[i].email == card.email) {
                        console.log("card.email matched @index: %s, trash card %s", i, this.trash[i].email);
                        index = i;
                        found = true;
                        break;
                    }
                }

                if(found) {
                    let restored = this.trash.splice(index, 1);
                    console.log("restore from trash: %O", restored);
                }
            },
            
            submit(event) {
                
                // get all emails to be trashed
                let emails = [];
                let i = 0;
                for(i =0; i < this.trash.length; i++) {
                    emails.push(this.trash[i].email);
                }
                
                console.log("flush to server -> %O", emails);

                let data = {
                    "emails": emails
                }

                let url = gparams.base + "/api/trash.php";
                let config = {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }

                axios.post(url, data, config).then(function(response){

                    var status = response.status || 500 ;
                    var data = response.data || {} ;
                    console.log("server response: %O", data);
                    console.log("page submit() completed");

                }).catch(function(error){
                    console.log("page submit() error");
                    console.log(error.response.data);
                    console.log(error.response);
                });

            } //:submitFile
        }
    });

    </script>

</body>



</html>