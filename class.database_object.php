<?php
/**
 * DatabaseObject Class
 * 
 * @package Main
 * @subpackage Basic
 * @author Faizan Ayubi
 */
class DatabaseObject {
	public static function find_all($field = "id") {
		return self::find_by_sql("SELECT {$field} FROM ".static::$table_name." ORDER BY id DESC");
	}
	
	public static function find_by_group($group, $field = "id") {
		return self::find_by_sql("SELECT {$field} FROM ".static::$table_name." GROUP BY({$group}) ORDER BY id DESC");
	}

	public static function find_by_id($given_id, $id=0, $attributes="*") {
		$result_array = self::find_by_sql("SELECT {$attributes} FROM ".static::$table_name." WHERE {$given_id} = '{$id}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false ;
	}

	public static function find_by_property($property, $property_id, $limit="1000", $offset="0", $attributes="id") {
		return self::find_by_sql("SELECT {$attributes} FROM ".static::$table_name." WHERE {$property} = '{$property_id}' ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
	}

	public static function search_by_property($property, $value, $limit="1000", $offset="0") {
		return self::find_by_sql("SELECT id FROM ".static::$table_name." WHERE {$property} LIKE '%$value%' ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
	}

	public static function find_by_properties($parameters, $limit="1000", $offset="0") {
		$where = "";
		$count = 1;
		$length = count($parameters);
		foreach ($parameters as $field => $value) {
			$where .= "{$field} = '{$value}'";
			if($count < $length) {
				$where .= " AND ";
			}
			$count++;
		}
		return self::find_by_sql("SELECT id FROM ".static::$table_name." WHERE {$where} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
	}
	
	public static function attributes_by_parameters($attributes, $parameters, $limit="1000", $offset="0") {
		$attr = "";
		$i = 1;
		$attr_length = count($attributes);
		foreach ($attributes as $attribute) {
			$attr .= "{$attribute}";
			if($i < $attr_length) {
				$attr .= ", ";
			}
			$i++;
		}
		
		$param = "";
		$j = 1;
		$param_length = count($parameters);
		foreach ($parameters as $field => $value) {
			$param .= "{$field} = '{$value}'";
			if($j < $param_length) {
				$param .= " AND ";
			}
			$j++;
		}
		return self::find_by_sql("SELECT {$attr} FROM ".static::$table_name." WHERE {$param} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
	}

	public static function find_only_by_property($parameters, $property, $property_id, $limit="1000", $offset="0") {
		$param = "";
		$count = 1;
		$length = count($parameters);
		foreach ($parameters as $field) {
			$param .= "{$field}";
			if($count < $length) {
				$param .= ", ";
			}
			$count++;
		}
		return self::find_by_sql("SELECT {$param} FROM ".static::$table_name." WHERE {$property} = '{$property_id}' ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
	}

	public static function find_by_daterange($property, $start, $end, $limit="1000", $offset="0") {
		return self::find_by_sql("SELECT id FROM ".static::$table_name." WHERE {$property} BETWEEN '{$start}' AND '{$end}' ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
	}

	public static function find_by_sql($sql="") {
		global $database;
		$result_set = $database->query($sql);
		$object_array = array();
		while ($row = $database->fetch_array($result_set)) {
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}

	private static function instantiate($record) {
		$object = new static;
		foreach ($record as $attribute => $value) {
			if ($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}

	private function has_attribute($attribute) {
		$object_vars = self::attributes();
		return array_key_exists($attribute, $object_vars);
	}

	protected function attributes() {
		$attributes = array();
		foreach(static::$db_fields as $field) {
			if(property_exists($this, $field)) {
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}

	protected function sanitized_attributes() {
		global $database;
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value) {
			$clean_attributes[$key] = $database->escape_value($value);
		}
		return $clean_attributes;
	}

	public function save() {
		return isset($this->id) ? $this->update() : $this->create();
	}

	public static function count_by_property($property, $value) {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".static::$table_name." WHERE {$property} LIKE '%$value%'";
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}
	
	public static function count_all() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".static::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}

	public function create() {
		global $database;
		$attributes = $this->sanitized_attributes();
		$sql  = "INSERT INTO ".static::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";

		if ($database->query($sql)) {
			$this->id = $database->insert_id();
			return true;
		} else {
			return false;
		}
	}

	public function update() {
		global $database;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach ($attributes as $key => $value) {
			$attribute_pairs[] = "{$key} = '{$value}'";
		}
		$sql  = "UPDATE ".static::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id = ".$database->escape_value($this->id);
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		$sql  = "DELETE FROM ".static::$table_name;
		$sql .= " WHERE id = ".$database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;	
	}
}

?>