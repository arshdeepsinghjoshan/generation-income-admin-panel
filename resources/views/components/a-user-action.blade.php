<div class="card">
    <h5 class="card-header">{{ __($title .' Actions') }}</h5>
    <div class="card-body">
        <div class="mt-2 text-right">
            <form method="post" action="{{ url('/state-change') }}">
                @csrf
                <input type="hidden" name="model_type" value="{{ get_class($model) }}" />
                <input type="hidden" name="model_id" value="{{ $model->id }}" />
                <input type="hidden" name="attribute" value="{{ $attribute }}" />
                {{$column}}
                <input type="hidden" name="column" value="{{ $column }}" />
                @foreach ($states as $stateValue => $stateLabel)
                @if ($model->{$attribute} != $stateValue)
                <button type="submit" class="{{$model->getStateButtonOption($stateValue)}}" name="workflow" value="{{ $stateValue }}">{{ __($stateLabel) }}</button>
                @endif
                @endforeach
            </form>
        </div>
    </div>
</div>