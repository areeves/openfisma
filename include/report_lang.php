<?PHP
$report_lang= array (
array (
),
array (
	"FISMA Report to OMB: POA&M Status Report",
	"POA&M Status Information",
	"Agency Wide",
	"System",
	"Total",
	"Brief Explanation",
	"A. Total number of weaknesses identified at the start of the reporting period",
	"B. Number of weaknesses for which corrective action was completed on time(including testing) by the end of the reporting period",
	"C. Number of weaknesses for which corrective action is ongoing and is on track to complete as originally scheduled",
	"D. Number of weaknesses for which corrective action has been delayed including a brief explanation for the delay",
	"E. Number of weaknesses discovered following the last POA&M update and a brief Explanation of how they were identified (e.g., agency review, IG evaluation, etc.)",
	"Total number of weaknesses remaining at the end of the reporting period"	
),
array (
"Results",
//"PO",
"System",
//"Tier",
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
"ECD"
),
array (
//because report 3 have many sub report,so we need more deep dimension 
//reports list
array(
"1" => "NIST Baseline Security Controls Report",
"2" => "FIPS 199 Categorization Breakdown",
"3" => "Products with Open Vulnerabilities",
"4" => "Software Discovered Through Vulnerability Assessments",
"5" => "Total # of Systems /w Open Vulnerabilities"
//,"6" => "XXX"
),
array(
	1=>array(
		"Management","Operational","Technical",
		"BLSR Category","Total Vulnerabilities",
		"Total"
	),
	2=>array(
		"FIPS 199 Category","Low","Moderate","High","Total Systems",
		"System Name","System Type","Mission Criticality","FIPS 199 Category",
		"Confidentiality","Integrity","Availability","Last Inventory Update"
	),
	3=>array(
	"Vendor","Product","Version","# of Open Vulnerabilities"
	),
	4=>array(
	"Vendor","Product","Version"
	),
	5=>array(
	"Total # Systems With Open Vulnerabilities",
	"Systems",
	"Open Vulnerabilities",
	"Total",
	"Total # of system with open vulnerability",
	"Total # of vulnerabilities",
	"# of Vulnerabilities",
	"Vulnerabilities per system"
	)
	//,6=>array(),
)
),
array("0"=>array("","All Status",0),
      "1"=>array("closed","Closed",0),
      "2"=>array("notclosed","Not Closed",0),
      "3"=>array("new","&nbsp;&nbsp;&nbsp;New",0),
      "4"=>array("open","&nbsp;&nbsp;&nbsp;Open",1),
      "5"=>array("en","&nbsp;&nbsp;&nbsp;Ev Needed",1)),
      
array("0"=>array("","Select Date Picker"),
      "1"=>array("30","0-29 days"),
      "2"=>array("60","30-59 days"),
      "3"=>array("90","60-89 days"),
      "4"=>array("120","90-119 days"),
      "5"=>array("greater","120 and greater days"))
);
?>
