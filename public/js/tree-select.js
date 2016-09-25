$(function () {
    'use strict';


    $('.tree-radio').on('change', function () {
        if (this.checked) {
            var selector = '#' + $(this).parents('.modal').attr('id');
            var trigger = $('[href="' + selector + '"]');

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
                generateCode(stack);
            }


        }

    });

    function generateCode($stack) {

        // var code = $stack.toString();
        // var splittedArray = code.split(',');
        // var finalcode = '';
        //
        // for (var i = 0; i < splittedArray.length; i++) {
        //     if (splittedArray)
        //         finalcode = finalcode + splittedArray[i].charAt(0) + i;
        //     console.log(finalcode);
        // }
        // $('#resource_code').val(finalcode);
    };
    $('.remove-parent').on('click', function () {
        $(this).parent().find('.select-parent').text($(this).attr('data'));
        $(this).modal().removeAttr('checked',false);
    });


});
//# sourceMappingURL=tree-select.js.map
