LimePHP.register("module.comments", function() {
    var $body = $("body");

    var $newComment = $(".comment.new");
    var $commentText = $newComment.find("textarea");
    var $button = $newComment.find("button");
    var $onEnter = $newComment.find("input[type=checkbox]");
    var isAddingComment = false;

    $commentText.on('keydown', function(e) {
        if ($onEnter.is(':checked') && e.keyCode === 13) submitComment();
    });

    $button.on('click', submitComment);

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

    $(document).on('click', '.comment .plus-one', function() {
        var $plusOne = $(this);
        var $comment = $plusOne.parents('.comment');

        var $current = $plusOne.find('.current');
        var $next = $plusOne.find('.next');
        var $equals = $plusOne.find('.equals');

        var current = parseInt($current.text());
        $next.text(current + 1);

        $plusOne.addClass('enabled');

        setTimeout(function() {
            $plusOne.removeClass('enabled');
            $plusOne.addClass('finishing');

            setTimeout(function() {
                $current.text($next.text());
                $plusOne.removeClass('finishing');
            }, 200);
        }, 1000);
    });
});