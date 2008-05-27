<div class="barleft">
<div class="barright">
<p><b>Administration:Users Summary</b><span><?php echo date('Y-m-d h:i:s:A');?></span>
</div>
</div>
<table class="tbframe" align="center"  width="98%">
    <tbody>
        <tr>
            <th>[<a href="/zfentry.php/panel/user/sub/list">Users list</a>] (total: <?php echo $this->total;?>)</th>
            <th>[<a href="/zfentry.php/panel/user/sub/create" title="add new Users">Add Users</a>]</th>
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
                <form name="query" method="post" action="/zfentry.php/panel/user/sub/list">
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
