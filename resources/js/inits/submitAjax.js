import {enableLoadModule, disableLoadModule} from './loadModule';
import {initialize} from '../initialize';

const SUBMIT_AJAX_CLASS = '.onSubmitAjax';

export default class SubmitAjax {
    constructor(element) {
        this.form = element;
        this.method = this.form.attr('method');
        this.action = this.form.attr('action');
        this.target = this.form.data('target');
        this.command = this.form.data('command');
        this.enctype = this.form.attr('enctype');

        this.initializeEvents();
    }

    ajax() {
        let data = this.form.serialize(),
            inputs = this.form.find('input[name]'),
            data2 = new FormData();

        if (this.enctype && this.enctype === 'multipart/form-data') {
            _.forEach(inputs, function (element, index) {
                let el = $(element);

                if (el.attr('type') === 'file') {
                    let files = el.prop('files');
                    data2.append(el.attr('name'), files[0], files[0].name);
                } else {
                    data2.append(el.attr('name'), el.val());
                }
            });
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:        this.method,
            url:         this.action,
            data:        (this.enctype && this.enctype === 'multipart/form-data') ? data2 : data,
            processData: (!(this.enctype && this.enctype === 'multipart/form-data')),
            contentType: (this.enctype && this.enctype === 'multipart/form-data') ? false : 'application/x-www-form-urlencoded',
            async:       true,
            beforeSend:  function () {
                enableLoadModule();
            },
            complete:    function () {
                disableLoadModule();
            },
            success:     (data) => {
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
            .off('submit')
            .on('submit', (event) => {
                event.preventDefault();
                this.ajax();
            });
    }

    static init() {
        $(SUBMIT_AJAX_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new SubmitAjax(element);
        });
    }
}

