<style type="text/css">
*{
font-family: Arial;
margin:0px;
padding:0px;
}
@page {
 margin-left:3cm 2cm 2cm 2cm;
}
table.grid{
width:20.99cm ;
font-size: 12px;
border-collapse:collapse;
}
table.grid th{
	padding:5px;
}
table.grid th{
background: #F0F0F0;
border-top: 0.2mm solid #000;
border-bottom: 0.2mm solid #000;
text-align:center;
border:1px solid #000;
}
table.grid tr td{
	padding:2px;
	border-bottom:0.2mm solid #000;
	border:1px solid #000;
}
h1{
font-size: 18px;
}
h2{
font-size: 14px;
}
h3{
font-size: 12px;
}
p {
font-size: 10px;
}
center {
	padding:8px;
}
.atas{
display: block;
width:20.99cm ;
margin:0px;
padding:0px;
}
.kanan tr td{
	font-size:12px;
}
.attr{
font-size:9pt;
width: 100%;
padding-top:2pt;
padding-bottom:2pt;
border-top: 0.2mm solid #000;
border-bottom: 0.2mm solid #000;
}
.pagebreak {
width:20.99cm ;
page-break-after: always;
margin-bottom:10px;
}
.akhir {
width:20.99cm ;
font-size:13px;
}
.page {
width:20.99cm ;
font-size:12px;
padding:10px;
}

</style>
<?php
if($num>0){
$kanan = "<table class='kanan' width='100%'>
	<tr>
	<td colspan='2'>Download By</td>
	<td width='5'  colspan='4'>: ".auth()->user()->username."</td>
	</tr>
	<tr>
	<td colspan='2'>Source</td>
	<td width='5' colspan='4'>: Production Schedule </td>
	</tr> 
	<tr>
	<td colspan='2'>Tanggal</td>
	<td width='5' colspan='4'>: ".$doc_date."</td>
	</tr> 
	<tr>
	<td colspan='2'>Category</td>
	<td width='5' colspan='4'>: ".$category."</td>
	</tr>
	<tr>
	<td colspan='2'>Line</td>
	<td width='5' colspan='4'>: ".$line."</td>
	</tr>
	<tr>
	<td colspan='2'>Shift</td>
	<td width='5' colspan='4'>: ".$shift."</td>
	</tr>
	<tr>
	<td colspan='2'>Jo Num</td>
	<td width='5' colspan='4'>: ".$docnum."</td>
	</tr>
	<tr>
	<td colspan='2'></td>
	<td width='5' colspan='4'></td>
	</tr>
	</table>";
function myheader($kanan){
?>
<div class="atas">
<table width="100%">
<tr>
	
    </td>
	<td width="100%" valign="top" style="alignment-adjust: middle;">
    	 <?php echo $kanan ;?>
    </td>
</tr>    
</table>
</div>
<table class="grid" width="100%">
<tr>
<th>No.</th> 
<th>Doc Date</th>  
<th>Achievement</th>
<th>JO Num</th> 
<th>Item No</th>
<th>Item Name</th>
<th>Cust</th>
<th>Line</th> 
<th>Process</th> 
<th>Plan</th> 
<th>Actual</th>
<th>Remark</th>  
</tr>   
<?php } 
function myfooter(){	
	echo "</table>";
}

$g_total=0;
$no=1;  
$page =1;
foreach($db_data as $row){   
	$trc_id = $row->jo_num ;    
	if(($no%15000) == 1){
   	if($no > 1){
   	    $ofPage = ceil(($num) / 15000);
        myfooter();
        echo "<div class=\"pagebreak\" align='right'>
		<div class='page' align='center' colspan='8'>Page $page of $ofPage</div>
		</div>";
		$page++;
  	} 
      myheader($kanan);
      }
?>
	<tr>
	<td align="center" width="20"><?php echo $no ; ?></td>
	<td align="left" width="100" ><?php echo $row->req_due_date ; ?></td> 
	<td align="center" width="20"><?php echo number_format($row->achievment) ; ?></td>
	<td align="left" width="100" ><?php echo $row->jo_num ; ?></td>  
	<td align="left" width="100" ><?php echo $row->item_no ; ?></td>
	<td align="left" width="100" ><?php echo $row->item_name ; ?></td>
	<td align="left" width="100" ><?php echo $row->partner_code ; ?></td> 
	<td align="left" width="100" ><?php echo $row->home_line_detail_id ; ?></td> 
	<td align="left" width="100" ><?php echo $row->process_detail_id ; ?></td> 
	<td align="left" width="100" ><?php echo number_format($row->qty_plan,0) ; ?></td> 
	<td align="left" width="100" ><?php echo number_format($row->qty_1,0) ; ?></td> 
	<td align="left" width="100" ><?php echo $row->remark_d ; ?></td>   
  	</tr>
    <?php
	$no++;
    $ofPage = ceil(($num) / 15000);
	}
echo "";
echo "";
myfooter();
echo "</table>";	
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=Production Schedule ".$category." ".$doc_date.".xls");
header("Pragma: no-cache");
header("Expires: 0"); 
?>
<?php } else {	 echo "<div><center><h1>No data available</h1></center></div>"; } ?>

