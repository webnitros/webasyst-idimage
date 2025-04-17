var idImage = {
    isInit: false,
    selector: '.idimage',
    selectorProductsId: '[data-product-num]',
    selectorProductsThumb: 'div.product__box--image',
    selectorThumb: '.js-product_gallery-images-main',
    selectorButton: '.idimage-similar',
    selectorModal: '.idimage-modal',
    init: function () {
        if (this.isInit) {
            return false;
        }
        this.isInit = true;
        this.regButton();
        this.handleClick();
    },
    handleClick: function () {

        // Обработчик событий
        $(document).on('click', this.selectorButton, function (e) {
            e.preventDefault();
            var productId = $(this).data('product-id')
            idImage.Modal.regBlock()
            idImage.Modal.show(productId)
        })

        // Регистрация кнопки в списке товара
        $(idImage.selectorProductsId).each(function (index, elem) {
            var $imageBox = $(elem).find(idImage.selectorProductsThumb);
            if ($imageBox.length) {
                var productId = $(elem).data('product-num');
                // Проверим, не добавлена ли уже кнопка
                if ($imageBox.find('.idimage-custom-photo-btn').length === 0) {
                    $imageBox.prepend(idImage.htmlButton(productId));
                }
            }
        })
    },

    htmlButton(productId) {
        return `
                <div class="${this.selector.slice(1)} idimage-custom-photo-btn">
                    <a class="${this.selectorButton.slice(1)}" data-product-id="${productId}" href="#">
                        <span>Похожие</span>
                    </a>
                </div>
            `;
    },
    regButton() {
        const el = $(this.selectorThumb);
        if (el.length) {
            const productId = $('#page-content').data('product-id')
            el.prepend(idImage.htmlButton(productId));
        }
    },

    Modal: {
        el: null,
        initialize: function () {
            $(document).on('click', idImage.selectorModal + ' .modal-closed', function (e) {
                e.preventDefault()
                msOneClick.Modal.hide()
                return false
            })

        },
        show: function (productId) {
            idImage.Product.similar(productId)

            $(idImage.selectorModal).modal('show')

            // После закрытия автоматически стерам данные
            $(idImage.selectorModal).on('hidden.bs.modal', function (e) {
                idImage.Modal.remove()
            })
        },
        hide: function () {
            $(idImage.selectorModal).modal('hide')
        },
        remove: function () {
            idImage.loading = false
            $(idImage.selectorModal).remove()
        },
        regBlock() {
            const el = $(idImage.selectorModal);
            if (el.length === 0) {
                idImage.Modal.el = $('<div>')
                    .addClass(idImage.selectorModal.slice(1))
                    .addClass('modal ')
                    .attr({
                        tabindex: '-1',
                        role: 'dialog',
                        'aria-hidden': 'true'
                    })
                    .appendTo('body');

                // Создание содержимого модального окна
                const modalDialog = $('<div>')
                    .addClass('modal-dialog')
                    .attr('role', 'document')
                    .appendTo(idImage.Modal.el);

                const modalContent = $('<div>')
                    .addClass('modal-content')
                    .appendTo(modalDialog);

                // Пример заголовка, тела и футера модального окна
                $('<div>')
                    .addClass('modal-header')
                    .append('<h5 class="modal-title">Похожие по фото</h5>')
                    //.append('<button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>')
                    .appendTo(modalContent);

                $('<div>')
                    .addClass('modal-body')
                    .text('Похожие по фото')
                    .appendTo(modalContent);

                $('<div>')
                    .addClass('modal-footer')
                    .append('<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>')
                    .appendTo(modalContent);
            }
        },
    },
    Product: {
        setEmptyModal() {
            $('.modal-body').html('Похожие товары не найдены.');
        },
        setProductsModal(items) {
            var output = '';
            for (var i = 0; i < items.length; i++) {
                var url = items[i].url;
                var thumb = items[i].thumb;
                var name = items[i].name;
                var probability = items[i].probability;
                output += '<a href="' + url + '"><div class="idimage-card">' +
                    '<img src="' + thumb + '" class="idimage-card-img-top">' +
                    '<span>' + name + '</span>' +
                    '<span>' + probability + '%</span>' +
                    '</div></a>';
            }

            var wrapper = '<div class="idimage-similar-wrapper">' +
                '<div class="idimage-similar-wrapper-products">' + output + '</div>' +
                '</div>';

            $('.modal-body').html(wrapper);
        },
        similar(productId) {

            const currentUrl = window.location.pathname + '?similar_product=' + productId;
            fetch(currentUrl, {
                method: 'GET',
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        idImage.Product.setProductsModal(data.data)
                    } else {
                        idImage.Product.setEmptyModal()
                    }
                })
                .catch(error => {
                    console.error("Ошибка при отправке:", error);
                });

        }
    }
}

$(document).ready(function ($) {
    idImage.init();
});
