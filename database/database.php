<?php

  class database {

    private $host = "127.0.0.1";
    private $dbname = "bestpricy";
    private $user = "root";
    private $pass = "root";

    public function connect () {
      try {
        $DBH = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);   
        $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        return $DBH;         
      }

      catch(PDOException $e) {
          echo "error message =".$e->getMessage();
          //log this
      }
    }
  }

  function get_DB(){
    $DB = new database();
    $DBH = $DB->connect();
    if (isset($DBH)) {
     return $DBH;
    }
  }

?>