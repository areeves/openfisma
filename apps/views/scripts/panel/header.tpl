<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td><img id="logo" src="/images/customer_logo.jpg" /></td>
		<td><span id="msgbar"></span></td>
		<td align="right"><ul class="loginfo">
                <li><form class="button_link" action="/zfentry.php/panel/user/sub/pwdchange">
                <input type="submit" value="Change Password" /></form>&nbsp;
                <form class="button_link" action="/zfentry.php/user/logout">
                <input type="submit" value="Logout" /></form><br>
				<li><b><?php echo $this->identity;  ?></b> is currently logged in 
			</ul></td>
	</tr>
</table>
<div id="menu">
<?php 
    if(isAllow('dashboard','read')) {
        echo '<ul><li>
             <h2><a href="/zfentry.php/panel/dashboard">Dashboard</a></h2>
             </ul>';
    }
    if(isAllow('finding','read')) {
        echo '<ul ><li > 
             <h2><a>Finding</a></h2>';
        echo '<ul>
             <li><a href="/zfentry.php/panel/finding/sub/summary">Finding Summary</a>
             <li><a href="/zfentry.php/panel/finding/sub/searchbox">Finding Search</a>';
        if(isAllow('finding','create')) {
            echo "\n",'<li><a href="/zfentry.php/panel/finding/sub/create">New Finding</a>
                <li><a href="/zfentry.php/finding/injection">Spreadsheet Upload</a>';
        }
        echo '</ul></ul>';
    }
    if(isAllow('remediation','read')) {
        echo '<ul><li>
              <h2><a href="/zfentry.php/panel/remediation/sub/index/">Remediation</a></h2>
              <ul>
              <li><a href="/zfentry.php/panel/remediation/sub/summary">Remediation Summary</a>
              <li><a href="/zfentry.php/panel/remediation/sub/searchbox">Remediation Search</a>
              </ul>
              </ul>';
    }
    if(isAllow('report','read')) { 
        echo "\n",'<ul><li><h2><a>Reports</a></h2>
              <ul>';
        if(isAllow('report', 'generate_poam_report' )) {
            echo "\n",'<li><a href="/zfentry.php/panel/report/sub/poam">POA&amp;M Report</a>';
        }            
        if(isAllow('report','generate_fisma_report')) {
            echo "\n",'<li><a href="/zfentry.php/panel/report/sub/fisma">FISMA POA&amp;M Report</a>';
        }
        if(isAllow('report','generate_general_report')) {
            echo "\n",'<li><a href="/zfentry.php/panel/report/sub/general">General Report</a>';
        }
        if(isAllow('report','generate_system_rafs')) {
            echo "\n",'<li><a href="/zfentry.php/panel/report/sub/rafs">Generate System RAFs</a>';
        }
        if(isAllow('report','generate_overdue_report')) {
            echo "\n",'<li><a href="/zfentry.php/panel/report/sub/overdue">Overdue Report</a>';
        }            
        echo'</ul>
             </ul>';
    }
    if(isAllow('admin','read')) {
        echo'<ul><li><h2><a>Administration</a></h2>';
        echo'<ul>';
        if(isAllow('admin_users','read')) {
            echo'<li><a href="/zfentry.php/panel/account/sub/list">Users</a>';
        }
        if(isAllow('admin_systems','read')) {
            echo'<li><a href="/zfentry.php/panel/system/sub/list">Systems</a>';
        }
        if(isAllow('admin_products','read')) {
            echo'<li><a href="/zfentry.php/panel/product/sub/list">Products</a>';
        }
        if(isAllow('asset','read')) {
            echo'<li><a href="/zfentry.php/panel/asset/sub/searchbox/s/search">Assets</a>';
        }
        if(isAllow('admin_system_groups','read')) {
            echo'<li><a href="/zfentry.php/panel/sysGroup/sub/list">System Group</a>';
        }
        if(isAllow('admin_sources','read')) {
            echo'<li><a href="/zfentry.php/panel/source/sub/list">Finding Sources</a>';
        }
        echo'<li><a href="/zfentry.php/panel/config">Configuration</a>';
        echo'</ul>
            </ul>';
    }
    /*
    if(isAllow('vulnerability','read')) {
        echo'<ul><li><h2><a href="/mainPanel.php?panel=association" >Vulnerability</a></h2>';
        echo'<ul><li><a href="#">Asset Dashboard</a>';
        if(isAllow('vulnerability','create')) {
            echo'<li><a href="#">Create an Asset</a>';
        }
        echo'</ul></ul>';
    }*/
?>
&nbsp;
</div>
