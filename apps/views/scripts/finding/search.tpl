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
<div style="text-align:left;margin:0 0 2px 13em">
                        <input type="button" id="all_finding" value="Select All" style="cursor:pointer;">
                        <input type="button" id="none_finding"  value="Select None" style="cursor:pointer;">
                        <input type="submit" id="button" value="Delete" style="cursor:pointer;">
</div>
<?php }?>
            <!-- Finding Search Results --> 
            <table class="tbframe">
                <tr align="center">
                    <?php if(isAllow('finding','delete')) {?>
                    <th></th>
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
                </tr>
                <?php if(!empty($this->findings)){
                    foreach($this->findings as $row){
                ?>
                <tr>
                    <?php if(isAllow('finding','delete')){?>
                    <td class="tdc" >
                        <input type="checkbox" name="id_<?php echo $row['id'];?>" value="<?php echo $row['id'];?>">
                    </td>
                    <?php }?>
                    <td class="tdc"><?php echo $row['id']; ?></td>
                    <td class="tdc"><?php echo $row['legacy_finding_id']; ?>&nbsp;</td>
                    <td class="tdc"><?php echo $row['status']; ?>&nbsp;</td>
                    <td class="tdc"><?php echo $this->source[$row['source_id']]; ?>&nbsp;</td>
                    <td class="tdc"><?php echo $this->system[$row['system_id']]; ?>&nbsp;</td>
                    <td class="tdc"><?php echo $row['ip'];?>&nbsp;</td>
                    <td class="tdc"><?php echo $row['port'];?>&nbsp;</td>
                    <td class="tdc">&nbsp;</td>
                    <td class="tdc">&nbsp;</td>
                    <td class="tdc"><?php echo date("Y-m-d",strtotime($row['discover_ts']));?>&nbsp;</td>
                    <?php if(isAllow('finding','read')){ ?>
                    <!--edit right-->
                    <td  class="tdc">
                      <a href="/zfentry.php/panel/finding/sub/edit/id/<?php echo $row['id'];?>" ><img src="/images/view.gif" border="0" ></a>
                    </td>
                    <?php }
                    ?>
                </tr>
                <?php }
                }?>
            </table>
</form>
