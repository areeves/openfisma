<?PHP

class Listing {
	
	// ----------------------------------------------------------------
	// 
	// VARIABLES
	// 
	// ----------------------------------------------------------------
	
	private $form_target;	
	private $form_start;
	private $form_close;
	
	private $columns;
	private $rows;
	
	private $row_index;
	
	private $show_create;
	private $show_actions;
	
	
	// ----------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// ----------------------------------------------------------------
	
	public function __construct() {
		
		// initialize our values
		$this->reset();
		
	}
	
	public function __destruct()  {}
	public function __ToString()  {}
	
	
	// ----------------------------------------------------------------
	// 
	// PRIVATE METHODS
	// 
	// ----------------------------------------------------------------	
	
	private function reset() {

		// reset the form start/close tags
		$this->form_target = 'NONE';		
		$this->form_start  = FALSE;
		$this->form_close  = FALSE;

		// create our columns array
		$this->columns = array();
		$this->rows    = array();
		
		$this->row_index = NULL;
		
		// track this independent of the row buttons
		$this->show_create = FALSE;

	}
	
	
	// ----------------------------------------------------------------
	// 
	// INTERFACE METHODS
	// 
	// ----------------------------------------------------------------

	public function setFormTarget($form_target = 'NONE') { $this->form_target = $form_target; }

	public function setCreateTarget($create_target = NULL) { $this->create_target = ($create_target) ? $create_target : $this->form_target; }
	public function setViewTarget($view_target = NULL)     { $this->view_target   = ($view_target)   ? $view_target   : $this->form_target; }
	public function setUpdateTarget($update_target = NULL) { $this->update_target = ($update_target) ? $update_target : $this->form_target; }
	public function setDeleteTarget($delete_target = NULL) { $this->delete_target = ($delete_target) ? $delete_target : $this->form_target; }

	public function setFormTags($form_start = FALSE, $form_close = FALSE) {
		
		$this->form_start = $form_start;
		$this->form_close = $form_close;
		
	}
	
	public function setRowIndex($row_index = NULL) { $this->row_index = $row_index; }

	public function showActions($show_actions = TRUE)       { $this->show_actions     = ($show_actions)    ? 1 : 0; }
	public function showCheckboxes($show_checkboxes = TRUE) { $this->show_checkboxes  = ($show_checkboxes) ? 1 : 0; }
	public function showCreate($show_create = TRUE)         { $this->show_create      = ($show_create)     ? 1 : 0; }
		
	public function addColumn($header, $show_header = TRUE, $key, $sortable = TRUE, $span = 1, $align = 'CENTER') {
		
		
		// create our new column
		$new_column = array();
		
		$new_column['header']      = $header;
		$new_column['show_header'] = ($show_header == TRUE) ? 1 : 0;
		$new_column['key']         = $key;
		$new_column['sortable']    = ($sortable == TRUE) ? 1 : 0;
		$new_column['span']        = $span;
		$new_column['align']       = $align;
		
		// save the column information
		array_push($this->columns, $new_column);
		
	}

	public function addRow($show_checkbox = FALSE, $row_data, $show_view = FALSE, $show_update = FALSE, $show_delete = FALSE) {
		
		// create our new row
		$new_row = array();
		
		// save the row data
		$new_row['row_data']      = $row_data;
		
		// update the options
		$new_row['show_checkbox'] = ($show_checkbox) ? 1 : 0;
		$new_row['show_view']     = ($show_view)     ? 1 : 0;
		$new_row['show_update']   = ($show_update)   ? 1 : 0;
		$new_row['show_delete']   = ($show_delete)   ? 1 : 0;
		
		// save the row
		array_push($this->rows, $new_row);
		
	}
	
	public function commit($_TEMPLATE) {

		// assign the form target
		$_TEMPLATE->assign('form_target', $this->form_target);
	
		// assign the form values
		$_TEMPLATE->assign('listing_form_start', $this->form_start);
		$_TEMPLATE->assign('listing_form_close', $this->form_close);
	
		// add in the header / row info
		$_TEMPLATE->assign('columns', $this->columns);
		$_TEMPLATE->assign('rows',    $this->rows);
		
		// which row to use as our index value
		$_TEMPLATE->assign('row_index', $this->row_index);
		
		// count the columns 
		$_TEMPLATE->assign('column_count', count($this->columns) + $this->show_checkboxes + $this->show_actions);
		
		// add in the checkboxes
		$_TEMPLATE->assign('show_checkboxes', $this->show_checkboxes);		
		$_TEMPLATE->assign('show_actions',    $this->show_actions);		
		$_TEMPLATE->assign('create_target',   $this->create_target);
		$_TEMPLATE->assign('view_target',     $this->view_target);
		$_TEMPLATE->assign('update_target',   $this->update_target);
		$_TEMPLATE->assign('delete_target',   $this->delete_target);
		
		
		// add in the buttons
		$_TEMPLATE->assign('show_create', $this->show_create);
		$_TEMPLATE->assign('show view',   $this->show_view);
		$_TEMPLATE->assign('show_update', $this->show_update);
		$_TEMPLATE->assign('show_delete', $this->show_delete);
		
	}
	
}

$_LISTING = new Listing();

?>