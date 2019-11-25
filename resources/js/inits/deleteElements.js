import {enableLoadModule, disableLoadModule} from './loadModule';
import {initialize} from '../initialize';

const DELETE_USERS_CLASS = '.js-delete-elements';

export default class DeleteElements {
    constructor(element) {
        this.deleteButton = element;
        this.action = this.deleteButton.data('form-action');
        this.method = this.deleteButton.data('action-method');
        this.nameElements = this.deleteButton.data('name-elements');

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
            data:       $('input[type=checkbox][name="' + this.nameElements + '[]"]:checked').serialize(),
            beforeSend: function () {
                enableLoadModule();
            },
            complete:   function () {
                disableLoadModule();
            },
            success:    (data) => {
                if(!data){
                    return;
                }
                if(data.text){
                    if(data.type){
                        toastr[data.type](data.text);
                    }else{
                        toastr['success'](data.text);
                    }
                }
                if(data.replace){
                    $(data.replace.selector).html(data.replace.html);
                    initialize();
                }
            }
        });
    }

    initializeEvents() {
        this.deleteButton
            .off('click')
            .on('click', (event) => {
                if (!confirm('Удалить?')) return;

                event.preventDefault();
                this.ajax();
            });
    }

    static init() {
        $(DELETE_USERS_CLASS).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new DeleteElements(element);
        });
    }
}

