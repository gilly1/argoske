
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo e(route('home',[$subdomain])); ?>" class="brand-link">
      <img src="<?php echo e(asset('images/logo.png')); ?>" alt="starehe" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light"><?php echo e(config('app.name', 'setUp')); ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo e(asset('images/logo.png')); ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo e(auth()->user()->name); ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" can="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               
          <li class="nav-item">
            <a href="<?php echo e(route('home')); ?>" class="nav-link <?php echo e(Request::is('home') ? 'active' : ''); ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>

          <li class="nav-item has-treeview <?php echo e(Request::is('prospect_customers*') || Request::is('inquiries*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link ">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Inquiry
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">              
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_prospect_customers')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('prospect_customers.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('prospect_customers/prospect_customers*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Prospect Customer</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_inquiries')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('inquiries.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('inquiries/inquiries*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Inquiry</p>
                </a>
                </li>
                <?php endif; ?>
                
              
            </ul>
          </li>
          
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_inquiry_calculators')): ?>
					<li class="nav-item">
					<a href="<?php echo e(route('inquiry_calculators.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('inquiry_calculators/inquiry_calculators*') ? 'active' : ''); ?>">
						<i class="far fa-circle nav-icon"></i>
						
                            <p> Inquiry Calculator</p>
						</a>
						</li>
						<?php endif; ?>

          <li class="nav-item has-treeview <?php echo e(Request::is('account_customers*') || Request::is('guarantors*') || Request::is('contracts*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link ">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Contract
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">              
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_account_customers')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('contract_group',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('contracts/contract_group*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Contract Payment</p>
                </a>
                </li>
                <?php endif; ?>           
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_account_customers')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('account_customers.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('account_customers/account_customers*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Account Customer</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_contracts')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('contracts.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('contracts/contracts*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Contract</p>
                </a>
                </li>
                <?php endif; ?>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_guarantors')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('guarantors.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('guarantors/guarantors*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Guarantor</p>
                </a>
                </li>
                <?php endif; ?>
              
            </ul>
          </li>


          <li class="nav-item has-treeview <?php echo e(Request::is('users*') || Request::is('roles*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link ">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_users')): ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('users.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('users/user*') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Users</p>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_roles')): ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('roles.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('roles/roles*') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>User Roles</p>
                  </a>
                </li>
              <?php endif; ?>
              <li class="nav-item">
                <a href="<?php echo e(route('changePassword.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('users/changePassword*') ? 'active' : ''); ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Change Password</p>
                </a>
              </li>
              
            </ul>
          </li>
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_designations','view_hierarchies','view_designation_hierarchies'])): ?>
          <li class="nav-item has-treeview <?php echo e(Request::is('designation_hierarchies/designation_hierarchies*') || Request::is('hierarchies/hierarchies*') || Request::is('designations/designations*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link ">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Users Groups
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_designations')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('designations.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('designations/designations*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Designation</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_hierarchies')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('hierarchies.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('hierarchies/hierarchies*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Hierarchy</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_designation_hierarchies')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('designation_hierarchies.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('designation_hierarchies/designation_hierarchies*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Designation Hierarchy</p>
                </a>
                </li>
                <?php endif; ?>
              
            </ul>
          </li>
          <?php endif; ?>

          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_model_mappings','view_approvers','view_approver_statuses','view_models_to_approves','view_model_tobe_approveds'])): ?>
          <li class="nav-item has-treeview <?php echo e(Request::is('model_mappings/model_mappings*') ||Request::is('models_to_approves/models_to_approves*') || Request::is('model_tobe_approveds/model_tobe_approveds*') ||  Request::is('approver_statuses/approver_statuses*') || Request::is('approvers/approvers*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link ">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Approvers
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_model_mappings')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('model_mappings.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('model_mappings/model_mappings*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Model Mapping</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_approvers')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('approvers.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('approvers/approvers*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Approvers</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_approver_statuses')): ?>
                <li class="nav-item">
                <a href="<?php echo e(route('approver_statuses.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('approver_statuses/approver_statuses*') ? 'active' : ''); ?>">
                  <i class="far fa-circle nav-icon"></i>
                  
                                  <p> Approver Statuses</p>
                  </a>
                  </li>
                  <?php endif; ?>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_models_to_approves')): ?>
                  <li class="nav-item">
                  <a href="<?php echo e(route('models_to_approves.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('models_to_approves/models_to_approves*') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    
                                    <p> Models To Approve</p>
                    </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_model_tobe_approveds')): ?>
                  <li class="nav-item">
                  <a href="<?php echo e(route('model_tobe_approveds.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('model_tobe_approveds/model_tobe_approveds*') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    
                                    <p> Model Tobe Approved</p>
                    </a>
                    </li>
                    <?php endif; ?>
              
            </ul>
          </li>
          <?php endif; ?>

          
          <li class="nav-item has-treeview <?php echo e(Request::is('notification*') || Request::is('logs*') ? 'menu-open' : ''); ?>">
            <a href="#" class="nav-link ">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Audit Trail
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">              
          
              <li class="nav-item">
                <a href="<?php echo e(route('notification',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('notification*') ? 'active' : ''); ?>">
                  <i class="nav-icon fas fa-bell"></i>
                  <p>
                    Notification
                  </p>
                </a>
              </li>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_logs')): ?>
              <li class="nav-item">
                <a href="<?php echo e(route('logs',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('logs*') ? 'active' : ''); ?>">
                  <i class="nav-icon fa fa-history"></i>
                  <p>
                    Logs
                  </p>
                </a>
              </li>
              <?php endif; ?>
              
            </ul>
          </li>

          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_dotenvs')): ?>
					<li class="nav-item">
					<a href="<?php echo e(route('dotenvs.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('dotenvs/dotenvs*') ? 'active' : ''); ?>">
						<i class="far fa-circle nav-icon"></i>
						
                            <p> Dotenv</p>
						</a>
						</li>
						<?php endif; ?>
						
						
					
					
					
						

            <li class="nav-item has-treeview <?php echo e(Request::is('round_methods*') || Request::is('regions*') || Request::is('statuses*') || Request::is('shops*') || Request::is('genders*') || Request::is('nationalities*') || Request::is('identification_types*') || Request::is('employers*') ? 'menu-open' : ''); ?>">
              <a href="#" class="nav-link ">
                <i class="nav-icon fa fa-cog"></i>
                <p>
                  Settings
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">              
            
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_round_methods')): ?>
                  <li class="nav-item">
                  <a href="<?php echo e(route('round_methods.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('round_methods/round_methods*') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p> Round Method</p>
                  </a>
                  </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_regions')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('regions.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('regions/regions*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Regions</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_shops')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('shops.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('shops/shops*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Shops</p>
                </a>
                </li>
                <?php endif; ?>
						
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_genders')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('genders.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('genders/genders*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Gender</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_nationalities')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('nationalities.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('nationalities/nationalities*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Nationality</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_identification_types')): ?>
              <li class="nav-item">
              <a href="<?php echo e(route('identification_types.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('identification_types/identification_types*') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                
                                <p> Identification Type</p>
                </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_employers')): ?>
                  <li class="nav-item">
                  <a href="<?php echo e(route('employers.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('employers/employers*') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    
                                    <p> Employer</p>
                    </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_statuses')): ?>
                <li class="nav-item">
                <a href="<?php echo e(route('statuses.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('statuses/statuses*') ? 'active' : ''); ?>">
                  <i class="far fa-circle nav-icon"></i>
                  
                                  <p> Status</p>
                  </a>
                  </li>
                <?php endif; ?>
                
              </ul>
            </li>

            <li class="nav-item has-treeview <?php echo e(Request::is('notification*') || Request::is('logs*') ? 'menu-open' : ''); ?>">
              <a href="#" class="nav-link ">
                <i class="nav-icon fa fa-users"></i>
                <p>
                  Audit Trail
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">              
            
                <li class="nav-item">
                  <a href="<?php echo e(route('notification',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('notification*') ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-bell"></i>
                    <p>
                      Notification
                    </p>
                  </a>
                </li>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_logs')): ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('logs',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('logs*') ? 'active' : ''); ?>">
                    <i class="nav-icon fa fa-history"></i>
                    <p>
                      Logs
                    </p>
                  </a>
                </li>
                <?php endif; ?>
                
              </ul>
            </li>

            

				
						
						


          <li class="nav-item">
            <a href="<?php echo e(route('main.index',[$subdomain])); ?>" class="nav-link <?php echo e(Request::is('main*') ? 'active' : ''); ?>">
              <i class="nav-icon fa fa-history"></i>
              <p>
                Add Crud
              </p>
            </a>
          </li>
               
          

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside><?php /**PATH C:\laragon\www\argostenancy\resources\views/inc/navigation/aside.blade.php ENDPATH**/ ?>