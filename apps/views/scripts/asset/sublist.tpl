<div class="barleft">
<div class="barright">
<p><b>Asset Search Results</b>
<span>
<a target='_blank' href="<?php echo $url.'/format/pdf'; ?>"><img src="/images/pdf.gif" border="0"></a>
<a href="<?php echo $url.'/format/xls'; ?>"><img src="/images/xls.gif" border="0"></a>
</span>
</div>
</div>
<form id="assetresult" method="post" action="/zfentry.php/asset/delete">
<table width="98%" align="center" border="0">
    <tr>
        <td>
            <div style="text-align:left;margin:0 0 2px">
            <?php if(isAllow('asset','delete')){ ?>
                <input type="button" name="select_all" value="Select All" style="cursor:pointer;">
                <input type="button" name="select_none"  value="Select None" style="cursor:pointer;">
                <input type="submit" id="button" value="Delete" style="cursor:pointer;">
            <?php } if(isAllow('asset','create')){ ?>
                <a id="create_asset" href="/zfentry.php/asset/create"><button class="action">Create an Asset</button></a>
            <?php } ?>
        </td>
        <td align="right">
            <?php echo $this->links['all'];?>
        </td>
    </tr>
</table>
<table width="98%" align="center" border="0" class="tbframe">
    <tr align="center">
        <th nowrap></th>
        <th>Asset Name</th>
        <th>System</th>
        <th>IP Address</th>
        <th>Port</th>
        <th>Product Name</th>
        <th>Vendor</th>
        <?php if(isAllow('asset','update')){
                 echo'<th nowrap>Edit</th>';
              }
              if(isAllow('asset','view')){
                 echo'<th nowrap>View</th>';
              }
        ?>
    </tr>
    <?php foreach($this->asset_list as $row){ ?>
    <tr>
        <?php if(isAllow('asset','delete')){ ?>
            <td align="center" class="tdc"><input type="checkbox" name="aid_<?php echo $row['aid'];?>" 
                value="<?php echo $row['aid'];?>"></td>
        <?php } else { ?>
            <td align="center" class="tdc">&nbsp;</td>
        <?php } ?>
        <td class="tdc">&nbsp;<?php echo $row['asset_name'];?></td>
        <td class="tdc">&nbsp;<?php echo $row['system_name'];?></td>
        <td class="tdc">&nbsp;<?php echo $row['address_ip'];?></td>
        <td class="tdc">&nbsp;<?php echo $row['address_port'];?></td>
        <td class="tdc">&nbsp;<?php echo $row['prod_name'];?></td>
        <td class="tdc">&nbsp;<?php echo $row['prod_vendor'];?></td>
        <?php if(isAllow('asset','edit')){ ?>
        <td class="tdc" align="center"><a href="/zfentry.php/panel/asset/sub/view/s/edit/id/<?php echo $row['aid'];?>"><img src="/images/edit.png" border="0"></a></td>
        <td class="tdc" align="center"><a href="/zfentry.php/panel/asset/sub/view/id/<?php echo $row['aid'];?>"><img src="/images/view.gif" border="0"></a></td>
        </tr>
        <?php } ?>
    <?php } ?>
</table>
</form>
            
            
