<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel">
			<div class="pull-left image">
				<img src="<?php echo $this->session->get_userdata()['tre_portal_image'];?>" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p><?php echo $this->session->userdata()['tre_portal_fullname'];?></p>
			</div>
		</div>
		<form action="<?php echo base_url('search/invoice_details'); ?>" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Search Invoice...">
				<span class="input-group-btn">
					<button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</form>
		<!-- Sidebar Menu -->
			<ul class="sidebar-menu" data-widget="tree">
				<li class="header">MAIN NAVIGATION</li>
				<li class="<?php echo ($this->uri->segment(1) == 'receivables' AND $this->uri->segment(2) == 'transaction') ? 'active' : ''; ?>">
					<a href="<?php echo base_url('receivables/transaction/per_customer');?>">
						<i class="fa fa-home"></i> <span>Transaction Summary</span>
					</a>
				</li>
				<li class="treeview <?php  echo ($this->uri->segment(2) == 'soa') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-file-text-o"></i> <span>Statement of Account</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					<ul class="treeview-menu">
						<?php 
						//~ VEHICLE -------------------------------------------------------------------------------
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin','IPC Vehicle','IPC Vehicle-Fleet'))){
						?>
							<li class="<?php echo ($this->uri->segment(4) == 'vehicle') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/soa/admin/vehicle'); ?> ">
									<i class="fa fa-circle-o"></i>Vehicle
								</a>
							</li>
						<?php 
						}
						?>
						
						<?php 
						//~ FLEET -------------------------------------------------------------------------------
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin','IPC Fleet','IPC Vehicle-Fleet'))){
						?>
							<li class="<?php echo ($this->uri->segment(4) == 'fleet') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/soa/admin/fleet'); ?> ">
									<i class="fa fa-circle-o"></i>Fleet
								</a>
							</li> 
						<?php 
						}
						?>
						
						<?php 
						//~ OTHERS -------------------------------------------------------------------------------
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin','IPC Parts'))){
						?>
							<li class="<?php echo ($this->uri->segment(4) == 'parts') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/soa/admin/parts'); ?> ">
									<i class="fa fa-circle-o"></i>Parts
								</a>
							</li>
						<?php 
						}
						?>
						
						<?php 
						//~ OTHERS -------------------------------------------------------------------------------
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin'))){
						?>
							<li class="<?php echo ($this->uri->segment(4) == 'others') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/soa/admin/others'); ?> ">
									<i class="fa fa-circle-o"></i>Others
								</a>
							</li> 
						<?php 
						}
						?>
						
						<?php 
						//~ POWERTRAIN -------------------------------------------------------------------------------
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin','IPC Parts'))){
						?>
							<li class="<?php echo ($this->uri->segment(4) == 'powertrain') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/soa/admin/powertrain'); ?> ">
									<i class="fa fa-circle-o"></i>Powertrain
								</a>
							</li> 
						<?php 
						}
						?>
						
						<?php 
						//~ EMPLOYEE -------------------------------------------------------------------------------
						if($this->session->tre_portal_user_type == 'Administrator'){
						?>
							<li class="<?php echo ($this->uri->segment(4) == 'employee') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/soa/admin/employee'); ?> ">
									<i class="fa fa-circle-o"></i>Employee
								</a>
							</li> 
						<?php 
						}
						?>
						
					</ul>
				</li>
				
				<?php 
				//~ RECEIVABLES AGING -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Administrator','IPC Parts','IPC Vehicle-Fleet','IPC Vehicle','IPC Fleet'))){
				?>
					<li class="<?php echo ($this->uri->segment(1) == 'receivables' AND $this->uri->segment(2) == 'aging') ? 'active' : ''; ?>">
						<a href="<?php echo base_url('receivables/aging/summary');?>">
							<i class="fa fa-paste"></i> <span>Accounts Receivable Aging</span>
						</a>
					</li>
				<?php 
				}
				?>
				
				<?php 
				//~ Credit Line Monitoring -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Administrator','IPC Parts'))){
				?>
					<li class="treeview <?php  echo ($this->uri->segment(2) == 'credit_line' AND $this->uri->segment(3) == 'monitoring') ? 'active' : ''; ?>">
						<a href="#">
							<i class="fa fa-credit-card"></i> <span>Credit Line Monitoring</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li class="<?php echo ($this->uri->segment(4) == 'parts') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/credit_line/monitoring/parts'); ?> ">
									<i class="fa fa-circle-o"></i>Parts
								</a>
							</li> 
						</ul>
					</li>
				<?php 
				}
				?>
				
				<?php 
				//~ CHECK WAREHOUSING -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Administrator'))){
				?>
					<li class="treeview <?php  echo ($this->uri->segment(2) == 'check_warehousing') ? 'active' : ''; ?>">
						<a href="#">
							<i class="fa fa-list-alt"></i> <span>Check Warehousing</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li class="<?php echo ($this->uri->segment(3) == 'entry') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/check_warehousing/entry'); ?> ">
									<i class="fa fa-circle-o"></i>New Entry
								</a>
							</li> 
							<li class="<?php echo ($this->uri->segment(3) == 'credit_hold_releasing') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/check_warehousing/credit_hold_releasing'); ?> ">
									<i class="fa fa-circle-o"></i>Credit Hold Releasing
								</a>
							</li> 
							<li class="<?php echo ($this->uri->segment(3) == 'pdc') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/check_warehousing/pdc'); ?> ">
									<i class="fa fa-circle-o"></i>Approved PDCs
								</a>
							</li> 
							<li class="<?php echo ($this->uri->segment(3) == 'pdc') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/check_warehousing/search'); ?> ">
									<i class="fa fa-circle-o"></i>Search
								</a>
							</li> 
						</ul>
					</li>
				<?php 
				}
				else if($this->session->tre_portal_user_type == 'Dealer Admin'){
				?>
				<li class="treeview <?php  echo ($this->uri->segment(2) == 'check_warehousing') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-truck"></i> <span>Reserved Units</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					<ul class="treeview-menu">
						<li class="treeview <?php echo (in_array($this->uri->segment(3),array('customer_entry','customer_entry_2'))) ? 'active' : ''; ?>" >
							<a  href=""><i class="fa fa-circle-o"></i> Request for Invoice
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li class="<?php echo ($this->uri->segment(3) == 'customer_entry' && $this->uri->segment(4) == 'vehicle') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/check_warehousing/customer_entry/vehicle'); ?> "><i class="fa fa-circle-o"></i> Vehicle</a></li>
								<li class="<?php echo ($this->uri->segment(3) == 'customer_entry' && $this->uri->segment(4) == 'vehicle_terms') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/check_warehousing/customer_entry/vehicle_terms'); ?> "><i class="fa fa-circle-o"></i> Vehicle w/ Terms</a></li>
								<li class="<?php echo ($this->uri->segment(3) == 'customer_entry' && $this->uri->segment(4) == 'fleet') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/check_warehousing/customer_entry/fleet'); ?> "><i class="fa fa-circle-o"></i> Fleet</a></li>
							</ul>
						</li> 
						<li class="<?php echo ($this->uri->segment(3) == 'customer_check_list') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('receivables/check_warehousing/customer_check_list'); ?> ">
								<i class="fa fa-circle-o"></i>For Invoice
							</a>
						</li> 
					</ul>
				<?php 
				}
				?>
				
				<?php 
				if($this->session->tre_portal_user_type == 'Administrator'){
				?>
					<li class="<?php echo ($this->uri->segment(1) == 'payables' AND $this->uri->segment(2) == 'check_writer') ? 'active' : ''; ?>">
						<a href="<?php echo base_url('payables/check_writer/generate');?>">
							<i class="fa fa-edit"></i> <span>Check Writer</span>
						</a>
					</li>
				<?php 
				}
				?>
				<li class="<?php echo ($this->uri->segment(1) == 'reports' AND $this->uri->segment(2) == 'dashboard') ? 'active' : ''; ?>">
					<a href="<?php echo base_url('reports/dashboard');?>">
						<i class="fa fa-bar-chart"></i> <span>Reports</span>
					</a>
				</li>
			</ul><!-- /.sidebar-menu -->
	</section>
<!-- /.sidebar -->
</aside>

