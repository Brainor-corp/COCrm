<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#passwordModal">
    Сменить пароль
</button>

<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="changePasswordForm" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Смена пароля</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="{{$user_id}}">

                    <label for="password">Новый пароль <span class="text-danger">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" required>

                    <label for="repeat_password">Повторите новый пароль <span class="text-danger">*</span></label>
                    <input type="password" id="repeat_password" name="repeat_password" class="form-control" required>

                    <span id="password_response"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Изменить пароль</button>
                </div>
            </form>
        </div>
    </div>
</div>