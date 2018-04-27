<?php 
$this->load->helper('number_helper');
$this->load->helper('date_helper');
$this->load->helper('null_helper');
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
						<p class="help-block">Search CS Number</p>
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
								'check_id' => nvl($row->CHECK_ID),
								'check_number' => nvl($row->CHECK_NUMBER),
								'check_bank' => nvl($row->CHECK_BANK),
								'check_date' => short_date($row->CHECK_DATE),
								'check_amount' => amount($row->CHECK_AMOUNT),
								'date_approved' => short_date($row->DATE_APPROVED)
							);
			}
			$lines[] = array(
							'cs_number' => $row->CS_NUMBER,
							'sales_model' => $row->SALES_MODEL,
							'order_number' => $row->ORDER_NUMBER,
							'invoice_number' => $row->TRX_NUMBER,
							'account_name' => $row->CUSTOMER_NAME,
							'amount_due' => $row->AMOUNT_DUE,
							'invoice_amount' => $row->INVOICE_AMOUNT,
							'status' => $row->STATUS,
						);
		}
		//~ print_r($lines);die();
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Vehicle Details</h3>
					<span class="pull-right"><span class="label label-info">Status : <?php echo $lines[0]['status']; ?></span></span>
				</div>
				<div class="box-body">
					<table class="table">
						<thead>
							<tr>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Account Name</th>
								<th>Order Number</th>
								<th>Invoice Number</th>
								<th class="text-right">Invoice Amount</th>
								<th class="text-right">Amount Due</th>
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
									<td><?php echo $row->order_number; ?></td>
									<td><?php echo NVL($row->invoice_number); ?></td>
									<td class="text-right"><?php echo amount($row->invoice_amount); ?></td>
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
					
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Check Details</h3>
				</div>
				<div class="box-body">
					<table class="table">
						<thead>
							<tr>
								<th>Check ID</th>
								<th>Check Number</th>
								<th>Check Bank</th>
								<th>Check Date</th>
								<th>Check Amount</th>
								<th>Approved Date</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($header as $row){
							?>
							<tr>
								<td><?php echo $row['check_id']; ?></td>
								<td><?php echo $row['check_number']; ?></td>
								<td><?php echo $row['check_bank']; ?></td>
								<td><?php echo $row['check_date']; ?></td>
								<td><?php echo $row['check_amount']; ?></td>
								<td><?php echo $row['date_approved']; ?></td>
							</tr>
							<?php 
							}
							?>
						</tbody>
					</table>
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
