@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped import-table">
        <thead>
        <tr>
            <th style="width: 130px"></th>
            <th style="width: 100px">Dòng số</th>
            <th style="width: 250px">{{ trans('models.document.attributes.order_code') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.order_no') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.is_collected_documents') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.status_collected_documents') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.time_collected_documents') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.date_collected_documents') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.time_collected_documents_reality') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.date_collected_documents_reality') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.num_of_document_page') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.document_type') }}</th>
            <th style="width: 250px">{{ trans('models.document.attributes.document_note') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                <td>{!! $entity['importable'] ? empty($entity['warning']) ? '<p class="text-success">'.trans('messages.valid').'</p>'
                    : '<p class="text-warning">'.implode('<br>',$entity['warning']).'</p>'
                    : '<p class="text-danger">'.implode('<br>',$entity['failures']).'</p>' !!}</td>
                <td class="text-center">{{$entity['row']}}</td>
                <td class="text-center">{{$entity['order_code']}}</td>
                <td class="text-center">{{$entity['order_no']}}</td>
                <td class="text-center">{{$entity['is_collected_documents_text']}}</td>
                <td class="text-center">{{$entity['status_collected_documents_text']}}</td>
                <td class="text-center">{{$entity['time_collected_documents']}}</td>
                <td class="text-center">{{$entity['date_collected_documents']}}</td>
                <td class="text-center">{{$entity['time_collected_documents_reality']}}</td>
                <td class="text-center">{{$entity['date_collected_documents_reality']}}</td>
                <td class="text-center">{{$entity['num_of_document_page']}}</td>
                <td class="text-center">{{$entity['document_type']}}</td>
                <td class="text-center">{{$entity['document_note']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>