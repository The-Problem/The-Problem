LimePHP.register("page.home", function() {
    var $search = $(".search-box"),
        $searchable = $(".searchable");

    var searchHooks = { notExists: true };
    var searchCache = {};

    $search.on("keydown", function() {
        setTimeout(function() {
            if (!searchHooks.notExists) searchHooks.cancel();

            var val = $search.val();
            if (searchCache[val]) $searchable.html(searchCache[val]);
            else {
                var isComplete = false;

                setTimeout(function() {
                    if (!isComplete) $searchable.addClass("searching");
                }, 100);

                searchHooks = LimePHP.request("get", LimePHP.path("ajax/sections/search"), {
                    query: val
                }, "html");

                searchHooks.success = function (data) {
                    $searchable.html(data);
                    searchCache[val] = data;
                };

                searchHooks.complete = function () {
                    isComplete = true;
                    $searchable.removeClass("searching");
                };
            }
        }, 0);
    });
});