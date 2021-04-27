<?php
$modal = isset($modal) ? $modal : 'goods_modal';
$table = isset($table) ? $table : 'table_goods';
$button = isset($button) ? $button : 'btn-goods';
?>
<div id="{{ $modal }}" class="modal fade" role="dialog" aria-labelledby="myModalLabel" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Chọn hàng hóa</h4>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    @if (isset($goodsOwners))
                        <div class="col-md-4 hide">
                            <label>Chủ hàng</label>
                            {!! MyForm::dropDown('goods_owner_id', null, $goodsOwners, true, ['class' =>
                            'form-group-right minimal', 'id' => 'goods_owner_id', 'disabled' => true]) !!}
                        </div>
                    @endif
                    @if (isset($goodsGroups))
                        <div class="col-md-4">
                            <label>Nhóm hàng hoá</label>
                            {!! MyForm::dropDown('goods_group_id', null, $goodsGroups, false, ['class' =>
                            'form-group-right minimal', 'id' => 'goods_group_id']) !!}
                        </div>
                    @endif
                </div>

                <table class="table" style="width:100%" id="{{ $table }}">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="width: 150px">{{ trans('models.goods_type.attributes.code') }}</th>
                        <th style="width: 250px">{{ trans('models.goods_type.attributes.title') }}</th>
                        <th style="width: 250px">{{ trans('models.goods_type.attributes.name_of_goods_group_id') }}
                        </th>
                        <th class="text-right" style="width: 150px">
                            {{ trans('models.goods_type.attributes.volume') }}</th>
                        <th class="text-right" style="width: 150px">
                            {{ trans('models.goods_type.attributes.weight') }}</th>
                        <th class="text-right" style="width: 150px">
                            {{ trans('models.goods_type.attributes.goods_unit_id') }}</th>
                        <th>{{ trans('models.goods_type.attributes.note') }}</th>
                    </tr>
                    </thead>
                </table>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <span class="padr20">
                        <button class="btn btn-default" data-dismiss="modal">
                            <span class="ls-icon ls-icon-reply" aria-hidden="true"></span> <i
                                    class="fa fa-backward"></i>{{ trans('actions.back') }}
                        </button>
                    </span>
                    <span>
                        <button class="btn btn-blue" id="{{ $button }}">
                            <span class="ls-icon ls-icon-check" aria-hidden="true"></span><i class="fa fa-save"></i>
                            {{ trans('actions.submit') }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
