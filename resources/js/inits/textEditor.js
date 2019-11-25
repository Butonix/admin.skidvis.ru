import EditorJS from '@editorjs/editorjs';
import {disableLoadModule, enableLoadModule} from './loadModule';
import UnderlineTool from './editorTools/underlineTool';
import StrikeTool from './editorTools/strikeTool';
import SmallTool from './editorTools/smallTool';

const TEXT_EDITOR_SELECTOR = 'text-editor'; //ID of element
const TEXT_IMAGES_SAVE_URL = 'https://admin.skidvis.ru/api/articles/text-image';

const List = require('@editorjs/list');
const Quote = require('@editorjs/quote');
const Header = require('@editorjs/header');
const ImageTool = require('@editorjs/image');
const Paragraph = require('@editorjs/paragraph');

export default class TextEditor {
    constructor(element) {
        this.element = element;
        this.editor = null;
        this.readonly = this.element.data('readonly');
        this.htmlContainer = this.element.parent().find('textarea[name=text]');
        this.oldEditor = this.htmlContainer.data('editor');
        this.form = this.element.closest('form.onSubmitAjaxQuill');
        this.article = {
            name:             this.form.find('input[name=name]'),
            shortDescription: this.form.find('textarea[name=short_description]'),
            author:           this.form.find('textarea[name=author]'),
            isActual:         this.form.find('input[name=is_actual]'),
            label:            this.form.find('select[name=article_label_id]'),
            organization:     this.form.find('select[name=organization_id]'),
            categories:       this.form.find('select[name^=categories]')
        };
        this.articleValues = {
            editor:     null,
            textImages: {}
        };

        this.initialize();
        this.initializeEvents();
    }

    initialize() {
        // Если выставлен параметр readonly и он true, то добавляем в редактор элемент, который перекрывает
        // редактор и не позволяет ничего редактировать
        if (this.readonly) {
            this.element.append(`<div class='text-editor--readonly'></div>`);
        }

        this.editor = new EditorJS({
            holder:      TEXT_EDITOR_SELECTOR,
            placeholder: 'Текст статьи',
            tools:       {
                underline: UnderlineTool,
                strike:    StrikeTool,
                small:     SmallTool,
                header:    {
                    class:         Header,
                    inlineToolbar: true
                },
                list:      {
                    class:         List,
                    inlineToolbar: true
                },
                quote:     {
                    class:         Quote,
                    inlineToolbar: true,
                    config:        {
                        quotePlaceholder:   'Введите цитату',
                        captionPlaceholder: 'Автор цитаты'
                    }
                },
                paragraph: {
                    class:         Paragraph,
                    inlineToolbar: true
                },
                image:     {
                    class:         ImageTool,
                    inlineToolbar: true,
                    config:        {
                        endpoints:          {
                            byFile: TEXT_IMAGES_SAVE_URL,
                            byUrl:  TEXT_IMAGES_SAVE_URL
                        },
                        field:              'image',
                        buttonContent:      'Выберите изображение',
                        captionPlaceholder: 'Подпись изображения',
                        uploader:           {
                            uploadByFile(file) {
                                return TextEditor.uploadImages(file).then(response => {
                                    let data = response.data;

                                    //Вывод уведомления о загрузке
                                    if (data.alert
                                        && data.alert.type
                                        && data.alert.text) {
                                        toastr[data.alert.type](data.alert.text);
                                    }

                                    return data;
                                })
                                    .catch(error => {
                                        console.log('При загрузке изображения произошла ошибка');
                                        console.log(error);
                                        toastr['error']('При загрузке изображения произошла ошибка');
                                    });
                            }
                        }
                    }
                }
            },
            // Данные, которые приходят с база данных для редактирование. Если их нет, отдаем пустой объект
            data: (typeof this.oldEditor !== 'undefined' && this.oldEditor !== null) ? this.oldEditor : {}
        });
    }

    /**
     * Функция для преобразования файла с изображением и отправки его на сервер для сохранения
     *
     * @param file
     * @returns {Promise<any>}
     */
    static uploadImages(file) {
        return new Promise((resolve, reject) => {
            let reader = new FileReader(),
                formData = new FormData(),
                base64data;

            //Конвертирование выбранного изображения в base64
            //и отправка данных на сервер для получения ссылки на изображение
            reader.onload = () => {
                base64data = reader.result;
                formData.append('image', base64data);
                return resolve(axios.post(TEXT_IMAGES_SAVE_URL, formData));
            };

            reader.readAsDataURL(file);
        });
    }

