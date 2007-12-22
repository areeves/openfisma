<table width='100%'>

  {* MAIN MENU ROW *}
  <tr>

	<td align='left'>

	{if $menu_header == 'dashboard'} <b> <a href='dashboard.php'>DASHBOARD</a> </b>
	{else}<a href='dashboard.php'>DASHBOARD</a>
	{/if}

	{*if $menu_header == 'system'} <b> SYSTEMS </b> 
	{else}SYSTEMS
	{/if*}
	
	{if $menu_header == 'remediation'} <b> <a href='remediation_list.php'>REMEDIATIONS</a> </b>
    {else}<a href='remediation_list.php'>REMEDIATIONS</a>
    {/if}

	{if $menu_header == 'finding'} <b> <a href='finding_list.php'>FINDINGS</a> </b>
	{else}<a href='finding_list.php'>FINDINGS</a>
	{/if}

	{if $menu_header == 'reporting'} <b> <a href='report_list.php'>REPORTS</a> </b>
	{else}<a href='report_list.php'>REPORTS</a>
	{/if}

	{*if $menu_header == 'vulns'} <b> <a href='vuln_list.php'>VULNERABILITIES</a> </b>
	{else}<a href='vuln_list.php'>VULNERABILITIES</a>
	{/if*}

	{if $menu_header == 'admin'} <b> <a href='admin.php'>ADMINISTRATION</a> </b>
	{else} <a href='admin.php'>ADMINISTRATION</a>
	{/if}

	</td>

  </tr>

  <tr>

    {* DASHBOARD SUBMENU ROW *}
    {if $menu_header == 'dashboard'}
	<td colspan='10'>[ dashboard submenu items ]</td>

    {* SYSTEM SUBMENU ROW *}
    {elseif $menu_header == 'system'}
	<td colspan='10'>[ system submenu items ]</td>

    {* FINDINGS SUBMENU ROW *}
    {elseif $menu_header == 'finding'}
	<td colspan='10'>[<i>
	   <a href='finding_list.php'>list</a> 
	   upload
	   </i>]
	</td>

    {* REMEDIATION SUBMENU ROW *}
    {elseif $menu_header == 'remediation'}
	<td colspan='10'>[ remediation submenu items ]</td>

    {* REPORTING SUBMENU ROW *}
    {elseif $menu_header == 'reporting'}
	<td colspan='10'>[<i>
	   <a href='report_poam.php'>POA&M</a>
	   </i>]
	</td>

    {* VULNERABILITIES SUBMENU ROW *}
    {elseif $menu_header == 'vuln'}
	<td colspan='10'>[ vulnerability submenu items ]</td>
	
    {* USERS SUBMENU ROW *}
    {elseif $menu_header == 'user'}
	<td colspan='10'>[ users submenu items ]</td>
	
    {* ASSETS SUBMENU ROW *}
    {elseif $menu_header == 'asset'}
	<td colspan='10'>[ assets submenu items ]</td>
	
    {* SYSTEM SUBMENU ROW *}
    {elseif $menu_header == 'system'}
	<td colspan='10'>[ system submenu items ]</td>

    {* ROLE SUBMENU ROW *}
    {elseif $menu_header == 'role'}
	<td colspan='10'>[ role submenu items ]</td>
	
    {* PRODUCT SUBMENU ROW *}
    {elseif $menu_header == 'product'}
	<td colspan='10'>[ product submenu items ]</td>
	
    {* PLUGIN SUBMENU ROW *}
    {elseif $menu_header == 'plugin'}
	<td colspan='10'>[ plugin submenu items ]</td>
	
    {* NETWORK SUBMENU ROW *}
    {elseif $menu_header == 'network'}
	<td colspan='10'>[ network submenu items ]</td>
	
    {* FUNCTION SUBMENU ROW *}
    {elseif $menu_header == 'function'}
	<td colspan='10'>[ function submenu items ]</td>
	
    {* BLSCR SUBMENU ROW *}
    {elseif $menu_header == 'blscr'}
	<td colspan='10'>[ blscr submenu items ]</td>
	
    {* FINDING SOURCE SUBMENU ROW *}
    {elseif $menu_header == 'finding source'}
	<td colspan='10'>[ finding source submenu items ]</td>
	
    {* ROLE FUNCTION SUBMENU ROW *}
    {elseif $menu_header == 'role function'}
	<td colspan='10'>[ role function submenu items ]</td>
	
    {* ROLE SYSGROUP SUBMENU ROW *}
    {elseif $menu_header == 'role sysgroup'}
	<td colspan='10'>[ role sysgroup submenu items ]</td>
	
    {* SYSTEM GROUP SUBMENU ROW *}
    {elseif $menu_header == 'system group'}
	<td colspan='10'>[ system group submenu items ]</td>
	
    {* USER SYSGROUP SUBMENU ROW *}
    {elseif $menu_header == 'user sysgroup'}
	<td colspan='10'>[ user sysgroup submenu items ]</td>
	
    {* POAM SUBMENU ROW *}
    {elseif $menu_header == 'poam'}
	<td colspan='10'>[ poam submenu items ]</td>
	
    {* POAM COMMENT SUBMENU ROW *}
    {elseif $menu_header == 'poam comment'}
	<td colspan='10'>[ poam comment submenu items ]</td>
	
    {* POAM EVIDENCE SUBMENU ROW *}
    {elseif $menu_header == 'poam evidence'}
	<td colspan='10'>[ poam evidence submenu items ]</td>
	
    {* FINDING VULN SUBMENU ROW *}
    {elseif $menu_header == 'finding vuln'}
	<td colspan='10'>[ finding vuln submenu items ]</td>
	
    {* SYSTEM ASSET SUBMENU ROW *}
    {elseif $menu_header == 'system asset'}
	<td colspan='10'>[ system asset submenu items ]</td>
	
    {* SYSTEM GROUP SYSTEM SUBMENU ROW *}
    {elseif $menu_header == 'system group system'}
	<td colspan='10'>[ system group system submenu items ]</td>
	
    {* USER SYSTEM ROLE SUBMENU ROW *}
    {elseif $menu_header == 'user system role'}
	<td colspan='10'>[ user system role submenu items ]</td>

    {* ADMINISTRATION SUBMENU ROW *}
    {elseif $menu_header == 'admin'}
	<td colspan='10'>[
      <a href='user_list.php'>users</a> - 
      <a href='asset_list.php'>assets</a> - 
      <a href='vuln_list.php'>vulns</a> - 
      <a href='system_list.php'>system</a> - 
      <a href='role_list.php'>role</a> - 
      <a href='product_list.php'>product</a> - 
      <a href='plugin_list.php'>plugin</a> - 
      <a href='network_list.php'>network</a> - 
      <a href='function_list.php'>function</a> - 
      <a href='finding_list.php'>finding</a> - 
      <a href='blscr_list.php'>blscr</a> - 
      <a href='findingsource_list.php'>finding source</a> - 
      <a href='rolefunction_list.php'>role function</a> - 
      <a href='rolesysgroup_list.php'>role sysgroup</a> - 
      <a href='systemgroup_list.php'>system group</a> - 
      <a href='usergroup_list.php'>user sysgroup</a> - 
      <a href='poam_list.php'>poam</a> -
      <a href='poamcomment_list.php'>poam comment</a> -
      <a href='poamevidence_list.php'>poam evidence</a> -
      <a href='findingvuln_list.php'>finding vuln</a> -
      <a href='systemasset_list.php'>system asset</a> -
      <a href='systemgroupsystem_list.php'>system group system</a> -
      <a href='usersystemrole_list.php'>user system role</a> ]
	</td>

	{* NO SUBMENU ITEMS *}
	{else}
	<td colspan='10'>... no functional area assigned to this page ...</td>
	{/if}

  </tr>

</table>

<hr>
<br>
