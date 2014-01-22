<?php
require_once("class.Database.inc.php");

//class to form and execute MySQL insert and update statements
class Autocomplete {

	protected $table;
	protected $column_name;

	public function __construct($column_name, $table){
		$this->column_name = $column_name;
		$this->table = $table;
	}

	public function set_table($table){
		$this->table = $table;
	}

	public function set_column_name($column_name){
		$this->column_name = $column_name;
	}

	//returns JSON of all organizations that match the list of chars wrapped in a data object array
	public function get_results_as_JSON($chars){
		$query = "SELECT $this->column_name FROM " . $this->table . " WHERE $this->column_name LIKE '" . $chars . "%' ORDER BY $this->column_name";
		//if there are results for the current characters requested
		if($matching_strings = Database::get_results_as_numerical_array($query, $this->table)){
			$obj = new stdClass();
			$obj->data = $matching_strings;
			return json_encode($obj);
		}else return "{ \"error\" : \"no results found\"}";
	}

	//adds the contents of a comma delimited organizations list to the organizations table
	//returns false on failure
	public function add_list_to_table($list){
		$query = "SELECT $this->column_name FROM " . $this->table;
		$list = commas_to_array($list);
		if($old_list = Database::get_results_as_numerical_array($query, $this->column_name)){
			foreach($list as $list_item){
				if(!in_array($list_item, $old_list)) $this->add_to_table($list_item);
			}
		}else{ //if there is nothing in the organizations table
			foreach($list as $list_item){
				$this->add_to_table($list_item);
			}
		}
	}

	//adds an organization to the organization table.
	//returns true on success and false on failure.
	protected function add_to_table($string){
		$query = "INSERT INTO " . $this->table . " (`$this->column_name`) VALUES ('" . $string . "')";
		return Database::execute_sql($query);
	}

}
?>