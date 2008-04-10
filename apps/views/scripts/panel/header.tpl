<ul class="loginfo">
    <li><b><?php echo $this->identity;  ?></b> is currently logged in  </li>
    <li> 
        <a class="action" href="/zfentry.php/user/pwdchange">Change Password</a>
        <a class="action" href="/zfentry.php/user/logout">Logout</a>
    </li>
</ul>
<img id="logo" src="/images/customer_logo.jpg" />

<div id="menu">
<?php 
    if(isAllow('dashboard','read')) {
        echo'<ul ><li >
             <a href="/zfentry.php/panel/dashboard"><h2>Dashboard</h2></a>
             </li></ul>';
    }
    if(isAllow('finding','read')) {
        echo'<ul ><li > 
             <a href="#"><h2>Finding</h2></a>'; 
        echo'<ul><li><a href="#">Finding Summary</a></li>';
        if(isAllow('finding','create')) {
            echo'<li><a href="#">New Finding</a></li>';
            echo'<li><a href="#">Upload Scan Results</a></li>';
            echo'<li><a href="#">Spreadsheet Upload</a></li>';
        }
        echo'</ul></li></ul>';
    }
    if(isAllow('asset','read')) {
        echo'<ul><li >
             <a href="#" ><h2>Assets</h2></a>';
        echo'<ul><li><a href="#">Asset Dashboard</a></li>';
        if(isAllow('asset','create')) {
            echo'<li><a href="#">Create an Asset</a></li>';
        }
        echo'</ul></li></ul>';
    }
    if(isAllow('remediation','read')) {
        echo'<ul><li > 
            <a href="/zfentry.php/panel/remediation"><h2>Remediation</h2></a> 
            </li></ul>';
    }
    if(isAllow('report','read')) { 
        echo'<ul><li><a href="/mainPanel.php?panel=test" ><h2>Reports</h2></a>';
        echo'<ul>';
        if(isAllow('report', 'generate_poam_report' )) {
            echo'<li><a href="#">POA&M Report</a></li>';
        }            
        if(isAllow('report','generate_fisma_report')) {
            echo'<li><a href="#">FISMA POA&M Report</a></li>';
        }
        if(isAllow('report','generate_general_report')) {
            echo'<li><a href="#">General Report</a></li>';
        }
        if(isAllow('report','generate_system_rafs')) {
            echo'<li><a href="#">Generate System RAFs</a></li>';
        }
        if(isAllow('report','generate_overdue_report')) {
            echo'<li><a href="#">Overdue Report</a></li>';
        }            
        echo'</ul></li></ul>';
    }
    if(isAllow('admin','read')) {
        echo'<ul><li><a href="/mainPanel.php?panel=admin" ><h2>Administration</h2></a>';
        echo'<ul>';
        if(isAllow('admin_users','read')) {
            echo'<li><a href="#">Users</a></li>';
        }
        if(isAllow('admin_systems','read')) {
            echo'<li><a href="#">Systems</a></li>';
        }
        if(isAllow('admin_products','read')) {
            echo'<li><a href="#">Products</a></li>';
        }
        if(isAllow('admin_system_groups','read')) {
            echo'<li><a href="#">System Group</a></li>';
        }
        if(isAllow('admin_functions','read')) {
            echo'<li><a href="#">Finding Sources</a></li>';
        }
        echo'</ul></li></ul>';
    }
    if(isAllow('vulnerability','read')) {
        echo'<ul><li><a href="/mainPanel.php?panel=association" ><h2>Vulnerability</h2></a>';
        echo'<ul><li><a href="#">Asset Dashboard</a></li>';
        if(isAllow('vulnerability','create')) {
            echo'<li><a href="#">Create an Asset</a></li>';
        }
        echo'</ul></li></ul>';
    }
?>
&nbsp;
</div>
