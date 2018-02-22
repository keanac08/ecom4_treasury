<?php 

?>
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-9">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">List of Reports</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="reports_tab" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>Report ID</th>
								<th>Report Name</th>
								<th>Report Output</th>
								<th>Generate</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						foreach($result as $row){
						?>
							<tr>
								<td><?php echo sprintf('REP-%05d', $row->REPORT_ID); ?></td>
								<td><?php echo $row->NAME; ?></td>
								<td><?php echo $row->TYPE; ?></td>
								<td width="10%" align="center"><a href="#" data-link="<?php echo $row->LINK; ?>" class="btn_dr_modal"><i class="fa fa-download"></i></a></td>
							</tr>
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
	<div class="modal-dialog">
		<div class="modal-content">
		
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {

		$('body').on('click','a.btn_dr_modal',function(){
			v_link = $(this).data('link');
			$.ajax({
				url: '<?php echo base_url(); ?>' + v_link,
				success: function(data)
				{					
					$('.modal-content').html(data);
					$('#myModal').modal({
						backdrop: 'static'
						//~ keyboard: false  // to prevent closing with Esc button (if you want this too)
					});
				}
			});
		});
	});
</script>

