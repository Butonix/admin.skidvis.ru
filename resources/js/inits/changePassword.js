import {enableLoadModule, disableLoadModule} from './loadModule';

const CHANGE_PASSWORD_CLASS = '.js-confirm-change-password';

export default class ChangePassword {
    constructor(element) {
        this.form = element;
        this.action = this.form.attr('action');
        this.hasInputMethod = this.form.find('input[name=_method');
        this.method = (this.hasInputMethod.length > 0) ? $(this.hasInputMethod[0]).val() : this.form.attr('method');

        this.initializeEvents();
    }

    ajax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:        this.action,
            type:       this.method,
            beforeSend: function () {
                enableLoadModule();
            },
            complete:   function () {
                disableLoadModule();
            },
            success:    (data) => {
                if (data.type) {
                    toastr[data.type](data.text);
                } else {
                    toastr['success'](data.text);
                }
            }
        });
    }

    initializeEvents() {
        this.form
            .off('click')
            .on('click', (event) => {
                if (!confirm('Обновить пароль?')) return;

                event.preventDefault();
                this.ajax();
            });
    }

    static init() {
        $(CHANGE_PASSWORD_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new ChangePassword(element);
        });
    }
}

