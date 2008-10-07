<?php
$output_cols = array(
"System",
"ID#",
"Description",
"Type",
"Status",
"Source",
"Asset",
"Location",
"Risk Level",
"Recommendation",
"Corrective Action",
"ECD",
"Control Y/N",
"Threats Y/N",
"Countermeasures Y/N"
);
$count_cols = count($output_cols);

require_once ('Spreadsheet/Excel/Writer.php'); // need fix 

$workbook  = new Spreadsheet_Excel_Writer();
$workbook->setVersion(8); // fixes 255 char truncation issue

$worksheet =& $workbook->addWorksheet();

$format_header =& $workbook->addFormat(array('Size' => 10,
    'Align' => 'center',
    'Color' => 'white',
    'FgColor' => 'black',
    ));
$format_header->setFontFamily('Times New Roman');

$format_times =& $workbook->addFormat(array('Size' => 10,
    'Align' => 'center',
    'Color' => 'black',
    'BorderColor '=> 'blue',
    'Bottom'=>1,'Top'=>1,'Left'=>1,'Right'=>1
    ));
$format_times->setFontFamily('Times New Roman');
$format_times->setAlign('left');
$format_times->setAlign('top');
$format_times->setTextWrap();
$rowi=0;
$headinfo="Report run time: ".date("Y-m-d H:i:s");
$worksheet->write($rowi++, 0, $headinfo,$format_times);
$worksheet->mergeCells($rowi,0,$rowi,$count_cols-1);
$worksheet->write($rowi++, 0, 'Results',$format_header);
$worksheet->setColumn(0,0,30);
$worksheet->setColumn(2,2,50);
$worksheet->setColumn(3,8,10);
$worksheet->setColumn(9,10,50);
$worksheet->setColumn(11,14,15);
$worksheet->mergeCells($rowi,0,$rowi,$count_cols-1);
//inject the titles
$worksheet->writeRow($rowi++,0,$output_cols,$format_times);

foreach( $this->poam_list as $p ) {
    $data = array(
        $this->system_list[$p['system_id']],
        $p['id'],
        $p['finding_data'],
        $p['type'],
        $p['status'],
        $p['source_id'] != 0 ? $this->source_list[$p['source_id']] : 'n/a',
        $p['asset_id'],
        !empty($p['network_id'])?$this->network_list[$p['network_id']]:'',
        $p['threat_level'],
        $p['action_suggested'],
        $p['action_planned'],
        $p['action_est_date'],
        NULL == $p['blscr_id'] ? 'N' : 'Y',
        $p['threat_level'] != 'NONE' && trim($p['threat_source']) != '' && trim($p['threat_justification']) != '' ? 'Y' : 'N',
        $p['cmeasure_effectiveness'] != 'NONE' && trim($p['cmeasure_effectiveness']) != '' && trim($p['cmeasure_justification']) != '' ? 'Y' : 'N');
    $worksheet->writeRow($rowi++,0,$data,$format_times); 
}

$worksheet->setHeader("                            ".$headinfo);
$workbook->send('report.xls');
$workbook->close();

?>
