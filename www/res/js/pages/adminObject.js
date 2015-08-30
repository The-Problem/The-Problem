LimePHP.register("pages.adminObject", function() {
    var $permissionSelects = $("section select"),
        $selector = $(".username-selector");

    // remember the previous value just in case something
    // goes wrong when the new value is submitted
    $permissionSelects.on('focus', function() {
        var $this = $(this);
        $this.data('previous', $this.val());
    });

    // the permission dropdown is changed
    $permissionSelects.on('change', function() {
        var $this = $(this);
        var $section = $this.parents("section");

        // disable while we submit
        $this.attr('disabled', true);

        // get the info we need to submit
        var permissionName = $section.data("permission");
        var objectId = $section.data("object");
        var newRank = $this.val();

        // send the request to "permissionRank"
        var r = LimePHP.request("get", LimePHP.path("ajax/admin/permissionRank"), {
            permission: permissionName,
            object: objectId,
            rank: newRank
        }, "json");

        r.error = function() {
            // revert to the old value because something went wrong
            $this.val($this.data('previous'));
        };
        r.complete = function() {
            // enable the select and store the new previous value
            $this.attr('disabled', false);
            $this.data('previous', $this.val());
        };
    });


    var currentSelected = false, usernameSearch;
    var canHide = true;

    var $searchField = $(".add-user");

    // when the user search field is changed
    $searchField.on('keydown', function(e) {
        var $input = $(this);

        // if the user pressed RETURN, click on the first result
        if (e.keyCode === 13) {
            $(".username-selector tr:first-child").click();
            return;
        }

        // wait a bit for the value to update
        setTimeout(function() {
            // get the data we need, then show the results
            var $section = $input.parents("section");
            var permissionName = $section.data("permission");
            var objectId = $section.data("object");
            var term = $input.val();
            showResultsFor(term, $input, permissionName, objectId, $section);
        }, 0);
    });
    // hide the results when the field is blurred, if we are not selecting a user
    $searchField.on('blur', function() {
        if (canHide) $selector.hide();
    });
    // show the results when the field is focused
    $searchField.on('focus', function() {
        var $this = $(this);
        $this.keydown();
    });

    // prevent the results from disappearing if we click on any
    // of the results
    $(document).on("mousedown", ".username-selector", function() {
        canHide = false;
    });
    $(document).on("mouseup", ".username-selector", function() {
        canHide = true;
    });

    // when the user clicks on one of the results
    $(document).on("click", ".username-selector tr", function() {
        if (currentSelected === false) return;

        // allow the selector to hide, then hide it
        canHide = true;
        $selector.hide();

        var $this = $(this);

        var $table = currentSelected.find(".user-list");
        var $input = currentSelected.find(".add-user");

        // disable the field while we send the request
        $input.attr('disabled', true);

        // get the user info to create the new table row
        var userImage = $this.children('.user-image').css('background-image'),
            userName = $this.children('.user-name').text();

        var dataUsername = $this.data('username');

        // get the information to send
        var permissionName = currentSelected.data("permission");
        var objectId = currentSelected.data("object");

        // send the request to "permissionUser" with the information it needs
        var r = LimePHP.request("post", LimePHP.path("ajax/admin/permissionUser"), {
            permission: permissionName,
            object: objectId,
            username: dataUsername,
            remove: false
        }, "json");

        r.success = function() {
            // create the new row in the added users table
            var $image = $("<td class='user-image'></td>");
            $image.css("background-image", userImage);
            var $name = $("<td class='user-name'></td>");
            $name.text(userName);
            var $remove = $("<td class='user-remove'><a title='Remove developer' href='javascript:void(0)'><i class='fa fa-times'></i></a></td>");

            var $newRow = $("<tr></tr>");
            $newRow.data("username", dataUsername);
            $newRow.append($image).append($name).append($remove);
            $table.append($newRow);

            // clear the search cache to prevent the new user showing up
            $input.data("usernameCache", {});
        };

        r.complete = function() {
            // reset the input, enable it, and focus it
            $input.val("");
            $input.attr('disabled', false);
            $input.focus();
        };
    });

    // the remove button was clicked on a user entry
    $(document).on("click", ".user-remove", function() {
        var $remove = $(this);
        var $row = $remove.parents("tr");
        var $section = $remove.parents("section");

        // prevent deleting it twice
        if ($row.data('submitting')) return;
        $row.data('submitting', true);

        // get the information we need to send
        var permissionName = $section.data("permission");
        var objectId = $section.data("object");
        var username = $row.data("username");

        // send the information the "permissionUser"
        var r = LimePHP.request("post", LimePHP.path("ajax/admin/permissionUser"), {
            permission: permissionName,
            object: objectId,
            username: username,
            remove: true
        }, "json");

        r.success = function() {
            // remove the row element
            $row.remove();
        };

        r.complete = function() {
            // allow deleting again
            $row.data('submitting', false);
        };
    });

    // show the results popup for a specific term
    function showResultsFor(term, $input, permission, object, $section) {
        // cache the results to reduce latency when searching
        if (!$input.data("usernameCache")) $input.data("usernameCache", {});
        var usernameCache = $input.data("usernameCache");

        // remember the current section, so we know what to submit when
        // a new user is added
        currentSelected = $section;

        // if the query is in the cache, show that
        if (term in usernameCache) showUserSelector(usernameCache[term], $input);
        else {
            // if we are already searching, cancel
            if (usernameSearch) usernameSearch.cancel();
            // send a request with the query and properties
            usernameSearch = LimePHP.request("get", LimePHP.path("ajax/admin/permissionSearch"), {
                query: term,
                permission: permission,
                object: object
            }, "json");

            usernameSearch.success = function(data) {
                // cache the result and then show it
                usernameCache[term] = data;
                showUserSelector(usernameCache[term], $input);
                $input.data("lastList", data);
            };
        }
    }

    function showUserSelector(items, $input) {
        // move the result table to the position of the input
        var offset = $input.offset();
        var height = $input.height();

        var top = offset.top + height + "px";
        var left = offset.left + "px";
        $selector.css({
            top: top,
            left: left
        });

        // place the HTML in the result table
        $selector.html(items.join(""));
        $selector.show();
    }
});