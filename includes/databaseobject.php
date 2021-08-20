<?php
class DatabaseObject {

	// ********************************************************************************
	// Protected instance functions
	protected function create($tablename) {
		
		global $pdo;
		foreach (get_object_vars($this) as $key => $value) {
			if ($key != "id") {
				$fields[] = "`". $key. "`";
				$placeholders[] = "?";
				$values[] = $value;
			}
		}
		$sql = "INSERT INTO `". DB_PREFIX. $tablename. "` (". implode(",", $fields). ") VALUES(". implode(",", $placeholders). ")";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($values);
		$this->id = $pdo->lastInsertId();	
	}

	protected function update($tablename) {

		global $pdo;	
		foreach (get_object_vars($this) as $key => $value) {
			$fields[] = "`". $key. "`=?";
			$values[] = $value; 
		}
		$sql = "UPDATE `". DB_PREFIX. $tablename. "` SET ". implode(",", $fields). " WHERE `id`=?";
		$values[] = $this->id;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($values);	
	}

	protected function deleteObject($tablename) {

		global $pdo;
		$stmt = $pdo->prepare("DELETE FROM `". DB_PREFIX. $tablename. "`
			WHERE `id` = ? LIMIT 1;");
		$values[] = $this->id;
		$stmt->execute($values);

		unset($this->id);
		return true;
	}
}
?>