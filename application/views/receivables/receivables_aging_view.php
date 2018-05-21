<?php 
$this->load->helper('number_helper');
?>
<link href="<?php echo base_url('resources/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" >
<section class="content">
	
	
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title">&nbsp;</h6>
					<div class="box-tools pull-right" style="margin-top: 5px;">
						<a class="text-red" target="_blank" data-toggle="tooltip" data-placement="top" title="PDF" href="<?php echo base_url("reports/aging_pdf/index/".str_replace('/', '', $as_of_date)); ?>" class=""><i class="fa fa-file-pdf-o"></i></a>
						<a class="text-success" target="_blank" data-toggle="tooltip" data-placement="top" title="Excel" href="<?php echo base_url("reports/receivables_excel/index/".str_replace('/', '', $as_of_date)); ?>" class=""><i class="fa fa-file-excel-o"></i></a>
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
								<th class="text-left">Profile ID</th>
								<th class="text-center">Profile Class</th>
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
								<tr style="<?php echo ($row->PROFILE_CLASS_ID == NULL) ? 'background-color: #f1f1f1;font-weight: bold;' : '';?>" >
									<td>
										<a class="btn btn-danger btn-xs <?php echo ($row->PROFILE_CLASS_ID == NULL) ? 'hidden':''; ?>" 
										   target="_blank" 
										   href="profile_summary/<?php echo $row->PROFILE_CLASS_ID; ?>/<?php echo str_replace('/', '', $as_of_date); ?>"><?php echo ($row->PROFILE_CLASS_ID != NULL) ? '<i class="glyphicon glyphicon-chevron-right"></i>':'';?>
										</a>
									</td>
									<td><?php echo $row->PROFILE_CLASS_ID;?></td>
									<td><?php echo $row->PROFILE_CLASS;?></td>
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
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<i class="fa fa-bar-chart"></i>
					<h3 class="box-title">AR Summary Graph</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<canvas id="line-chart2" width="700" height="310"></canvas>
				</div><!-- /.box-body -->
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

<script src="<?php echo base_url('resources/plugins/chartjs/Chart.min.js');?>"></script>
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
        
        var as_of_date = $('.as_of_date').val();
        
        $.ajax({
			url : "<?php echo base_url();?>receivables/aging/ajax_summary_chart",
			type : "POST",
			data: {
					as_of_date : as_of_date
					},
			success : function(data){
				var info = JSON.parse(data);

				var v_profile_class = [];
				var v_contingent = [];
				var v_current = [];
				var v_pastdue = [];
				var v_total = [];

				for(var i in info) {
					v_profile_class.push(info[i].PROFILE_CLASS);
					v_current.push(info[i].CURRENT_RECEIVABLES);
					v_contingent.push(info[i].CONTINGENT_RECEIVABLES);
					v_pastdue.push(info[i].PAST_DUE);
					v_total.push(info[i].TOTAL);
				}
				
				var chartdata = {
					labels: v_profile_class,
					datasets: [
						//~ {
							//~ label: "Total",
							//~ fill: true,
							//~ stack: 'Stack 1',
							//~ backgroundColor: "#f56954",
							//~ data: v_total
						//~ },
						{
							label: "Unpulledout",
							fill: true,
							stack: 'Stack 0',
							backgroundColor: "#39CCCC",
							data: v_contingent
						},
						{
							label: "Current",
							fill: true,
							stack: 'Stack 0',
							backgroundColor: "#00a65a",
							data: v_current
						},
						
							{
							label: "Past Due",
							fill: true,
							stack: 'Stack 0',
							backgroundColor: "#f39c12",
							data: v_pastdue
						}
					]
				};

				//~ var ctx = $("#line-chart");
				var ctx = $("#line-chart2");

				var LineGraph = new Chart(ctx, {
					type: 'bar',
					data: chartdata,
					options: {
						scales: {
							xAxes: [{
								stacked: true,
								//~ ticks: {
									min: 100000000,
									//~ beginAtZero: true,
									//~ callback: function(value, index, values) {
										//~ if(parseInt(value) >= 1000){
											//~ return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
										//~ } 
										//~ else {
											//~ return value;
										//~ }
									//~ }
								 //~ }	
							}],
							yAxes: [{
								stacked: true,
								ticks: {
									beginAtZero: true,
									callback: function(value, index, values) {
										if(parseInt(value) >= 1000){
											return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
										} 
										else {
											return value;
										}
									}
								 }
							}]
						},
						tooltips: {
							mode: 'index',
							intersect: true,
							 callbacks:  {
								afterTitle: function() {
									window.total = 0;
								},
								label: function(tooltipItem, data) {
									var tag = data.datasets[tooltipItem.datasetIndex].label;
									var valor = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
									window.total += parseFloat(valor);
									
									tab = "";
									for (var i = parseFloat(valor).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').length; i <= 16; i++){
										tab = tab + " ";
									}
									
									if(tag == "Current"){
										tab2 = "         ";
									}
									else if(tag == "Past Due"){
										tab2 = "      ";
									}
									else if(tag == "Unpulledout"){
										tab2 = "";
									}
									else{
										tab2 = "";
									}
									
									return tag + tab2 + " : " + tab + parseFloat(valor).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');             
								},
								footer: function() {
									return "TOTAL                :    " + window.total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
								}
							}
						}
					}
					
				});
			},
			error : function(data) {

			}
		});

	});
</script>

