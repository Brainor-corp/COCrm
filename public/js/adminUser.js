$('#changePasswordForm').on('submit', function (e) {
    let form = $(this);
    e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'post',
        url: '/changeUserPassword',
        data: form.serialize(),
        cache: false,
        success: function(data){
            $('#password_response').text(data).removeClass().addClass('text-success');
        },
        error: function (data) {
            $('#password_response').text('Произошла ошибка, пожалуйста обновите страницу и попробуйте заново.').removeClass().addClass('text-danger');
        }
    });
});