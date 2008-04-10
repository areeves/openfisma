<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" type="text/javascript" src="/javascripts/jquery.js"></script>

<link rel="stylesheet" type="text/css" href="/stylesheets/layout.css">
<link rel="stylesheet" type="text/css" href="/stylesheets/fisma.css">
<link rel="stylesheet" type="text/css" href="/stylesheets/main.css">
</head>
<body>

<div id='container'>

    <div id='top' >
        <?php echo $this->layout()->header; ?>
    </div>
    <div id="content">
        <div id='detail'>
        <?php echo $this->layout()->CONTENT; ?>
		</div>

        <div id='bottom'>
            <table width="100%">
            <tr><td colspan=2><hr color="#44637A" size="1"></hr></td></tr>
            <tr> <td > Found a Bug? or Have a Suggestion? <a href="https://sourceforge.net/tracker/?group_id=208522" target="_blank">Report it Here</a> </td>
                 <td align="right"> <i>Powered by <a href="http://www.openfisma.org">OpenFISMA</a></i> </td>
            </tr>
            </table>
        </div>
    </div>

</div>

</body>
</html>
