<div class="barleft">
<div class="barright">
<p><b>Finding Search Results</b>
   <span><!-- Pagination -->
            <?php echo $this->links['all'];?>
   </span>
</p>
</div>
</div>
<form id="findingresult" method="post" action="/zfentry.php/panel/finding/sub/delete">
<?php if(isAllow('finding','delete')){ ?>
            <!-- Allow Multiple Deletion if the user has the appropriate rights -->
                        <input type="button" id="all_finding" value="Select All" style="cursor:pointer;">
                        <input type="button" id="none_finding"  value="Select None" style="cursor:pointer;">
                        <input type="submit" id="button" value="Delete" style="cursor:pointer;">
            <!-- End Multiple Deletion -->
<?php }?>
            <!-- Finding Search Results --> 
            <table class="tbframe">
                <tr align="center">
                    <?php if(isAllow('finding','delete')) {?>
                    <th></td>
                    <?php } ?>
                    <th>ID<img src="/images/up_arrow.gif" border="0"> 
                          <img src="/images/down_arrow.gif" border="0"></th>
                    <th>Legacy Finding ID</th>
                    <th>Status<img src="/images/up_arrow.gif" border="0"> 
                              <img src="/images/down_arrow.gif" border="0"></th>
                    <th>Source<img src="/images/up_arrow.gif" border="0"> 
                              <img src="/images/down_arrow.gif" border="0"></th>
                    <th>System<img src="/images/up_arrow.gif" border="0"> 
                              <img src="/images/down_arrow.gif" border="0"></th>
                    <th>IP<img src="/images/up_arrow.gif" border="0"> 
                          <img src="/images/down_arrow.gif" border="0"></th>
                    <th>Port<img src="/images/up_arrow.gif" border="0"> 
                            <img src="/images/down_arrow.gif" border="0"></th>
                    <th>Product<img src="/images/up_arrow.gif" border="0"> 
                               <img src="/images/down_arrow.gif" border="0"></th>
                    <th>Vulnerabilities<img src="/images/up_arrow.gif" border="0"> 
                                       <img src="/images/down_arrow.gif" border="0"></th>
                    <th>Discovered<img src="/images/up_arrow.gif" border="0"> 
                                  <img src="/images/down_arrow.gif" border="0"></th>
                    <?php if(isAllow('finding','read')) {?>
                    <!--edit right-->
                    <th nowrap>Detail</th>
                    <?php }?>
                    <?php if(isAllow('finding','update')) {?>
                    <!--view right-->
                    <th nowrap>Action</th>
                    <?php }?>
                </tr>
                <?php if(!empty($this->findings)){
                    foreach($this->findings as $row){
                ?>
                <tr>
                    <?php if(isAllow('finding','delete')){?>
                    <td align="center" >
                        <input type="checkbox" name="id_<?php echo $row['id'];?>" value="<?php echo $row['id'];?>">
                    </td>
                    <?php }?>
                    <td align="center" class="tdc"><?php echo $row['id']; ?></td>
                    <td><?php echo $row['legacy_finding_id']; ?>&nbsp;</td>
                    <td><?php echo $row['status']; ?>&nbsp;</td>
                    <td><?php echo $this->source[$row['source_id']]; ?>&nbsp;</td>
                    <td><?php echo $this->system[$row['system_id']]; ?>&nbsp;</td>
                    <td><?php echo $row['ip'];?>&nbsp;</td>
                    <td><?php echo $row['port'];?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="center"><?php echo date("Y-m-d",strtotime($row['discover_ts']));?>&nbsp;</td>
                    <?php if(isAllow('finding','read')){ ?>
                    <!--edit right-->
                    <td align="center" >
                      <a href="/zfentry.php/panel/finding/sub/edit/id/<?php echo $row['id'];?>" ><img src="/images/view.gif" border="0" ></a>
                    </td>
                    <?php }
                    if(isAllow('finding','update')){
                        if('OPEN' == $row['status']){
                    ?>
                    <td align="center">
                        <a style="text-decoration:none;" href="/zfentry.php/finding/convert/id/<?php echo $row['id'];?>"><button>Convert</button></a>
                    </td>
                    <?php }
                    }
                    ?>
                </tr>
                <?php }
                }?>
            </table>
</form>
