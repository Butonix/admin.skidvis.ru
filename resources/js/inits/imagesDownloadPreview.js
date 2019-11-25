import {disableLoadModule, enableLoadModule} from './loadModuleImages';
import {initialize} from '../initialize';

const IMAGES_DOWNLOAD_PREVIEW_SELECTOR = '.js-images-preview';
const SLIDER_SELECTOR = '.carousel';
const THUMBS_PARENT_SELECTOR = '.thumb-file';
const THUMBS_SELECTOR = '.thumb-file__wrapper';
const ACTION_METHOD = 'POST';
const ACTION = $('#image-container-store').data('action');

export default class ImagesDownloadPreview {
    constructor(element) {
        //Input, в который загружаются изображения
        this.input = element;

        //Родительский блок
        this.parent = this.input.parent();

        //Input для хранение base64 для отправки на сервер (используется не везде)
        this.base64 = this.parent.find('.js-image-base64');

        //Условие проверки соотношения сторон
        this.checkAspectRatio = this.input.data('check-aspect-ratio');

        //Условие проверки формата файла
        this.checkSVG = this.input.data('check-svg');

        //Тип обрабатываемого поля (изображения).
        //Если присутствует, то позволяет произвести обработку по особым условиям (к примеру, у полей с data-image-type = icon)
        //не будут создаваться поля для хранения id изображений, потому что они уже присутствуют с собственными атрибутами name
        this.imageType = this.input.data('image-type');

        //Переменная для отправки изображение на сервер на сохранение
        this.data = new FormData();

        //Получение индекса текущего элемента, требуется для правильной отправки данных на сервер (используется в массиве images)
        this.indexOfElement = $(IMAGES_DOWNLOAD_PREVIEW_SELECTOR).index(this.input);

        this.initializeEvents();
    }

    //Если возвращается false, то изображение на слайдере было заменено, иначе было добавлено новое изображение на слайдер
    addImageToSlider(src) {
        let slider = $(SLIDER_SELECTOR),
            sliderInner = slider.find('.carousel-inner'),
            carouselIndicators = slider.find('.carousel-indicators'),
            hasSliderActiveSlide = slider.find('.carousel-item.active').length,
            slide, carouselIndicator,
            carouselItemByIndex = sliderInner.find('.carousel-item:nth-child(' + (this.indexOfElement + 1) + ')'),
            hasSliderImageByIndex = carouselItemByIndex.length;

        if (hasSliderImageByIndex) {
            carouselItemByIndex.css({
                'background-image': 'url(' + src + ')',
                'border-style':     'none'
            });

            let thumbsCount = $(THUMBS_SELECTOR).length;

            return this.indexOfElement === (thumbsCount - 1);
        } else {
            if (hasSliderActiveSlide === 0) {
                slide = `<div class='carousel-item active product__cover' style='background-image: url(${src});'></div>`;
                carouselIndicator = `<li data-target='#carouselExampleIndicators' data-slide-to='0' class='active'></li>`;
            } else {
                let lastCarouselIndicator = carouselIndicators.find('li').last(),
                    dataSlideTo = parseInt(lastCarouselIndicator.data('slide-to')) + 1;

                slide = `<div class='carousel-item product__cover' style='background-image: url(${src});'></div>`;
                carouselIndicator = `<li data-target='#carouselExampleIndicators' data-slide-to='${dataSlideTo}'></li>`;
            }

            carouselIndicators.append(carouselIndicator);
            sliderInner.append(slide);

            return true;
        }
    }

    static addNewThumb() {
        let uniqueId = Math.random().toString(36).substr(2, 9),
            thumb = `<div class='thumb-file__wrapper'>
                        <div class='thumb-file__body thumb-file__body--empty'>
                            <label for='image-input-${uniqueId}'>
                                <i class='fas fa-camera'></i>
                            </label>
                            <input accept='image' type='file' class='js-images-preview thumb-file__body__preview' id='image-input-${uniqueId}'>
                            <div class='load-module-image'></div>
                        </div>
                    </div>`;

        let thumbsParent = $(THUMBS_PARENT_SELECTOR),
            thumbs = $(THUMBS_SELECTOR),
            thumbsCount = thumbs.length;

        if (thumbsCount < 7) {
            thumbsParent.append(thumb);
        }
    }

