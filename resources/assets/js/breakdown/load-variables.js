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
