LimePHP.register("module.comments", function() {
    var $body = $("body");

    var $newComment = $(".comment.new");
    var $commentText = $newComment.find("textarea");
    var $button = $newComment.find("button");
    var $onEnter = $newComment.find("input[type=checkbox]");
    var isAddingComment = false;

    $commentText.on('keydown', function(e) {
        if ($onEnter.is(':checked') && e.keyCode === 13 && !e.shiftKey) submitComment();
    });

    $button.on('click', function(e) {
        e.preventDefault();
        submitComment();
    });

    function submitComment() {
        if (isAddingComment) return;
        isAddingComment = true;

        $commentText.prop('disabled', true);
        $button.prop('disabled', true);

        var text = $commentText.val();

        var r = LimePHP.request("post", LimePHP.path("ajax/bugs/addComment"), {
            bug: $newComment.data('bugId'),
            value: text
        }, "json");

        r.success = function(data) {
            $(data.html).hide().insertBefore($newComment).show().find("span.timeago").timeago();
            window.scrollTo(0, document.body.scrollHeight);
        };

        r.complete = function() {
            $commentText.val("");
            $commentText.prop('disabled', false);
            $button.prop('disabled', false);
            isAddingComment = false;
        };
    }

    $(document).on('keydown', '.comment .content textarea.edit', function(e) {
        if (e.keyCode !== 13 || e.shiftKey) return;

        submit($(this));
    });
    $(document).on('blur', '.comment .content textarea.edit', function() {
        submit($(this));
    });

    function submit($editText) {
        var $comment = $editText.parents('.comment');
        var $val = $comment.find('.content > div');

        if (!$comment.data('editing')) return;

        $editText.attr('disabled', true);

        var r = LimePHP.request("post", LimePHP.path("ajax/bugs/editComment"), {
            id: $comment.data('id'), value: $editText.val()
        }, "json");

        r.success = function(data) {
            $val.html(data.value);
        };
        r.complete = function() {
            $val.show();
            $editText.hide();
            $editText.attr('disabled', false);
            $comment.data('editing', false);
        };
    }

    $(document).on('click', '.comment .header .delete', function() {
        var $delete = $(this);
        var $comment = $delete.parents('.comment');

        var id = $comment.data("id");
        var r = LimePHP.request("get", LimePHP.path("ajax/bugs/removeComment"), { id: id }, "json");
        r.success = function(data) {
            if (data.redirect) location.href = data.redirect;
            else $comment.remove();
        }
    });

    $(document).on('click', '.comment .header .edit', function() {
        var $edit = $(this);
        var $comment = $edit.parents('.comment');
        var $val = $comment.find('.content > div');
        var $editText = $comment.find('textarea.edit');

        $val.hide();
        $editText.show().focus();

        $comment.data('editing', true);
    });

    $(document).on('click', '.comment .plus-one:not(.cant)', function() {
        var $plusOne = $(this);
        if ($plusOne.data('loading')) return;
        $plusOne.data('loading', true);

        var $comment = $plusOne.parents('.comment');

        var $current = $plusOne.find('.current');
        var $hover = $plusOne.find('.hover');
        var $next = $plusOne.find('.next');
        var $equals = $plusOne.find('.equals');
        var $icon = $plusOne.find('i.fa');

        //var current = parseInt($current.text());
        //$next.text(current + 1);

        var hasPlused = $plusOne.data('has');

        $plusOne.addClass('enabled');

        var r = LimePHP.request("post", LimePHP.path("ajax/bugs/plusOneComment"), {
            id: $comment.data("id"),
            action: hasPlused ? 'downvote' : 'upvote'
        }, "json");

        r.success = function() {
            $plusOne.removeClass('enabled');
            $plusOne.addClass('finishing');

            $plusOne.data('has', !hasPlused);

            setTimeout(function() {
                var currentVal = parseInt($next.text());

                $current.text(currentVal);
                $plusOne.removeClass('finishing');

                if (hasPlused) {
                    $hover.text(' + 1');
                    $next.text(currentVal + 1);
                    $icon.removeClass('fa-thumbs-down').addClass('fa-thumbs-up');
                } else {
                    $hover.text(' - 1');
                    $next.text(currentVal - 1);
                    $icon.removeClass('fa-thumbs-up').addClass('fa-thumbs-down');
                }

                $plusOne.data('loading', false);
            }, 200);
        };
        r.error = function() {
            $plusOne.removeClass('enabled');
            $plusOne.data('loading', false);
        };
    });
});