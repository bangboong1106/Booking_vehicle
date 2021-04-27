<style>
    .wrapper {
        position: relative;
        overflow: auto;
        border: 1px solid white;
        white-space: nowrap;
    }

    .sticky-col {
        position: -webkit-sticky;
        position: sticky;
        background-color: white;
    }

    .first-col {
        width: 200px;
        min-width: 200px;
        max-width: 200px;
        left: 0px;
        padding-left: 0px;
    }

    .second-col {
        width: 200px;
        min-width: 200px;
        max-width: 200px;
        left: 200px;
    }

    .third-col {
        width: 200px;
        min-width: 200px;
        max-width: 200px;
        left: 400px;
    }

    th:nth-child(4), 
    td:nth-child(4) {
        padding-left: 15px;
        min-width: 150px!important;
    }


    th:nth-last-child(1), 
    td:nth-last-child(1) {
        padding-right: 15px;
        min-width: 150px!important;
    }

    .col-min-width {
        min-width: 135px;
    }

    .split-order1 td {
        vertical-align: top;
    }
</style>

<div class="container1 split-order1">
    <div class="row1">
        <table style="width:100%;">
            <thead>
                <th class="sticky-col first-col" style="padding-left: 15px">Đối tác vận tải</th>
                <th class="sticky-col second-col">Xe</th>
                <th class="sticky-col third-col">Tài xế</th>
                @foreach($entity->goods as $goods)
                    <th scope="row" class="col-min-width">{{ $goods['goods_type'] }}</th>
                @endforeach
            </thead>
            <tbody>
                @for ($i = 0; $i <= $quantities-1; $i++)
                    <tr>
                        <td class="sticky-col first-col" style="padding-left: 15px">
                            <div class="form-group">
                                <select class="select2 form-control select-partner" id="partner-id-{{$i}}" name="partner_ids[]" data-index={{$i}}>
                                </select>
                            </div>
                        </td>
                        <td class="sticky-col second-col">
                            <div class="form-group">
                                <select class="select2 select-vehicle-1" id="vehicle-id-{{$i}}" name="vehicle_ids[]" disabled data-index={{$i}}>
                                </select>
                            </div>
                        </td>
                        <td class="sticky-col third-col">
                            <div class="form-group">
                                <select class="select2 select-driver-1" id="driver-id-{{$i}}" name="driver_ids[]" disabled data-index={{$i}}>
                                </select>
                            </div>
                        </td>
                        @if (!empty($entity->goods))
                            @foreach($entity->goods as $key => $goods)
                                @php
                                    $total = $goods['quantity'];
                                    $result = $total / $quantities; // tỉ số giữa tổng số hàng / số lượng đơn muốn tách
                                    $tempArr = [];

                                    // Nếu tổng số hàng >= số lượng đơn muốn tách
                                    if ($result >= 1 ) {
                                        if (is_int($result)) {
                                            // TH1: chia hết
                                            for ($y = $total; 0 < $y && $y <= $total; $y -= $result) {
                                                $tempArr[] = $result;
                                            }
                                        } else {
                                            // TH2: chia có dư
                                            // VD: 19/ 3 
                                            // 19/ 2 = 9 + 9
                                            // 19 - (9 + 9) = 1
                                            // => 9 + 9 +1
                                            for ($y = $quantities - 1; 0 < $y && $y <= $quantities -1; $y--) {
                                                $tempArr[] = floor($total /  ($quantities - 1));
                                            }

                                            if (array_sum($tempArr) < $total) {
                                                $tempArr[] = $total - array_sum($tempArr);
                                            }
                                        }
                                    } else {
                                        // TH3: Tổng số hàng < số lượng đơn muốn tách ( < 1 )
                                        // Các số sẽ làm tròn thành 1
                                        for ($y = $total; 0 < $y && $y <= $total; $y--) {
                                            $tempArr[] = ceil($result);
                                        }
                                    }
                                @endphp

                                <td class="col-min-width">
                                    <div class="form-group" style="width:100%">
                                        <input
                                            type="text"
                                            class="number-input-split-order-modal form-control order-customer-goods-{{$goods['order_goods_id']}}"
                                            name="order_split_list[{{$i}}][goods_list][{{$goods['goods_type_id']}}]"
                                            value="{{isset($tempArr[$i]) ? $tempArr[$i] : 0}}"
                                            onkeypress="return event.charCode &gt;= 48 &amp;&amp; event.charCode &lt;= 57"
                                        />
                                        @if ($i == 0)
                                            <span id="err-input-{{$goods['order_goods_id']}}" class="invalid-feedback" style="display: none"></span>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        @else
                            <input type="hidden" name="order_split_list[{{$i}}][goods_list]" value="0">
                        @endif
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>


