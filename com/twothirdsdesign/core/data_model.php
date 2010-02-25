<?php
/**
* 
*/
class DataModel
{
		protected static $table_name;
		protected static $db_exclude = array();

		// Common DB methods
		public static function find_all(){
			global $wpdb;
			return self::find_by_sql( "SELECT * FROM ". $wpdb->prefix . static::$table_name );
		}

		public static function find_by_id( $id=0 ){
			if( empty( $id ) ) {
				return false;
			}
			global $wpdb;
			$sql  = "SELECT * FROM " . $wpdb->prefix . static::$table_name;
			$sql .= " WHERE id=" . $wpdb->escape( $id ) . " LIMIT 1;";
			$object_array = self::find_by_sql( $sql );	
			
			return !empty($object_array) ? array_shift( $object_array ) : false;
		}

		public static function find_by_sql($sql=''){
			global $wpdb;
			$result_set = $wpdb->get_results( $sql , ARRAY_A );
			$object_array = array();
			if($result_set){
				foreach ( $result_set as $row ) {
					$object_array[] = static::instantiate( $row );
				}
				return $object_array;
			}else {
				return false;
			}
		}

		public static function count_all(){
			global $wpdb;
			$sql = "SELECT COUNT(*) FROM " . $wpdb->prefix . static::$table_name;
			return $wpdb->get_var( $sql );
			//return array_shift($row);
		}

		public function find_limit_offset( $limit=0, $offset=0 ){
			global $wpdb;
			$sql  = "SELECT * FROM " . $wpdb->prefix . static::$table_name;
			$sql .= " LIMIT {$limit} OFFSET {$offset}";
			return self::find_by_sql( $sql ); 
		}
		
		public function find_where_column_value($column, $value='', $limit=0){
			if (!empty($column)) {
				global $wpdb;
				
				$sql = "SELECT * FROM " . $wpdb->prefix . static::$table_name;
				$sql.= " WHERE '{$column}' =".$wpdb->escape($value);
				if(!empty($limit)){
					$sql .= " LIMIT {$limit}";
				}
				return self::find_by_sql( $sql );	
			}else {
				return false;
			}	
		}
		
		// DB CRUD 
		public function save(){
			return empty( $this->id ) ? $this->create() : $this->update();
		}

		protected function create(){
			global $wpdb;

			$attributes = $this->sanitized_attributes();

			$sql  = "INSERT INTO " . $wpdb->prefix . static::$table_name . " ("; 
			$sql .= join(", ", array_keys($attributes));
			$sql .= ") VALUES ('";
			$sql .= join("', '", array_values($attributes));
			$sql .= "')";

			if($wpdb->query( $sql )) {
				$this->id = $wpdb->insert_id;
				return ture;
			} else {
				echo $sql;
				return false;
			}
		}

		protected function update(){
			global $wpdb;

			$attributes = $this->sanitized_attributes();
			$attribute_pairs = array();
			foreach ( $attributes as $key => $value ) {
				$attribute_pairs[] = "{$key}='{$value}'";
			}

			$sql  = "UPDATE ". $wpdb->prefix . static::$table_name ." SET ";
			$sql .= join( ", ", $attribute_pairs );

			$sql .= " WHERE id=" . $this->id;
			
			return ( $wpdb->query( $sql ) == 1 ) ? ture : false;
		}

		public function delete(){
			global $wpdb;

			$sql  = "DELETE FROM ". $wpdb->prefix . static::$table_name ." WHERE ";
			$sql .= "id=" . $this->id . " LIMIT 1";

			return ( $wpdb->query( $sql ) == 1 ) ? ture : false;
		}


		// Create a user Object inta
		private static function instantiate( $record ){
			$class_name = get_called_class();

			$object = new $class_name;

			//more dynamic approch
			foreach( $record as $attribute => $value ){
				if( $object->has_attribute( $attribute ) ){
					$object->$attribute = $value;
				}
			}
			return $object;
		}


		// Checks for the Attribute
		private function has_attribute( $attribute ){
			$object_vars = $this->attributes();
			return array_key_exists( $attribute, $object_vars );
		}

		protected function attributes(){

			$filtered_attributes = get_object_vars( $this );

			foreach ( static::$db_exclude as $value ) {
				if( array_key_exists( $value, $filtered_attributes ) )
					unset( $filtered_attributes[ $value ] );
			}
			return $filtered_attributes;
		}

		protected function sanitized_attributes(){
			global $wpdb;

			$clean_attributes = array();

			foreach ($this->attributes() as $key => $value) {
				$clean_attributes[$key] = $wpdb->escape($value);
			}
			return $clean_attributes;
		}
	}
?>