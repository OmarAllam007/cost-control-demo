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
            }).error(function(){
                showError();
            });
        } else {
            showEmpty();
        }
    });

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
            console.log(res);
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