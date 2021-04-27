<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.1/css/select.dataTables.min.css">
<link rel="stylesheet"
      href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css">
@include('backend.quicksearch._contact_list')
<script>
    if (typeof contactUri === 'undefined') {
        contactUri = '{{route('quicksearch.contact')}}';
    }
</script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>
<script type="text/javascript"
        src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
<script type="text/javascript"
        src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
{{Html::script(buildVersion(public_url('js' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR  . 'autoload' . DIRECTORY_SEPARATOR . 'contact-search.js')))}}
