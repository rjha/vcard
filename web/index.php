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
    $gparams->numberOfPages = $dao->getNumberOfPages();


?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.rawgit.com/Chalarangelo/mini.css/v3.0.1/dist/mini-default.min.css">
    
    <style>

        .text-center {
            text-align: center;
        }

        .align-right {
            float: right;
        }

        #message {
            margin-bottom: 30px;
            padding: 10px;
            background: azure;
        }

        .pagination-input {
            width: 71px;
        }
        
        .strike {
            background-color: bisque;
        }

        div.sticky-element {
            position: -webkit-sticky;
            position: sticky;
            z-index: 1000;
        }
        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-25 {
            margin-top: 25px;
        }

        .top-height-56 {
            top: 56px;
        }

        .top-height-100 {
            top: 100px;
        }

        .tabnav a {
            border-bottom: 3px solid transparent;
        }

        .tabnav a:hover {
            border-bottom: 3px solid #1976d2;
        }

        .tabnav a.active {
            border-bottom: 3px solid #1976d2;
        }


    </style>

</head>

<body>


<div class="container" id="container">
    <header class="sticky text-center">
        <h1> visiting cards database </h1>
    </header>
    <div class="sticky-element top-height-56" id="message">
        <div class="row">
            <span v-show="pageMessage" v-text="pageMessage">&nbsp;</span>
            <span v-show="!pageMessage">&nbsp;</span>
        </div>
    </div>
    <div class="row sticky-element top-height-100 mb-20" style="border-bottom: .0625rem solid var(--header-border-color);">
        <div class="col-md-8">
            <header class="tabnav" style="border-bottom:0px;">
                <a href="#"  v-on:click="switchTabs('masterTab')" v-bind:class="{ active: masterTab }" class="button">Master</a>
                <a href="#" v-on:click="switchTabs('trashTab')" v-bind:class="{ active: trashTab }" class="button">Trash</a>
            </header>
        </div>
        <div class="col-md-4" style="background: var(--header-back-color);">
            <div class="input-group align-right">
                <input type="text" id="search"/>
                <button class="small primary">Search</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" v-show="masterTab">
            <div class="text-center" v-show="cards.length == 0">
                <h2>No data found.</h2>
            </div>
            <div id="cards" v-show="cards.length > 0">
                <div class="row" v-for="card in cards" v-bind:class="{strike: card.trash}">
                    <div class="col-sm-4"> {{card.name}} </div>
                    <div class="col-sm-4"> {{card.email}} </div>
                    <div class="col-sm-4">
                        <span v-if="!card.trash"><a href="#" v-on:click="trashCard($event, card)">Trash</a> </span>
                        <span v-if="card.trash"><a href="#" v-on:click="restoreCard($event, card)">Restore</a> </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" v-show="trashTab">
            <div class="text-center" v-show="trash.length == 0">
                <h2>No data found.</h2>
            </div>
            <div class="row" v-show="trash.length > 0" v-for="card in trash">
                <div class="col-sm-4"> {{card.name}} </div>
                <div class="col-sm-4"> {{card.email}} </div>
                <div class="col-sm-4">
                    <span v-if="card.trash"><a href="#" v-on:click="restoreCard($event, card)">Restore</a> </span>
                </div>
            </div>
        </div>
    </div>
    <footer class="sticky">

        <div class="row">
            <div class="col-sm-4" id="navigation">
                <p class="doc">
                    <a href="/index.php?page=<?php echo $previousPage; ?>" class="doc">&lt;&nbsp;previous</a>
                    &nbsp;
                    <a href="/index.php?page=<?php echo $nextPage; ?>" class="doc">next&nbsp;&gt;</a>
                    &nbsp;
                    <input class="pagination-input" v-model="page"/> &#47; {{numberOfPages}}
                    <a href="#" v-on:click="gotoPage($event)" class="doc"> jump</a>
                </p>
            </div>
            <div class="col-sm-4 text-center">
                <p class="doc mt-25">
                    <span class="doc">Trash &nbsp; {{trash.length}}</span>
                </p>
            </div>

            <div class="col-sm-4">
                <div class="align-right">
                    <p class="doc mt-25">
                        <a href="#" class="doc">Cancel</a>
                        &nbsp;|&nbsp;
                        <a href="#" v-on:click="submit($event)" class="doc">Submit</a>
                    </p>
                </div>
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
        created () {
            this.switchTabs("masterTab");
        },
        data() {
            return {
                cards: gparams.cards,
                numberOfPages: gparams.numberOfPages,
                trash: [],
                page : 1,
                masterTab: true,
                trashTab: false,
                pageMessage: ""
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

                axios.post(url, data, config).then( response => {

                    var status = response.status || 500 ;
                    var data = response.data || {} ;

                    this.pageMessage = data.message;
                    console.log("server response: %O", data);
                    console.log("page submit() completed");

                }).catch( error => {
                    console.log("page submit() error");
                    console.log(error.response.data);
                    console.log(error.response);
                });

            }, //:submitFile
            switchTabs(tab) {
                if (tab === 'masterTab') {
                    this.trashTab = false;
                    this.masterTab = true;
                    this.pageMessage = "";
                } else {
                    this.trashTab = true;
                    this.masterTab = false;
                    this.pageMessage = "";
                }
            }
        }
    });

    </script>

</body>



</html>