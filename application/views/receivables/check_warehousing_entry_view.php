<link href="<?php echo base_url('resources/plugins/tokenfield/css/bootstrap-tokenfield.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/sweetalert/sweetalert.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-9">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Search cs number here:</h3>
				</div>
				<div class="box-body">
					<form>
						<div class="form-group">
							<input type="text" id="tokenfield" class="form-control" name="cs_numbers" value="<?php echo $cs_numbers; ?>" />
						</div>
						<div class="form-group text-right">
							<button id="btn-search" type="button" class="btn btn-danger">Search</button>						
						</div>
					</form>
				</div>
				<div id="result" class="box-body">
				
				</div>
			</div>
		</div>
	</div>
</section>
<script src="<?php echo base_url('resources/plugins/tokenfield/bootstrap-tokenfield.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/sweetalert/sweetalert.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/blockUI/blockUI.js');?>"></script>
<script>
	$(document).ready(function() {
		$('#tokenfield').tokenfield();
		
		$('button#btn-search').click(function(){
			
			 //~ $.blockUI(); 
			var cs_numbers = $('input[name=cs_numbers]').val();
			
			$.ajax({
				type:'POST',
				data:{
					cs_numbers : cs_numbers
				},
				beforeSend : function() {
				  $.blockUI({ 
						message:  '<h4><i class="fa fa-spinner fa-pulse"></i> &nbsp; Searching... Please wait...</h4>',
						css: { 
							border: 'none', 
							padding: 0, 
							backgroundColor: '#000', 
							'-webkit-border-radius': '7px', 
							'-moz-border-radius': '7px', 
							opacity: .7,
							color: '#fff'
						}
					}); 
				}, 
				url: '<?php echo base_url();?>receivables/check_warehousing/ajax_search_cs_number',
				success:function(data){
					$('#result').html(data);
					$.unblockUI();
				}
			});
		});
	});
</script>
