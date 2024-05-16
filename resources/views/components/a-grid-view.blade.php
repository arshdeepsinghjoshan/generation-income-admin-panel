<link rel="stylesheet" href="{{ asset('assets/css/datatables.min.css') }}">
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" /> -->
<table id="{{ $id }}" class="table">
    <thead>
        <tr>
            @foreach($columns as $column)
            <th>
                @if(method_exists($model, 'attributeLabels'))
                {{ $model->attributeLabels($column) }}
                @else
                @if(is_array($column))
                @if(isset($column['label']))
                {{$column['label']}}
                @else
                {{ ucwords(str_replace('_', ' ', $column['attribute'])) }}
                @endif
                @else
                {{ ucwords(str_replace('_', ' ', $column)) }}
                @endif

                @endif
            </th>
            @endforeach
        </tr>

    </thead>
    <tbody>
    </tbody>
</table>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="{{ asset('assets/js/datatables.min.js') }}"></script>

<script>
    (function($) {
        'use strict';
        $(document).ready(function() {

            var d{{ $id }}  = $('#{{ $id }}').DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    [10, 25, 50, 100, 500]
                ],
                processing: true,
                serverSide: true,
                autoWidth: false,
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                language: {
                    processing: ' <div class="spinner-border" style="width: 50px; height: 50px;" role="status"><span class="visually-hidden">Loading...</span></div>'
                },

                ajax: {
                    url: "/{{ $url }}",
                    type: "GET",
                    data: function (d) {
                    @foreach($customfilterIds as $customfilterId) 

                    d.{{$customfilterId}} = $('#{{$customfilterId}}').val();
                    @endforeach

                }
                },

                columns: [
                    @foreach($columns as $column) {

                        @if(is_array($column))
                        data: '{{ $column['attribute'] ?? "" }}',
                        name: '{{ $column['attribute'] ?? "" }}'
                        @else
                        data: '{{ $column }}',
                        name: '{{ $column }}'
                        @endif
                    },
                    @endforeach
                ],

                buttons: [
                    @foreach($buttons as $button)
                    @if(is_array($button)) {
                        @foreach($button as $key => $value)
                        '{{ $key }}': '{{ $value }}',
                        @endforeach
                    },
                    @else '{{ $button }}',
                    @endif
                    @endforeach
                ]

            });
            $('#{{$filterButtonId}}').on('click', function () {
                d{{ $id }}.ajax.reload();
        });
        });


    })(jQuery);
</script>