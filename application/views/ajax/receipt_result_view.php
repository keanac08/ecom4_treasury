<?php 
$this->load->helper('number_helper');
?>
<div class="modal-header">
	<h4 class="modal-title">Search Results</h4>
</div>
<div class="modal-body">
	<div class="row">
		
	</div> 
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th class="text-center">&nbsp;</th>
						<th class="text-center">Receipt ID</th>
						<th class="text-center">Receipt Number</th>
						<th class="text-right">Receipt Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach($result as $row){
					?>
						<tr>
							<td class="text-center"><a href="<?php echo base_url('receivables/receipt/collection/'.$row->RECEIPT_ID); ?>" class="btn btn-danger btn-xs"><i class="fa fa-search"></i></a></td>
							<td class="text-center"><?php echo $row->RECEIPT_ID; ?></td>
							<td class="text-center"><?php echo $row->RECEIPT_NUMBER; ?></td>
							<td class="text-right"><?php echo amount($row->RECEIPT_AMOUNT); ?></td>
						</tr>
					<?php 
					}
					?>
					</tbody>
			</table>
		</div>
	</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

