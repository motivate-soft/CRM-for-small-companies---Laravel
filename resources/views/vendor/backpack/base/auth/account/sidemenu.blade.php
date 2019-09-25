<div class="box">
    <div class="box-body box-profile">
	    <img class="profile-user-img img-responsive img-circle" src="{{ backpack_user()->getAvatar() }}">
	    <h3 class="profile-username text-center">{{ backpack_auth()->user()->name }}</h3>
	</div>

	<ul class="nav nav-pills nav-stacked">

	  <li role="presentation"
		@if (Request::route()->getName() == 'backpack.account.info')
	  	class="active"
	  	@endif
	  	><a href="{{ route('backpack.account.info') }}">{{ trans('backpack::base.update_account_info') }}</a></li>

	  <li role="presentation"
		@if (Request::route()->getName() == 'backpack.account.password')
	  	class="active"
	  	@endif
	  	><a href="{{ route('backpack.account.password') }}">{{ trans('backpack::base.change_password') }}</a></li>
		@role('company')
	  <li role="presentation"
			@if (Request::route()->getName() == 'backpack.account.holidaydays')
			class="active"
				@endif
		><a href="{{ backpack_url('holidaydays').'/'.backpack_user()->id.'/edit' }}">{{ trans('fields.holiday_days') }} and {{ trans('fields.working_days') }}</a></li>
		<li role="presentation"
			@if (Request::route()->getName() == 'backpack.account.alert')
			class="active"
				@endif
		><a href="{{ backpack_url('alert') }}">{{ trans('fields.alert') }} {{ trans('fields.configuration') }}</a></li>
		@endrole
	</ul>
</div>
