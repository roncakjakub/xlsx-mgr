<?php /**
 * 
 */
class DbController 
{
	public $pdo;
	function __construct($pdo)
	{
		$this->pdo = $pdo;
		$this->pdo->query("SET NAMES UTF8");
		
	}

	/**
     * @param $table - tabuľka
     * @param $cols - text
     * @param null $where - text
     * @param null $orderby - text
     * @return bool
     */

	function select($table,$cols,$where=NULL,$join=NULL,$orderby=NULL){
		/********************************
			Ak jestem where, pridaj k nemu aj slovíčko do klauzuly.
		********************************/
		$where=($where)? "where ".$where : "" ;
		$orderby=($orderby)? "order by ".$orderby : "" ;

		return $this->pdo->query("SELECT ".$cols." FROM ".$table." ".$join." ".$where." ".$orderby);
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

		if($res=$this->pdo->query("SELECT ID FROM ".$table." ".$where))
		return $res->fetch()["ID"];
		else return false;
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
		$sql = "INSERT INTO ".$table." (".$cols.") VALUES (".$qm.")";
		return ($this->pdo->prepare($sql)->execute(array_values($values))?$this->pdo->lastInsertId():false);
	}
	/**
     * @return array
     */
	
		function getFullData($possName=NULL,$searchString=NULL){
			$rowID=0;
		/********************************
			Zisti pocet prvkov v velues referencii
		********************************/
		$where=($possName)?"nazov=".$possName:NULL;
		$dbResult=$this->select("dataview","*",$where /*Where hocičo je $searchString*/);
			
			while ($row=$dbResult->fetch()) {
			$invArr=$this->select("investori","investori.*","inv_midd.riadok_fk=".$row["ID"], "join inv_midd on inv_midd.investor_fk=investori.ID");
				//Ak je, v tomto prípade 0. prvok, NULL - riadok neexistuje 
				$numericRow=array_values($row);
				list(
						/*ID*/,
						$array[$rowID]["rowNO"],
						$array[$rowID]["subor_fk"],
						$array[$rowID]["notif"],
						$array[$rowID]["content"],
						$array[$rowID]["downloaded"],
						$array[$rowID]["archived"],
						$array[$rowID]["expirKnow"],
						/*enddate*/,
						$array[$rowID]["nazov"],
						$array[$rowID]["fileName"],
				)=$numericRow;

				$array[$rowID]["empty"]=0;
				$array[$rowID]["nameArr"]=$array[$rowID]["emailArr"]=array();
				while ($invRow=$invArr->fetch()) {
					array_push($array[$rowID]["nameArr"], $invRow["meno"]);
					array_push($array[$rowID]["emailArr"], $invRow["email"]);
				}

				$explodedDate=explode("-", $row["expDate"]);
				$array[$rowID]["enddate"]=$explodedDate[2].".".$explodedDate[1].".".$explodedDate[0];	
				
				$rowID++;
 			}

			return $array;
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