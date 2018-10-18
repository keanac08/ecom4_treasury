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
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Regular User','Dealer Admin','Dealer Vehicle','IPC Vehicle','IPC Vehicle-Fleet'))){
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
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Regular User','Dealer Admin','Dealer Vehicle','IPC Fleet','IPC Vehicle-Fleet'))){
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
						//~ PARTS -------------------------------------------------------------------------------
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Regular User','Dealer Admin','Dealer Parts','IPC Parts'))){
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
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Regular User','Dealer Admin','IPC Parts'))){
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
						if(in_array($this->session->tre_portal_user_type, array('Administrator','Regular User','Dealer Admin','IPC Parts'))){
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
						if(in_array($this->session->tre_portal_user_type, array( 'Administrator','Regular User'))){
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
				//~ SO STATUS -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Dealer Admin','Dealer Parts', 'Dealer Vehicle'))){
				?>
				<li class="treeview <?php  echo ($this->uri->segment(1) == 'sales_order') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-edit"></i> <span>Sales Order</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					<ul class="treeview-menu">
					<?php 
					//~ PARTS -------------------------------------------------------------------------------
					if(in_array($this->session->tre_portal_user_type, array('Dealer Admin','Dealer Parts'))){
					?>
						<li class="<?php echo ($this->uri->segment(2) == 'parts' and $this->uri->segment(3) == 'status') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('sales_order/parts/status'); ?> ">
								<i class="fa fa-circle-o"></i>DBS SO Status
							</a>
						</li>
					<?php 
					}
					?>
					<?php 
					//~ Vehicle -------------------------------------------------------------------------------
					if(in_array($this->session->tre_portal_user_type, array('Dealer Admin','Dealer Vehicle'))){
					?>
						<li class="<?php echo ($this->uri->segment(2) == 'vehicle' and $this->uri->segment(3) == 'tagged') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('sales_order/vehicle/tagged'); ?> ">
								<i class="fa fa-circle-o"></i>Vehicle Tagged Units
							</a>
						</li>
					<?php 
					}
					?>
					</ul>
				</li>
				<?php
				}
				?>
				
				<?php 
				//~ INVOICES -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Dealer Admin'))){
				?>
				<li class="treeview <?php  echo ($this->uri->segment(2) == 'invoice') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-file-text"></i> <span>Invoices</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					<ul class="treeview-menu">
					<?php 
					//~ BY Date Range -------------------------------------------------------------------------------
					if(in_array($this->session->tre_portal_user_type, array('Dealer Admin'))){
					?>
						<li class="<?php echo ($this->uri->segment(2) == 'invoice' and $this->uri->segment(3) == 'by_date_range') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('receivables/invoice/by_date_range'); ?> ">
								<i class="fa fa-circle-o"></i>By Date Range
							</a>
						</li>
					<?php 
					}
					?>
					</ul>
				</li>
				<?php
				}
				?>
				
				<?php 
				//~ RECEIVABLES AGING -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Administrator','IPC Parts','IPC Vehicle-Fleet','IPC Vehicle','IPC Fleet'))){
				?>
					<li class="<?php echo ($this->uri->segment(1) == 'receivables' AND $this->uri->segment(2) == 'aging') ? 'active' : ''; ?>">
						<a href="<?php echo base_url('receivables/aging/summary');?>">
							<i class="fa fa-paste"></i> <span>Profile Accounts Receivable</span>
						</a>
					</li>
				<?php 
				}
				?>
				
				
				
				<?php 
				//~ RECEIPT -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin'))){
				?>
				<li class="<?php echo ($this->uri->segment(1) == 'receivables' AND $this->uri->segment(2) == 'receipt') ? 'active' : ''; ?>">
					<a href="<?php echo base_url('receivables/receipt/search');?>">
						<i class="fa fa-file-text-o"></i><span><?php echo $this->session->tre_portal_user_type == 'Dealer Admin' ? '':'Collection '?>Receipt</span>
					</a>
				</li>
				<?php 
				}
				?>
				
				
				<?php 
				//~ PAYMENTS -------------------------------------------------------------------------------
				if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin'))){
				?>
					<li class="treeview <?php  echo (in_array($this->uri->segment(2), array('payment','check_warehousing'))) ? 'active' : ''; ?>">
						<a href="#">
							<i class="fa fa-credit-card"></i> <span>Payments</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li class="treeview <?php echo ($this->uri->segment(2) == 'payment' AND in_array($this->uri->segment(3),array('parts','dated','advance_payment','regular_pdc'))) ? 'active' : ''; ?>" >
								<a  href=""><i class="fa fa-circle-o"></i>Vehicle
									<span class="pull-right-container">
										<i class="fa fa-angle-left pull-right"></i>
									</span>
								</a>
								<ul class="treeview-menu">
									<li class="<?php echo ($this->uri->segment(2) == 'payment' AND $this->uri->segment(3) == 'dated') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/payment/dated'); ?> "><i class="fa fa-circle-o"></i>w/o Terms</a></li>
