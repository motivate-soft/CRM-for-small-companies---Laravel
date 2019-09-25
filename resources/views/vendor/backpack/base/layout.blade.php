<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('backpack::inc.head')
</head>
<body class="hold-transition {{ config('backpack.base.skin') }} sidebar-mini">
	<script type="text/javascript">
		/* Recover sidebar state */
		(function () {
			if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
				var body = document.getElementsByTagName('body')[0];
				body.className = body.className + ' sidebar-collapse';
			}
		})();
	</script>
    <!-- Site wrapper -->
    <div class="wrapper">

      @include('backpack::inc.main_header')

      <!-- =============================================== -->

      @include('backpack::inc.sidebar')

      <!-- =============================================== -->

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
         @yield('header')

        <!-- Main content -->
        <section class="content">

          @yield('content')

        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

      <footer class="main-footer text-sm clearfix">
        @include('backpack::inc.footer')
      </footer>
    </div>
    <!-- ./wrapper -->


    @yield('before_scripts')
    @stack('before_scripts')

    @include('backpack::inc.scripts')
    @include('backpack::inc.alerts')

    @yield('after_scripts')
    @stack('after_scripts')
    <script src="https://www.gstatic.com/firebasejs/4.6.2/firebase.js"></script>
    <script>
        /* Store sidebar state */
        // $('.sidebar-toggle').click(function(event) {
        //   event.preventDefault();
        //   if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
        //     sessionStorage.setItem('sidebar-toggle-collapsed', '');
        //   } else {
        //     sessionStorage.setItem('sidebar-toggle-collapsed', '1');
        //   }
        // });

        // Set active state on menu element
        var current_url = "{{ Request::fullUrl() }}";
        var full_url = current_url+location.search;
        var $navLinks = $("ul.sidebar-menu li a");
        // First look for an exact match including the search string
        var $curentPageLink = $navLinks.filter(
            function() { return $(this).attr('href') === full_url; }
        );
        // If not found, look for the link that starts with the url
        if(!$curentPageLink.length > 0){
            $curentPageLink = $navLinks.filter(
                function() { return $(this).attr('href').startsWith(current_url) || current_url.startsWith($(this).attr('href')); }
            );
        }

        $curentPageLink.parents('li').addClass('active');

        // firebas config
        var config = {
            apiKey: "AIzaSyBjpweFh63mmXB-5mOHmIVUfIrW0oN_G3A",
            authDomain: "fcc-book-trading-e8821.firebaseapp.com",
            databaseURL: "https://fcc-book-trading-e8821.firebaseio.com",
            projectId: "fcc-book-trading-e8821",
            storageBucket: "fcc-book-trading-e8821.appspot.com",
            messagingSenderId: "160797425400",
            appId: "1:160797425400:web:ec6e870a9fc3971f"
        };

        firebase.initializeApp(config);

        const messaging = firebase.messaging();

        @if(backpack_user()->role == \App\User::ROLE_EMPLOYEE)
            messaging.onMessage(function(payload) {
                console.log('From Company To Employee');
                console.log('Message received. ', payload);

                var type = payload['data']['type'];
                switch (type){
                    case 'vacation':
                        var count = parseInt($(".vacation_notification").html());
                        if($(".vacation_notification").length == 0){
                            var badge = "<span class='label pull-right bg-red vacation_notification'>1</span>";
                            $('.vacation_notification_container').html(badge);
                        }else{
                            $('.vacation_notification').html(count+1);
                        }
                        break;
                    case 'expense':
                        var count = parseInt($(".expense_notification").html());
                        if($(".expense_notification").length == 0){
                            var badge = "<span class='label pull-right bg-red expense_notification'>1</span>";
                            $('.expense_notification_container').html(badge);
                        }else{
                            $('.expense_notification').html(count+1);
                        }
                        break;
                    case 'medical':
                        var count = parseInt($(".medical_notification").html());
                        if($(".medical_notification").length == 0){
                            var badge = "<span class='label pull-right bg-red medical_notification'>1</span>";
                            $('.medical_notification_container').html(badge);
                        }else{
                            $('.medical_notification').html(count+1);
                        }
                        break;
                    case 'absences':
                        var count = parseInt($(".absences_notification").html());
                        if($(".absences_notification").length == 0){
                            var badge = "<span class='label pull-right bg-red absences_notification'>1</span>";
                            $('.absences_notification_container').html(badge);
                        }else{
                            $('.absences_notification').html(count+1);
                        }
                        break;
                    case 'incidents':
                        var count = parseInt($(".incidents_notification").html());
                        if($(".incidents_notification").length == 0){
                            var badge = "<span class='label pull-right bg-red incidents_notification'>1</span>";
                            $('.incidents_notification_container').html(badge);
                        }else{
                            $('.incidents_notification').html(count+1);
                        }
                        break;
                    case 'other':
                        var count = parseInt($(".other_notification").html());
                        if($(".other_notification").length == 0){
                            var badge = "<span class='label pull-right bg-red other_notification'>1</span>";
                            $('.other_notification_container').html(badge);
                        }else{
                            $('.other_notification').html(count+1);
                        }
                        break;
                }
            });
            // function overtime(){
            //     $.ajax({
            //         url: 'http://localhost:8000/e_over',
            //         type: 'GET',
            //         success: function(response){
            //             // Perform operation on the return value
            //             // alert(response);
            //         }
            //     });
            // }
            //
            // setInterval(overtime,60*1000);

            // function exceedtime(){
            //     $.ajax({
            //         url: 'http://localhost:8000/e_exceed',
            //         type: 'GET',
            //         success: function(response){
            //             // Perform operation on the return value
            //             // alert(response);
            //         }
            //     });
            // }

            // setInterval(exceedtime,1*3600*1000);
        @endif
        @if(backpack_user()->role == \App\User::ROLE_COMPANY)
            messaging.onMessage(function(payload) {
                console.log('From Employee To Company');
                console.log('Message received. ', payload);
                var type = payload['data']['type'];
            switch (type){
                case 'vacation':
                    var count = parseInt($(".vacation_notification").html());
                    if($(".vacation_notification").length == 0){
                        var badge = "<span class='label pull-right bg-red vacation_notification'>1</span>";
                        $('.vacation_notification_container').html(badge);
                    }else{
                        $('.vacation_notification').html(count+1);
                    }
                    break;
                case 'expense':
                    var count = parseInt($(".expense_notification").html());
                    if($(".expense_notification").length == 0){
                        var badge = "<span class='label pull-right bg-red expense_notification'>1</span>";
                        $('.expense_notification_container').html(badge);
                    }else{
                        $('.expense_notification').html(count+1);
                    }
                    break;
                case 'medical':
                    var count = parseInt($(".medical_notification").html());
                    if($(".medical_notification").length == 0){
                        var badge = "<span class='label pull-right bg-red medical_notification'>1</span>";
                        $('.medical_notification_container').html(badge);
                    }else{
                        $('.medical_notification').html(count+1);
                    }
                    break;
                case 'absences':
                    var count = parseInt($(".absences_notification").html());
                    if($(".absences_notification").length == 0){
                        var badge = "<span class='label pull-right bg-red absences_notification'>1</span>";
                        $('.absences_notification_container').html(badge);
                    }else{
                        $('.absences_notification').html(count+1);
                    }
                    break;
                case 'incidents':
                    var count = parseInt($(".incidents_notification").html());
                    if($(".incidents_notification").length == 0){
                        var badge = "<span class='label pull-right bg-red incidents_notification'>1</span>";
                        $('.incidents_notification_container').html(badge);
                    }else{
                        $('.incidents_notification').html(count+1);
                    }
                    break;
                case 'other':
                    var count = parseInt($(".other_notification").html());
                    if($(".other_notification").length == 0){
                        var badge = "<span class='label pull-right bg-red other_notification'>1</span>";
                        $('.other_notification_container').html(badge);
                    }else{
                        $('.other_notification').html(count+1);
                    }
                    break;
            }

            });

           
        @endif

    </script>
	<script>
        $(function () {
			@if(backpack_user()->role == \App\User::ROLE_COMPANY)
			var current_url = '{{Request::segment(1)}}';
            if(current_url == 'employees' || current_url == 'employee_holiday_days'){
                $('table th:first').trigger('click');
            }
			
			@endif
        })
    </script>
    <!-- JavaScripts -->
    {{-- <script src="{{ mix('js/app.js') }}"></script> --}}
</body>
</html>
