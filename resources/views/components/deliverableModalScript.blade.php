<script>
$(document).ready(function() {
    $(document).on('click', 'a.openDeliverableModal', function(e) {
        e.preventDefault();
        $('#deliverableModal .modal-body').html('<h3 class="text-center"><img src="{{ url('img/loading.gif') }}" /> Loading...</h3>');
        $('#deliverableModal').modal('show');
        history.pushState({}, '', $(this).attr('href'));
        axios.get($(this).attr('href')).then(function(response) {
            $('#deliverableModal form').attr('action', $(response.data).find('#deliverableForm').attr('action'));
            $('#deliverableModal .modal-title').html($(response.data).find('.ajaxModalHeader h4').html());
            $('#deliverableModal .modal-body').html($(response.data).find('.ajaxModalBody').html());
            $('.ajaxModalScript').html($(response.data).find('script')[0]);
        });
    });
    $('#deliverableModal').on('hide.bs.modal', function () {
        history.pushState({}, '', '{{ url()->current() }}');
    });
    $(document).on('submit', '#deliverableModal form', function(e) {
        e.preventDefault();
        var form = $(this);
        console.log('formurl:');
        console.log(form.attr('action'));
        var formData = new FormData(form[0]);
        $('#deliverableModal .modal-body').html('<h3 class="text-center"><img src="{{ url('img/loading.gif') }}" /> Saving...</h3>');
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
                $('#deliverableModal .modal-title').html($(response).find('.ajaxModalHeader h4').html());
                $('#deliverableModal .modal-body').html($(response).find('.ajaxModalBody').html());
                $('.ajaxModalScript').html($(response).find('script')[0]);
                $('#deliverableModal').modal('hide');
                location.reload();
            }
        });
    });
});
</script>
<div class="ajaxModalScript"></div>