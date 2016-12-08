;(function(window, document, $){

    $(function(){
        var breakdowns = {};

        var loader = $('<span class="label label-info"><i class="fa fa-refresh fa-spin"></i> Loading...</span>');
        var errorMessage = $('<span class="label label-danger"><i class="fa fa-exclamation-circle"></i> <span></span></span>');
        var templateInput = $('#TemplateID');
        var breakdownEmptyText = templateInput.find('option[value=""]').text();
        var project = $('#ProjectId').val();

        var hideLoader = function () {
            loader.remove();
        };

        var showLoader = function () {
            hideError();
            templateInput.before(loader);
        };

        var showError = function (message) {
            hideLoader();
            errorMessage.find('span').text(message);
            templateInput.before(errorMessage);
        };

        var hideError = function() {
            errorMessage.remove();
        };

        var fillBreakdowns = function (options) {
            var oldValue = templateInput.val();

            var optionsHtml = '<option value="">' + breakdownEmptyText + '</option>';

            for (var key in options) {
                var selected = '';
                if (key == oldValue) {
                    selected = ' selected="selected"';
                }
                optionsHtml += '<option value="' + key + '"' + selected +'>' + options[key] + '</option>';
            }

            templateInput.html(optionsHtml);
        };

        $('.activity-input').on('change', function(){

            if (this.checked) {
                var value = this.value;

                if (value) {
                    if (breakdowns[value]) {
                        fillBreakdowns(breakdowns[value]);
                    } else {
                        showLoader();
                        $.ajax({ url: '/api/breakdown-template', dataType: 'json', data: {activity: value ,project_id:project}})
                            .then(function(response){
                                fillBreakdowns(response);
                                hideLoader();
                            }, function(){
                                showError('Cannot load breakdowns');
                                fillBreakdowns([]);
                            });
                    }
                } else {
                    fillBreakdowns([]);
                }
            }

        }).change();
    });

}(window, document, jQuery));
;(function(window, document, $) {

    //Cache important elements
    var templateInput = $('#TemplateID');
    var resourcesContainer = $('#resourcesContainer');
    var costInput = $('#CostAccount');
    var wbsLevel = $('#WbsID');

    //Get templates contents
    var emptyAlert = $('#resourcesEmptyAlert').html();
    var resourcesLoading = $('#resourcesLoading').html();
    var resourcesError = $('#resourcesError').html();
    var containerTemplate = $('#containerTemplate').html();
    var resourceRowTemplate = $('#resourceRowTemplate').html();
    var variableTemplate = $('#variableTemplate').html();

    templateInput.on('change', function(){
        loadResources();
    });

    function loadResources()
    {
        var value = templateInput.val();
        var costAccount = costInput.val();
        var wbs = wbsLevel.val();
        if (value && costAccount) {
            showLoading();
            $.ajax({
                url: '/api/std-activity-resource',
                data: {template: value, cost_account: costAccount ,wbs_level_id:wbs},
                dataType: 'json'
            }).done(function(response){
                buildResources(response);
            }).error(function(){
                showError();
            });
        } else {
            showEmpty();
        }
    }

    function showLoading() {
        resourcesContainer.html(resourcesLoading);
    }

    function showError() {
        resourcesContainer.html(resourcesError);
    }

    function showEmpty() {
        resourcesContainer.html(emptyAlert);
    }

    function buildResources(resources) {
        var res;
        var counter = 0;
        var key;

        var table = $(containerTemplate);

        for (res in resources) {
            var rowObject = $(resourceRowTemplate.replace(/##/g, counter));

            for (key in resources[res]) {
                var input = rowObject.find('[j-model="' + key + '"]');
                input.val(resources[res][key]);
            }

            if ($.isPlainObject(resources[res].variables) && !$.isEmptyObject(resources[res].variables)) {
                rowObject.find('.edit-variables').show();
                var variablesContainer = rowObject.find('.variables-container');
                var variableHtml = '';
                for (var order in resources[res].variables) {
                    variableHtml = $(variableTemplate.replace(/%res%/g, counter).replace(/%index%/g, order));
                    variableHtml.find('label.var-name').html(resources[res].variables[order]);
                    variablesContainer.append(variableHtml);
                }
            } else {
                rowObject.find('.edit-variables').hide();
            }

            table.find('tbody').append(rowObject);
            counter++;
        }

        resourcesContainer.html('').append(table);
    }

    var costAccountsCache = {};
    costInput.on('change', function(){
        var val = $(this).val();
        if (val && $('[j-model="budget_qty"]').length == 0) {
            loadResources();
        }

        if (costAccountsCache.hasOwnProperty(val)) {
            var account = costAccountsCache[val];
            $('[j-model="budget_qty"]').val(account.budget_qty);
            $('[j-model="eng_qty"]').val(account.eng_qty);
        } else {
            resourcesContainer.prepend(resourcesLoading);
            $.ajax({
                url: '/api/cost-accounts/account',
                data: {account: val},
                dataType: 'json'
            }).then(function(account){
                costAccountsCache[val] = account;
                $('[j-model="budget_qty"]').val(account.budget_qty);
                $('[j-model="eng_qty"]').val(account.eng_qty);
                resourcesContainer.find('#resourcesLoading').remove();
            }, function() {
                resourcesContainer.find('#resourcesLoading').remove();
            });
        }
    });
}(window, document, jQuery));
;(function (w, d, $) {
    $(function () {
        var variablesPane = $('#VariablesPane');
        var setVariablesPane = $('#SetVariablesPane');
        var variableTemplate = $('#variableTemplate').html();

        var buildVariables = function (variables) {
            var empty = (Object.keys(variables).length == 0);

            if (!empty) {
                var key, prop, variable, varHtml, html = '';
                setVariablesPane.show();
                for (key in variables) {
                    variable = variables[key];
                    varHtml = variableTemplate;
                    for (prop in variable) {
                        console.log(prop);
                        var regexp = new RegExp('\{' + prop + '\}', 'g');
                        varHtml = varHtml.replace(regexp, variable[prop]);
                    }

                    html += varHtml;
                }
                variablesPane.html(html);
            }
        }

        variablesPane.html('');
        setVariablesPane.hide();

        var activities = $('.activity-input').on('change', function () {
            if (this.checked) {
                var value = $(this).val();
                if (value) {
                    $.ajax({
                        url: '/api/std-activity/variables/' + value,
                        dataType: 'json'
                    }).success(buildVariables);
                }
            }
        }).change();
    });
}(window, document, jQuery));

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
$(function () {
    'use strict';

    $('.tree-radio').on('change', function(){
        if (this.checked) {
            var selector = '#' + $(this).parents('.modal').attr('id');
            var trigger = $('[href="' + selector +'"]');

            var label = $(this).data('label');
            if (label) {
                trigger.text(label);
            } else {
                var stack = [];
                var parent = $(this).closest('.tree--item--label');
                var text = parent.find('.node-label').text();
                stack.push(text);

                parent = parent.parents('li').first().parents('li').first();

                while (parent.length) {
                    text = parent.find('.node-label').first().text();
                    stack.push(text);
                    parent = parent.parents('li').first();
                }

                trigger.html(stack.reverse().join(' &raquo; '));
            }
        }
    });

    $('.remove-tree-input').on('click', function (e) {
        e.preventDefault();
        $(this).parent().find('.tree-open').text($(this).data('label'));
        $($(this).data('target')).find('.tree-radio').removeAttr('checked');
    });
});
//# sourceMappingURL=breakdown.js.map
