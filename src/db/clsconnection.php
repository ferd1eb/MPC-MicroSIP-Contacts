<?php
    if (isset($IncludeConfig)) {
        if ($IncludeConfig) include "config.php";
    } else {
        include "config.php";
    }
    
    class Connection {
        private $con;
        private $res;
        
        private $host;
        private $database;
        private $user;
        private $password;
        
        public $hasrow = FALSE;
        public $rowcount = 0;
        
        function __construct() {
            $this->host     = ServerName;
            $this->database = DbName;
            $this->user     = DbUser;
            $this->password = DbPwd;
        }
        
        function ConnectionSetup($server, $database, $user, $password) {
            $this->host     = $server;
            $this->database = $database;
            $this->user     = $user;
            $this->password = $password;
        }
        
        function OpenConnection($EnableUTF8 = true, $Charset = 'utf8') {
            $this->con = new mysqli($this->host, 
                                    $this->user, 
                                    $this->password, 
                                    $this->database);
            
            if($EnableUTF8) $this->con->set_charset($Charset);
//            $this->execute("SET NAMES 'utf8'");
            
            if (!$this->con) {
                $this->writelog('Connection Failed');

                die;
            }
        }
        
        function CloseRecordset() {
            while ($this->con->more_results()) {
                $this->res->close();
                
                $this->con->next_result();
                $this->res = mysqli_use_result($this->con);
            }
        }
        
        function CloseConnection() {
            mysqli_close($this->con);
        }
        
        function execute($qry) {
            try {
                $log_qry = $this->escape($qry);
                mysqli_query($this->con,"INSERT INTO query_log (qry) VALUE ('$log_qry')");
                
                mysqli_query($this->con,$qry);
                $this->CloseRecordset();
            } catch (Exception $ex) {
                $this->writelog($ex->getMessage);
            }
        }
        
        function ExecuteScalar($qry, $column) {
            try {
                $this->query($qry);
                
                $row = $this->getrow();
                
                return $row[$column];
                
                $this-CloseRecordset();
            } catch (Exception $ex) {
                $this->writelog($ex->getMessage);
            }
        }
        
        function query($qry, $log_query = false) {
            if ($log_query) $this->writelog("Query Log\n$qry");
            
            $log_qry = $this->escape($qry);
            mysqli_query($this->con,"INSERT INTO query_log (qry) VALUE ('$log_qry')");
            
            $this->res = mysqli_query($this->con, $qry);
            
            if(!$this->res) {
                $this->writelog("$qry\n".mysqli_error($this->con));
            }
        }
        
        function getrow() {
            try {
                return mysqli_fetch_assoc($this->res);
            } catch (Exception $ex) {
                $this->writelog($ex->getMessage);
            }
        }
        
        function getrows() {
            try {
                $rows = array();
                
                while ($row = $this->getrow()) array_push($rows, $row);
                
                return $rows;
            } catch (Exception $ex) {
                $this->writelog($ex->getMessage);
            }
        }
        
        function escape($str) {
            return mysqli_real_escape_string($this->con, $str);
        }
        
        function rowcount() {
            try {
                $rowcount = mysqli_num_rows($this->res);  
            } catch (Exception $ex) {
                $this->writelog($ex->getMessage);
                
                $rowcount = 0;
            }

            return $rowcount;
        }
        
        function hasrow() {
            try {
                $rowcount = mysqli_num_rows($this->res);
            } catch (Exception $ex) {
                $this->writelog($ex->getMessage);
                
                $rowcount = 0;
            }
            
            if ($rowcount > 0) {
                return true;
            } else {
                return false;
            }
        }
        
        function writelog($err) {
            $logfile = "error.php";
            $logpath = $_SERVER['DOCUMENT_ROOT'];
            
            if(RunType == 'Live') {
                $logpath = $logpath . '/db';
            } elseif(RunType == 'Test') {
                $logpath = $logpath . '/test/db';
            } else {
                $logpath = $logpath . '/globaltracker/db';
            }
            
            $today = date("m/d/Y H:m:s");
            $file = fopen($logpath . '/' . $logfile, "a");
            
            fwrite($file, $today . "\n" . $_SERVER['PHP_SELF'] . "\n" . $err . "\n" . PHP_EOL);
            fclose($file);
        }
    }

    include "sysconfig.php";
