
<!-- NIST Baseline Security Controls -->
<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td  width="5%"></td>   
    <td width="25%" valign="top">
        <!--Management-->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tbframe"  >
            <tr align="center">
                <td colspan="2"><b>Management</b></td>
            </tr>
            <tr align="center">
                <th>BLSR Category</th>
                <th>Total Vulnerabilities</th>
            </tr>
            <?php foreach($this->rpdata[0] as $item){ ?>
            <tr align="center">
                <td class="tdc"><?php echo $item['t'];?></td>
                <td class="tdc"><?php echo $item['n'];?></td>
            </tr>
            <?php $this->sum0 = $this->sum0+ $item['n'];?>
            <?php } ?>
            <tr align="center">
                <td class="tdc">Total</td>
                <td class="tdc"><?php echo $this->sum0;?></td>
            </tr>
        </table>
    </td>
    <td  width="5%"></td>   
    <td width="25%"  valign="top">
        <!--Operational-->  
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tbframe"  >
            <tr align="center">
                <td colspan="2"><b>Operational</b></td>
            </tr>
            <tr align="center">
                <th>BLSR Category</th>
                <th>Total Vulnerabilities</th>
            </tr>
                <?php foreach($this->rpdata[1] as $item){ ?>
            <tr align="center">
                <td class="tdc"><?php echo $item['t'];?></td>
                <td class="tdc"><?php echo $item['n'];?></td>
            </tr>
                <?php $this->sum1 = $this->sum1+$item['n'];?>
                <?php } ?>
            <tr align="center">
                <td class="tdc">Total</td>
                <td class="tdc"><?php echo $this->sum1;?></td>
            </tr>
        </table>
    </td>
    <td  width="5%"></td>   
    <td width="25%"  valign="top">
        <!--Technical-->    
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tbframe"  >
            <tr align="center">
                <td colspan="2"><b>Technical</b></td>
            </tr>
            <tr align="center">
                <th>BLSR Category</th>
                <th>Total Vulnerabilities</th>
            </tr>
                <?php foreach($this->rpdata[2] as $item){ ?>
            <tr align="center">
                <td class="tdc"><?php echo $item['t'];?></td>
                <td class="tdc"><?php echo $item['n'];?></td>
            </tr>
                <?php $this->sum2 = $this->sum2+$item['n'];?>
                <?php } ?>
            <tr align="center">
                <td class="tdc">Total</td>
                <td class="tdc"><?php echo $this->sum2;?></td>
            </tr>
        </table>
    </td>
    <td  width="5%"></td>   
    </tr>
</table>

