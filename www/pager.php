<?php

//  
//  INCLUDES
// 

require_once('../lib/Config.class.php');


// 
// CLASS DEFINITION
// 

class Pager {
  
  // ---------------------------------------------------------------------------
  // 
  // VARIABLES
  // 
  // ---------------------------------------------------------------------------

  // configuration variables
  private $page_size;
  private $page_interval;

  // list variables
  private $list_size;
  private $list_offset;

  // ---------------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // ---------------------------------------------------------------------------

  public function  __construct($list_size = NULL) {

	// set up some default values
	$this->page_size     = $_CONFIG->PAGE_SIZE();
	$this->page_interval = $_CONFIG->PAGE_INTERVAL();
	$this->list_size     = 0;
	$this->list_offset   = 0;

  } // __construct()


  public function __destruct() {  } // __destruct()


  public function __ToString() { return  ""; } // __ToString()



  // ---------------------------------------------------------------------------
  // 
  // SET METHODS
  // 
  // ---------------------------------------------------------------------------

  public function setListSize($list_size = 0)     { $this->list_size = $list_size; }
  public function setListOffset($list_offset = 0) { $this->list_offset = $list_offset; }

  public function setPageSize($page_size = 0) {

	// set to value if page_size > 0, otherwise default
	if ($page_size > 0) { $this->page_size = $page_size; }
	else { $this->page_size = $_CONFIG->PAGE_SIZE(); }


  } // setPageSize
  


  // ---------------------------------------------------------------------------
  // 
  // GET METHODS
  // 
  // ---------------------------------------------------------------------------

  public function getListOffset()    { return $this->list_offset;     }
  public function getListStart()     { return $this->list_offset + 1; }
  public function getListEnd() {

	// return the last item in the list if list_offset +  page_size is larger than list_size
	if ($this->list_size <= ($this->list_offset + $this->page_size)) { return $this->list_size; }
	else { return $this->list_offset + $this->page_size; } 

  } // getListEnd()


  public function getPageSize()  { return $this->page_size; }
  public function getPageSizes() {

	// set up our array
	$last_page = getLastPage();
	$sizes     = array();

	// add page sizes n * page_size, while n < number of pages
	for ($i = 1; $i < $last_page; $i++) { array_push($sizes, $i * $this->page_interval); }
	
	// return the results
	return $sizes;

  } // getPageSizes()


  public function getPageJumps()     { 

	// set up our array
	$last_page = getLastPage();
	$jumps     = array();

	// add pages 1 .. last_page
	for ($i = 1; $i <= $last_page; $i++) { array_push($jumps, $i); }
	
	// return the results
	return $jumps;

  } // getPageJumps()


  public function getCurrentPage(  ) {

	return ceil(($this->list_offset + 1) / $this->page_size);

  } // getCurrentPage()


  public function getLastPage() { 

	return ceil($this->list_size / $this->page_size);

  } // getLastPage()


  // ---------------------------------------------------------------------------
  // 
  // ACTION METHODS - these actions adjust $list_offset
  // 
  // ---------------------------------------------------------------------------

  public function page_first() { 

	// reset the offset to 0
	$this->list_offset = 0;

  } // page_first()


  public function page_prev()  { 

	// set offset back 1 page_size, or to 0 in case of negative
	if (($this->list_offset - $this->page_size) < 0) { $this->list_offset = 0; }
	else { $this->list_offset -= $this->page_size; }

  } // page_prev()


  public function page_next()  {

	// grab the page values
	$current = getCurrentPage();
	$last    = getLastPage();

	// go to the next page, or stay at last if already there
	if (($current + 1) <= $last) { $this->list_offset = $this->page_size * $current;  }
	else { $this->list_offset = $this->page_size * ($last -1); }


  } // page_next()


  public function page_last()  {

	$this->list_offset = (getLastPage() - 1) * $this->page_size;

  } // page_last()


  public function page_jump($target_page = 1)  {

	// move offset to first item on the desired page
	$this->list_offset = ($this->page_size * $target_page - 1);

  } // page_jump()


} // Pager

?>