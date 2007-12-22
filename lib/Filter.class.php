<?PHP

class Filter {
	
	
	// ----------------------------------------------------------------
	// 
	// VARIABLES
	// 
	// ----------------------------------------------------------------
	
	private $ALL;
	private $bool_array;
	
	private $filter_list;
	
	private $form_start;
	private $form_close;
	
	
	// ----------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// ----------------------------------------------------------------

	public function __construct() {
	
		// set up our bool_array
		$this->bool_array[0] = 'is not';
		$this->bool_array[1] = 'is';
		
		// set up our ALL value
		$this->setALL();
		
		// set up our filter_list
		$this->filter_list = array();
		
		// default to being a standalone form
		$this->setFormTags(TRUE, TRUE);
		
	}
	
	public function __destruct() {}
	
	public function __ToString() {}	


	// ----------------------------------------------------------------
	// 
	// INTERFACE METHODS
	// 
	// ----------------------------------------------------------------

	public function setALL($text = '-- all --') { $this->ALL = $text; }
	public function getALL()                    { return $this->ALL;  }

	public function arrayToOptions($array){
	
		// create a new associative array
		$hash = array();
	
		// loop through our array to make the val => val hash
		foreach ($array as $item) { $hash[$item] = $item; }
	
		// return the hash
		return $hash;	
	
	}

	public function setFormTags($form_start = FALSE, $form_close = FALSE ) {
	
		if ($form_start) { $this->form_start = '1'; } else { $this->form_start = '0'; }
		if ($form_close) { $this->form_close = '1'; } else { $this->form_close = '0'; }
		
	}

	public function addFilter($title, $prefix, $options, $selected, $show_bool, $bool_selected = 1) {
		
		// prepend our any to the counter
		$options = array($this->ALL => $this->ALL) + $options;
		if (! $selected) { $selected = $this->ALL; };
		
		// create our new filter
		$new_filter = array();
		$new_filter['title']         = $title;
		$new_filter['prefix']        = $prefix;
		$new_filter['options']       = $options;
		$new_filter['selected']      = $selected;
		$new_filter['show_bool']     = $show_bool;
		$new_filter['bool_name']     = $prefix.'_bool';
		$new_filter['bool_selected'] = $bool_selected;
		
		// add the new filter to our filter_list
		array_push($this->filter_list, $new_filter);
	
	}

	public function commit($_TEMPLATE) {

		// assign our filter_list to the template
		$_TEMPLATE->assign('bool_array',  $this->bool_array);
		$_TEMPLATE->assign('filter_list', $this->filter_list);
		
		$_TEMPLATE->assign('filter_form_start',  $this->form_start);
		$_TEMPLATE->assign('filter_form_close',  $this->form_close);
		
	}
	

}

$_FILTER = new Filter();

?>