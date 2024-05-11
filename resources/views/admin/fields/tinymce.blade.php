@component($typeForm, get_defined_vars())
    <div data-controller="tinymce" id="{{ $id }}" class="mce-container" data-language="{{$language ?? 'en'}}" style="min-height: {{ $attributes['height'] }}" >
        <input name="{{ $attributes["name"] }}" value="{!! $value !!}" @if($attributes["required"] === true) required @endif class="tinymce" id="tinymce-wrapper-{{$id}}" style="min-height: {{ $attributes['height'] }}" data-repeater-name-key="{{ $attributes['data-repeater-name-key'] }}"/>
    </div>
@endcomponent
