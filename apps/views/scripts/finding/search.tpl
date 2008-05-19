<div class="barleft">
<div class="barright">
<p><b>Finding Search Results</b>
   <span><!-- Pagination -->
            <?php echo $this->links['all'];?>
   </span>
</p>
</div>
</div>
<?php if(isAllow('finding','delete')){ ?>
            <!-- Allow Multiple Deletion if the user has the appropriate rights -->
                        <input name="button" type="button" id="button" value="Select All" onclick="selectall('finding', 'fid_', true);" style="cursor:pointer;">
                        <input name="button" type="button" id="button" value="Select None" onclick="selectall('finding', 'fid_', false);" style="cursor:pointer;">
                        <input  name="button" type="button" id="button" value="Delete" 
                                onClick="document.finding.sbt.value='delete'; return deleteconfirm('finding','fid_','finding');" style="cursor:pointer;">
            <!-- End Multiple Deletion -->
<?php }?>
            <!-- Finding Search Results --> 
            <table width="100%" class="tbframe">
                <tr align="center">
                    <?php if(isAllow('finding','delete')) {?>
                    <th></td>
                    <?php } ?>
                    <th nowrap>ID               <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('id', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('id', 1)"></th>
                    <th nowrap>Status           <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('status', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('status', 1)"></td>
                    <th nowrap>Source           <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('source', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('source', 1)"></td>
                    <th nowrap>System           <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('system', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('system', 1)"></td>
                    <th nowrap>IP               <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('ip', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('ip', 1)"></td>
                    <th nowrap>Port             <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('port', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('port', 1)"></td>
                    <th nowrap>Product          <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('product', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('network', 1)"></td>
                    <th nowrap>Vulnerabilities  <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('vulner', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('vulner', 1)"></td>
                    <th nowrap>Discovered       <input type="image" src="/images/up_arrow.gif" border="0" onClick="order_page('date', 0)"> 
                                                <input type="image" src="/images/down_arrow.gif" border="0" onClick="order_page('date', 1)"></td>
                    <?php if(isAllow('finding','update')) {?>
                    <!--edit right-->
                    <th nowrap>Edit</td>
                    <?php }?>
                    <?php if(isAllow('finding','read')) {?>
                    <!--view right-->
                    <th nowrap>View</td>
                    <?php }?>
                </tr>
                <?php if(!empty($this->findings)){
                    foreach($this->findings as $row){
                ?>
                <tr>
                    <?php if(isAllow('finding','delete')){?>
                    <td align="center" >
                        <input type="checkbox" name="fname_<?php echo $row['id'];?>" value="fid_<?php echo $row['id'];?>">
                    </td>
                    <?php }?>
                    <td align="center" class="tdc"><?php echo $row['id']; ?></td>
                    <td ><?php echo $row['status']; ?>&nbsp;</td>
                    <td ><?php echo $this->source[$row['source_id']]; ?>&nbsp;</td>
                    <td ><?php echo $this->system[$row['sys_id']];?>&nbsp;</td>
                    <td ><?php echo $row['ip'];?>&nbsp;</td>
                    <td ><?php echo $row['port'];?>&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td  align="center"><?php echo date("Y-m-d",strtotime($row['discovered']));?>&nbsp;</td>
                    <?php if(isAllow('finding','update')){ ?>
                    <!--edit right-->
                    <td align="center" >
                      <a href="/zfentry.php/finding/edit/fid/<?php echo $row['id'];?>" ><img src="/images/edit.png" border="0" ></a>
                    </td>
                    <?php }
                    if(isAllow('finding','read')) {?>
                    <!--view right-->
                    <td align="center" >
                      <a href="/zfentry.php/finding/view/fid/<?php echo $row['id'];?>" ><img src="/images/view.gif" border="0" ></a>
                    </td>
                    <?php }?>
                </tr>
                <?php }
                }?>
            </table>

