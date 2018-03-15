<?php 
$this->load->helper('number_helper');
$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/tokenfield/css/bootstrap-tokenfield.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/sweetalert/sweetalert.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-body">
					<form class="form-inline" method="post">
						<div class="form-group">
							<input type="text" id="tokenfield" class="form-control" name="q" value="<?php echo $q; ?>" />
						</div>
						<div class="form-group text-right">
							<button id="btn-search" type="submit" class="btn btn-danger">Search</button>						
						</div>
						<p class="help-block">Search CS Number or Check ID/Number</p>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php 
	if(isset($results) AND !empty($results) ){
		$lines = array();
		$header = array();
		foreach($results as $row){
			if(empty($header)){
				$header[] = array(
								'check_id' => $row->CHECK_ID,
								'check_number' => $row->CHECK_NUMBER,
								'check_bank' => $row->CHECK_BANK,
								'check_date' => short_date($row->CHECK_DATE),
								'check_amount' => amount($row->CHECK_AMOUNT)
							);
			}
			$lines[] = array(
							'cs_number' => $row->CS_NUMBER,
							'sales_model' => $row->SALES_MODEL,
							'invoice_number' => $row->TRX_NUMBER,
							'account_name' => $row->ACCOUNT_NAME,
							'amount_due' => $row->AMOUNT_DUE,
						);
		}
		//~ print_r($lines);die();
	?>
	<div class="row">
		<div class="col-md-3">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Approved Check Details</h3>
				</div>
				<div class="box-body">
					<strong>Check ID</strong>
					<p class="text-muted">
						<?php echo $header[0]['check_id'];?>
					</p>
					<strong>Check Number</strong>
					<p class="text-muted">
						<?php echo $header[0]['check_number'];?>
					</p>
					<strong>Check Bank</strong>
					<p class="text-muted">
						<?php echo $header[0]['check_bank'];?>
					</p>
					<strong>Check Date</strong>
					<p class="text-muted">
						<?php echo $header[0]['check_date'];?>
					</p>
					<strong>Check Amount</strong>
					<p class="text-muted">
						<?php echo $header[0]['check_amount'];?>
					</p>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Vehicle Details</h3>
				</div>
				<div class="box-body">
					<table class="table">
						<thead>
							<tr>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Account Name</th>
								<th>Invoice Number</th>
								<th class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$total_amount = 0;
							foreach($lines as $row){
								$row = (object)$row;
							?>
								<tr>
									<td><?php echo $row->cs_number; ?></td>
									<td><?php echo $row->sales_model; ?></td>
									<td><?php echo $row->account_name; ?></td>
									<td><?php echo $row->invoice_number; ?></td>
									<td class="text-right"><?php echo amount($row->amount_due); ?></td>
								</tr>
							<?php 
							$total_amount += $row->amount_due;
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					<strong>Total Amount : <?php echo amount($total_amount); ?></strong>
				</div>
			</div>
		</div>
	</div>
	<?php 
	}
	else if($q != NULL){
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Search Results for "<?php echo $q; ?>"</h3>
				</div>
				<div class="box-body">
					No Results Found...
				</div>
			</div>
		</div>
	</div>
	<?php 
	}
	?>
</section>
<script>
	$(document).ready(function() {
	
	});
</script>
