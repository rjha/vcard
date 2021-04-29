<?php

    include ('vcard.inc');
    include (APP_WEB_DIR . '/app/inc/header.inc');
	
    use \com\indigloo\Url as Url;
	use \com\indigloo\Configuration as Config;
	use \com\yuktix\dao\Card as CardDao;
    use \com\indigloo\exception\APIException as APIException;

    $tab = Url::tryQueryParam("tab");
    $tab = (empty($tab)) ? "main" : $tab;

    $page = Url::tryQueryParam("page");
    $page = (empty($page)) ? 0 : $page;

    $dao = new CardDao();
    $totalPages = 1;

    if($tab == 'main') {
        $result = $dao->getMainItems($page);
        $totalPages = $dao->getTotalMainPages();

    } else if ($tab == 'trash') {
        $result = $dao->getTrashItems($page);
        $totalPages = $dao->getTotalTrashPages();
    } else {
        echo "unknown database name" ;
        exit(1);
    }

    $gparams = new \stdClass;
    $gparams->base = Url::base();
    $gparams->tab = $tab;
    $gparams->pageNumber = $page;

    $gparams->cards = $result;
    $gparams->totalPages = $totalPages;


?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.rawgit.com/Chalarangelo/mini.css/v3.0.1/dist/mini-default.min.css">
    
    <style>

        header {
            height: 6.5000rem;
            border-bottom: transparent ;
            margin-bottom: 21px;

        }

        header input {
            height: 31px;   
        }

        #page-message {
            height:41px; 
            background: beige; 
            border-top: 1px solid #ccc;
        }

        .align-right {
            float: right;
        }

        #navigation input {
            width: 71px;
            height: 31px;
        }

        .strike {
            background-color: bisque;
        }

        .tabnav a {
            border-bottom: 3px solid transparent;
            padding: 11px; 
        }

        .tabnav a.active {
            border-bottom: 3px solid #1976D2;
        }
      

    </style>

</head>

<body>


