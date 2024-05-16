<div class="container-xxl mt-2 ">

    <nav>
        <ol class="breadcrumb">
            @foreach ($columns as $column)
                @if (is_array($column))
                    @if (isset($column['label']))
                        <li class="breadcrumb-item"><a href="{{ url($column['url']) }}">{{ ucwords(str_replace('_', ' ', $column['label'])) }}</a></li>
                    @endif
                @else
                    <li class="breadcrumb-item">{{ ucwords(str_replace('_', ' ', $column)) }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
