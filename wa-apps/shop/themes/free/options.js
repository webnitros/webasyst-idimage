$('.products-pagination select').each(function () {
    var $this = $(this),
        numberOfOptions = $(this).children('option').length;

    $this.addClass('select-hidden');
    $this.wrap("<div class='select'></div>");
    $this.after("<div class='select-styled'></div>");

    var $styledSelect = $this.next('div.select-styled');
    $styledSelect.text($('#set-per-page option:selected').text());

    var $list = $('<ul />', {
        'class': 'select-options'
    }).insertAfter($styledSelect);

    for (var i = 0; i < numberOfOptions; i++) {
        $('<li />', {
            text: 'Показывать по ' + $this.children('option').eq(i).text(),
            rel: $this.children('option').eq(i).val()
        }).appendTo($list);
        if ($this.children('option').eq(i).is(':selected')) {
            $('li[rel="' + $this.children('option').eq(i).val() + '"]').addClass('is-selected')
        }
    }

    var $listItems = $list.children('li');

    $styledSelect.click(function (e) {
        e.stopPropagation();
        $('div.select-styled.active').not(this).each(function () {
            $(this).removeClass('active').next('ul.select-options').hide();
        });
        $(this).toggleClass('active').next('ul.select-options').toggle();
    });

    $listItems.click(function (e) {
        e.stopPropagation();
        // $('.cat-onpage').addClass('-hide')
        // $styledSelect.text($(this).text()).removeClass('active');
        $this.val($(this).attr('rel'));
        $list.hide();

        a = {expires: 7, path: "/"};
        $.cookie("products_per_page", $this.val(), a);

        location.reload();
    });
    $(document).click(function () {
        $styledSelect.removeClass('active');
        $list.hide();
    });
});