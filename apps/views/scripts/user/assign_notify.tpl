<style type="text/css">
.block {
	border:1px #44637A solid;
	padding:10px;
}
.block .inline {
	float:left;
	clear:right;
	padding:0 20px;
}
.block #move {
	padding-top:40px;
}
.block #actionButton { padding:10px 20px; clear:left}
.block select {
	width:300px;
}
</style>
<div class="barleft">
    <div class="barright">
        <p><b>Notification Events</b> 
    </div>
</div>
<div class='block'>
    <form name="event_form" enctype="application/x-www-form-urlencoded" method="post" action="/panel/user/sub/savenotify">
    <table>
        <tr>
            <td colspan="2"><i>You can limit how often the system sends you e-mails:</i></td>
        </tr>
        <tr>
            <td><b>Notify Frequency (hours):</b></td>
            <td><input name="notify_frequency" type="text" value="<?php echo round($this->notify_frequency, 2);?>" size="30"/></td>
        </tr>
        <tr>
            <td colspan="2"><i>If you would like to receive e-mail notifications at an alternate address, enter that address below:</i></td>
        </tr>
        <tr>
            <td><b>Notify E-mail:</b></td>
            <td><input name="notify_email" type="text" value="<?php echo $this->notify_email;?>" size="30" /></td>
        </tr>
        <tr>
            <td colspan="2"><p>
        <div class="inline"><b>Available events:</b><br/>
            <?php echo $this->formSelect('availableEvents',null, array('multiple'=>"multiple", 'size' => '20'), $this->availableList)?>        </div>
        <div class="inline" id="move">
            <p>
                <input type="button" name="add" id="addNotificationEvents" value="->">
            </p>
            <p>
                <input type="button" name="remove" id="removeNotificationEvents" value="<-">
            </p>
        </div>
        <div class="inline" id="enable"><b>Selected events:</b><br />
        <?php echo $this->formSelect('enableEvents',null, array('multiple' => 'multiple', 'size' => '20'), $this->enableList)?></div>
        </td></tr>
        <tr>
            <td colspan="2">
            <div id="actionButton">
                <input name="save" id="save" value="save" type="submit">
                <input name="reset" id="reset" value="reset" type="reset">
            </div>
            </td>
        </tr>
        </table>
    </form>
</div>
