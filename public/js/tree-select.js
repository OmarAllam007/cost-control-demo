$(function () {
    'use strict';

    $('.tree-radio').on('change', function(){
        if (this.checked) {
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

            var selector = '#' + $(this).parents('.modal').attr('id');
            var trigger = $('[href="' + selector +'"]');
            trigger.html(stack.reverse().join(' &raquo; '));
        }
    });
});
//# sourceMappingURL=tree-select.js.map