<script>
    // $(function(){
        let goods = @json($entity->goods);
        var order_id = @json($entity->id)

        var is_valid_partner = true;
        var is_valid_vehicle = true;
        var is_valid = true;

        function toggleErrorsInput(valid, id, text) {
            $('#'+id+'-error').remove();
            let element = $('#'+id);

            if (valid == false) {
                let html = '<span id="'+ id +'-error" class="invalid-feedback" style="display: inline;">'+ text+'</span>'
                element.parent().append(html);
                element.parent().find('.select2-container').removeClass('is-valid').addClass('is-invalid');
            } else {
                element.parent().find('.select2-container').removeClass('is-invalid').addClass('is-valid');
            }
        }

        // submit
        $('#submit-split-order').unbind().click(function(){

            goods.forEach(function(element){
                let tempTotal = 0;
                let inputs = $(".split-order1 .order-customer-goods-"+element.order_goods_id+"").serializeArray();
                inputs.forEach(e => tempTotal += Math.abs(parseFloat(e.value)));

                if (tempTotal == element.quantity) {
                    $(".split-order1 .order-customer-goods-"+element.order_goods_id+"").removeClass( "is-invalid" ).addClass('is-valid');
                    $("#err-input-" + element.order_goods_id).css('display', 'none');
                    is_valid = true;
                } else {
                    $(".split-order1 .order-customer-goods-"+element.order_goods_id+"").removeClass('is-valid').addClass( "is-invalid" );
                    $("#err-input-" + element.order_goods_id).text('').text("Số lượng hiện tại không bằng tổng (" + tempTotal + "/" + element.quantity + ")").css('display','inline');

                    is_valid = false;
                }
            });

            var dataInputs = $(".split-order1 input").serializeArray();

            $.each($(".select-partner"), function(){
                let p = $(this);
                let selector = $('#'+p.attr('id'));

                if (p.val() != null) {
                    dataInputs.push({
                        name: "order_split_list[" + p.data('index') + "][partner_id]",
                        value: p.val()
                    });

                    // is_valid_partner = true;

                    // toggleErrorsInput(true, p.attr('id'));
                } else {
                    // is_valid_partner = false;
                    
                    // toggleErrorsInput(false, p.attr('id'), 'Vui lòng chọn đối tác');

                    // return false;
                }
            });

            $.each($(".select-vehicle-1"), function(){                
                let t = $(this);
                let vehicle = $('#'+t.attr('id'));
                let driver = $('#driver-id-'+ t.data('index'));

                if (((t.val() != null && driver.val() != null ) || (t.val() == null && driver.val() == null)) && t.data('index') !== undefined) {
                    if (t.val() > 0) {
                        dataInputs.push({name: "order_split_list["+t.data('index')+"][vehicle_id]", value: t.val()});
                    }

                    is_valid_vehicle = true;

                    toggleErrorsInput(true, t.attr('id'));
                    toggleErrorsInput(true, driver.attr('id'));
                } else {
                    if (t.val() == null) {
                        toggleErrorsInput(false, t.attr('id'), 'Vui lòng chọn xe');
                    } else {
                        toggleErrorsInput(false, driver.attr('id'), 'Vui lòng chọn tài xế');
                    }
                    is_valid_vehicle = false;

                    return false;
                }
            });

            if (!is_valid || !is_valid_partner || !is_valid_vehicle) {
                return;
            } else if (is_valid && is_valid_partner && is_valid_vehicle) { 
                dataInputs.push({name: "order_id", value: order_id });

                $.each($(".select-driver-1"), function(){                
                    let t = $(this);

                    if (t.val() != null && t.data('index') !== undefined) {
                        dataInputs.push({name: "order_split_list["+t.data('index')+"][driver_id]", value: t.val()})
                    }
                });

                Object.entries(dataInputs).forEach(([key, val]) => {
                    if ($.isNumeric(val.value)) {
                        val.value = Math.abs(parseFloat(val.value));
                    }
                });

                sendRequest({
                url: "{{route('order.splitOrderSave')}}",
                type: "POST",
                data: dataInputs,
                },
                    function (response) {
                        if (response.errorCode != 0) {
                            // toastr["error"](response.message);
                        } else {
                            toastr["success"]("Tách đơn hàng thành công");
                            $(".unselected-all-btn").trigger("click");
                            oneLogGrid._ajaxSearch($(".list-ajax"));

                            $('#modal-split-order').modal('hide');

                            $('#content-split-order').html('');
                        }
                    }
                );
            }
        });

    // });
</script>