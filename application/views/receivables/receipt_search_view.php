<?php 
$this->load->helper('number_helper');
$this->load->helper('null_helper');
?>
<section class="content">
	<div class="row">
		<div class="col-md-4">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title">Search : </h6>
				</div>
				<div class="box-body">
					<div class="input-group">
						<input type="text" class="form-control" name="search_receipt" placeholder="Search for Receipt, Invoice or CS Number">
						<span class="input-group-btn">
							<button id="btn-search" class="btn btn-danger" type="button">Go!</button>
						</span>
					</div><!-- /input-group -->
					<p class="help-block"></p>
				</div>
			</div>
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title">Header Details</h6>
				</div>
				<div class="box-body">
					<strong>Receipt Number</strong>
					<p class="text-muted">-</p>
					
					<strong>Customer Name</strong>
					<p class="text-muted">-</p>
					
					<strong>Receipt Amount</strong>
					<p class="text-muted">-</p>
					
					<strong>Applied Amount</strong>
					<p class="text-muted">-</p>
					
					<strong>Unapplied Amount</strong>
					<p class="text-muted">-</p>
				</div>
			</div>
		</div>
		<div class="col-md-8" style="min-height:400px;">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title">Line Details</h6>
				</div>
				<div class="box-body">
					<table class="table">
						<thead>
							<tr>
								<th>Invoice Number</th>
								<th>CS Number</th>
								<th>Amount Applied</th>
								<th>Amount Due Remaining</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if(!isset($results)){
								for($i=0;$i<2;$i++){
								?>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<?php 
								}
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
			<!-- test
			test
			test
			test -->
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('body').on('click','button#btn-search',function(){
			//~ alert('aw');
			var search_receipt = $('input[name=search_receipt]').val();
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>receivables/receipt/ajax_find_receipt_id',
				data: {
						search_receipt: search_receipt
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});
	});
</script>