<div class="container" id="container">
    <header class="sticky">
        <div class="row">
            <div class="col-md-4">
                <span> Visiting cards database </span>
            </div>

            <div class="col-md-4">
                <div class="tabnav">
                    <a href="index.php?tab=main" v-bind:class="{active: display.tab == 'main'}" >main</a>
                    <a href="index.php?tab=trash" v-bind:class="{active: display.tab == 'trash'}">trash</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="input-group align-right">
                    <input type="text" id="search"  v-model="box.token" />
                    <button class="small primary" v-on:click="search($event)">Search</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="page-message" class="col-md-12">
                <span v-text="page.message">&nbsp;</span>
            </div>

        </div>
    </header>


   
    <div id="cards" v-show="display.tab == 'main' ">
        <div class="row" v-for="card in cards" v-bind:class="{strike: card.trash}">
            <div class="col-sm-4"> {{card.name}} </div>
            <div class="col-sm-4"> {{card.email}} </div>
            <div class="col-sm-4">
                <span v-if="!card.trash"><a href="#" v-on:click="trashCard($event, card)">Trash</a> </span>
                <span v-if="card.trash"><a href="#" v-on:click="restoreCard($event, card)">Restore</a> </span>
            </div>
        </div>
    </div> <!-- main -->

    <div v-show="display.tab == 'trash' ">

        <div class="row" v-for="card in cards">
            <div class="col-sm-4"> {{card.name}} </div>
            <div class="col-sm-4"> {{card.email}} </div>
            <div class="col-sm-4"> &nbsp;</div>
        </div>
       
    </div>  <!-- trash -->

    <footer class="sticky">

        <div class="row">
            <div class="col-sm-4" id="navigation">
                &nbsp;<a href="#" v-on:click="gotoPreviousPage($event)">[previous]</a>
                &nbsp;<a href="#" v-on:click="gotoNextPage($event)">[next]</a>
                &nbsp;<input v-model="box.jump"/> &#47; {{page.total}}
                &nbsp;<a href="#" v-on:click="gotoPage($event)">[jump]</a>
            </div>

            <div class="col-sm-4 text-center" v-show="display.tab == 'main' ">
                <span>Trash &nbsp; {{trash.length}}</span>
            </div>

            <div class="col-sm-4" v-show="display.tab == 'main' ">
                <a href="/app/download.php" class="doc">[download]</a>&nbsp;
                <a href="#" v-on:click="submit($event)">[submit]</a>
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
                page : {
                    "number": gparams.pageNumber,
                    "message": "...",
                    "total": gparams.totalPages
                },
                display: {
                    "tab": gparams.tab 
                },
                box: {
                    "jump": gparams.pageNumber,
                    "token": null  
                }
            }
        },

        methods: {

            gotoPreviousPage(event) {

                var pageNum = parseInt(this.page.number, 10);
                if (isNaN(pageNum)) { 
                    pageNum =  0; 
                } else {
                    pageNum = pageNum - 1;
                }

                pageNum = (pageNum < 0) ? 0: pageNum;
                console.log("jump to page ->  %O", pageNum);
                window.location.href = gparams.base + "/index.php?page=" + pageNum + "&tab=" + this.display.tab;

            },

            gotoNextPage(event) {

                var pageNum = parseInt(this.page.number, 10);
                if (isNaN(pageNum)) { 
                    pageNum =  0; 
                } else {
                    pageNum = pageNum + 1;
                }

                pageNum = (pageNum > this.page.total) ? this.page.total : pageNum;
                console.log("jump to page ->  %O", pageNum);
                window.location.href = gparams.base + "/index.php?page=" + pageNum + "&tab=" + this.display.tab;;

            },
            gotoPage(event) {

                var pageNum = parseInt(this.box.jump, 10);
                if (isNaN(pageNum)) { 
                    pageNum =  0; 
                }

                console.log("jump to page ->  %O", pageNum);
                window.location.href = gparams.base + "/index.php?page=" + pageNum + "&tab=" + this.display.tab;;

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
                
                // get all cards to be trashed
                let items = [];
                let i = 0;
                for(i =0; i < this.trash.length; i++) {
                    items.push({
                        "name": this.trash[i].name,
                        "email": this.trash[i].email
                    });
                }
                
                console.log("send trash to server -> %O", items);

                let data = {
                    "items": items
                }

                let url = gparams.base + "/api/trash.php";
                let config = {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }

                axios.post(url, data, config).then( response => {

                    let status = response.status || 500 ;
                    let data = response.data || {};
                    this.page.message = data.message || data.error ;
                    console.log("server response: %O", data);

                    // data.error 
                    // data.code 
                    console.log("page submit() completed");

                }).catch( error => {

                    this.page.message = "unknown error happened" ;
                    console.log("page submit() error");
                    console.log(error.response);

                });

            },

            search(event) {
                
                console.log("search token -> %s", this.box.token);
                
                // let shims = new Map();
                // shims.set("main", "/app/shim/search/main.php");
                // shims.set("trash", "/app/shim/search/trash.php")
                // let url = gparams.base + shims.get(this.display.tab);

                let url = gparams.base + "/app/shim/search/main.php"

                let data = {
                    "token": this.box.token,
                    "tab": this.display.tab
                }

                let config = {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }

                axios.post(url, data, config).then( response => {

                    let i = 0;
                    let status = response.status || 500 ;
                    let data = response.data || {};
                    this.page.message = data.message || data.error;

                    console.log("server response: %O", data);
                    if((data.code == 200) && data.rows && (data.rows.length > 0)) {
                        this.cards = data.rows;
                    } else {
                        this.page.message = "search returned no result, [error: " + this.page.message + "]";
                    }

                    console.log("page search() completed");

                }).catch( error => {

                    this.page.message = "unknown error happened" ;
                    console.log("page search() error");
                    console.log(error);

                });

            }

        }
    });

    </script>

</body>



</html>