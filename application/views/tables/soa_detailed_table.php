<div class="row">
	<div class="col-md-offset-9 col-md-3">
		<div class="input-group">
			<input id="search_table" type="text" class="form-control" placeholder="Search . . .">
			<span class="input-group-addon"><i class="fa fa-search"></i></span>
		</div>
	</div>
	<div class="col-md-12">
		&nbsp;
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<table id="table_soa" class="table table-hover">
			<thead>
				<tr>
					<th class="text-center">&nbsp;</th>
					<th class="text-center"><?php echo (in_array( $profile_class_id, array(1040,1045,1043))) ? 'CS Number' : ''; ?></th>
					<th class="text-center">Invoice Number</th>
					<th class="text-center">Invoice Date</th>
					<th class="text-center"><?php echo (in_array( $profile_class_id, array(1040,1045,1043))) ? 'Pullout Date' : 'Delivery Date'; ?></th>
					<th class="text-center">Payment Terms</th>
					<th class="text-right">Invoice Amount</th>
					<th class="text-right">WHT Amount</th>
					<th class="text-right">Balance</th>
					<th class="text-center">Days Overdue</th>
					<?php 
					if (in_array( $profile_class_id, array(1040,1045,1043))){ 
					?>
					<th class="text-center">PDC Number</th>
					<?php 
					}
					if (in_array( $profile_class_id, array(1040))){
					?>
					<th class="text-center">Fleet Name</th>
					<?php 
					}
					?>
				</tr>
			</thead>
			<tbody>
				
			<?php
			$past_due_01_15 = 0;
			$past_due_16_30 = 0;
			$past_due_31_60 = 0;
			$past_due_61_90 = 0;
			$past_due_91_120 = 0;
			$past_due_over_120 = 0;
			$contingent = 0;
			$current = 0;
			
			$total_past_due = 0;
			
			foreach($soa_detailed as $row){
				
				if($row->INVOICE_ID != NULL){
					
					if($row->DELIVERY_DATE == NULL){
						$current += $row->BALANCE;
					}
					else if($row->DAYS_OVERDUE == 0){
						$contingent += $row->BALANCE;
					}
					else if($row->DAYS_OVERDUE > 0 AND $row->DAYS_OVERDUE <= 15){
						$past_due_01_15 += $row->BALANCE;
					}
					else if($row->DAYS_OVERDUE > 15 AND $row->DAYS_OVERDUE <= 30){
						$past_due_16_30 += $row->BALANCE;
					}
					else if($row->DAYS_OVERDUE > 30 AND $row->DAYS_OVERDUE <= 60){
						$past_due_31_60 += $row->BALANCE;
					}
					else if($row->DAYS_OVERDUE > 60 AND $row->DAYS_OVERDUE <= 90){
						$past_due_61_90 += $row->BALANCE;
					}
					else if($row->DAYS_OVERDUE > 90 AND $row->DAYS_OVERDUE <= 120){
						$past_due_91_120 += $row->BALANCE;
					}
					else if($row->DAYS_OVERDUE > 120){
						$past_due_over_120 += $row->BALANCE;
					}

					
				?>
					<tr class="text-<?php echo ($row->DELIVERY_DATE == NULL ? 'yellow' : ($row->DAYS_OVERDUE > 0 ? 'danger':'success')); ?>" >
						<td>
							<a class="modal_trigger btn btn-<?php echo ($row->DELIVERY_DATE == NULL ? 'warning' : ($row->DAYS_OVERDUE > 0 ? 'danger':'success')); ?> btn-xs " href="javascript:;" data-invoice_id="<?php echo $row->INVOICE_ID; ?>" ><i class="fa  fa-chevron-circle-right"></i></a>
						</td>
						<td class="text-center"><?php echo (in_array( $profile_class_id, array(1040,1045,1043))) ? $row->CS_NUMBER : ''; ?></td>
						<td class="text-center"><?php echo $row->INVOICE_NO; ?></td>
						<td class="text-center"><?php echo short_date($row->INVOICE_DATE); ?></td>
						<td class="text-center"><?php echo short_date($row->DELIVERY_DATE); ?></td>
						<td class="text-center"><?php echo $row->PAYMENT_TERM; ?></td>
						<td class="text-right"><?php echo amount($row->TRANSACTION_AMOUNT); ?></td>
						<td class="text-right"><?php echo amount($row->WHT_AMOUNT); ?></td>
						<td class="text-right"><?php echo amount($row->BALANCE); ?></td>
						<td class="text-center"><?php echo $row->DAYS_OVERDUE; ?></td>
						<?php 
						if (in_array( $profile_class_id, array(1040,1045,1043))){ 
						?>
						<td class="text-center">-</td>
						<?php 
						}
						if (in_array( $profile_class_id, array(1040))){
						?>
						<td class="text-center">-</td>
						<?php 
						}
						?>
					</tr>
				<?php 
				}
				 else if(
						($row->INVOICE_ID == NULL AND $row->DUE_DATE != NULL) OR
						($row->INVOICE_ID == NULL AND $row->DUE_DATE == NULL AND $row->DELIVERY_DATE == NULL)
						){
				?>
					<tr class="item text-<?php echo ($row->DELIVERY_DATE == NULL ? 'yellow' : ($row->DAYS_OVERDUE > 0 ? 'danger':'success')); ?>">
						<td colspan="1">&nbsp;</td>
						<td colspan="5" class="text-center">
							<strong>
								<?php echo ($row->DELIVERY_DATE == NULL ? 'Subtotal' : ($row->DAYS_OVERDUE > 0 ? 'Subtotal Past Due':'Subtotal Due')); ?> &emsp; <?php echo short_date($row->DUE_DATE); ?>
							</strong>
						</td>
						<td class="text-right"><strong><?php echo amount($row->TRANSACTION_AMOUNT); ?></strong></td>
						<td class="text-right"><strong><?php echo amount($row->WHT_AMOUNT); ?></strong></td>
						<td class="text-right"><strong><?php echo amount($row->BALANCE); ?></strong></td>
						<td colspan="1">&nbsp;</td>
						<?php 
						if (in_array( $profile_class_id, array(1040,1045,1043))){ 
						?>
						<td class="text-center">&nbsp;</td>
						<?php 
						}
						if (in_array( $profile_class_id, array(1040))){
						?>
						<td class="text-center">&nbsp;</td>
						<?php 
						}
						?>
					</tr>
					<tr>
						<td colspan="10">&nbsp;</td>
						<?php 
						if (in_array( $profile_class_id, array(1040,1045,1043))){ 
						?>
						<td class="text-center">&nbsp;</td>
						<?php 
						}
						if (in_array( $profile_class_id, array(1040))){
						?>
						<td class="text-center">&nbsp;</td>
						<?php 
						}
						?>
					</tr>
				<?php 
				}
			}
			?>
			</tbody>
		</table>
		<a id="back-to-top" href="#" class="btn btn-info btn-lg" role="button"><i class="fa fa-chevron-up"></i></a>
	</div>
</div>

