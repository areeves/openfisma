<!-- Total# of Systems /w Open Vulnerabilities -->
<table width="95%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width=10></td>
        <td align="left"><b>Total # of system with open vulnerability: </b> <?php echo $this->rpdata[0];?></td>
        <td align="right"><b>Total # fo vulnerabilities: </b></td>
        <td width=10 align="left"><div id=sum></div></td>
        <td width=10></td>
    </tr>
</table>
    <?php
        $i = 0;
        $sum0 = 0;
        foreach($this->rpdata[1] as $rec){
            $i++;
            if($i % $this->colnum == 1){
    ?>
    <br>
<table width="95%" align="center" border="0" cellpadding="0" cellspacing="0"  class="tipframe">
    <tr align="center">
        <td width="<?php echo $this->colwidth;?>%">
            <table border="0" cellpadding="5" cellspacing="0"  width="100%" height="100%">
                <tr><th>Systems</th></tr>
                <tr><th nowrap>Open Vulnerabilities</th></tr>
            </table>
        </td>
            <?php $rbflag = 0;
                } 
            ?>        
        <td width="<?php echo $this->colwidth;?>%">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
                <tr><td class="tdc" align="center"><?php echo $rec['nick'];?></td></tr>
                <tr><td class="tdc" align="center"><?php echo $rec['num'];?></td></tr>
            </table>
        </td>
        <?php if($i % $this->colnum == 0){ ?>
    </tr>
</table>
        <?php $tbflag = 1;
              }
              $sum0 = $sum0+$rec['num'];
           }
           if($tbflag != 1){
               $sumtd = $this->colnum-$i%$this->colnum;
               if($sumtd != $this->colnum){
                   $sumtd = $sumtd + 1;
                   foreach($sumtd as $addtd){
        ?>
    <td width="<?php echo $this->colwidth;?>%">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
    <tr><td class="tdc" align="center">&nbsp;</td></tr>
    <tr><td class="tdc" align="center">&nbsp;</td></tr>
</table>
</td>
<?php } 
}
?>
</tr>
</table>
<?php } ?>
<script>
    sum.innerHTML = <?php echo $sum0;?>;
</script>
