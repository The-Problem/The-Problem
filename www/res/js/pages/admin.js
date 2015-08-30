LimePHP.register("page.admin", function() {
    // special handlers for properties where we want to do something
    // extra
    var specialHandlers = {
        "overview-visibility": {
            visibility: {
                // disable the registration radio buttons if the visibility is private
                post: function(value, $parent) {
                    $parent.find(".column.registration input").attr('disabled', value === "private");
                }
            }
        }
    };

    // show a saving animation on the current admin tab
    // keeps a stack so that multiple animations can be started
    // and stopped and it only stops when all have finished
    var saveStack = 0,
        $currentAdminTab = $(".container nav .item.selected i"),
        currentAdminTabClass = $currentAdminTab.attr('class');

    // start the saving animation
    function startSaving() {
        saveStack++;

        if (saveStack !== 1) return;
        $currentAdminTab.attr('class', 'fa fa-cog fa-spin');
    }

    // stop the saving animation
    function stopSaving() {
        saveStack--;
        if (saveStack < 0) saveStack = 0;

        if (saveStack = 0) return;
        $currentAdminTab.attr('class', currentAdminTabClass);
    }

    // get any input fields in form sections
    var $inputs = $("section input, section select");

    var currentSelected = false;

    // when the input fields change
    $inputs.on('change', function() {
        var $this = $(this);
        var $section = $this.parents("section");

        var type = $section.data("type");
        var name = $this.attr("name");

        // call any special handlers for the current field
        var handlers = {};
        if (specialHandlers[type] && specialHandlers[type][name]) handlers = specialHandlers[type][name];
        if (handlers.pre) handlers.pre($this.val(), $section);

        // get the value and disable any inputs in the section
        var value = $this.val();
        var $disabled = $section.find('input, select');
        $disabled.attr('disabled', true);

        // start the saving animation, and request the "update" page with the
        // info on the section
        startSaving();
        var hooks = LimePHP.request("post", LimePHP.path("ajax/admin/update"), {
            type: type,
            name: name,
            value: value
        }, "json");

        hooks.success = function(data) {
            // update the field to the new (or old) value
            $this.val(data.value);
        };

        hooks.error = function(err, data) {
            // handle a few directives for if the user isn't logged in,
            // isn't an admin, or needs to enable SUDO mode
            if (data.login) window.location.href = LimePHP.path("login");
            else if (data.home) window.location.href = LimePHP.path();
            else if (data.sudo) window.location.href = LimePHP.path("sudo");
        };

        hooks.complete = function() {
            // stop the saving animation, un-disable the fields, and call the post functions for the special handlers
            stopSaving();
            $disabled.attr('disabled', false);
            if (handlers.post) handlers.post($this.val(), $section);
        };
    });

    // searchable tables
    var $tables = $(".list-table");
    $tables.find(".table-search input").on('keydown', function(e) {
        var $this = $(this);
        var $table = $this.parents(".list-table");
        var $tableRows = $table.children(".table-row");

        // if the user pressed ENTER, click on the first entry
        if (e.keyCode == 13) {
            $tableRows.filter(':visible').first().children('.overview').click();
            return;
        }

        // wait a bit for the input's value to change
        setTimeout(function() {
            // search through all rows and hide them if they don't match the query
            var term = $.trim($this.val().toLowerCase());

            $tableRows.each(function() {
                var $this = $(this);
                if ($this.data('search').toLowerCase().indexOf(term) === -1 || $this.hasClass('new-section')) $this.hide();
                else $this.show();
            });
        }, 0);
    });

    var $rows = $(".section-list .table-row");

    // display section information when you click on a section row
    $(document).on('click', ".section-list .table-row .overview",  function() {
        var $overview = $(this);
        var $row = $overview.parent(".table-row");
        var $options = $row.children(".options");

        $options.show();
        $overview.hide();

        // if the information hasn't loaded, fetch the module
        if (!$row.data("loaded")) {
            LimePHP.library("modules").get("adminSection", $options, {
                id: $row.data("id")
            }, false, false, function() {
                // add the handler for the "close" button, to hide
                // the options and show the table row
                $options.find(".close").on('click', function() {
                    $options.hide();
                    $overview.show();
                });
            });
            $row.data("loaded", true);
        }
    });

    var usernameSearch, $selector = $(".username-selector");

    // search through users to add them as developers
    $(document).on("keydown", ".developer-list input", function(e) {
        var $input = $(this);

        // if the user pressed ENTER, add the first user in
        // the list as a developer
        if (e.keyCode === 13) {
            $(".username-selector tr:first-child").click();
            return;
        }

        // wait a bit for the value to update
        setTimeout(function() {
            var $row = $input.parents(".table-row");

            var term = $input.val();
            showResultsFor(term, $input, $row.data('id'));
        }, 0);
    });

    var canHide = true;
    // hide the popup list when the input is blurred, unless the
    // user clicked on an entry
    $(document).on("blur", ".developer-list input", function() {
        if (canHide) $selector.hide();
    });
    // show the list when the input is focused
    $(document).on("focus", ".developer-list input", function() {
        var $this = $(this);
        $this.keydown();
    });

    // disable the blur from hiding the list when the mouse goes down
    // to allow the "click" event to fire on the individual
    // user
    $(document).on("mousedown", ".username-selector", function() {
        canHide = false;
    });
    // re-enable blur just in case
    $(document).on("mouseup", ".username-selector", function() {
        canHide = true;
    });
    // when the user clicks 'remove' on a developer
    $(document).on("click", ".user-remove", function() {
        var $remove = $(this);
        var $row = $remove.parents(".developer-list tr");
        // if the developer is already being removed, stop
        if ($row.data('submitting')) return;
        $row.data('submitting', true);

        // get some info about the section
        var $section = $row.parents(".table-row");
        var $input = $section.find(".developer-list input");

        var sectionId = $section.data("id");
        var username = $row.data("username");

        function removeRow() {
            $row.remove();

            var $devCount = $section.find(".overview .developers");
            var newAmount = parseInt($devCount.text()) - 1;
            $devCount.text(newAmount);

            // update the count in the summary and row
            if (newAmount === 0) newAmount = "no";
            $row.find(".developer-list .total").text("There " + (newAmount === 1 ? "is " : "are ") + newAmount + " developer" + (newAmount === 1 ? "" : "s") + ".");

            $input.data("usernameCache", {});
        }

        // if the section exists, send a request and then remove the row
        // otherwise if the section is new, just update the HTML
        if (sectionId) {
            startSaving();
            var r = LimePHP.request("get", LimePHP.path("ajax/admin/removeDev"), {
                section: sectionId,
                username: username
            }, "json");

            r.success = removeRow;

            r.complete = function () {
                stopSaving();
                $row.data('submitting', false);
            };
        } else removeRow();
    });
    // the user clicked on an item in the user selector
    $(document).on("click", ".username-selector tr", function() {
        // if no input is selected, bail
        if (currentSelected === false) return;

        // re-enable focusing
        canHide = true;
        $selector.hide();

        // get some info on the section, and some other elements
        var $this = $(this);
        var $row;
        if (currentSelected) $row = $(".table-row[data-id=" + currentSelected + "]");
        else $row = $(".table-row.new-section");
        var $table = $row.find(".developer-list table");
        var $input = $row.find(".developer-list input");
        var $devCount = $row.find(".overview .developers");

        // prevent the user changing the value of the input
        $input.attr('disabled', true);

        // get the image of the item clicked on, to add the new row
        var userImage = $this.children('.user-image').css('background-image'),
            userName = $this.children('.user-name').text();

        var dataUsername = $this.data('username');

        // update the developer count
        var newAmount = parseInt($devCount.text()) + 1;

        function addRow() {
            // create the new row HTML
            var $image = $("<td class='user-image'></td>");
            $image.css("background-image", userImage);
            var $name = $("<td class='user-name'></td>");
            $name.text(userName);
            var $remove = $("<td class='user-remove'><a title='Remove developer' href='javascript:void(0)'><i class='fa fa-times'></i></a></td>");

            var $newRow = $("<tr></tr>");
            $newRow.data("username", dataUsername);
            $newRow.append($image).append($name).append($remove);
            $table.append($newRow);

            $input.data("usernameCache", {});

            $devCount.text(newAmount);

            // update developer counts
            if (newAmount === 0) newAmount = "no";
            $row.find(".developer-list .total").text("There " + (newAmount === 1 ? "is " : "are ") + newAmount + " developer" + (newAmount === 1 ? "" : "s") + ".");
        }

        // if the section exists, send a request and then add the row
        // otherwise if the section is new, just update the HTML
        if (currentSelected) {
            startSaving();
            var r = LimePHP.request("get", LimePHP.path("ajax/admin/addDev"), {
                section: currentSelected,
                username: dataUsername
            }, "json");

            r.success = addRow;

            r.complete = function () {
                stopSaving();
                $input.val("");
                $input.attr('disabled', false);
                $input.focus();
            };
        } else {
            addRow();
            $input.val("");
            $input.attr('disabled', false);
            $input.focus();
        }
    });

    // shows results in the user selector
    function showResultsFor(term, $input, rowId) {
        // cache results to reduce lag when searching for something you've
        // already searched for
        if (!$input.data("usernameCache")) $input.data("usernameCache", {});
        var usernameCache = $input.data("usernameCache");

        // set this so that when you click on an item, it knows which
        // section you are talking about
        currentSelected = rowId;

        // if the term is cached, show it
        if (term in usernameCache) showUserSelector(usernameCache[term], $input);
        else {
            // if a search is in progress, cancel it
            if (usernameSearch) usernameSearch.cancel();

            // request the new list
            usernameSearch = LimePHP.request("get", LimePHP.path("ajax/admin/devSearch"), {
                query: term,
                section: rowId
            }, "json");

            usernameSearch.success = function(data) {
                // show the list
                usernameCache[term] = data;
                showUserSelector(usernameCache[term], $input);
                $input.data("lastList", data);
            };
        }
    }

    // shows a user list in the user selector
    function showUserSelector(items, $input) {
        // position the selector to be at the location of the input field
        var offset = $input.offset();
        var height = $input.height();

        var top = offset.top + height + "px";
        var left = offset.left + "px";
        $selector.css({
            top: top,
            left: left
        });

        // fill it with the new HTML
        $selector.html(items.join(""));
        $selector.show();
    }

    var $addSection = $(".add-section");

    // when the add section button is clicked
    $addSection.on('click', function() {
        // hide the button so you can't try to add multiple sections
        // at once
        var $addSection = $(this);
        $addSection.hide();

        // create a random colour
        var color = Math.floor(Math.random() * 15) + 1;

        // generate the section HTML
        var $newRow = $('<div class="table-row new-section">' +
            '<div class="overview" style="display:none">' +
                '<p class="name"></p>' +
                '<p class="description"></p>' +
                '<p class="developers">0</p>' +
                '<p class="bugs"><em class="none">No bugs</em></p>' +
            '</div><div class="options" style="display:block">' +
                '<div class="section-header">' +
                    '<div class="section-tile color-' + color + '"></div>' +
                    '<div class="right-column">' +
                        '<div class="section info">' +
                            '<h2><input type="text" class="new-section-name" placeholder="Section Name" /></h2>' +
                            '<textarea class="new-section-description" placeholder="Section Description"></textarea>' +
                        '</div>' +
                        '<div class="section developer-list">' +
                            '<h3>Developers</h3>' +
                            '<p class="total">Developers are the people who work on your section, and have some ' +
                            'elevated privelidges.</p>' +
                            '<table></table>' +
                            '<input type="text" placeholder="Add a developer..." />' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="section buttons">' +
                    '<button class="cancel-add-section">Cancel</button>' +
                    '<button class="green finish-add-section" data-color="' + color + '">Apply</button>' +
                '</div>' +
            '</div>' +
        '</div>');

        // add the HTML to the list, enable textarea autosizing, and focus the name box
        $(".section-list").append($newRow);
        autosize($newRow.find('textarea'));
        $newRow.find("input.new-section-name").focus();
    });
    // the cancel button on the new section was clicked
    $(document).on('click', '.cancel-add-section', function() {
        // remove the new section element and show the create section button
        $(".table-row.new-section").remove();
        $addSection.show();
    });
    $(document).on('keydown', '.new-section-name', function() {
        // remove the error background when something is entered in the field
        var $this = $(this);
        setTimeout(function() {
            if ($.trim($this.val()).length) $this.removeClass("error");
        }, 0);
    });
    $(document).on('click', '.finish-add-section', function() {
        var $this = $(this);

        // make sure there is a section name provided
        var $name = $(".new-section-name"), name = $name.val();
        if (!$.trim(name).length) {
            $name.addClass("error");
            return;
        }

        // get the description and list of the developers that have been added
        var $description = $(".new-section-description"), description = $description.val();
        var $devRows = $(".new-section .developer-list table tr");

        var devs = $devRows.map(function() {
            return $(this).data('username');
        }).toArray();

        // get the Cancel and Apply buttons to hide so you can't
        // submit the section twice
        var $buttons = $(".new-section .buttons");

        // disable the input fields in the new section
        var $inputs = $([$name, $description, ".new-section .developer-list input"]);
        $inputs.attr('disabled', true);
        $buttons.hide();

        startSaving();
        // request "addSection" and provide the information on the section
        var r = LimePHP.request("post", LimePHP.path("ajax/admin/addSection"), {
            name: name,
            description: description,
            devs: devs,
            color: $this.data("color")
        }, "json");

        r.success = function(data) {
            // create the real new section row, using the data returned
            // by the database
            var $newRow = $("<div class='table-row'></div>");
            $newRow.data("id", data.id);
            $newRow.data("search", name.toLowerCase());

            var $name = $("<p class='name'></p>").text(name);
            var $description = $("<p class='description'></p>").text(description);
            var $developers = $("<p class='developers'></p>").text(devs.length);

            // highlight if there are no developers
            if (!devs.length) {
                $developers.addClass("highlight");
                $newRow.addClass("highlight");
            }

            var $bugs = $("<p class='bugs'>No bugs</p>");
            var $overview = $("<div class='overview'></div>");
            $overview.append($name).append($description).append($developers).append($bugs);

            var $options = $("<div class='options' style='display:none'></div>");
            $newRow.append($overview).append($options);

            // add the new HTML to the list
            $(".section-list").append($newRow);

            // remove the new section HTML
            $(".table-row.new-section").remove();
            // show the new section button
            $addSection.show();
        };

        r.complete = stopSaving;
    });

    var $userListSelect = $(".user-list select");

    // the user rank selector is focused
    $userListSelect.on('focus', function() {
        // store the previous value so we can revert to it
        // if something fails when it is changed
        var $this = $(this);
        $this.data('previous', $this.val());
    });
    $userListSelect.on('change', function() {
        var $this = $(this);
        var $row = $this.parents(".table-row");
        // change the class of the select to the class of the option,
        // to make it coloured correctly
        $this.attr("class", $this.children(":selected").text().toLowerCase());

        // disable while we submit
        $this.attr('disabled', true);

        startSaving();
        // send a request with the username we are changing, and the new rank
        var r = LimePHP.request("get", LimePHP.path("ajax/admin/changeRank"), {
            username: $row.data('username'),
            rank: $this.val()
        }, "json");

        // remember the previous value
        var previous = $this.data('previous');

        r.error = function(err) {
            // something went wrong, revert to the previous value
            $this.val(previous);
            $this.attr("class", $this.children(":selected").text().toLowerCase());
        };
        r.complete = function() {
            stopSaving();
            // enable the select again, and store the new value
            $this.attr('disabled', false);
            $this.data('previous', $this.val());
        };
    });
});