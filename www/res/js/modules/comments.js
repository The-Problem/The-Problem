LimePHP.register("module.comments", function() {
    var $body = $("body");

    var $newComment = $(".comment.new");
    var $commentText = $newComment.find("textarea");
    var $button = $newComment.find("button");
    var $onEnter = $newComment.find("input[type=checkbox]");
    var isAddingComment = false;

    // submit the new comment if 'submit on enter' is checked and the user presses ENTER
    // without the SHIFT key
    $commentText.on('keydown', function(e) {
        if ($onEnter.is(':checked') && e.keyCode === 13 && !e.shiftKey) submitComment();
    });

    // submit the new comment if the user presses the COMMENT button
    $button.on('click', function(e) {
        e.preventDefault();
        submitComment();
    });

    function submitComment() {
        // prevent submitting while it is already submitting
        if (isAddingComment) return;
        isAddingComment = true;

        // disable the new comment text, and the COMMENT button
        $commentText.prop('disabled', true);
        $button.prop('disabled', true);

        var text = $commentText.val();

        // send a request to "ajax/bugs/addComment" with the bug ID and comment text
        var r = LimePHP.request("post", LimePHP.path("ajax/bugs/addComment"), {
            bug: $newComment.data('bugId'),
            value: text
        }, "json");

        r.success = function(data) {
            // add the comment HTML and scroll the page down to show it
            $(data.html).hide().insertBefore($newComment).show().find("span.timeago").timeago();
            window.scrollTo(0, document.body.scrollHeight);
        };

        r.complete = function() {
            // clear the comment text and re-enable
            $commentText.val("");
            $commentText.prop('disabled', false);
            $button.prop('disabled', false);
            isAddingComment = false;
        };
    }

    $(document).on('keydown', '.comment .content textarea.edit', function(e) {
        // finish editing when ENTER is pressed without SHIFT
        if (e.keyCode !== 13 || e.shiftKey) return;

        submit($(this));
    });
    $(document).on('blur', '.comment .content textarea.edit', function() {
        // finish editing when the textarea is blurred
        submit($(this));
    });

    function submit($editText) {
        var $comment = $editText.parents('.comment');
        var $val = $comment.find('.content > div');

        // prevent submitting the edit twice
        if (!$comment.data('editing')) return;

        // disable the edit textarea
        $editText.attr('disabled', true);

        // send a request to "ajax/bugs/editComment" with the comment ID and new value
        var r = LimePHP.request("post", LimePHP.path("ajax/bugs/editComment"), {
            id: $comment.data('id'),
            value: $editText.val()
        }, "json");

        r.success = function(data) {
            // update the comment with the processed value
            $val.html(data.value);
        };
        r.complete = function() {
            // hide the textarea and show the comment content
            $val.show();
            $editText.hide();
            $editText.attr('disabled', false);
            $comment.data('editing', false);
        };
    }

    $(document).on('click', '.comment .header .delete', function() {
        // delete the comment
        var $delete = $(this);
        var $comment = $delete.parents('.comment');

        var id = $comment.data("id");
        // send a request to "ajax/bugs/removeComment" with the comment ID
        var r = LimePHP.request("get", LimePHP.path("ajax/bugs/removeComment"), { id: id }, "json");
        r.success = function(data) {
            // if there is a redirect set, go to that page (used for deleting a bug)
            if (data.redirect) location.href = data.redirect;
            else $comment.remove();
        }
    });

    $(document).on('click', '.comment .header .edit', function() {
        // start editing when the edit button is clicked
        var $edit = $(this);
        var $comment = $edit.parents('.comment');
        var $val = $comment.find('.content > div');
        var $editText = $comment.find('textarea.edit');

        $val.hide();
        // focus the edit textarea
        $editText.show().focus();

        // remember that we are currently editing
        $comment.data('editing', true);
    });

    $(document).on('click', '.comment .plus-one:not(.cant)', function() {
        // upvote a comment
        var $plusOne = $(this);
        if ($plusOne.data('loading')) return;
        $plusOne.data('loading', true);

        var $comment = $plusOne.parents('.comment');

        // get the elements in the upvote to show the animation
        var $current = $plusOne.find('.current');
        var $hover = $plusOne.find('.hover');
        var $next = $plusOne.find('.next');
        var $equals = $plusOne.find('.equals');
        var $icon = $plusOne.find('i.fa');

        var hasPlused = $plusOne.data('has');

        // start animating
        $plusOne.addClass('enabled');

        // send a request to plusOneComment with the comment ID, and whether to upvote or downvote
        var r = LimePHP.request("post", LimePHP.path("ajax/bugs/plusOneComment"), {
            id: $comment.data("id"),
            action: hasPlused ? 'downvote' : 'upvote'
        }, "json");

        r.success = function() {
            // on success, finish the animation and update the number
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
            // on error, revert to before
            $plusOne.removeClass('enabled');
            $plusOne.data('loading', false);
        };
    });
});