    initializeEvents() {
        this.form
            .off('submit')
            .on('submit', (event) => {
                event.preventDefault();
                event.stopPropagation();

                let canSubmit,
                    requiredFields = {
                        name:         false,
                        descr_author: false,
                        categories:   false
                    };

                //Валидация названия статьи
                if (this.article.name.val().length === 0) {
                    if (!this.article.name.hasClass('is-invalid')) {
                        this.article.name.parent().addClass('has-danger');
                        this.article.name.addClass('is-invalid');
                        this.article.name.after(`<div class='invalid-feedback'>Укажите название для статьи</div>`);
                    }
                    requiredFields.name = false;
                } else {
                    if (this.article.name.hasClass('is-invalid')) {
                        this.article.name.parent().removeClass('has-danger');
                        this.article.name.removeClass('is-invalid');
                        this.article.name.parent().find('.invalid-feedback').remove();
                    }
                    requiredFields.name = true;
                }

                //Валидация кратного описания и авторства. Если нет краткого описания, то должно быть авторство или наоборот.
                if (this.article.shortDescription.val().length === 0 && this.article.author.val().length === 0) {
                    if (!this.article.shortDescription.hasClass('is-invalid')) {
                        this.article.shortDescription.parent().addClass('has-danger');
                        this.article.shortDescription.addClass('is-invalid');
                        this.article.shortDescription.after(`<div class='invalid-feedback'>Без указания автора необходимо указать краткое описание</div>`);
                    }

                    if (!this.article.author.hasClass('is-invalid')) {
                        this.article.author.parent().addClass('has-danger');
                        this.article.author.addClass('is-invalid');
                        this.article.author.after(`<div class='invalid-feedback'>Без указания краткого описания необходимо указать автора</div>`);
                    }
                    requiredFields.descr_author = false;
                } else {
                    if (this.article.shortDescription.hasClass('is-invalid')) {
                        this.article.shortDescription.parent().removeClass('has-danger');
                        this.article.shortDescription.removeClass('is-invalid');
                        this.article.shortDescription.parent().find('.invalid-feedback').remove();
                    }

                    if (this.article.author.hasClass('is-invalid')) {
                        this.article.author.parent().removeClass('has-danger');
                        this.article.author.removeClass('is-invalid');
                        this.article.author.parent().find('.invalid-feedback').remove();
                    }
                    requiredFields.descr_author = true;
                }

                //Валидация кол-ва выбранных категорий (не более 3)
                if (this.article.categories.val().length > 3) {
                    if (!this.article.categories.hasClass('is-invalid')) {
                        this.article.categories.parent().addClass('has-danger');
                        this.article.categories.addClass('is-invalid');
                        $('.dashboardcode-bsmultiselect ul.form-control').css({
                            'border-color': 'red'
                        });
                        this.article.categories.parent().append(`<div class='invalid-feedback'>Необходимо указать не более 3 категорий для статьи</div>`);
                    }
                    requiredFields.categories = false;
                } else {
                    if (this.article.categories.hasClass('is-invalid')) {
                        this.article.categories.parent().removeClass('has-danger');
                        this.article.categories.removeClass('is-invalid');
                        this.article.categories.parent().find('.invalid-feedback').remove();
                        $('.dashboardcode-bsmultiselect ul.form-control').css({
                            'border-color': '#ced4da'
                        });
                    }
                    requiredFields.categories = true;
                }

                //Проверка все ли условия валидации выполнены. Если что-то не выполнено,
                //запрос на сохранение не будет отправлен
                for (let key in requiredFields) {
                    if (false === requiredFields[key]) {
                        canSubmit = false;
                        break;
                    }

                    canSubmit = true;
                }

                if (canSubmit) {
                    //Получение input'a главного изображения с id изображения
                    this.article.cover = $('input[name^=images]');

                    //Получение данных из редактора в виде JSON и дальнейшая обработка данных, после которой
                    //происходит отправка данных на сервер
                    this.editor.save().then((articleText) => {
                        console.log(articleText);
                        this.articleValues.editor = articleText;
                        //Поиск input'ов всех изображений из текста и сохранение их id
                        //для дальнейшей связки в БД
                        this.articleValues.textImages = TextEditor.executeImages(articleText);
                        this.articleValues.text = $('.codex-editor__redactor').html();

                        //Получение значений из всех полей формы
                        for (let key in this.article) {
                            if (typeof key === 'undefined') {
                                continue;
                            }

                            let inputName = this.article[key].attr('name');

                            if (typeof  inputName === 'undefined') {
                                continue;
                            }

                            if (inputName === 'categories[]') {
                                inputName = 'categories';
                            }

                            if (inputName === 'textImages[]') {
                                inputName = 'textImages';
                            }

                            if (inputName.indexOf('images[') !== -1) {
                                inputName = 'images';
                            }

                            if (inputName === 'is_actual') {
                                this.articleValues[inputName] = this.article[key].prop('checked');
                                continue;
                            }

                            this.articleValues[inputName] = this.article[key].val();
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type:        this.form.data('method'),
                            url:         this.form.data('url'),
                            data:        JSON.stringify(this.articleValues),
                            contentType: 'application/json',
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

                                if (data
                                    && data.type
                                    && data.text) {
                                    toastr[data.type](data.text);
                                }
                            },
                            error:       function (data) {
                                console.log('Ajax error:', data);
                                toastr['error']('Произошла ошибка. Обновите страницу или попробуйте позже.');
                            }
                        });
                    });
                }
            });
    }

    /**
     * Функция для получения ID всех изображений, что добавлены в редактор
     *
     * @param data
     */
    static executeImages(data) {
        let result = {};

        for (let block of data.blocks) {
            switch (block.type) {
                case 'image': {
                    let imageId = block.data.file.id;
                    result[imageId] = imageId;
                }
                    break;
                default:
                    break;
            }
        }

        return result;
    }

    static init() {
        $('#' + TEXT_EDITOR_SELECTOR).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new TextEditor(element);
        });
    }
}
