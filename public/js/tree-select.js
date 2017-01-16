$(function () {
    'use strict';

    $('.tree-radio').on('change', function(){
        if (this.checked) {
            var selector = '#' + $(this).closest('.modal').attr('id');
            console.log(selector);
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
//# sourceMappingURL=tree-select.js.map
