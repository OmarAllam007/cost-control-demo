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