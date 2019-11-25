import {disableLoadModule, enableLoadModule} from './loadModule';
import {initialize} from '../initialize';

const CHANGE_SUBMIT_CLASS = '.onChangeSubmitAjax';

export default class ChangeSubmitAjax {
    constructor(element) {
        this.form = element;
        this.method = this.form.attr('method');
        this.action = this.form.attr('action');
        this.target = this.form.data('target');
        this.command = this.form.data('command');

        this.initializeEvents();
    }

    ajax() {
        $.ajax({
            type:       this.method,
            url:        this.action,
            data:       this.form.serialize(),
            async:      true,
            beforeSend: function () {
                enableLoadModule();
            },
            complete:   function () {
                disableLoadModule();
            },
            success:    (data) => {
                console.log(data);

                if ($('.modal:visible').length && $('body').hasClass('modal-open')) {
                    $('.modal').modal('hide');
                }

                if (data.data && this.target && this.command) {
                    switch (this.command) {
                        case 'before': {
                            $('[' + this.target + ']').before(data.data);
                        }
                            break;
                        case 'after': {
                            $('[' + this.target + ']').after(data.data);
                        }
                            break;
                        case 'replace': {
                            $('[' + this.target + ']').after(data.data).remove();
                        }
                            break;
                    }
                    // initAjax();
                }


                if (this.command && this.command === 'remove') {
                    $('[' + this.target + ']').remove();
                }

                if (data.message
                    && data.message.type
                    && data.message.text) {
                    toastr[data.message.type](data.message.text);
                }

                if (data.replace) {
                    $(data.replace.selector).html(data.replace.html);
                    initialize();
                    // unInit();
                    // init();
                }

                if (data.console) {
                    console.log(data.console);
                }

                if (this.method === 'GET') {
                    history.pushState(this.form.serialize(), document.title, window.location.pathname + '?' + this.form.serialize());
                    // initAjax();
                }

            },
            error:      function (data) {
                console.log('Ajax error:', data);
                toastr['error']('Произошла ошибка. Обновите страницу или попробуйте позже.');
            }
        });
    }

    initializeEvents() {
        this.form
            .off('change')
            .on('change', (event) => {
                event.preventDefault();
                this.ajax();
            });
    }

    static init() {
        $(CHANGE_SUBMIT_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new ChangeSubmitAjax(element);
        });
    }
}

