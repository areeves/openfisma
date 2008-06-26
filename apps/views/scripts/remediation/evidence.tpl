    <?php 
    require_once( MODELS . DS . 'comments.php');
    $evidence = $this->ev;
    $evaluation = $this->eval;
    $comment = new Comments();
    ?>
<form action="/zfentry.php/remediation/evidence/id/<?php echo $evidence['id'];?>" method="post"
 name='eval_ev<?php echo $evidence['id'];?>' >
    <table cellpadding='3' cellspacing='1' class='tipframe' >
    <tr><th colspan=2 align='left'>Evidence Submitted by 
    <?php echo $evidence['submitted_by'];?> on <?php echo $evidence['submit_ts'];?>
    </th></tr>

    <tr> 
        <td colspan=2><b>Evidence:</b>
        <?php 
            $url = $evidence['submission'];
            $ev_path[] = $evidence['submission'];
            $ev_path[] ='evidence' . DS . $evidence['poam_id']. DS. $evidence['submission'];
            $link = '%filename%';
            foreach( $ev_path as $path ) {
                if(file_exists($path) ) {
                    $url = str_replace('\\', '/',$path);
                    $link = "<a href='/{$url}' target='_blank'>%filename%</a>";
                }
            }
            echo str_replace('%filename%',basename($url),$link);

        ?>
        </td>
    </tr>
    <?php 
    $value = 'NONE';
    $editable = true;
    $eval_model = new Evaluation();
    $ev_evallist = $eval_model->getEvEvalList() ;
    foreach($ev_evallist as $k=>$v) { 
        $name = &$v['name'];
        if(isset($evaluation[$name])) {
            if( $evaluation[$name]['group'] != 'EVIDENCE' ) {
                var_dump($evaluation[$name]);
                continue;
            }
            $value = &$evaluation[$v['name']]['decision'];
            $username = nullGet($evaluation[$name]['username'],'...');
            $date = nullGet($evaluation[$name]['date'],'');
        }else{
            if($value == 'DENIED' ){
                $value = 'EXCLUDED';
            }else if($value == 'APPROVED') {
                $value = 'NONE';
            }
        }
        echo  "<tr><td><b>$name:</b>";
        if($value=='NONE' && $editable ){
            echo '<input type="hidden" name="evaluation" value="'.$k.'"/>';
            echo '<input type="hidden" name="topic" value="" />';
            echo '<input type="hidden" name="reject" value="" />';
            echo '<input type="hidden" name="decision" value="APPROVE" />';
            echo '<input type="submit" value="APPROVE" />';
            echo '<input type="button" value="DENY" '.
                    'onclick="comment(document.eval_ev'.$evidence['id'].');" />';
            $editable=false;
        }else{
            echo "$value </td>";
            if( $value == 'APPROVED' ) {
                echo "<td> <i> BY $username ON $date </i></td>";
            }else if($value == 'DENIED') {
                $row = $comment->fetchRow('poam_evaluation_id = '.$evaluation[$name]['eval_id']);
                echo "<td>";
                if( !empty($row) ) {
                    echo "<b>{$row->topic}</b>:{$row->content}";
                }
                echo "<i>BY $username ON $date </i></td>";
            }
        }
        echo "</tr>";
    }
    ?>
    </table>
</form>

<div id='comment_dialog' style="display:none">
Topic:
<input type="text" name="topic" size=80 value="" />
Justification:
<textarea name="reason" rows="3" cols="76" ></textarea>
</div>
