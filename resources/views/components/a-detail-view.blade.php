@if($icon)
<x-a-update-menu-items :model="$model" :action="'view'" />
@endif

@if($type=="single")
<div class="table-responsive">

    <table id="department-detail-view" class="table table-striped table-bordered detail-view">
        <tbody>
            @foreach ($column as $row)
            @php
            $isVisible = true;
            if (is_array($row) && isset($row['visible'])) {
            $isVisible = (bool) $row['visible'];
            }
            @endphp
            @if($isVisible)
            <tr>
                <th>{{ is_array($row) ? ucfirst($row['label'] ?? ucwords(str_replace('_', ' ', $row['attribute'])) ?? 'N/A') : ucwords(str_replace('_', ' ', $row)) }}</th>
                <td colspan="1">
                    @if (is_array($row))
                    {{ $row['value'] ?? $model->{$row['attribute']} ?? 'N/A' }}
                    @else
                    {{ $model->$row ?? 'N/A' }}
                    @endif
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="table-responsive">

    <table id="department-detail-view" class="table table-striped table-bordered detail-view">
        <tbody>
            @for ($i = 0; $i < count($column); $i+=2) <tr>
                <th>{{ is_array($column[$i]) ? ucfirst($column[$i]['label'] ?? ucwords(str_replace('_', ' ', $column[$i]['attribute'])) ?? 'N/A') : ucwords(str_replace('_', ' ', $column[$i])) }}</th>
                <td>{{ is_array($column[$i]) ? $column[$i]['value'] ?? $model->{$column[$i]['attribute']} ?? 'N/A' : $model->{$column[$i]} ?? 'N/A' }}</td>
                @if ($i + 1 < count($column)) <th>{{ is_array($column[$i + 1]) ? ucfirst($column[$i + 1]['label'] ?? ucwords(str_replace('_', ' ', $column[$i + 1]['attribute'])) ?? 'N/A') : ucwords(str_replace('_', ' ', $column[$i + 1])) }}</th>
                    <td>{{ is_array($column[$i + 1]) ? $column[$i + 1]['value'] ?? $model->{$column[$i + 1]['attribute']} ?? 'N/A' : $model->{$column[$i + 1]} ?? 'N/A' }}</td>
                    @endif
                    </tr>
                    @endfor
        </tbody>
    </table>
</div>

@endif