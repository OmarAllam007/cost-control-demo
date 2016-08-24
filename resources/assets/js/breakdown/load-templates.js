;(function(window, document, $){

    $(function(){
        var breakdowns = {};

        var loader = $('<span class="label label-info"><i class="fa fa-refresh fa-spin"></i> Loading...</span>');
        var errorMessage = $('<span class="label label-danger"><i class="fa fa-exclamation-circle"></i> <span></span></span>');
        var templateInput = $('#TemplateID');
        var breakdownEmptyText = templateInput.find('option[value=""]').text();

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
            var optionsHtml = '<option value="">' + breakdownEmptyText + '</option>';
            for (var key in options) {
                optionsHtml += '<option value="' + key + '">' + options[key] + '</option>';
            }
            templateInput.html(optionsHtml);
        };

        $('#ActivityID').on('change', function(){
            var value = this.value;
            if (value) {
                if (breakdowns[value]) {
                    fillBreakdowns(breakdowns[value]);
                } else {
                    showLoader();
                    $.ajax({ url: '/api/breakdown-template', dataType: 'json', data: {activity: value}})
                    .then(function(response){
                        fillBreakdowns(response);
                        hideLoader();
                    }, function(response){
                        showError('Cannot load breakdowns');
                        fillBreakdowns([]);
                    });
                }
            } else {
                fillBreakdowns([]);
            }
        }).change();
    });

}(window, document, jQuery));