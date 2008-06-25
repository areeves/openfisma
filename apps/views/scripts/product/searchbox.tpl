<?php
$fid_array = array('name'=>'Product Name',
                   'vendor'=>'Vendor',
                   'version'=>'Version');
?>
<div class="barleft">
<div class="barright">
<p><b>Administration:Products Summary</b><span><?php echo date('Y-m-d h:i:s:A');?></span>
</div>
</div>
<table class="tbframe" align="center"  width="98%">
    <tbody>
        <tr>
            <th>[<a href="/zfentry.php/panel/product/sub/list">Products list</a>] (total: <?php echo $this->total;?>)</th>
            <th>[<a href="/zfentry.php/panel/product/sub/create" title="add new Products">Add Products</a>]</th>
            <th>
                <table align="center">
                    <tbody>
                        <tr height="22">
                            <td><b>Page:&nbsp;</b></td>
                            <td><?php echo $this->links['all'];?></td>
                            <td>|</td>
                        </tr>
                    </tbody>
                </table>
            </th>
            <th>
                <table align="center">
                <form name="query" method="post" action="/zfentry.php/panel/product/sub/list">
                    <tbody>
                        <tr>
                            <td><b>Query:&nbsp;</b></td>
                            <td><?php echo $this->formSelect('fid',nullGet($this->fid,'name'),null,$fid_array);?></td>
                            <td><input name="qv" value="<?php echo $this->qv;?>" title="Input your query value" size="10" maxlength="40" type="text"></td>

                             <td><input value="Search" title="submit your request" type="submit"></td>
                        </tr>
                    </tbody>
                </form>
                </table>
            </th>
        </tr>
    </tbody>
</table>