    /**
     *
     * @param image
     * @returns {boolean}
     */
    saveImagesIds(image) {
        let newSliderImage = false;

        Object.keys(image).forEach((key) => {
            if (this.imageType && this.imageType === 'icon') {
                if (key === 'src') {
                    let src = image[key];
                    this.setBackgroundToParentBlock(src);
                }

                if (key === 'id') {
                    this.parent.find('input[name^=icon]').val(image[key]);
                }
            } else {
                if (key === 'src') {
                    let src = image[key];
                    newSliderImage = this.addImageToSlider(src);
                    this.setBackgroundToParentBlock(src);
                    return;
                }

                if (key === 'id') {
                    this.parent.append(`<input type='hidden' name='images[${this.indexOfElement}][id]' value='${image[key]}'>`);
                    return;
                }

                this.parent.append(`<input type='hidden' name='images[${this.indexOfElement}][${key}][id]' value='${image[key]['id']}'>`);
            }
        });

        return newSliderImage;
    }

    sendAjax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:        ACTION_METHOD,
            url:         ACTION,
            data:        this.data,
            contentType: false,
            processData: false,
            async:       true,
            beforeSend:  () => {
                enableLoadModule(this.parent);
            },
            complete:    () => {
                disableLoadModule(this.parent);
            },
            success:     (data) => {
                // console.log(data);
                let image = data.image,
                    newSliderImage;
                this.parent.find('input[name^=images]').remove();
                newSliderImage = this.saveImagesIds(image);

                //Если изображение было добавлено на слайдер, а не заменено старое изображение
                if (newSliderImage) {
                    ImagesDownloadPreview.addNewThumb();
                }

                initialize();
            },
            error:       function (data) {
                console.log('Ajax error:', data);
                toastr['error']('Произошла ошибка. Обновите страницу или попробуйте позже.');
            }
        });
    }

    setBackgroundToParentBlock(data) {
        this.parent.css({
            'background-image': 'url(' + data + ')',
            'border-style':     'none',
            'box-shadow':       'none'
        });
    }

    setPreview(f) {
        let reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = () => {
            let base64data = reader.result;
            this.base64.val(base64data);
            this.data.append('image', base64data);

            if (ACTION) {
                this.sendAjax();
            }

            this.setBackgroundToParentBlock(base64data);

            if (this.parent.hasClass('empty')) {
                this.parent.removeClass('empty');
            }
        };
        // Read in the image file as a data URL.
        reader.readAsDataURL(f);
    }

    handleFileSelect(event) {
        let file = event.target.files,
            f = file[0];

        if (this.checkSVG) {
            $('.category__icon--proportions').removeClass('text-danger').addClass('text-muted');
            if (f.type.indexOf('svg') === -1) {
                this.input.closest('.col-lg-12').find('.category__icon--svg').removeClass('text-muted').addClass('text-danger');
                toastr['error']('Сбой загрузки. Формат иконки должен быть SVG');
                return 0;
            } else {
                this.input.closest('.col-lg-12').find('.category__icon--svg').removeClass('text-danger').addClass('text-muted');
            }
        }

        // if (this.checkAspectRatio && this.checkAspectRatio === '1:1') {
        //     let img = new Image();
        //     img.src = window.URL.createObjectURL(f);
        //     img.onload = () => {
        //         let width = img.naturalWidth,
        //             height = img.naturalHeight;
        //
        //         window.URL.revokeObjectURL( img.src );
        //
        //         if(width/height !== 1) {
        //             $('.category__icon--proportions').removeClass('text-muted').addClass('text-danger');
        //             $(event.target).val('');
        //         } else {
        //             $('.category__icon--proportions').removeClass('text-danger').addClass('text-muted');
        //             this.setPreview(f);
        //         }
        //     };
        // } else {
        this.setPreview(f);
        // }
    }

    initializeEvents() {
        this.input
            .off('change')
            .on('change', (event) => {
                this.handleFileSelect(event);
            });
    }

    static init() {
        $(IMAGES_DOWNLOAD_PREVIEW_SELECTOR).each(function (index, DOMElement) {
            let element = $(DOMElement);
            new ImagesDownloadPreview(element);
        });
    }
}
