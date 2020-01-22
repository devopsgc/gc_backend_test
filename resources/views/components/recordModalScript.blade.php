<script>
$(document).ready(function() {
    $(document).on('click', 'a.openRecordModal', function(e) {
        e.preventDefault();
        $('#recordModal .modal-title').html('');
        $('#recordModal .modal-body').html('<h3 class="text-center"><img src="{{ url('img/loading.gif') }}" /> Loading...</h3>');
        $('#recordModal').modal('show');
        history.pushState({}, '', $(this).attr('href'));
        axios.get($(this).attr('href')).then(function(response) {
            $('#recordModal form').attr('action', $(response.data).find('#recordForm').attr('action'));
            $('#recordModal .modal-title').html($(response.data).find('.ajaxModalHeader h5').html());
            $('#recordModal .modal-body').html($(response.data).find('.ajaxModalBody').html());
            $('.ajaxModalScript').html($(response.data).find('script')[0]);
        });
    });
    $('#recordModal').on('hide.bs.modal', function () {
        history.pushState({}, '', '{{ url()->current() }}');
    });
    $('.modal').on('hidden.bs.modal', function(e) {
        if ($('.modal:visible').length) {
            $('body').addClass('modal-open');
        }
    });
    $(document).on('submit', '#recordModal form', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);
        $('#recordModal .modal-body').html('<h3 class="text-center"><img src="{{ url('img/loading.gif') }}" /> Saving...</h3>');
        $.ajax({
            url: form.attr('action'),
            data: formData,
            type: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-PJAX', true);
            },
            enctype: 'multipart/form-data',
            cache: false,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#recordModal .modal-title').html($(response).find('.ajaxModalHeader h4').html());
                $('#recordModal .modal-body').html($(response).find('.ajaxModalBody').html());
                $('.ajaxModalScript').html($(response).find('script')[0]);
                $('#recordModal :submit').trigger('reloadRecord');
            }
        });
    });
});
</script>
<div class="ajaxModalScript"></div>