LimePHP.register("page.home", function() {
    var $search = $(".search-box"),
        $searchable = $(".searchable"),
        $sections = $searchable.children("section"),
        $none = $searchable.children(".none"),
        $body = $("body"),
        $$body = $(".body"),
        $header = $("header.big, header.entire-page"),
        $$header = $("header");

    var loggedin = $body.hasClass("loggedin");

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
            console.log(amount);

            if (amount) $none.css("display", "none");
            else $none.css("display", "");
        }
    }

    search($search.val());

    $search.on("keydown", function(e) {
        if (e.keyCode === 13) {
            // go to the first section
            var $showing = $sections.filter(":visible");
            location.href = $showing[0].firstElementChild.href;
        }

        setTimeout(function() {
            search($search.val());
        }, 0);
    });

    var $login = $(".login-box"),
        $loginSpinner = $(".login-spinner"),
        $loginError = $(".login-error"),
        $username = $login.children("input[name=username]"),
        $password = $login.children("input[name=password]");

    $login.on('submit', function(e) {
        if (loggedin) return;
        e.preventDefault();

        $login.hide();
        $loginSpinner.show();

        var r = LimePHP.request('post', LimePHP.path('ajax/user/login'), {
            username: $username.val(),
            password: $password.val()
        }, 'json');

        r.success = function(data) {
            $body.addClass("loggedin");
            loggedin = true;
            $body.removeClass("hide-header");
            $header.hide();

            var $welcomeH1 = $("<h1>Welcome, <a></a>.</h1>");
            $welcomeH1.children("a").attr('href', LimePHP.path("~" + data.username))
                                    .text(data.name);

            var $welcome = $("<div class='welcome'></div>");
            $welcome.append($welcomeH1).prependTo($$body);

            var $leftColumn = $(".content .left-column");

            var $sectionH2 = $leftColumn.children('h2');

            if (data.devSections.length) {
                var $devHeader = $("<h2>Sections where you're a developer</h2>");
                var $sectionList = $("<div class='section-list'></div>");
                $sectionList.html(data.devSections);

                $devHeader.prependTo($leftColumn).after($sectionList);
                $sectionH2.text("More Sections");
            }

            $searchable.html(data.sections);
            $sections = $searchable.children("section");

            var $rightColumn = $("<div class='right-column'></div>");
            if (data.notifications.length) {
                $rightColumn.append("<h2>Notifications</h2><div class='notification-list'>" + data.notifications + "</div>");
            }
            if (data.myBugs.length) {
                $rightColumn.append("<h2>My Bugs</h2><div class='notification-list'>" + data.myBugs + "</div>");
            }
            $(".columns").append($rightColumn);

            $$header.html($$header.html() + data.header);

            var $notifications = $("<div></div>").hide();
            $body.append($notifications);

            LimePHP.library("modules").get("notification", $notifications, {}, false, true);
        };

        r.error = function(err, status) {
            if (err.message) $loginError.text(err.message);
            $password.val("");
            $login.show();
            $password.focus();
        };

        r.complete = function() {
            $loginSpinner.hide();
        };

        return false;
    });
    $(".register-btn").on('click', function(e) {
        e.preventDefault();

        $login.attr("action", LimePHP.path("signup"));
        $login.submit();

        return false;
    });
});