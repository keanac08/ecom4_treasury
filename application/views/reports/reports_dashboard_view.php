<?php 

?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-9">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">List of Reports</h3>
				</div>
				<div class="box-body">
					<table class="table table-bordered" id="reports_tab" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Report ID</th>
								<th>Report Name</th>
								<th>Report Output</th>
								<th class="text-center">Generate</th>
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
								<?php 
								if($row->ACTION == 'modal'){
								?>
								<td width="10%" align="center"><a href="#" data-link="<?php echo $row->LINK; ?>" class="btn btn-xs btn-<?php echo $row->TYPE == 'Excel' ? 'success':'danger'; ?> btn_dr_modal"><i class="fa <?php echo $row->TYPE == 'Excel' ? 'fa-file-excel-o':'fa-file-pdf-o'; ?>"></i></a></td>
								<?php 
								}
								else if($row->ACTION == 'direct'){
								?>
								<td width="10%" align="center"><a target="_blank" href="<?php echo $row->LINK; ?>" class="btn btn-xs btn-<?php echo $row->TYPE == 'Excel' ? 'success':'danger'; ?> btn_dr_modal"><i class="fa <?php echo $row->TYPE == 'Excel' ? 'fa-file-excel-o':'fa-file-pdf-o'; ?>"></i></a></td>
								<?php 
								}
								?>
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
	<div class="modal-dialog modal-danger">
		<div class="modal-content">
		
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
	$(document).ready(function() {
		
		$('#reports_tab').DataTable({
			//~ 'bSort' : false
		});

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

