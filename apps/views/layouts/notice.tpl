<?php
    echo $this->doctype();
?>
<html>
<head>
<?php
    $this->headTitle()->setSeparator(' - ');
    $this->headTitle()->prepend(readSysConfig('system_name'));
    echo $this->headTitle();
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="/stylesheets/layout.css">
<link rel="stylesheet" type="text/css" href="/stylesheets/fisma.css">
<link rel="stylesheet" type="text/css" href="/stylesheets/main.css">
<link rel="icon"
      type="image/ico"
      href="/images/favicon.ico" />

</head>
<body>
<div id='container'>

    <div id="content">
        <?php echo $this->layout()->CONTENT; ?>

        <div id='bottom'>
            <?php 
                $this->action('footer', 'panel'); 
                echo $this->layout()->footer; 
            ?>
        </div>
    </div>

</div>
</body>
</html>