<!--
									<li class="<?php echo ($this->uri->segment(2) == 'payment' AND $this->uri->segment(3) == 'pdc') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/payment/pdc'); ?> "><i class="fa fa-circle-o"></i> PDC (W/ Payment Terms)</a></li>
-->
									<li class="treeview <?php echo ($this->uri->segment(2) == 'payment' AND in_array($this->uri->segment(3),array('regular_pdc','advance_payment'))) ? 'active' : ''; ?>" >
										<a  href=""><i class="fa fa-circle-o"></i>w/ Terms
											<span class="pull-right-container">
												<i class="fa fa-angle-left pull-right"></i>
											</span>
										</a>
										<ul class="treeview-menu">
											<li class="<?php echo ($this->uri->segment(2) == 'payment' AND $this->uri->segment(3) == 'regular_pdc') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/payment/regular_pdc/vehicle'); ?> "><i class="fa fa-circle-o"></i> Regular PDC</a></li>
											<li class="<?php echo ($this->uri->segment(2) == 'payment' AND $this->uri->segment(3) == 'advance_payment') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/payment/advance_payment'); ?> "><i class="fa fa-circle-o"></i> Adv. Payment (w/ Disc.)</a></li>
										</ul>
									</li> 
								</ul>
							</li> 
							<li class="<?php echo ($this->uri->segment(2) == 'payment' AND $this->uri->segment(3) == 'parts') ? 'active' : ''; ?>"><a href="<?php echo base_url('receivables/payment/parts'); ?> "><i class="fa fa-circle-o"></i> Parts</a></li>
							<li class="<?php echo ($this->uri->segment(3) == 'customer_check_list') ? 'active' : ''; ?>" >
								<a href="<?php echo base_url('receivables/check_warehousing/customer_check_list'); ?> ">
									<i class="fa fa-circle-o"></i>Check Warehousing
								</a>
							</li> 
						</ul>
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
						<i class="fa fa-truck"></i> <span>Tagged Units</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					<ul class="treeview-menu">
						<li class="treeview <?php echo (in_array($this->uri->segment(3),array('customer_entry','customer_entry_2'))) ? 'active' : ''; ?>" >
							<a  href=""><i class="fa fa-circle-o"></i>New Request for Invoice
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
								<i class="fa fa-circle-o"></i>Requests for Invoice
							</a>
						</li> 
					</ul>
				</li>
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
				
				<?php 
				if($this->session->tre_portal_user_type == 'Administrator'){
				?>
					<li class="<?php echo ($this->uri->segment(1) == 'users' AND $this->uri->segment(2) == 'log') ? 'active' : ''; ?>">
						<a href="<?php echo base_url('users/log');?>">
							<i class="fa fa-users"></i> <span>User Logs</span>
						</a>
					</li>
				<?php 
				}
				?>
			</ul><!-- /.sidebar-menu -->
	</section>
<!-- /.sidebar -->
</aside>

