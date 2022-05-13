<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="{{route('home',[$subdomain])}}" class="brand-link">
		<img src="{{ asset('images/logo.png')}}" alt="starehe" class="brand-image img-circle elevation-3"
			 style="opacity: .8">
		<span class="brand-text font-weight-light">{{ config('app.name', 'setUp') }}</span>
	  </a>
  
	  <!-- Sidebar -->
	  <div class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
		  <div class="image">
			<img src="{{ asset('images/logo.png')}}" class="img-circle elevation-2" alt="User Image">
		  </div>
		  <div class="info">
			<a href="#" class="d-block">{{auth()->user()->name}}</a>
		  </div>
		</div>
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" can="menu" data-accordion="false"> 
				<li class="nav-item">
					<a href="{{route('home')}}" class="nav-link {{Request::is('home') ? 'active' : '' }}">
					  <i class="nav-icon fas fa-tachometer-alt"></i>
					  <p>
						Dashboard
					  </p>
					</a>
				  </li>
				  
          <li class="nav-item has-treeview {{Request::is('users*') || Request::is('roles*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link ">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('view_users')
                <li class="nav-item">
                  <a href="{{route('users.index',[$subdomain])}}" class="nav-link {{Request::is('users/user*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Users</p>
                  </a>
                </li>
              @endcan
              @can('view_roles')
                <li class="nav-item">
                  <a href="{{route('roles.index',[$subdomain])}}" class="nav-link {{Request::is('roles/roles*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>User Roles</p>
                  </a>
                </li>
              @endcan
              <li class="nav-item">
                <a href="{{route('changePassword.index',[$subdomain])}}" class="nav-link {{Request::is('users/changePassword*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Change Password</p>
                </a>
              </li>
              
            </ul>
          </li>

<?php
$sidebarmenu = (new App\Repositories\MainRepository($subdomain.'side_bars',App\Model\SideBar::orderby('menu_order')->get()))->all();
$refs = array();
$list = array();

foreach($sidebarmenu as $data)
{
	$thisref = &$refs[ $data->id ];
	$thisref['menu_parent_id'] = $data->parent_id;
	$thisref['menu_item_name'] = $data->custom_title ? $data->custom_title : $data->title;
	$thisref['url'] = $data->url;
	$thisref['icon'] = $data->icon;
	$thisref['permission'] = $data->permission;
  
	if(!Route::has($data->url)){
		continue;
	}

	if ($data->parent_id == 0)
	{
		$list[ $data->id ] = &$thisref;

	}
	else
	{
		$refs[ $data->parent_id ]['children'][ $data->id ] = &$thisref;
	}
}
function create_list( $arr ,$urutan,$subdomain)
{
	if($urutan==0){
		$html = "";
	}else
	{
	 $html = "\n<ul class=\"nav nav-treeview \" >\n";
	}
	foreach ($arr as $key=>$v)
	{
		if($urutan==0){
			if(!\Auth::user()->canany($v['permission'])){
				continue;
			}

			$html .= "\n<li class=\"nav-item has-treeview\"  >\n";
			$html .= ' <a href="'.route($v['url'],[$subdomain]).'" class="nav-link ">';
			$html .= '<i class="nav-icon '.$v['icon'].'"></i>';
			$html .= '<p>'.$v['menu_item_name'];

			if(array_key_exists('children', $v)){
				$html .= '<i class="right fas fa-angle-left"></i>';
			}
			$html .= '</p> </a>';

		}
		
		if (array_key_exists('children', $v))
		{
			$html .= create_list($v['children'],1,$subdomain);
		}
		else{
			if($urutan==1){
				if(!\Auth::user()->can($v['permission'])){
					continue;
				}
				$html .= "\n<li class=\"nav-item\"  >\n";
				$html .= ' <a href="'.route($v['url'],[$subdomain]).'" class="nav-link ">';
				$html .= '<i class="nav-icon '.$v['icon'].'"></i>';
				$html .= '<p>'.$v['menu_item_name'];
				$html .= '</p> </a>';
				$html .= "</li>\n";
			}
		}
	}
	if($urutan==1){
		$html .= "</ul>\n";
	}

	return $html;
}
?>

{!! create_list( $list,0,$subdomain ) !!}


          
<li class="nav-item">
	<a href="{{route('notification',[$subdomain])}}" class="nav-link {{Request::is('notification*') ? 'active' : '' }}">
	  <i class="nav-icon fas fa-bell"></i>
	  <p>
		Notification
	  </p>
	</a>
  </li>

  @can('view_dotenvs')
			<li class="nav-item">
			<a href="{{route('dotenvs.index',[$subdomain])}}" class="nav-link {{Request::is('dotenvs/dotenvs*') ? 'active' : '' }}">
				<i class="far fa-circle nav-icon"></i>
				
					<p> Dotenv</p>
				</a>
				</li>
				@endcan
				@can('view_side_bars')
			<li class="nav-item">
			<a href="{{route('side_bars.index',[$subdomain])}}" class="nav-link {{Request::is('side_bars/side_bars*') ? 'active' : '' }}">
				<i class="far fa-circle nav-icon"></i>
				
					<p> Side Bar</p>
				</a>
				</li>
				@endcan
				{{-- add link --}}

  @can('view_logs')
  <li class="nav-item">
	<a href="{{route('logs',[$subdomain])}}" class="nav-link {{Request::is('logs*') ? 'active' : '' }}">
	  <i class="nav-icon fa fa-history"></i>
	  <p>
		Logs
	  </p>
	</a>
  </li>
  @endcan

  <li class="nav-item">
	<a href="{{route('main.index',[$subdomain])}}" class="nav-link {{Request::is('main*') ? 'active' : '' }}">
	  <i class="nav-icon fa fa-history"></i>
	  <p>
		Add Crud
	  </p>
	</a>
  </li>
  <li class="nav-item">
	<a href="{{route('git.index',[$subdomain])}}" class="nav-link {{Request::is('git*') ? 'active' : '' }}">
	  <i class="nav-icon fa fa-history"></i>
	  <p>
		Git
	  </p>
	</a>
  </li>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>