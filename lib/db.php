<?php
function getDatabase()
{
    $dbconn = pg_connect("host=ec2-54-227-241-179.compute-1.amazonaws.com
  port=5432 dbname=d4ieg9ce7qihnf user=doirzncaoefasd password=0955cadb61b87148265f253f9b11a740c24b806bbb7d9b24c2b992da74861a99")
    or die("Connect falsed");

    return $dbconn;
}
class postgresql
{
    public $dbconn;
    public $debug;

    /**
     * open DB connection
     */
    public function __construct($debug)
    {
        try {
            $this->dbconn = pg_connect("host=ec2-54-227-241-179.compute-1.amazonaws.com
  port=5432 dbname=d4ieg9ce7qihnf user=doirzncaoefasd password=0955cadb61b87148265f253f9b11a740c24b806bbb7d9b24c2b992da74861a99") or die('Could not connect: ' . pg_last_error());
        } catch (Exception $ex) {}
        $this->debug = ($debug === '1');
    }

    public function isValid()
    {
        return $this->dbconn !== null;
    }
    /**
     * Close DB connection
     */
    public function close()
    {
        if ($this->isValid()) {
            pg_close($this->dbconn);
            $this->dbconn = null;
        }
    }

    /**
     * execute INSERT, SELETE,... sql, return void, no need to clear result
     */
    public function execute($sql)
    {
        if ($this->debug === true) {
            echo ('Query : ' . $sql);
        }
        if ($this->isValid()) {
            try {
                pg_exec($sql) or die('Execute failed: ' . pg_last_error());
            } catch (Exception $ex) {}
        }
    }
    /**
     * execute SELECT sql, return result or null, need to clear  afer fetching result
     */
    public function select($sql)
    {
        if ($this->debug === true) {
            echo ('Query : ' . $sql);
        }
        if ($this->isValid()) {
            try {
                $result = pg_query($sql) or die('Query failed: ' . pg_last_error());
                return $result;
            } catch (Exception $ex) {}
        }
        return null;
    }

    /**
     * Close result returned from select function
     */
    public function closeResult($result)
    {
        if ($result !== null) {
            pg_free_result($result);
        }
    }
}
