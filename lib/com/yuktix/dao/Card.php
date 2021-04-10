<?php

namespace com\yuktix\dao {

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\Logger as Logger;

    class Card {

        private $dbh ;

        function __construct() {
            $this->dbh = PDOWrapper::getHandle();
        }

        function store($name, $email) {

            // start Tx
            $this->dbh->beginTransaction();

            $sql = "insert INTO card_master(name, email, created_on, updated_on) "
            ." VALUES(:name, :email, now(), now()) ON DUPLICATE KEY UPDATE version = version+1 " ;

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(":name", $name, \PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, \PDO::PARAM_STR);
            $stmt->execute();

            //end Tx
            $this->dbh->commit();
            return TRUE ;
        }

        function get($page) {

            $this->dbh->beginTransaction();
            // page size = 50 
            $page_size = 50;
            $start = $page * $page_size;
            $sql = "select name, email from card_master order by email asc limit %d, %d " ;
            $sql = sprintf($sql, $start, $page_size);

            // print($sql);
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // var_dump($result);
            return $result;

        }

    }
}

?>
