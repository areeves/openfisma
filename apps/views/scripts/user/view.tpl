<div class="barleft">
<div class="barright">
<p><b>Administration: Users Detail</b>
</div>
</div>
<table width="98%" align="center" >
    <tr>
        <td align="right" class="thc" width="200">Last Name:</td>
        <td class="tdc">&nbsp;<?php echo $this->user['lastname'];?></td>
    </tr>
    <tr>
        <td align="right" class="thc">First Name:</td>
        <td class="tdc">&nbsp;<?php echo $this->user['firstname'];?></td></tr>
    <tr>
        <td align="right" class="thc">Office Phone:</td>
        <td class="tdc">&nbsp;<?php echo $this->user['officephone'];?></td></tr>
    <tr>
        <td align="right" class="thc">Mobile Phone:</td>
        <td class="tdc">&nbsp;<?php echo $this->user['mobilephone'];?></td>
    </tr>
    <tr>
        <td align="right" class="thc">Email:</td>
        <td class="tdc">&nbsp;<?php echo $this->user['email'];?></td></tr>
    <tr>
        <td align="right" class="thc">Role:</td>
        <td class="tdc">&nbsp;
        <?php 
            foreach($this->roles as $r) {
                echo $r['name'],',';
            }
        ?>
        </td>
    </tr>
    <tr>
        <td align="right" class="thc">Title:</td>
        <td class="tdc">&nbsp;<?php echo $this->user['title'];?></td>
    </tr>
    <tr>
        <td align="right" class="thc">Status:</td>
        <td class="tdc">&nbsp;<?php echo 1 == $this->user['status']?'Active':'Suspend';?></td>
    </tr>
    <tr>
        <td align="right" class="thc">Username:</td>
        <td class="tdc">&nbsp;<?php echo $this->user['username'];?></td>
    </tr>
</table>
<br><br>
<fieldset style="border:1px solid #BEBEBE; padding:3"><legend><b>Systems</b></legend>
<?php echo $this->msg;?>
<table border="0" width="100%">
<?php 
    foreach($this->systems as $system ){ 
?>
<tr>
    <td>
       <?php echo $system; ?>
    </td>
</tr>

<?php } ?>
</table>
</fieldset>
