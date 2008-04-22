<div class="barleft">
<div class="barright">
<p><b>Dashboard</b><span><?PHP echo $this->escape($this->Current_time);?></span>
</div>
</div>

<br>


<table width="95%" align="center"  border="0" cellpadding="10" class="tipframe">
	<tr>
		<td  align="left"><b>Alerts </b><br>
			<br>
			<!-- Awaiting Mitigation Strategy -->
			<li>There are <b><?PHP echo $this->escape($this->open);?></b> finding(s) awaiting a mitigation strategy and approval.</li>
			<!-- Awaiting Evidence -->
			<li>There are <b><?PHP echo $this->escape($this->need_ev_ot);?></b> finding(s) awaiting evidence.
            <!-- Overdue Awaiting Evidence -->
			<li>There are <b><?PHP echo $this->escape($this->need_ev_od);?></b> overdue finding(s) awaiting evidence.
            <br>
		</td>
	</tr>
</table>

<br>
<br>


<table width="95%" align="center" border="0" cellpadding="0" cellspacing="0" class="tipframe">
	<tr><td colspan="3"  align="left"><b>&nbsp;&nbsp;&nbsp;Management Overview </b></td></tr>
    <tr>
      <td width="33%"  align="center"><?PHP echo $this->OneChart; ?></td>
      <td width="34%"  align="center"><?PHP echo $this->TwoChart; ?></td>
      <td width="33%"  align="center"><?PHP echo $this->ThreeChart; ?></td>
    </tr>
    <tr>
      <td width="33%"  align="center">Current Distribution of<br>POA&M Status</td>
      <td width="34%"  align="center">Current POA&M Item<br>Totals by Status</td>
      <td width="33%"  align="center">Current Distribution of<br>POA&M Type</td>
    </tr>
</table>


