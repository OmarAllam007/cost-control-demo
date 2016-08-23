;(function(window, document, $) {

    //Cache important elements
    var templateInput = $('#TemplateID');
    var resourcesContainer = $('#resourcesContainer');

    //Get templates contents
    var emptyAlert = $('#resourcesEmptyAlert').html();
    var resourcesLoading = $('#resourcesLoading').html();
    var resourcesError = $('#resourcesError').html();
    var resourceRowTemplate = $('#resourceTemplate').html();

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
        console.log('called');
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

        resourcesContainer.html('');

        for (res in resources) {
            var rowObject = $(resourceRowTemplate.replace(/##/g, counter));

            for (key in resources[res]) {
                var input = rowObject.find('[j-model="' + key + '"]');
                input.val(resources[res][key]);
            }

            resourcesContainer.append(rowObject);
            counter++;
        }

        
    }
}(window, document, jQuery));