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
;(function(window, document, $) {

    //Cache important elements
    var templateInput = $('#TemplateID');
    var resourcesContainer = $('#resourcesContainer');

    //Get templates contents
    var emptyAlert = $('#resourcesEmptyAlert').html();
    var resourcesLoading = $('#resourcesLoading').html();
    var resourcesError = $('#resourcesError').html();
    var containerTemplate = $('#containerTemplate').html();
    var resourceRowTemplate = $('#resourceRowTemplate').html();

    templateInput.on('change', function(){
        var value = this.value;
        if (value) {
            showLoading();
            $.ajax({
                url: '/api/std-activity-resource',
                data: {template: value},
                dataType: 'json'
            }).done(function(response){
                buildResources(response);
            }).error(function(response){
                showError();
            });
        } else {
            showEmpty();
        }
    }).change();

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
        var html = '';
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

            table.find('tbody').append(rowObject);
            counter++;
        }

        resourcesContainer.html('').append(table);
    }
}(window, document, jQuery));
//# sourceMappingURL=breakdown.js.map
