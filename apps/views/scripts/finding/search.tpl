<div class="barleft">
<div class="barright">
<p><b>Finding Search Results</b><span><?PHP echo date('Y-M-D h:i:s:A'); ?></span></p
</div>
</div>

<br>

<table width="98%" align="center">
    <tr>
        <td align="left">
            <?php if(isAllow('finding','delete')){ ?>
            <!-- Allow Multiple Deletion if the user has the appropriate rights -->
            <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="left">
                        <input name="button" type="button" id="button" value="Select All" onclick="selectall('finding', 'fid_', true);" style="cursor:pointer;">
                        <input name="button" type="button" id="button" value="Select None" onclick="selectall('finding', 'fid_', false);" style="cursor:pointer;">
                        <input  name="button" type="button" id="button" value="Delete" 
                                onClick="document.finding.sbt.value='delete'; return deleteconfirm('finding','fid_','finding');" style="cursor:pointer;">
                    </td>
                </tr>
            </table>
            <!-- End Multiple Deletion -->
        </td>
        <td align="right">
        
            <!-- Pagination -->
            <table>
                <tr>
                    <td>
                        <input type="hidden" name="pageno" value="<?php echo $this->pageno; ?>">
                        <input type="hidden" name="totalpage" value="<?php echo $this->totalpage; ?>">
                        <?php if(1 < $this->pageno) { ?>
                        <input name="button" type="button" id="button" value="Previous" onClick="pageskip('finding','prev');" style="cursor:pointer;">
                        <?php } ?>
                    </td>
                    <td>&nbsp;Page:</td>
                    <td><input type="text" name="pageno" value="<?php echo $this->pageno; ?>" size="5" maxlength="5" readonly="yes">&nbsp;</td>
                    <td>
                        <?php if($this->pageno != $this->totalpage) {?>
                        <input name="button" type="button" id="button" value="Next" onClick="pageskip('finding','next');" style="cursor:pointer;">
                        <?php }?>
                    </td>
                    <td align=right>&nbsp; Total pages: <b><?php echo $this->totalpage; ?></b></td>
                </tr>
            </table>
            <!-- End Pagination -->     
        
        </td>
    </tr>
    <?php }?>
    <tr>
        <td colspan="2">
            <!-- Finding Search Results --> 
            <table width="100%" align="left" border="1" cellpadding="5" cellspacing="0" class="tbframe">
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
        </td>
    </tr>
</table>

