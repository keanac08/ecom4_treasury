<?php 
	$this->load->helper('number_helper');
	$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Tagged Units</h3>
					<div style="margin-top: 5px;" class="box-tools pull-right">
						<a href="http://localhost/treasury/reports/tagged_excel" title="" data-placement="top" data-toggle="tooltip" target="_blank" class="text-green" data-original-title="Excel"><i class="fa fa-file-excel-o"></i></a>
					</div>
				</div>
				<div class="box-body">
					<table id="myTable" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Account Name</th>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Body Color</th>
								<th>Tagged Date</th>
								<th>Aging</th>
								<th class="text-right">Amount Due</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($result as $row){
							?>
							<tr>
								<td><?php echo $row->ACCOUNT_NAME;?></td>
								<td><?php echo $row->CS_NUMBER;?></td>
								<td><?php echo $row->SALES_MODEL;?></td>
								<td><?php echo $row->BODY_COLOR;?></td>
								<td><?php echo short_date($row->TAGGED_DATE);?></td>
								<td><?php echo $row->AGING;?></td>
								<td class="text-right"><?php echo amount($row->AMOUNT_DUE);?></td>
							</tr>
							<?php 
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>


<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
	$(document).ready(function(){
		$('#myTable').DataTable();
	});
</script>
