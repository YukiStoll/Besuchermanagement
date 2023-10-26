<script src="{{ asset('assets/js/core/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/typeahead.bundle.min.js') }}"></script>

<script>
    $('form').submit(function() {
        $('.saveButton').attr("disabled", "true").html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>  @lang('main.loading')...");
    })
</script>
