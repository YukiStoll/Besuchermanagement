<script src="{{ asset('assets/js/core/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/typeahead.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap-select.js') }}"></script>

<script>
    $('form').submit(function() {
        $('.saveButton').attr("disabled", "true").html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>  @lang('main.loading')...");
    })

    function escapeHtml(unsafe)
    {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    $('body').on('keydown', 'input, select', function(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
        focusable = form.find('input,select,textarea').filter(':visible');
        /*
        console.log($(this));
        console.log($(this).attr("readonly"));
        console.log(($(this).attr("readonly") === false));
        console.log(($(this).attr("readonly") === undefined));
        console.log($(this).attr("hidden"));
        console.log(($(this).attr("hidden") === false));
        console.log(($(this).attr("hidden") === undefined));
        console.log("============================================================");

        focusable = form.find('input,a,select,button,textarea').filter(function() {
                if(($(this).attr("readonly") === false || $(this).attr("readonly") === undefined) && ($(this).attr("hidden") === false || $(this).attr("hidden") === undefined))
                {
                    console.log("true");
                    return true;
                }
                else
                {
                    //console.log("false");
                    return false
                }
            });
            */
        next = focusable.eq(focusable.index(this)+1);
        if (next.length) {
            next.focus();
        } else {
            form.submit();
        }
        return false;
    }
});
</script>
