<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class VulnList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'VULNERABILITIES'); 

  } // __construct()
  

  public function __destruct() {

	// call the parent destructor
	parent::__destruct();

  } // __destruct()
  
  public function __ToString() {} // __ToString()
 	

  // -----------------------------------------------------------------------
  // 
  // PARAMETERS
  // 
  // -----------------------------------------------------------------------
  
  public function getVulnSeq($isKey = FALSE)                                        { array_push($this->params, 'vuln_seq');                                          if ($isKey) { $this->key = 'vuln_seq'; } }
  public function getVulnType($isKey = FALSE)                                       { array_push($this->params, 'vuln_type');                                         if ($isKey) { $this->key = 'vuln_type'; } }
  public function getVulnDescPrimary($isKey = FALSE)                                { array_push($this->params, 'vuln_desc_primary');                                 if ($isKey) { $this->key = 'vuln_desc_primary'; } }
  public function getVulnDescSecondary($isKey = FALSE)                              { array_push($this->params, 'vuln_desc_secondary');                               if ($isKey) { $this->key = 'vuln_desc_secondary'; } }
  public function getVulnDateDiscovered($isKey = FALSE)                             { array_push($this->params, 'vuln_date_discovered');                              if ($isKey) { $this->key = 'vuln_date_discovered'; } }
  public function getVulnDateModified($isKey = FALSE)                               { array_push($this->params, 'vuln_date_modified');                                if ($isKey) { $this->key = 'vuln_date_modified'; } }
  public function getVulnDatePublished($isKey = FALSE)                              { array_push($this->params, 'vuln_date_published');                               if ($isKey) { $this->key = 'vuln_date_published'; } }
  public function getVulnSeverity($isKey = FALSE)                                   { array_push($this->params, 'vuln_severity');                                     if ($isKey) { $this->key = 'vuln_severity'; } }
  public function getVulnLossAvailability($isKey = FALSE)                           { array_push($this->params, 'vuln_loss_availability');                            if ($isKey) { $this->key = 'vuln_loss_availability'; } }
  public function getVulnLossConfidentiality($isKey = FALSE)                        { array_push($this->params, 'vuln_loss_confidentiality');                         if ($isKey) { $this->key = 'vuln_loss_confidentiality'; } }
  public function getVulnLossIntegrity($isKey = FALSE)                              { array_push($this->params, 'vuln_loss_integrity');                               if ($isKey) { $this->key = 'vuln_loss_integrity'; } }
  public function getVulnLossSecurityAdmin($isKey = FALSE)                          { array_push($this->params, 'vuln_loss_security_admin');                          if ($isKey) { $this->key = 'vuln_loss_security_admin'; } }
  public function getVulnLossSecurityUser($isKey = FALSE)                           { array_push($this->params, 'vuln_loss_security_user');                           if ($isKey) { $this->key = 'vuln_loss_security_user'; } }
  public function getVulnLossSecurityOther($isKey = FALSE)                          { array_push($this->params, 'vuln_loss_security_other');                          if ($isKey) { $this->key = 'vuln_loss_security_other'; } }
  public function getVulnTypeAccess($isKey = FALSE)                                 { array_push($this->params, 'vuln_type_access');                                  if ($isKey) { $this->key = 'vuln_type_access'; } }
  public function getVulnTypeInput($isKey = FALSE)                                  { array_push($this->params, 'vuln_type_input');                                   if ($isKey) { $this->key = 'vuln_type_input'; } }
  public function getVulnTypeInputBound($isKey = FALSE)                             { array_push($this->params, 'vuln_type_input_bound');                             if ($isKey) { $this->key = 'vuln_type_input_bound'; } }
  public function getVulnTypeInputBuffer($isKey = FALSE)                            { array_push($this->params, 'vuln_type_input_buffer');                            if ($isKey) { $this->key = 'vuln_type_input_buffer'; } }
  public function getVulnTypeDesign($isKey = FALSE)                                 { array_push($this->params, 'vuln_type_design');                                  if ($isKey) { $this->key = 'vuln_type_design'; } }
  public function getVulnTypeException($isKey = FALSE)                              { array_push($this->params, 'vuln_type_exception');                               if ($isKey) { $this->key = 'vuln_type_exception'; } }
  public function getVulnTypeEnvironment($isKey = FALSE)                            { array_push($this->params, 'vuln_type_environment');                             if ($isKey) { $this->key = 'vuln_type_environment'; } }
  public function getVulnTypeConfig($isKey = FALSE)                                 { array_push($this->params, 'vuln_type_config');                                  if ($isKey) { $this->key = 'vuln_type_config'; } }
  public function getVulnTypeRace($isKey = FALSE)                                   { array_push($this->params, 'vuln_type_race');                                    if ($isKey) { $this->key = 'vuln_type_race'; } }
  public function getVulnTypeOther($isKey = FALSE)                                  { array_push($this->params, 'vuln_type_other');                                   if ($isKey) { $this->key = 'vuln_type_other'; } }
  public function getVulnRangeLocal($isKey = FALSE)                                 { array_push($this->params, 'vuln_range_local');                                  if ($isKey) { $this->key = 'vuln_range_local'; } }
  public function getVulnRangeRemote($isKey = FALSE)                                { array_push($this->params, 'vuln_range_remote');                                 if ($isKey) { $this->key = 'vuln_range_remote'; } }
  public function getVulnRangeUser($isKey = FALSE)                                  { array_push($this->params, 'vuln_range_user');                                   if ($isKey) { $this->key = 'vuln_range_user'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterVulnSeq($value = NULL, $bool = TRUE)                                        { $this->filters['vuln_seq']                                          = array($value, $bool); }
  public function filterVulnType($value = NULL, $bool = TRUE)                                       { $this->filters['vuln_type']                                         = array($value, $bool); }
  public function filterVulnDescPrimary($value = NULL, $bool = TRUE)                                { $this->filters['vuln_desc_primary']                                 = array($value, $bool); }
  public function filterVulnDescSecondary($value = NULL, $bool = TRUE)                              { $this->filters['vuln_desc_secondary']                               = array($value, $bool); }
  public function filterVulnDateDiscovered($value = NULL, $bool = TRUE)                             { $this->filters['vuln_date_discovered']                              = array($value, $bool); }
  public function filterVulnDateModified($value = NULL, $bool = TRUE)                               { $this->filters['vuln_date_modified']                                = array($value, $bool); }
  public function filterVulnDatePublished($value = NULL, $bool = TRUE)                              { $this->filters['vuln_date_published']                               = array($value, $bool); }
  public function filterVulnSeverity($value = NULL, $bool = TRUE)                                   { $this->filters['vuln_severity']                                     = array($value, $bool); }
  public function filterVulnLossAvailability($value = NULL, $bool = TRUE)                           { $this->filters['vuln_loss_availability']                            = array($value, $bool); }
  public function filterVulnLossConfidentiality($value = NULL, $bool = TRUE)                        { $this->filters['vuln_loss_confidentiality']                         = array($value, $bool); }
  public function filterVulnLossIntegrity($value = NULL, $bool = TRUE)                              { $this->filters['vuln_loss_integrity']                               = array($value, $bool); }
  public function filterVulnLossSecurityAdmin($value = NULL, $bool = TRUE)                          { $this->filters['vuln_loss_security_admin']                          = array($value, $bool); }
  public function filterVulnLossSecurityUser($value = NULL, $bool = TRUE)                           { $this->filters['vuln_loss_security_user']                           = array($value, $bool); }
  public function filterVulnLossSecurityOther($value = NULL, $bool = TRUE)                          { $this->filters['vuln_loss_security_other']                          = array($value, $bool); }
  public function filterVulnTypeAccess($value = NULL, $bool = TRUE)                                 { $this->filters['vuln_type_access']                                  = array($value, $bool); }
  public function filterVulnTypeInput($value = NULL, $bool = TRUE)                                  { $this->filters['vuln_type_input']                                   = array($value, $bool); }
  public function filterVulnTypeInputBound($value = NULL, $bool = TRUE)                             { $this->filters['vuln_type_input_bound']                             = array($value, $bool); }
  public function filterVulnTypeInputBuffer($value = NULL, $bool = TRUE)                            { $this->filters['vuln_type_input_buffer']                            = array($value, $bool); }
  public function filterVulnTypeDesign($value = NULL, $bool = TRUE)                                 { $this->filters['vuln_type_design']                                  = array($value, $bool); }
  public function filterVulnTypeException($value = NULL, $bool = TRUE)                              { $this->filters['vuln_type_exception']                               = array($value, $bool); }
  public function filterVulnTypeEnvironment($value = NULL, $bool = TRUE)                            { $this->filters['vuln_type_environment']                             = array($value, $bool); }
  public function filterVulnTypeConfig($value = NULL, $bool = TRUE)                                 { $this->filters['vuln_type_config']                                  = array($value, $bool); }
  public function filterVulnTypeRace($value = NULL, $bool = TRUE)                                   { $this->filters['vuln_type_race']                                    = array($value, $bool); }
  public function filterVulnTypeOther($value = NULL, $bool = TRUE)                                  { $this->filters['vuln_type_other']                                   = array($value, $bool); }
  public function filterVulnRangeLocal($value = NULL, $bool = TRUE)                                 { $this->filters['vuln_range_local']                                  = array($value, $bool); }
  public function filterVulnRangeRemote($value = NULL, $bool = TRUE)                                { $this->filters['vuln_range_remote']                                 = array($value, $bool); }
  public function filterVulnRangeUser($value = NULL, $bool = TRUE)                                  { $this->filters['vuln_range_user']                                   = array($value, $bool); }  

} // class VulnList

?>