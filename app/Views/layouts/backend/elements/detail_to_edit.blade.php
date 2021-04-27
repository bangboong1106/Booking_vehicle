<?php
$controlType = isset($controlType) ? $controlType : 'text';
$isEditable = isset($isEditable) ? $isEditable : 'true';
//$isEditable = false;
$isHiddenLabel = isset($isHiddenLabel) ? $isHiddenLabel : false;

if ($isHiddenLabel == true) {
    $label = '';
} else {
    $label = isset($label) ? $label : $entity->tA($property);
}
$is_unlock = !isset($entity->is_lock) || (isset($entity->is_lock) && $entity->is_lock == 0);
?>
<div class="{!! isset($widthWrap) ? $widthWrap  : 'col-md-4' !!} edit-group-control">
    {!! $isHiddenLabel ? '' : MyForm::label($property, $label, [], false).'<br/>'  !!}
    @if(isset($value))
        @switch($controlType)
            @case('image')
            @if($value != '' )
                <img src="{{ $value }}" class="img-fluid">
            @else
                {!! MyForm::text('', $value, ['class'=>'view-control disabled']) !!}
            @endif
            @break
            @case('label')
            {!! MyForm::label($property, $value === '' ? '-' : $value, ['id'=> $property, 'class'=>'view-control disabled', 'style'=>'word-break: break-word;'], false) !!}
            @break
            @case('number')
            {!! MyForm::text($property, $value, [
                'class'=> (isset($append) ? 'append ' : '').'number-input view-control disabled'
            ]) !!}
            @break
            @default
            {!! MyForm::text($property, $value, ['class'=>'view-control disabled']) !!}
        @endswitch
    @else
        {{--        {!! $controlType !!}--}}
        {{--        {!! $entity[$property] !!}--}}
        @switch($controlType)
            @case('date')
            {!! MyForm::text($property,
            empty($entity[$property]) ? '-' : $entity->getDateTime($property,'d-m-Y'),
            ['class'=>'datepicker date-input view-control disabled', 'autocomplete'=>'off']) !!}
            @break
            @case('datetime')
            {!! MyForm::text($property,
            empty($entity[$property]) ? '-' : $entity->getDateTime($property,'d-m-Y H:i'),
            ['class'=>'datepicker date-input view-control disabled', 'autocomplete'=>'off']) !!}
            @break
            @case('number')
            {!! MyForm::text($property, empty($entity[$property]) ? '0' : numberFormat($entity[$property]), [
                'class'=> (isset($append) ? 'append ' : '').'number-input view-control disabled'
            ]) !!}
            @break
            @case('bool')
            <span class="view-control" id="{{$property}}">
                @if($entity[$property] == 1)
                    <i class="fa fa-check" aria-hidden="true"></i>
                @else
                    <i class="fa"></i>
                @endif
            </span>
            @break
            @case('textarea')
            {!! MyForm::textarea($property, empty($entity[$property]) ? '-' : $entity[$property], ['class'=>'view-control disabled', 'rows' => '3']) !!}
            @break
            @case('link')
                @if(isset($entity['name_of_'.$property]))
                    <a class="{{ $model }}-detail view-control" href="#"
                            {{-- data-show-url="{{ isset($showAdvance) ? route('{{ $model }}.show', $entity[$property] ) : '' }}" --}}
                            data-id="{{ $entity[$property] }}"
                            >
                            {{ $entity['name_of_'.$property] }}
                    </a>
                @else
                    <span class="view-control" href="#">-</span>
                @endif
            @break
            @default
            {!! MyForm::text($property, empty($entity[$property]) ? '-' :$entity[$property], ['class'=>'view-control disabled']) !!}
            @break
        @endswitch
    @endif

    @if(isset($append))
        <span class="append-currency">{{$append}}</span>
    @endif

    @if(isset($showAdvance) && $isEditable == true && $is_unlock)
        <span class="edit edit-control"><i class="fa fa-pencil"></i></span>
        <span class="accept edit-control hidden"><i class="fa fa-check"></i></span>
        <span class="cancel edit-control hidden"><i class="fa fa-close"></i></span>
    @endif
</div>
