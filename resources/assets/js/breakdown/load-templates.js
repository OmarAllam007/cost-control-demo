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