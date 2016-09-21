$(function () {
    'use strict';
    var Code = '';
    function getCode($string) {
        var res = $string.split(" ");
        for (var i = 0; i < res.length; i++) {
            if(res[i].toString().substr(0,1)==0)
            {
                continue;
            }
            else {
                Code = Code + res[i].toString().substr(0, 1);
            }
        }
        Code = Code.replace(/0/,'');
        console.log(Code.replace(/»/, ''));
    }



    $('.tree-radio').on('change', function(){
        if (this.checked) {
            var selector = '#' + $(this).parents('.modal').attr('id');
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


                console.log(getCode(stack.reverse().join()));
                trigger.html(stack.reverse().join(' &raquo; '));

            }

        }
    });




});

//# sourceMappingURL=tree-select.js.map
