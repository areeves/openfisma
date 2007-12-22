<?php

// 
// CLASS DEFINITION
// 

class Pager {
  
	// ---------------------------------------------------------------------------
  	// 
  	// VARIABLES
  	// 
  	// ---------------------------------------------------------------------------

  	// static variables variables
	private $form_start;
	private $form_close;

  	private $list_size;

	private $page_offset;
  	private $page_size;
  	
  	private $page_interval;
  	private $page_sizes;

  	private $current_page;



  	// ---------------------------------------------------------------------------
  	// 
  	// CLASS METHODS
  	// 
  	// ---------------------------------------------------------------------------

  	public function  __construct($page_size = 0, $page_interval = 0) {

		// set up some default values
		$this->list_size     = 0;
		$this->page_interval = $page_interval;

		$this->setPageSize($page_size);
		$this->setCurrentPage(1);

  	} // __construct()


  	public function __destruct() {  } // __destruct()


  	public function __ToString() { 

		return  
	  		"\n<pre>".
	  		"\nPAGER".
			"\n-----".
	  		"\ncurrent_page : ".$this->current_page.
	  		"\nlast_page    : ".$this->getLastPage().
	  		"\n".
	  		"\npage_offset  : ".$this->getPageOffset().
	  		"\nlist_start   : ".$this->getListStart().
	  		"\nlist_end     : ".$this->getListEnd().
	  		"\nlist_size    : ".$this->getListSize().
	  		"\n".
	  		"\npage_size    : ".$this->page_size.
	  		"\npage_sizes   : ".$this->getPageSizes().
	  		"\n</pre>";

  	} // __ToString()


	// ---------------------------------------------------------------------------
	// 
  	// INTERNAL METHODS - PAGE UPDATING
  	// 
  	// ---------------------------------------------------------------------------

  	private function getLastPage()  { return ceil($this->list_size / $this->page_size);    }
 	private function getListSize()  { return $this->list_size; }

 	private function getListStart() { 

		// return the smaller between the list end and the offset + 1
		return min($this->getListEnd(), ($this->getPageOffset() + 1));

  	} // getListStart()


  	private function getListEnd() {

		// return the smaller between the list size and the next page offset
		return min(($this->getPageOffset() + $this->page_size), $this->list_size);

  	} // getListEnd()


  	private function page_jump($target_page = 1)  {

		// move offset to first item on the desired page
		if (($target_page > 0) && ($target_page <= $this->getLastPage()))  {

	  		$this->current_page = $target_page;

		} // if target_page

  	} // page_jump()


  	private function page_prev() { $this->page_jump($this->current_page - 1); } // page_prev()
  	private function page_next() { $this->page_jump($this->current_page + 1); } // page_next()

	// ---------------------------------------------------------------------------
	// 
  	// INTERNAL METHODS - DROPDOWN GENERATION
  	// 
  	// ---------------------------------------------------------------------------	
	
	private function getPageSizes() {

		// set up our array
		$pages = ceil($this->list_size / $this->page_interval);
		$sizes = array();

		// add page sizes n * page_size, while n < number of pages
		for ($i = 1; $i <= $pages; $i++) { array_push($sizes, $i * $this->page_interval); }
	
		// return the results
		return $sizes;

  	} // getPageSizes()


  	private function getPageJumps()     { 

		// set up our array
		$last_page = $this->getLastPage();
		$jumps     = array();

		// add pages 1 .. last_page
		for ($i = 1; $i <= $last_page; $i++) { array_push($jumps, $i); }
	
		// return the results
		return $jumps;

  	} // getPageJumps()



	// ---------------------------------------------------------------------------
	// 
  	// SETTER METHODS
  	// 
  	// ---------------------------------------------------------------------------
  	
  	public function setFormTags($form_start = FALSE, $form_close = FALSE ) {
	
		if ($form_start) { $this->form_start = '1'; } else { $this->form_start = '0'; }
		if ($form_close) { $this->form_close = '1'; } else { $this->form_close = '0'; }
		
	}
	
	// this comes from the base FooList.class
  	public function setListSize($list_size)          { $this->list_size = $list_size;      }
  	
  	// these two come from the form
	public function setPageSize($page_size = 1)      { $this->page_size = $page_size;      }	
  	public function setCurrentPage($target_page = 1) { $this->current_page = $target_page; }


  	// ---------------------------------------------------------------------------
  	// 
  	// GETTER METHODS
  	// 
  	// ---------------------------------------------------------------------------

	public function getPageOffset() { return ($this->current_page - 1) * $this->page_size; }
	public function getPageSize()   { return $this->page_size; }


  	// ---------------------------------------------------------------------------
  	// 
  	// ACTION METHODS
  	// 
  	// ---------------------------------------------------------------------------

	public function doPageAction($action, $page_size, $page_jump = 1) {
	
		// act on the action given to us	
		switch ($action) {
			
			// handle pager requests
 			case 'page_jump'  : $this->page_jump($page_jump); break;
 			case 'page_first' : $this->page_jump(1); break;
 			case 'page_prev'  : $this->page_prev();  break;
 			case 'page_next'  : $this->page_next();  break;
 			case 'page_last'  : $this->page_jump($this->getLastPage());  break;
 			case 'page_size'  : $this->setPageSize($page_size);	$this->page_jump(1); break;
 			
 			// no action specified or something other than 
			default           : $this->page_jump(1);
			
		}		
		
	}

	public function commit($_TEMPLATE) {
		
		$_TEMPLATE->assign('pager_form_start', $this->form_start);
		$_TEMPLATE->assign('pager_form_close', $this->form_close);
		
		$_TEMPLATE->assign('page_size',        $this->page_size);
		
		$_TEMPLATE->assign('current_page',     $this->current_page);
		$_TEMPLATE->assign('last_page',        $this->getLastPage());
		
		$_TEMPLATE->assign('page_sizes',       $this->getPageSizes());
		$_TEMPLATE->assign('page_jumps',       $this->getPageJumps());
		
		$_TEMPLATE->assign('list_size',        $this->list_size);
		$_TEMPLATE->assign('list_start',       $this->getListStart());
		$_TEMPLATE->assign('list_end',         $this->getListEnd());
		
	}
		


} // Pager


// -----------------------------------------------------------------------------
// 
// MAIN
// 
// -----------------------------------------------------------------------------

// INCLUDES
require_once('Config.class.php');

// create our pager instance
$_PAGER = new Pager($_CONFIG->PAGE_SIZE(), $_CONFIG->PAGE_INTERVAL());

?>