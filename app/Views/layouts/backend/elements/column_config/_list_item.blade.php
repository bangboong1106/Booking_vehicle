<?php
$field = array_key_exists('list_field', $attribute) ? $attribute['list_field'] : $attribute['attribute'];
$function = array_key_exists('function', $attribute) ? $attribute['function'] : '';
?>
@switch($attribute["data_type"])
    @case("string")
    @if (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky'])
        <td class="text-middle" data-name="true">
            <a class="detail-toggle" href="#">
                @if (array_key_exists('escape_html', $attribute) && $attribute['escape_html'])
                    {!! $entity->{$field} !!}
                @else
                    @if (array_key_exists('is_avatar', $attribute) && $attribute['is_avatar'])
                        <div class="person-circle chr-{{ strtoupper($entity->{$field}[0]) }}" style="cursor: pointer;">
                            {{ strtoupper($entity->{$field}[0]) }}
                        </div>{{ $entity->{$field} }}
                    @else
                        {{ empty($function) ? $entity->{$field} : $entity->{$function}() }}
                    @endif
                @endif
            </a>
        </td>
    @else
        <td class="text-middle">
            @if (array_key_exists('escape_html', $attribute) && $attribute['escape_html'])
                {!! strpos($field, '|') ? $entity->tryGet(explode('|', $field)[0])->{explode('|', $field)[1]} :
                (empty($function) ? $entity->{$field} : $entity->{$function}()) !!}
            @else
                @if (array_key_exists('is_avatar', $attribute) && $attribute['is_avatar'])
                    <div class="person-circle chr-{{ strtoupper($entity->{$field}[0]) }}" style="cursor: pointer;">
                        {{ strtoupper($entity->{$field}[0]) }}
                    </div>{{ $entity->{$field} }}
                @else
                    {{ strpos($field, '|') ? $entity->tryGet(explode('|', $field)[0])->{explode('|', $field)[1]} : (empty($function) ? $entity->{$field} : $entity->{$function}()) }}
                @endif
            @endif

        </td>
    @endif
    @break
    @case("link")
    <?php $f = strpos($field, '|') ? $entity->tryGet(explode('|', $field)[0])->{explode('|', $field)[1]} :
    $entity->{$field}; ?>
    @if (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky'])
        <td class="text-middle" data-name="true">
            <a class="view-detail-info" href="#" data-id="{{ $entity->{$attribute['link_field']} }}"
                data-show-url="{{ route($attribute['entity'] . '.show', empty($entity->{$attribute['link_field']}) ? 0 : $entity->{$attribute['link_field']}) }}">
                {!! $f !!}</a>
        </td>
    @else
        <td class="text-middle">
            <a class="view-detail-info" href="#" data-id="{{ $entity->{$attribute['link_field']} }}"
                data-show-url="{{ route($attribute['entity'] . '.show', empty($entity->{$attribute['link_field']}) ? 0 : $entity->{$attribute['link_field']}) }}">
                {!! $f !!}</a>
        </td>
    @endif
    @break
    @case("list")
    <td class="text-middle">{!! empty($function) ? $entity->{$field} : $entity->{$function}() !!}</td>
    @break
    @case("number")
    <td class="text-right">
        <?php
        $result = '';
        if (strpos($field, '/')) {
        $arr = explode('/', $field);
        $tmp = [];
        foreach ($arr as $item) {
        $tmp[] = empty($entity->{$item}) ? '0' : numberFormat($entity->{$item});
        }
        $result = implode('*', $tmp);
        } else {
        $result = empty($entity->{$field}) ? '0' : numberFormat($entity->{$field});
        }
        ?>
        {{ $result }}
    </td>
    @break
    @case("time")
    <td class="text-middle text-center">
        {{ $entity->getDateTime($field, 'H:i') }}
    </td>
    @break
    @case("date")
    <td class="text-middle text-center">
        {{ $entity->getDateTime($field, 'd-m-Y') }}
    </td>
    @break
    @case("datetime")
    <td class="text-middle text-center">
        {{ strpos($field, '|') ? $entity->getDateTime(explode('|', $field)[0], 'd-m-Y') . ' ' . $entity->getDateTime(explode('|', $field)[1], 'H:i') : $entity->getDateTime($field, 'd-m-Y H:i') }}
    </td>
    @break
    @case('bool')
    <td class="text-middle">
        @if ($entity->{$field} == 1)
            <i class="fa fa-check" aria-hidden="true"></i>
        @endif
    </td>
    @break
    @case('phone')
    <td class="text-middle">
        <a href="tel:{{ $entity->{$field} }}"><i class="fa fa-phone" aria-hidden="true"></i>{{ $entity->{$field} }}
        </a>
    </td>
    @break
    @case('email')
    <td class="text-middle">
        <a href="mailto:{{ $entity->{$field} }}"><i class="fa fa-envelope" aria-hidden="true"></i>{{ $entity->{$field} }}
        </a>
    </td>
    @break
    @case('location')
    <td class="text-middle">
        <a target="_blank"
            href="https://www.google.com/maps/search/?api=1&query={!!  empty($entity->{$field}) ? '' : $entity->{$field} !!}">
            {!! empty($entity->{$field}) ? '' : '<i class="fa fa-map-marker" aria-hidden="true"></i>' . $entity->{$field}
            !!}
        </a>
    </td>
    @break
    @case('download')
    <td class="text-center">
        <?php $file_id = strpos($field, '|') ? $entity->tryGet(explode('|', $field)[0])->{explode('|',
        $field)[1]} : $entity->{$field}; ?>
        @if (!empty($file_id))
            <a href="{{ route('file.downloadFile', $file_id) }}">
                <i class="fa fa-download"></i>
            </a>
        @endif
    </td>
    @break
    @case('image')
    <td class="text-center">
        @if(!empty($entity->file_id))
        <?php $image = route('file.getImage', [
        'id' => $entity->file_id,
        'width' => 36,
        'height' => 36,
        ]); ?>
        <img src="{{ $image }}" class="avatar">
        @endif
    </td>
    @break
    @case('concat_string')
    <td class="text-left">
        {{ implode('; ', $entity->{$attribute["relation"]}()->pluck($attribute["field"])->toArray()) }}
    </td>
    @break
    @case('ins_user')
    <td class="text-middle">
        {!! empty($entity->insUser)
        ? ''
        : '<div class="person-circle" style="cursor: pointer;line-height: unset;">
            <img src="' .
                    route('file.getImage', [
                        'id' => empty($entity->insUser->avatar_id) ? '00000000-0000-0000-0000-000000000000' : $entity->insUser->avatar_id,

                        'width' => 24,
                        'height' => 24,
                    ]) .
                    '" class="avatar">
        </div><a href="#" class="admin-detail" data-id="' .
                    $entity->ins_id .
                    '" data-show-url="' .
                    route('admin.show', $entity->ins_id) .
                    '">' .
            $entity->insUser->username .
            '</a>' !!}
    </td>
    @break
    @case('upd_user')
    <td class="text-middle">
        {!! empty($entity->updUser)
        ? ''
        : '<div class="person-circle" style="cursor: pointer;line-height: unset;">
            <img src="' .
                    route('file.getImage', [
                        'id' => empty($entity->updUser->avatar_id) ? '00000000-0000-0000-0000-000000000000' : $entity->updUser->avatar_id,

                        'width' => 24,
                        'height' => 24,
                    ]) .
                    '" class="avatar">
        </div><a href="#" class="admin-detail" data-id="' .
                    $entity->upd_id .
                    '" data-show-url="' .
                    route('admin.show', $entity->upd_id) .
                    '">' .
            $entity->updUser->username .
            '</a>' !!}
    </td>
    @break
@endswitch
