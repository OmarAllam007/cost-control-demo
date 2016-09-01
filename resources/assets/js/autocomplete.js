(function (window, document, $) {
    var listTemplate = '<div class="panel panel-info autocomplete-panel"><div class="list-group"></div></div>';
    var loadingTemplate = '<div class="list-group-item text-center"><i class="fa fa-spinner fa-spin"></i></div>';
    var loadingError = '<div class="list-group-item list-group-item-danger"><i class="fa fa-exclamation-circle"></i> Error loading options</div>';
    var emptyTemplate = '<div class="list-group-item list-group-item-warning"><i class="fa fa-exclamation-triangle"></i> No options found</div>'


    var buildOptions = function (options) {
        var isArray = $.isArray(options);
        var isObject = $.isPlainObject(options);

        if (!(isArray && options.length) && !(isObject && !$.isEmptyObject(options))) {
            return emptyTemplate;
        }

        var html = '';

        for (var i in options) {
            if (options.hasOwnProperty(i)) {
                var val = isArray ? options[i] : i;
                html += '<a href="#" class="list-group-item option-link" data-val="' + val + '">' + options[i] + '</a>';
            }
        }

        return html;
    };

    $.fn.completeList = function (options) {
        $(this).each(function (index, element) {
            var $element = $(element);

            $element.attr('autocomplete', 'off');
            $element.parents('.form-group').css({position: 'relative'});

            var $list = $(listTemplate);
            var $body = $list.find('.list-group');

            var lastValue = '';

            var loadOptions = function () {
                var optionsHtml = '';
                var val = $element.val();
                if (options.options && ($.isArray(options.options) || $.isPlainObject(options.options))) {
                    optionsHtml = buildOptions(options.options);
                    $body.html(optionsHtml);
                } else if ($body.data('options') && lastValue === val) {
                    optionsHtml = buildOptions($body.data('options'));
                    $body.html(optionsHtml);
                } else if (options.url) {
                    $body.html(loadingTemplate);
                    $.ajax({
                        type: 'get',
                        data: {term: val},
                        url: options.url,
                        dataType: 'json'
                    }).then(function (result) {
                        lastValue = val;
                        optionsHtml = buildOptions(result);
                        $body.html(optionsHtml);
                    }, function () {
                        $body.html(loadingError);
                    });
                } else {
                    $body.html(emptyTemplate);
                }
            };

            $element.on('focus', function () {
                loadOptions();
                $list.show();
            }).on('blur', function () {
                setTimeout(function () {
                    $list.hide();
                }, 100);
            }).on('keyup', function (e) {
                if (e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40) {
                    loadOptions();
                }
            }).on('keydown', function (e) {
                if (e.key.toLowerCase() == 'arrowdown') {
                    if ($list.find('a.active').length == 0) {
                        $list.find('a').first().addClass('active');
                    } else {
                        if ($list.find('a.active').next('a').length) {
                            $list.find('a.active').removeClass('active').next('a').addClass('active');
                        }
                    }
                } else if (e.key.toLowerCase() == 'arrowup') {
                    if ($list.find('a.active').length == 0) {
                        $list.find('a').first().addClass('active');
                    } else {
                        if ($list.find('a.active').prev('a').length) {
                            $list.find('a.active').removeClass('active').prev('a').addClass('active');
                        }
                    }
                }
            }).on('keyup keydown keypress', function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    var text = $list.find('a.active').text();
                    if (text) {
                        $(this).val(text).change();
                        $list.hide();
                    }
                }
            });

            $body.on('click', '.option-link', function (e) {
                e.preventDefault();
                $element.val($(this).data('val')).change();
            });

            $element.after($list.hide());
        });
    };
}(window, document, jQuery));