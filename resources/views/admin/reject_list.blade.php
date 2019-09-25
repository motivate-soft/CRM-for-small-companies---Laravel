@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>{{ $title }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
            <li class="active">{{ $title }}</li>
        </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{ trans('fields.name') }}</th>
                            <th>{{ trans('fields.email') }}</th>
                            {{--<th>{{ trans('fields.status') }}</th>--}}
                            <th>{{ trans('fields.created_at') }}</th>
                            <th>{{ trans('fields.billing_status') }}</th>
                            <th>{{ trans('fields.expire_date') }}</th>
                            <th>{{ trans('fields.last_login_at') }}</th>
                            <th>{{ trans('fields.action') }}</th>
                        </tr>
                        </thead>
                    <tbody>
                    @foreach($table as $item)
                        <tr>
                            <td>
                                {{ $item->name }}
                            </td>
                            <td>
                                {{ $item->email }}
                            </td>
                            {{--<td>--}}
                                {{--{{ trans('fields.' . $item->status) }}--}}
                            {{--</td>--}}
                            <td>
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            </td>
                            <td>
                                @if($item->billing_state)
                                    {{ trans('fields.' . $item->billing_state) }}
                                @endif
                            </td>
                            <td>
                                @if($item->expire_date)
                                {{ \Carbon\Carbon::parse($item->expire_date)->format('d M Y') }}
                                @endif
                            </td>
                            <td>
                                @if($item->user->last_login_at)
                                {{ \Carbon\Carbon::parse($item->user->last_login_at)->format('d M Y') }}
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-xs btn-danger" href="{{ url('reject_list/reject/' . $item->id) }}" data-toggle="tooltip" ><i class="fa fa-remove"></i> {{ trans('fields.reject') }}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                    {{--<div class="pull-right">--}}
                        {{--{!! $table->links() !!}--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('after_styles')
    <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap.min.css">

    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/list.css') }}">

    <style>
        .dt-buttons {
            display: inline-block;
        }
    </style>

@endsection
@section('after_scripts')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>


    <script>

        var crud = {
            exportButtons: JSON.parse('true'),
            functionsToRunOnDataTablesDrawEvent: [],
            addFunctionToDataTablesDrawEventQueue: function (functionName) {
                if (this.functionsToRunOnDataTablesDrawEvent.indexOf(functionName) == -1) {
                    this.functionsToRunOnDataTablesDrawEvent.push(functionName);
                }
            },
            responsiveToggle: function(dt) {
                $(dt.table().header()).find('th').toggleClass('all');
                dt.responsive.rebuild();
                dt.responsive.recalc();
            },
            executeFunctionByName: function(str, args) {
                var arr = str.split('.');
                var fn = window[ arr[0] ];

                for (var i = 1; i < arr.length; i++)
                { fn = fn[ arr[i] ]; }
                fn.apply(window, args);
            },
            dataTableConfiguration: {

                @if (false)
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal( {
                            header: function ( row ) {
                                // show the content of the first column
                                // as the modal header
                                var data = row.data();
                                return data[0];
                            }
                        } ),
                        renderer: function ( api, rowIdx, columns ) {
                            var data = $.map( columns, function ( col, i ) {
                                var allColumnHeaders = $("#crudTable thead>tr>th");

                                if ($(allColumnHeaders[col.columnIndex]).attr('data-visible-in-modal') == 'false') {
                                    return '';
                                }

                                return '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    '<td style="vertical-align:top;"><strong>'+col.title.trim()+':'+'<strong></td> '+
                                    '<td style="padding-left:10px;padding-bottom:10px;">'+col.data+'</td>'+
                                    '</tr>';
                            } ).join('');

                            return data ?
                                $('<table class="table table-striped table-condensed m-b-0">').append( data ) :
                                false;
                        },
                    }
                },
                fixedHeader: true,
                @else
                responsive: false,
                scrollX: true,
                @endif

                    @if (true)
                stateSave: true,
                @endif
                autoWidth: false,
                ordering: false,
                /* Disable initial sort */
                aaSorting: [],
                language: {
                    "emptyTable":     "{{ trans('backpack::crud.emptyTable') }}",
                    "info":           "{{ trans('backpack::crud.info') }}",
                    "infoEmpty":      "{{ trans('backpack::crud.infoEmpty') }}",
                    "infoFiltered":   "{{ trans('backpack::crud.infoFiltered') }}",
                    "infoPostFix":    "{{ trans('backpack::crud.infoPostFix') }}",
                    "thousands":      "{{ trans('backpack::crud.thousands') }}",
                    "lengthMenu":     "{{ trans('backpack::crud.lengthMenu') }}",
                    "loadingRecords": "{{ trans('backpack::crud.loadingRecords') }}",
                    "processing":     "<img src='{{ asset('vendor/backpack/crud/img/ajax-loader.gif') }}' alt='{{ trans('backpack::crud.processing') }}'>",
                    "search":         "{{ trans('backpack::crud.search') }}",
                    "zeroRecords":    "{{ trans('backpack::crud.zeroRecords') }}",
                    "paginate": {
                        "first":      "{{ trans('backpack::crud.paginate.first') }}",
                        "last":       "{{ trans('backpack::crud.paginate.last') }}",
                        "next":       ">",
                        "previous":   "<"
                    },
                    "aria": {
                        "sortAscending":  "{{ trans('backpack::crud.aria.sortAscending') }}",
                        "sortDescending": "{{ trans('backpack::crud.aria.sortDescending') }}"
                    },
                    "buttons": {
                        "copy":   "{{ trans('backpack::crud.export.copy') }}",
                        "excel":  "{{ trans('backpack::crud.export.excel') }}",
                        "csv":    "{{ trans('backpack::crud.export.csv') }}",
                        "pdf":    "{{ trans('backpack::crud.export.pdf') }}",
                        "print":  "{{ trans('backpack::crud.export.print') }}",
                        "colvis": "{{ trans('backpack::crud.export.column_visibility') }}"
                    },
                },
                dom:
                    "<'row hidden'<'col-sm-6 hidden-xs'i><'col-sm-6 hidden-print'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row m-t-10'<'col-sm-6 col-md-4'l><'col-sm-2 col-md-4 text-center'B><'col-sm-6 col-md-4 hidden-print'p>>",
            }
        }
    </script>

    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.bootstrap.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js" type="text/javascript"></script>
    <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js" type="text/javascript"></script>
    <script>
        crud.dataTableConfiguration.buttons = [
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> {{ trans('backpack::crud.export.export') }}',
                buttons: [
                    {
                        name: 'copyHtml5',
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [':visible:not(.not-export-col):not(.hidden):not([data-visible-in-export=false])'],
                        },
                        action: function(e, dt, button, config) {
                            crud.responsiveToggle(dt);
                            $.fn.DataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                            crud.responsiveToggle(dt);
                        }
                    },
                    {
                        name: 'excelHtml5',
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [':visible:not(.not-export-col):not(.hidden):not([data-visible-in-export=false])'],
                        },
                        action: function(e, dt, button, config) {
                            crud.responsiveToggle(dt);
                            $.fn.DataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                            crud.responsiveToggle(dt);
                        }
                    },
                    {
                        name: 'csvHtml5',
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: [':visible:not(.not-export-col):not(.hidden):not([data-visible-in-export=false])'],
                        },
                        action: function(e, dt, button, config) {
                            crud.responsiveToggle(dt);
                            $.fn.DataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                            crud.responsiveToggle(dt);
                        }
                    },
                    {
                        name: 'pdfHtml5',
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: [':visible:not(.not-export-col):not(.hidden):not([data-visible-in-export=false])'],
                        },
                        orientation: 'landscape',
                        action: function(e, dt, button, config) {
                            crud.responsiveToggle(dt);
                            $.fn.DataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                            crud.responsiveToggle(dt);
                        }
                    },
                    {
                        name: 'print',
                        extend: 'print',
                        exportOptions: {
                            columns: [':visible:not(.not-export-col):not(.hidden):not([data-visible-in-export=false])'],
                        },
                        action: function(e, dt, button, config) {
                            crud.responsiveToggle(dt);
                            $.fn.DataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                            crud.responsiveToggle(dt);
                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-eye-slash"></i> {{ trans('backpack::crud.export.column_visibility') }}',
                columns: ':not(.not-export-col):not([data-visible-in-export=false])'
            }
        ];

        // move the datatable buttons in the top-right corner and make them smaller
        function moveExportButtonsToTopRight() {
            crud.table.buttons().each(function(button) {
                if (button.node.className.indexOf('buttons-columnVisibility') == -1 && button.node.nodeName=='BUTTON')
                {
                    button.node.className = button.node.className + " btn-sm";
                }
            })

            $(".dt-buttons").appendTo($('#datatable_button_stack' ));
            $('.dt-buttons').css('display', 'inline-block');
        }

        crud.addFunctionToDataTablesDrawEventQueue('moveExportButtonsToTopRight');
    </script>

    <script type="text/javascript">
        jQuery(document).ready(function($) {

            crud.table = $("table").DataTable(crud.dataTableConfiguration);

            // on DataTable draw event run all functions in the queue
            // (eg. delete and details_row buttons add functions to this queue)
            $('table').on( 'draw.dt',   function () {
                crud.functionsToRunOnDataTablesDrawEvent.forEach(function(functionName) {
                    crud.executeFunctionByName(functionName);
                });
            } ).dataTable();

            // when datatables-colvis (column visibility) is toggled
            // rebuild the datatable using the datatable-responsive plugin
            $('table').on( 'column-visibility.dt',   function (event) {
                crud.table.responsive.rebuild();
            } ).dataTable();

            @if (false)
            // when columns are hidden by reponsive plugin,
            // the table should have the has-hidden-columns class
            crud.table.on( 'responsive-resize', function ( e, datatable, columns ) {
                if (crud.table.responsive.hasHidden()) {
                    $("table").removeClass('has-hidden-columns').addClass('has-hidden-columns');
                } else {
                    $("table").removeClass('has-hidden-columns');
                }
            } );
            @else
            // make sure the column headings have the same width as the actual columns
            // after the user manually resizes the window
            var resizeTimer;
            function resizeCrudTableColumnWidths() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    // Run code here, resizing has "stopped"
                    crud.table.columns.adjust();
                }, 250);
            }
            $(window).on('resize', function(e) {
                resizeCrudTableColumnWidths();
            });
            $(document).on('expanded.pushMenu', function(e) {
                resizeCrudTableColumnWidths();
            });
            $(document).on('collapsed.pushMenu', function(e) {
                resizeCrudTableColumnWidths();
            });
            $('h4.box-title').on('click', function (e) {
                resizeCrudTableColumnWidths();
            });
            @endif

        });
    </script>


@endsection


