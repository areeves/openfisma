<div class="barleft">
<div class="barright">
<p><b>Remediation Summary</b><span><?php echo date('Y-M-D h:i:s:A');?></span></p>
</div>
</div>
<table align="center" cellpadding="5" class="tbframe">
    <tr align="center">
        <th>Action Owner</th>
        <th>New</th>
        <th>Open</th>
        <th>EN</th>
        <th>EO</th>
        <!--th>EP</th-->
        <th>EP (SSO)</th>
        <th>EP (S&P)</th>
        <th>ES</th>
        <th>CLOSED</th>
        <th>Total</th>
    </tr>

    <!-- SUMMARY LOOP -->
    <?php 
    foreach($this->summary as $sid=>$system){
        $base_url ="/zfentry.php/panel/remediation/sub/summary/s/search/filter_system/".$sid."";
    ?>
    <tr>
        <td width='45%' align='left'   class='tdc'>(<?php echo $system['action_owner_nickname'];?>)<?php echo $system['action_owner_name'];?></td>
        <td align='center' class='tdc'><?php echo $system['NEW'] == ''?'-':'<a href="'.$base_url.'/filter_status/NEW">'.$system['NEW'].'</a>';?></td>
        <td align='center' class='tdc'><?php echo $system['OPEN']== ''?'-':'<a href="'.$base_url.'/filter_status/OPEN">'.$system['OPEN'].'</a>';?></td>
        <td align='center' class='tdc'><?php echo $system['EN']== ''?'-':'<a href="'.$base_url.'/filter_status/EN">'.$system['EN'].'</a>';?></td>
        <td align='center' class='tdc'><?php echo $system['EO']== ''?'-':'<a href="'.$base_url.'/filter_status/EO">'.$system['EO'].'</a>';?></td>
        <td align='center' class='tdc'><?php echo $system['EP_SSO']==''?'-':'<a href="'.$base_url.'/filter_status/EP-SSO">'.$system['EP_SSO'].'</a>';?></td>
        <td align='center' class='tdc'><?php echo $system['EP_SNP']==''?'-':'<a href="'.$base_url.'/filter_status/EP-SNP">'.$system['EP_SNP'].'</a>';?></td>
        <td align='center' class='tdc'><?php echo $system['ES']==''?'-':'<a href="'.$base_url.'/filter_status/ES">'.$system['ES'].'</a>';?></td>
        <td align='center' class='tdc'><?php echo $system['CLOSED']==''?'-':'<a href="'.$base_url.'/filter_status/CLOSED">'.$system['CLOSED'].'</a>';?></td>       
        <td align='center' class='tdc'><b><?php echo $system['TOTAL']==''?'0':'<a href="'.$base_url.'">'.$system['TOTAL'].'</a>';?></b></td>
    </tr>
    <?php }?>
    <tr>
        <td width='45%' align='center' class='tdc'><b>TOTALS</b></td>
        <td class='tdc'><b><?php echo $this->totals['NEW']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/NEW">'.$this->totals['NEW'].'</a>';?></b></td>
        <td class='tdc'><b><?php echo $this->totals['OPEN']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/OPEN">'.$this->totals['OPEN'].'</a>';?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EN']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/EN">'.$this->totals['EN'].'</a>';?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EO']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/EO">'.$this->totals['EO'].'</a>';?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EP_SSO']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/EP-SSO">'.$this->totals['EP_SSO'].'</a>';?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EP_SNP']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/EP-SNP">'.$this->totals['EP_SNP'].'</a>';?></b></td>
        <td class='tdc'><b><?php echo $this->totals['ES']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/ES">'.$this->totals['ES'].'</a>';?></b></td>
        <td class='tdc'><b><?php echo $this->totals['CLOSED']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search/filter_status/CLOSED">'.$this->totals['CLOSED'].'</a>';?></b></td>     
        <td class='tdc'><b><?php echo $this->totals['TOTAL']==''?'0':'<a href="/zfentry.php/panel/remediation/sub/summary/s/search">'.$this->totals['TOTAL'].'</a>';?></b></td>
    </tr>
</table>
<br>
