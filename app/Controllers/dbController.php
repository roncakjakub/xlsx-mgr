<?php /**
 * 
 */
class DbController 
{
	public $pdo;
	function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	/**
     * @param $table - tabuľka
     * @param $cols - text
     * @param null $where - text
     * @param null $orderby - text
     * @return bool
     */

	function select($table,$cols,$where=NULL,$orderby=NULL){
		/********************************
			Ak jestem where, pridaj k nemu aj slovíčko do klauzuly.
		********************************/
		$where=($where)? "where ".$where : "" ;
		$orderby=($orderby)? "order by ".$orderby : "" ;
		return $this->pdo->query("SELECT ".$cols." FROM ".$table." ".$where." ".$orderby)->fetch();
	}

	/**
     * @param $table - tabuľka
     * @param null $where - text
     * @return bool
     */

	function getCount($table,$where=NULL){
		/********************************
			Ak jestem where, pridaj k nemu aj slovíčko do klauzuly.
		********************************/
		$where=($where)? "where ".$where : "" ;
		return $this->pdo->query("SELECT count(ID) as cnt FROM ".$table." ".$where)->fetch()["cnt"];
	}

	/**
     * @param $table - tabuľka
     * @param $where - text
     * @return bool
     */

	function getID($table,$where){
		/********************************
			Ak jestem where, pridaj k nemu aj slovíčko do klauzuly.
		********************************/
		$where=($where)? "where ".$where : "" ;
		return $this->pdo->query("SELECT ID as id FROM ".$table." ".$where)->fetch()["id"];
	}
	/**
     * @param $table - tabuľka
     * @param $cols - text
     * @param $values - array
     * @return bool
     */
	
		function insert($table,$cols,$values){
		/********************************
			Zisti pocet prvkov v velues referencii
		********************************/

		$qm = implode(',', array_fill(0, count($values), '?'));
			var_dump($qm);die();
		$sql = "INSERT INTO ".$table." (".$cols.") VALUES (".$qm.")";
		return ($this->pdo->prepare($sql)->execute(array_values($values))?$this->pdo->lastInsertId():false);
	}

	/**
     * @param $table - tabuľka
     * @param $cols - array
     * @param $values - array
     * @param $where - podmienka
     * @return bool
     */
	
	function update($table,$cols,$values,$where=NULL){
		/********************************
			Zisti pocet prvkov v velues referencii
		********************************/
		$where=($where)? "where ".$where : "" ;
		$qm="";
		for ($i=0; $i < sizeof($values); $i++) {
			$qm.= $cols[$i]." = ? ". ((sizeof($values)!=$i+1)?", ":"");
		}
		$sql = "UPDATE ".$table." set ".$qm." ".$where;
		return $this->pdo->prepare($sql)->execute(array_values($values));
	}

	/**
     * @param $table - tabuľka
     * @param $where - podmienka
     * @return bool
     */
	
	function delete($table,$where){
		$sql = "DELETE from ".$table." where ".$where;
		return $this->pdo->exec($sql);
	}
} ?>