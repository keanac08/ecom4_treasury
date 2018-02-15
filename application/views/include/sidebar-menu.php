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
		<form action="" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Search...">
				<span class="input-group-btn">
					<button type="submit" id="search-btn" class="btn btn-flat">
						<i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</form>
		<!-- Sidebar Menu -->
			<ul class="sidebar-menu" data-widget="tree">
				<li class="header">MAIN NAVIGATION</li>
				<li class="<?php echo ($this->uri->segment(1) == 'dashboard') ? 'active' : ''; ?>">
					<a href="<?php echo base_url('dashboard');?>">
						<i class="fa fa-home"></i> <span>Transaction Summary</span>
					</a>
				</li>
				<li class="treeview
					<?php  echo (
					            $this->uri->segment(3) == 'parts'
							 OR $this->uri->segment(3) == 'vehicle'
							 OR $this->uri->segment(3) == 'fleet'
							 OR $this->uri->segment(3) == 'others'
							 OR $this->uri->segment(3) == 'powertrain'
							 OR $this->uri->segment(3) == 'employee'
							 ) ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-file-text-o"></i> <span>Statement of Account</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					<ul class="treeview-menu">
						<li class="<?php echo ($this->uri->segment(3) == 'vehicle') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('soa/admin/vehicle'); ?> ">
								<i class="fa fa-circle-o"></i>Vehicle
							</a>
						</li> 
						<li class="<?php echo ($this->uri->segment(3) == 'fleet') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('soa/admin/fleet'); ?> ">
								<i class="fa fa-circle-o"></i>Fleet
							</a>
						</li> 
						<li class="<?php echo ($this->uri->segment(3) == 'parts') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('soa/admin/parts'); ?> ">
								<i class="fa fa-circle-o"></i>Parts
							</a>
						</li> 
						<li class="<?php echo ($this->uri->segment(3) == 'others') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('soa/admin/others'); ?> ">
								<i class="fa fa-circle-o"></i>Others
							</a>
						</li> 
						<li class="<?php echo ($this->uri->segment(3) == 'powertrain') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('soa/admin/powertrain'); ?> ">
								<i class="fa fa-circle-o"></i>Powertrain
							</a>
						</li> 
						<li class="<?php echo ($this->uri->segment(3) == 'employee') ? 'active' : ''; ?>" >
							<a href="<?php echo base_url('soa/admin/employee'); ?> ">
								<i class="fa fa-circle-o"></i>Employee
							</a>
						</li> 
					</ul>
					<li class="<?php echo ($this->uri->segment(1) == 'receivables') ? 'active' : ''; ?>">
						<a href="<?php echo base_url('receivables/summary');?>">
							<i class="fa fa-paste"></i> <span>Receivables Summary</span>
						</a>
					</li>
					<li class="<?php echo ($this->uri->segment(1) == 'check_writer') ? 'active' : ''; ?>">
						<a href="<?php echo base_url('check_writer/generate');?>">
							<i class="fa fa-edit"></i> <span>Check Writer</span>
						</a>
					</li>
				</li>
			</ul><!-- /.sidebar-menu -->
	</section>
<!-- /.sidebar -->
</aside>

