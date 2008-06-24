    <?php 
    $evidence = $this->ev;
    $evaluation = $this->eval;
    ?>
<form action="/zfentry.php/remediation/evidence/id/<?php echo $evidence['id'];?>" method="post">
    <table cellpadding='3' cellspacing='1' class='tipframe' >
    <tr><th align='left'>Evidence Submitted by 
    <?php echo $evidence['submitted_by'];?> on <?php echo $evidence['submit_ts'];?>
    </th></tr>

    <tr> 
        <td><b>Evidence:</b>
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
        if(isset($evaluation[$v['name']])) {
            if( $evaluation[$v['name']]['group'] != 'EVIDENCE' ) {
                continue;
            }
            $value = &$evaluation[$v['name']]['decision'];
        }else{
            if($value == 'DENIED' ){
                $value = 'EXCLUDED';
            }else if($value == 'APPROVED') {
                $value = 'NONE';
            }
        }
        echo  "<tr><td><b>$name:</b>";
        if($value=='NONE' && $editable ){
            echo '<input type="hidden" name="evaluation" value="'.$k.'">';
            echo '<input type="submit" name="decision" value="APPROVE">';
            echo '<input type="submit" name="decision" value="DENY">';
            $editable=false;
        }else{
            echo "$value";
        }
        echo "</td></tr>";
    }
    ?>
    </table>
</form>
