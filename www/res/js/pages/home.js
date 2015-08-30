LimePHP.register("page.home", function() {
    // get all of the elements we are going to need
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

    // parallax the login header
    var $window = $(window);
    $window.on('scroll', function(e) {
        $scrollContainer.css('top', $window.scrollTop() / 2);
    });
    $window.scroll();


    function search(val) {
        // search sections
        if (val === "") {
            // if no value is supplied, show all sections instead
            // of searching through them all (which has the same
            // result but is slower)
            $sections.css("display", "");
            $none.css("display", "none");
        } else {
            // conver the value to lowercase for case-insensitive comparison
            val = val.toLowerCase();
            var amount = 0;
            $sections.each(function (index, elt) {
                var $elt = $(elt);

                // the name of the section is stored in the "data-name" attribute
                var name = $elt.data("name");
                // if the name doesn't match, set it to not be displayed
                if (name.indexOf(val) === -1) $elt.css("display", "none");
                else {
                    $elt.css("display", "");
                    amount++;
                }
            });

            // if no sections match, show the "Nothing matches" text
            if (amount) $none.css("display", "none");
            else $none.css("display", "");
        }
    }

    // start searching on page load if something is in the box
    if ($search.length) search($search.val());

    $(document).on("keydown", ".search-box", function(e) {
        var $this = $(this);

        if (e.keyCode === 13) {
            // go to the first section if the user presses ENTER in the search field
            var $showing = $sections.filter(":visible");
            location.href = $showing[0].firstElementChild.href;
        }

        // wait for the text to update and then search
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
        // sets the CSS properties for the login animations
        var $title = $("header.big h1");
        var $subtitle = $("header.big h2");

        // calculate the position of the title and set it to be fixed
        // to that position so we can animate it
        var titlePosition = $title.offset();
        $title.css({
            position: "fixed",
            left: titlePosition.left,
            top: titlePosition.top
        });

        $subtitle.hide();

        // add classes to start animation
        $title.addClass("moving");
        $header.addClass("moving");

        // wait for our changes to propagate, then change some properties
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

            // when the first stage is done, fade in the normal header (with header buttons)
            // and then hide our big animated header
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
        // the register button sets allowSubmit so that
        // the form can be submitted and take the user to the
        // register page
        if ($login.data("allowSubmit")) return;

        // prevent the form from submitting
        e.preventDefault();

        // dont login if we are already logged in
        if (loggedin) return;

        // disable the username/password fields, hide the buttons and
        // show the spinner
        $loginInputs.attr('disabled', true);
        $loginButtons.hide();
        $loginSpinner.show();

        // send a request to the login AJAX page with the username and password
        var r = LimePHP.request('post', LimePHP.path('ajax/user/login'), {
            username: $username.val(),
            password: $password.val()
        }, 'json');

        r.success = function(data) {
            // this means the username/password was correct, so add
            // the "loggedin" class
            $body.addClass("loggedin");
            loggedin = true;

            // hide the login form and spinner
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
            // the username/password was wrong, so show
            // an error message
            if (err.message) $loginError.text(err.message);
            // reset the password, show the login form, and focus the password field
            $password.val("");
            $login.show();
            $password.focus();

            // enable the login inputs
            $loginInputs.attr('disabled', false);
            $loginButtons.show();
            $loginSpinner.hide();
        };

        return false;
    });
    $(".register-btn").on('click', function(e) {
        // prevent default submission
        e.preventDefault();

        // disable the inputs, hide the buttons and show the spinner
        $loginInputs.attr('disabled', true);
        $loginButtons.hide();
        $loginSpinner.show();

        // change "allowSubmit" so the form animation code will let
        // the form submit
        $login.data("allowSubmit", true);
        // change the form's action to the signup page, then submit
        // it
        $login.attr("action", LimePHP.path("signup"));
        $login.submit();

        return false;
    });
});