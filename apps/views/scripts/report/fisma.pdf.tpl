<?php

require_once( VENDORS .DS. 'pdf' . DS . 'class.ezpdf.php');

$summary=$this->summary;

$fields=array('status'=>'POA&M Status Information','Agency Wide','System','Total','Brief Explanation');
$table=array(
array('status'=>'A. Total number of weaknesses identified at the start of the reporting period',$summary['AAW'] ,$summary['AS'],$summary['AAW']+$summary['AS'],''),
array('status'=>'B. Number of weaknesses for which corrective action was completed on time(including testing) by the end of the reporting period',$summary['BAW'] ,$summary['BS'],$summary['BAW']+$summary['BS'],''),
array('status'=>'C. Number of weaknesses for which corrective action is ongoing and is on track to complete as originally scheduled ',$summary['CAW'],$summary['CS'],$summary['CAW']+$summary['CS'],''),
array('status'=>'D. Number of weaknesses for which corrective action has been delayed including a brief explanation for the delay ',$summary['DAW'] ,$summary['DS'],$summary['DAW']+$summary['DS'],''),
array('status'=>'E. Number of weaknesses discovered following the last POA&M update and a brief Explanation of how they were identified (e.g., agency review, IG evaluation, etc.)',$summary['EAW'],$summary['ES'],$summary['EAW']+$summary['ES'],''),
array('status'=>'Total number of weaknesses remaining at the end of the reporting period ',$summary['FAW'],$summary['FS'],$summary['FAW']+$summary['FS'],'')
);
define('REPORT_FOOTER_WARNING', "WARNING: This report is for internal, official use only.  This report contains sensitive computer security related information. Public disclosure of this information would risk circumvention of the law. Recipients of this report must not, under any circumstances, show or release its contents for purposes other than official action. This report must be safeguarded to prevent improper disclosure. Staff reviewing this document must hold a minimum of Public Trust Level 5C clearance.");
define('ORIENTATION', 'orient');
define('PAPERTYPE', 'pagesz');
define('PGWIDTH', 'pgwidth');
define('PGHEIGHT', 'pgheight');
define('FONTS', VENDORS . DS . 'pdf' . DS . 'fonts');

$page_config = array( ORIENTATION => 'landscape', PAPERTYPE => 'LETTER', PGWIDTH => 792, PGHEIGHT=>612);
$pdf =& new Cezpdf($page_config[PAPERTYPE], $page_config[ORIENTATION]);
$pdf->selectFont(FONTS . DS . "Helvetica.afm");
$top_margin=50;
$bottom_margin=100;
$left_margin=50;
$right_margin=50;

$page_config[PGWIDTH];
$page_config[PGHEIGHT];
$content_width=$page_config[PGWIDTH]-$left_margin-$right_margin;
$content_height=$page_config[PGHEIGHT]-$top_margin-$bottom_margin;

$pdf->ezSetMargins($top_margin, $bottom_margin, $left_margin, $right_margin);

$pdf->addTextWrap($left_margin,$content_height+110,$content_width,8,'Report run time:'.Zend_Date::now()->toString('Y-m-d H:i:s'),'right');
$pdf->line($left_margin,$content_height+105,$left_margin+$content_width,$content_height+105);

$footer=REPORT_FOOTER_WARNING;
$line_height=8;
$yPos=$bottom_margin-$line_height;
while(!empty($footer)){
$yPos-=$line_height;
$footer=$pdf->addTextWrap($left_margin,$yPos,$content_width,$line_height,$footer,'left');
}
$pdf->ezText('FISMA Report to OMB: POA&M Status Report',16,array('justification'=>'centre'));
$title='';
if($this->criteria['system_id'])
{
    $title.=' [System]: '.$this->criteria['system_id'];
}else {
        $title.=' [System]: All';
}
if($this->criteria['startdate'])
{
    $title.=' [Start date]: '.$this->criteria['startdate'];
}
if($this->criteria['enddate'])
{
    $title.=' [End date]: '.$this->criteria['enddate'];
}

$pdf->ezTable($table, $fields,$title,
    array('fontSize'=>8,'maxWidth'=>$content_width,'titleFontSize'=>12));

header('Pragma: private');
header('Cache-control: private');
echo $pdf->ezOutput();
?>