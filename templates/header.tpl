<html>

<head>

  <title>OVMS > {$this_title}</title>

</head>

{* DISPLAY HEADER FOR AN ACTIVE SESSION *}
{if $SESSION_ACTIVE eq '0'}

<body>

<table width='100%' border='0'>

	<form action='{$this_page}' method='POST'>
	<tr>

		{* SHOW LOGIN FORM *}
		<td align='right'>
			Username: <input type='text' name='SESSION_USERNAME'><br>
			Password: <input type='password' name='SESSION_PASSWORD'><br>
			<input type='hidden' name='SESSION_ACTION' value='LOGIN'>
			<input type='image' value='LOGIN' src='images/button_login.png'>
		</td>

	</tr>
	</form>

</table>

{* DISPLAY HEADER FOR AN INACTIVE SESSION *}
{else}
<body>

<table width='100%' border='0'>
	<form action='{$this_page}' method='POST'>
	<tr>
		<td align='right'>
		    <i>{$DATE}<br>
			<i>Welcome, {$SESSION_USERNAME}!</i><br>
			<input type='hidden' name='SESSION_ACTION' value='LOGOUT'>
			<input type='image' value='LOGOUT' src='images/button_logout.png'>
		</td>
	</tr>
	</form>
</table>
{/if}

<hr>
