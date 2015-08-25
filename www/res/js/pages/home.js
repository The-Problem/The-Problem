LimePHP.register("page.home", function() {
    var $search = $(".search-box"),
        $searchable = $(".searchable"),
        $sections = $searchable.children("section"),
        $none = $searchable.children(".none");

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

    var $login = $(".login-box");
    $(".register-btn").on('click', function(e) {
        $login.attr("action", LimePHP.path("signup"));
        $login.submit();

        e.preventDefault();
    });
});