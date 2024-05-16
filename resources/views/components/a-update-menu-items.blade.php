<div class="mt-1">
    <div class="text-right mb-2">
        @foreach ($model->updateMenuItems($action, $model) as $menu)
        @php
        $value = true;
        if (isset($menu['visible'])) {
        $value = $menu['visible'] ? true : false;
        }

        @endphp
        @if(isset($menu) && $value)
        <a class="{{isset($menu['color']) ? $menu['color'] : 'btn btn-warning'}} " href="{{isset($menu['url']) ? $menu['url'] : ''}}">
            <i class="{{isset($menu['label']) ? $menu['label'] : ''}}" data-toggle="tooltip" title="{{ isset($menu['title']) ? $menu['title'] : ''}}"></i>
             @if(isset($menu['text']) && $menu['text'] )  <span style="color: @if(isset($menu['textColor']) ){{$menu['textColor'] }}  @endif ;">{{$menu['title']}}</span> @endif
        </a>
        @endif
        @endforeach
    </div>
</div>