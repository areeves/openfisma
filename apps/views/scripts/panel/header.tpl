<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td><img id="logo" src="/images/customer_logo.jpg" /></td>
		<td><span id="msgbar"></span></td>
		<td align="right"><ul class="loginfo">
				<li> <a href="/zfentry.php/panel/user/sub/pwdchange"><button class="action" >Change Password</button></a>&nbsp;<a href="/zfentry.php/user/logout"><button class="action" >Logout</button></a></li><br>
				<li><b><?php echo $this->identity;  ?></b> is currently logged in </li>
			</ul></td>
	</tr>
</table>
<div id="menu">
<?php 
    if(isAllow('dashboard','read')) {
        echo'<ul ><li >
             <a href="/zfentry.php/panel/dashboard"><h2>Dashboard</h2></a>
             </li></ul>';
    }
    if(isAllow('finding','read')) {
        echo'<ul ><li > 
             <a ><h2>Finding</h2></a>'; 
        echo'<ul><li><a href="/zfentry.php/panel/finding/sub/summary">Finding Summary</a></li>';
        echo'<li><a href="/zfentry.php/panel/finding/sub/searchbox">Finding Search</a></li>';
        if(isAllow('finding','create')) {
            echo'<li><a href="/zfentry.php/panel/finding/sub/create">New Finding</a></li>';
            echo'<li><a href="/zfentry.php/finding/upload">Upload Scan Results</a></li>';
            echo'<li><a href="/zfentry.php/finding/injection">Spreadsheet Upload</a></li>';
        }
        echo'</ul></li></ul>';
    }
    if(isAllow('remediation','read')) {
        echo'<ul><li >
            <a href="/zfentry.php/panel/remediation/sub/index/"><h2>Remediation</h2></a> 
            <ul><li><a href="/zfentry.php/panel/remediation/sub/summary">Remediation Summary</a></li>
            <li><a href="/zfentry.php/panel/remediation/sub/searchbox">Remediation Search</a></li></ul></li></ul>';
    }
    if(isAllow('report','read')) { 
        echo'<ul><li><a><h2>Reports</h2></a>';
        echo'<ul>';
        if(isAllow('report', 'generate_poam_report' )) {
            echo'<li><a href="/zfentry.php/panel/report/sub/poam">POA&M Report</a></li>';
        }            
        if(isAllow('report','generate_fisma_report')) {
            echo'<li><a href="/zfentry.php/panel/report/sub/fisma">FISMA POA&M Report</a></li>';
        }
        if(isAllow('report','generate_general_report')) {
            echo'<li><a href="/zfentry.php/panel/report/sub/general">General Report</a></li>';
        }
        if(isAllow('report','generate_system_rafs')) {
            echo'<li><a href="/zfentry.php/panel/report/sub/rafs">Generate System RAFs</a></li>';
        }
        if(isAllow('report','generate_overdue_report')) {
            echo'<li><a href="/zfentry.php/panel/report/sub/overdue">Overdue Report</a></li>';
        }            
        echo'</ul></li></ul>';
    }
    if(isAllow('admin','read')) {
        echo'<ul><li><a><h2>Administration</h2></a>';
        echo'<ul>';
        if(isAllow('admin_users','read')) {
            echo'<li><a href="/zfentry.php/panel/account/sub/list">Users</a></li>';
        }
        if(isAllow('admin_systems','read')) {
            echo'<li><a href="/zfentry.php/panel/system/sub/list">Systems</a></li>';
        }
        if(isAllow('admin_products','read')) {
            echo'<li><a href="/zfentry.php/panel/product/sub/list">Products</a></li>';
        }
        if(isAllow('asset','read')) {
            echo'<li><a href="/zfentry.php/panel/asset/sub/searchbox/s/search">Assets</a></li>';
        }
        if(isAllow('admin_system_groups','read')) {
            echo'<li><a href="/zfentry.php/panel/sysGroup/sub/list">System Group</a></li>';
        }
        if(isAllow('admin_sources','read')) {
            echo'<li><a href="/zfentry.php/panel/source/sub/list">Finding Sources</a></li>';
        }
        echo'<li><a href="/zfentry.php/panel/config">Configuration</a></li>';
        echo'</ul></li></ul>';
    }
    /*
    if(isAllow('vulnerability','read')) {
        echo'<ul><li><a href="/mainPanel.php?panel=association" ><h2>Vulnerability</h2></a>';
        echo'<ul><li><a href="#">Asset Dashboard</a></li>';
        if(isAllow('vulnerability','create')) {
            echo'<li><a href="#">Create an Asset</a></li>';
        }
        echo'</ul></li></ul>';
    }*/
?>
&nbsp;
</div>
