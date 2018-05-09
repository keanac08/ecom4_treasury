<?php 
$this->load->helper('number_helper');
$this->load->helper('profile_class_helper');
?>
<link href="<?php echo base_url('resources/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title"><?php echo $profile_class[0]->NAME; ?></h6>
					<div class="box-tools pull-right" style="margin-top: 5px;">
							<a class="text-red" target="_blank" data-toggle="tooltip" data-placement="top" title="PDF" href="<?php echo base_url("reports/aging_profile_pdf/index/".str_replace('/', '', $as_of_date))."/".$profile_class_id; ?>" class=""><i class="fa fa-file-pdf-o"></i></a>
							<a class="text-success" target="_blank" data-toggle="tooltip" data-placement="top" title="Excel" href="<?php echo base_url("reports/receivables_excel/index/".str_replace('/', '', $as_of_date)."/".$profile_class_id); ?>" class=""><i class="fa fa-file-excel-o"></i></a>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-sm-3">
							<form id="myForm" class="form-inline" method="POST" accept-charset="utf-8">
								<div class="input-group date" id="datetimepicker">
									<input type="text" class="form-control" name="as_of_date" value="<?php echo $as_of_date; ?>"/>
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
								</div>
							</form>
						</div>
					</div>
					<br />
					<table class="table table-condensed table-hover" class="display" cellspacing="0" width="100%">
						<thead >
							<tr>
								<th class="text-center">&nbsp;</th>
								<th class="text-center">Customer ID</th>
								<th class="text-center">Customer Name</th>
								<th class="text-center info text-info">Unpulledout Receivables</th>
								<th class="text-center success text-success">Current Receivables</th>
								<th class="text-center warning text-yellow">Past Due Receivables</th>
								<th class="text-center danger text-red">Total Receivables</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($results as $row){
							?>
								<tr style="<?php echo ($row->CUSTOMER_ID == NULL) ? 'background-color: #f1f1f1;font-weight: bold;' : '';?>" >
									<td><a target="blank" class="btn btn-danger btn-xs <?php echo ($row->CUSTOMER_ID == NULL) ? 'hidden':''; ?>" 
												href="<?php echo base_url(); ?>receivables/soa/admin/<?php echo get_sales_type($row->PROFILE_CLASS_ID); ?>/<?php echo $row->PROFILE_CLASS_ID; ?>/<?php echo ($row->CUSTOMER_ID * 101403); ?>/<?php echo date('mdY', strtotime($as_of_date)); ?>">
												<i class="glyphicon glyphicon-chevron-right"></i>
											</a>
										</td>
									<td><?php echo $row->CUSTOMER_ID;?></td>
									<td><?php echo $row->CUSTOMER_NAME;?></td>
									<td class="text-right info text-info"><?php echo amount($row->CONTINGENT_RECEIVABLES);?></td>
									<td class="text-right success text-success"><?php echo amount($row->CURRENT_RECEIVABLES);?></td>
									<td class="text-right warning text-yellow"><?php echo amount($row->PAST_DUE);?></td>
									<td class="text-right danger text-red"><?php echo amount($row->TOTAL);?></td>
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

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- test
			test
			test
			test -->
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/moment/js/moment.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js');?>"></script>
<script>
	$(document).ready(function() {
		
		$('[data-toggle="tooltip"]').tooltip(); 
		
		$('#datetimepicker').datetimepicker({
			//~ debug:true,
			format: 'MM/DD/YYYY'
			 //~ maxDate: moment()
		});
		
		$('#datetimepicker').on('dp.change', function (e) {
			$('#myForm').submit();
        });

	});
</script>

