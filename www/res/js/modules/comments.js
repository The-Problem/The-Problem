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