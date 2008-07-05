<?php
require_once( VENDORS .DS. 'pdf' . DS . 'class.ezpdf.php');

$fields=array('status'=>'POA&M Status Information','Agency Wide','System','Total','Brief Explanation');
$table=array(
array('status'=>'A. Total number of weaknesses identified at the start of the reporting period',$this->AAW ,$this->AS,$this->AAW+$this->AS,''),
array('status'=>'B. Number of weaknesses for which corrective action was completed on time(including testing) by the end of the reporting period',$this->BAW ,$this->BS,$this->BAW+$this->BS,''),
array('status'=>'C. Number of weaknesses for which corrective action is ongoing and is on track to complete as originally scheduled ',$this->CAW ,$this->CS,$this->CAW+$this->CS,''),
array('status'=>'D. Number of weaknesses for which corrective action has been delayed including a brief explanation for the delay ',$this->DAW ,$this->DS,$this->DAW+$this->DS,''),
array('status'=>'E. Number of weaknesses discovered following the last POA&M update and a brief Explanation of how they were identified (e.g., agency review, IG evaluation, etc.)',$this->EAW ,$this->ES,$this->EAW+$this->ES,''),
array('status'=>'Total number of weaknesses remaining at the end of the reporting period ',$this->FAW ,$this->FS,$this->FAW+$this->FS,'')
);

define('REPORT_FOOTER_WARNING', "WARNING: This report is for internal, official use only.  This report contains sensitive computer security related information. Public disclosure of this information would risk circumvention of the law. Recipients of this report must not, under any circumstances, show or release its contents for purposes other than official action. This report must be safeguarded to prevent improper disclosure. Staff reviewing this document must hold a minimum of Public Trust Level 5C clearance.");

define('ORIENTATION', 'orient');
define('PAPERTYPE', 'pagesz');
define('PGWIDTH', 'pgwidth');
define('PGHEIGHT', 'pgheight');
define('FONTS', VENDORS . DS . 'pdf' . DS . 'fonts');

$poam_config = array( ORIENTATION => 'landscape', PAPERTYPE => 'LETTER', PGWIDTH => 792);
$pdf =& new Cezpdf($poam_config[PAPERTYPE], $poam_config[ORIENTATION]);
$pdf->selectFont(FONTS . DS . "Helvetica.afm");

$pdf->ezTable($table, $fields,'FISMA Report to OMB: POA&M Status Report',
    array('fontSize'=>8,'maxWidth'=>700));
$pdf->ezStream();

?>