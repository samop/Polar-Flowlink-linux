<?php
class baza{
	public $dbhandle=NULL;
	public $ok=false;
	public $rows=0;
	public $query=NULL;
	public $crow=NULL;

	public function connect(){
		include './config.php';
		if($dbms=='postgres') $this->dbhandle=pg_connect("host=".$dbhost." dbname=".$dbname." user=".$dbuser." password=".$dbpasswd);
		else $this->dbhandle=NULL;
	}

	public function query($sql){
		$this->query=pg_query($this->dbhandle,$sql);
		if($this->query) {
		$this->ok=true;
		$this->rows=pg_num_rows($this->query);
		}
		else $this->ok=false;
	}

	public function insertblob($filename){
		$this->query=pg_query($this->dbhandle,"begin");
		$blob=pg_lo_import($this->dbhandle, $filename);
		pg_query($this->dbhandle,"commit");
		return $blob;
		//TODO: kontola, ali je bil query ok izveden!
	}

	public function getblob($oid, $filename){
		$this->query=pg_query($this->dbhandle,"begin");
		$blob=pg_lo_export($this->dbhandle, $oid, $filename);
		pg_query($this->dbhandle,"commit");
		return $blob;
		//TODO: kontola, ali je bil query ok izveden!
	}

	public function getstreamblob($oid){
		$this->query=pg_query($this->dbhandle,"begin");
		$handle = pg_lo_open($this->dbhandle, $oid, "r");
		$blob=pg_lo_read_all($handle);
		pg_query($this->dbhandle,"commit");
		return $blob;
		//TODO: kontola, ali je bil query ok izveden!
	}

	public function deleteblob($oid){
		$this->query=pg_query($this->dbhandle,"begin");
		$blob=pg_lo_unlink($this->dbhandle, $oid);
		pg_query($this->dbhandle,"commit");
		return $blob;
		//TODO: kontola, ali je bil query ok izveden!
	}

	public function close(){
		pg_close($this->dbhandle);
	}

	public function getrow($rowno){
		if($this->ok){
		$this->crow=pg_fetch_array($this->query,$rowno,PGSQL_ASSOC);
	//	return 1;
		}
		else
		{
		$this->crow=NULL;
	//	return 0;
		}
	}
}

?>
