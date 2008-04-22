<div class="barleft">
<div class="barright">
<p><b>Administration:Users Summary</b><span><?php echo date('Y-m-d h:i:s:A');?></span>
</div>
</div>
<table class="tbframe" align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
    <tbody>
        <tr>
            <th>[<a href="/zfentry.php/panel/user">Users list</a>] (total: <?php echo $this->total;?>)</th>
            <th>[<a href="/zfentry.php/user/create" title="add new Users">Add Users</a>]</th>
            <th>
                <table align="center" border="0" cellpadding="1" cellspacing="1">
                    <tbody>
                        <tr height="22">
                            <td><b>Page:&nbsp;</b></td>
                            <!--<td>|</td>
                            <td>First</td>
                            <td>|</td>
                            <td>Prev</td>
                            <td>|</td>
                            <td><a href="#" title="the next page">Next</a></td>
                            <td>|</td>
                            <td><a href="#" title="go to the last page">Last</a></td>
                            <td>|</td>
                            <form name="scroll" action="/zfentry.php/user/searchbox" method="post">
                            <td><input name="tid" value="1" type="hidden">
                            <input name="pgno" value="1" size="2" maxlength="5" type="text">
                            </td>
                            <td><input value="Go" type="submit"></td></form>-->
                            <td><?php echo $this->links['all'];?></td>
                            <td>|</td>
                        </tr>
                    </tbody>
                </table>
            </th>
            <th>
                <input name="r_do" value="query" type="hidden">
                <input name="tid" value="1" type="hidden">
                <input name="qno" value="1" type="hidden">
                <table align="center" border="0" cellpadding="1" cellspacing="1">
                <form name="query" method="post" action="/zfentry.php/user/searchbox">
                    <tbody>
                        <tr>
                            <td><b>Query:&nbsp;</b></td>
                            <td><select name="fid">
                            <?php foreach($this->fid_array as $k=>$v){
                                if($k == $this->fid){
                                    $selected = " selected";
                                }
                                else {
                                    $selected = "";
                                }
                                echo'<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
                            }
                            ?>
                            </select></td>
                            <td><input name="qv" value="<?php echo $this->qv;?>" title="Input your query value" size="10" maxlength="20" type="text"></td>

                             <td><input value="Search" title="submit your request" type="submit"></td>
                        </tr>
                    </tbody>
                </form>
                </table>
            </th>
        </tr>
    </tbody>
</table>
