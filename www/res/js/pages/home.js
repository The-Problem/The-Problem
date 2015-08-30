LimePHP.register("page.home", function() {
    var $search = $(".search-box"),
        $searchable = $(".searchable"),
        $sections = $searchable.children("section"),
        $none = $searchable.children(".none"),
        $body = $("body"),
        $$body = $(".body"),
        $header = $("header.big, header.entire-page"),
        $$header = $("header:not(.big, .entire-page)"),
        $scrollContainer = $("header.big .scroll-container");

    var loggedin = $body.hasClass("loggedin");

    var $window = $(window);
    $window.on('scroll', function(e) {
        $scrollContainer.css('top', $window.scrollTop() / 2);
    });
    $window.scroll();


    function search(val) {
        if (val === "") {
            $sections.css("display", "");
            $none.css("display", "none");
        } else {
            val = val.toLowerCase();
            var amount = 0;
            $sections.each(function (index, elt) {
                var $elt = $(elt);

                var name = $elt.data("name");
                if (name.indexOf(val) === -1) $elt.css("display", "none");
                else {
                    $elt.css("display", "");
                    amount++;
                }
            });

            if (amount) $none.css("display", "none");
            else $none.css("display", "");
        }
    }

    if ($search.length) search($search.val());

    $(document).on("keydown", ".search-box", function(e) {
        var $this = $(this);

        if (e.keyCode === 13) {
            // go to the first section
            var $showing = $sections.filter(":visible");
            location.href = $showing[0].firstElementChild.href;
        }

        setTimeout(function() {
            search($this.val());
        }, 0);
    });

    var $login = $(".login-box"),
        $loginSpinner = $(".login-spinner"),
        $loginError = $(".login-error"),
        $username = $login.children("input[name=username]"),
        $password = $login.children("input[name=password]"),
        $loginInputs = $login.children('input'),
        $loginButtons = $(".login-box .buttons");


    function showLoginAnimation() {
        var $title = $("header.big h1");
        var $subtitle = $("header.big h2");

        var titlePosition = $title.offset();
        $title.css({
            position: "fixed",
            left: titlePosition.left,
            top: titlePosition.top
        });

        $subtitle.hide();

        $title.addClass("moving");
        $header.addClass("moving");

        setTimeout(function() {
            $title.css({
                left: 0,
                top: 23,
                fontSize: 22.8833,
                marginLeft: 20,
                marginTop: -11
            });

            $title.find("img").css({
                width: 31,
                height: 35,
                verticalAlign: -9,
                marginTop: 0
            });
            $title.find("span").css({
                marginLeft: 7
            });
            $header.css({
                height: 70
            });

            setTimeout(function() {
                $body.removeClass("hide-header");
                $header.css({
                    opacity: 0
                });
                setTimeout(function() {
                    $header.css({ display: 'none' });
                }, 500);
            }, 1000);
        }, 0);
    }


    $login.on('submit', function(e) {
        if ($login.data("allowSubmit")) return;

        e.preventDefault();

        if (loggedin) return;

        $loginInputs.attr('disabled', true);
        $loginButtons.hide();
        $loginSpinner.show();

        var r = LimePHP.request('post', LimePHP.path('ajax/user/login'), {
            username: $username.val(),
            password: $password.val()
        }, 'json');

        r.success = function(data) {
            $body.addClass("loggedin");
            loggedin = true;

            $login.hide();
            $loginSpinner.hide();

            // Add items to header
            $$header.html($$header.html() + data.header);

            // show the logged in homepage module
            LimePHP.library("modules").get("loggedInHome", $(".content"), [], false, true, function() {
                // refresh elements
                $search = $(".search-box");
                $searchable = $(".searchable");
                $sections = $searchable.children("section");
                $none = $searchable.children(".none");

                // refresh timeago
                $(".content span.timeago").timeago();
            });

            // show the notification sidebar
            var $notifications = $("<div></div>").hide();
            $body.append($notifications);

            LimePHP.library("modules").get("notification", $notifications, {}, false, true);

            // the page is ready, show the animation
            showLoginAnimation();
        };

        r.error = function(err, status) {
            if (err.message) $loginError.text(err.message);
            $password.val("");
            $login.show();
            $password.focus();

            $loginInputs.attr('disabled', false);
            $loginButtons.show();
            $loginSpinner.hide();
        };

        r.complete = function() {

        };

        return false;
    });
    $(".register-btn").on('click', function(e) {
        e.preventDefault();

        $loginInputs.attr('disabled', true);
        $loginButtons.hide();
        $loginSpinner.show();

        $login.data("allowSubmit", true);
        $login.attr("action", LimePHP.path("signup"));
        $login.submit();

        return false;
    });
});