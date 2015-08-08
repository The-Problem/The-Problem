/**
 * Default LimePHP libraries
 *
 * These libraries go along with the libraries in the LimeMore package.
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 */
LimePHP.register("lime", function() {
    /**
     * Image library
     *
     * Provides some image utilities
     */
    LimePHP.addlibrary("image", function(exports) {
        /**
         * Preload an image or a set of images
         *
         * Pass the list of images as the arguments, arrays will be flattened.
         */
        exports.preload = function() {
            $.each(arguments, function(i, itm) {
                if ($.isArray(this)) exports.preload.apply(exports, itm);
                else (new Image()).src = itm;
            });
        }
    });
    
    /**
     * Validator library
     *
     * Uses the LimePHP validator over AJAX
     */
    LimePHP.addlibrary("validator", function(exports) {
        /**
         * Validate one item
         *
         * The callback will normally be passed one parameter - a boolean. If the boolean is true,
         * the string is not valid. If the boolean is false and a second parameter is provided, an
         * error occured. The second parameter provides information about the error.
         *
         * @param {Function} callback The callback to call when the operation is complete
         * @param text The text to validate
         * @param {String} type The type of validation to perform
         * @param {Object} options Options to pass to the validator
         */
        exports.validate = function(callback, text, type, options) {
            var data = {
                "text": text,
                "type": type,
                "options": JSON.encode(options)
            };
            var r = LimePHP.request("post", LimePHP.path("/ajax/validator/"), data);
            r.success = function(data)  { callback(data["result"]); }
            r.fail =    function(error) { callback(false, error);   }
        }
        
        /**
         * Batch validate a series of inputs
         *
         * @param {Function} callback The callback to call when the operation is complete
         * @param {Array} values The values to use - see the documentation for Validator::batchvalidate for more info
         */
        exports.batchvalidate = function(callback, values) {
            var results = [];
            $.each(values, function(i, options) {
                var value = options["value"];
                var type = options["type"];
                var message = options["message"];
                var parameters = (options.hasOwnProperty("parameters") ? options["parameters"] : {});
                
                exports.validate(function(success) {
                    if (success) results.push(message);
                    
                    if (results.length === values.length) callback(results);
                }, value, type, parameters);
            });
        }
    });
    
    /**
     * Module Library
     *
     * Provides the ability to load modules - used by the module manager
     */
    LimePHP.addlibrary("modules", function(exports) {
        function insertAjax(data, $elt, fill) {
            if (!fill) {
                var $newBody = $(data["body"]);
                $newBody.replaceAll($elt).css("display", "none").slideDown("fast");
            } else $elt.html(data["body"]);
            
            var $h = $("head");
            for (var i = 0; i < data["head"].length; i++) $h.append(data["head"][i]);
        }
        exports.insertAjax = insertAjax;
        
        /**
         * Loads a module by its ID
         *
         * @param {String} id The id of the module
         * @param {String} path The base path to get the module from, default is LimePHP.path()
         */
        exports.load = function(id, path) {
            path = path || LimePHP.path();
            
            var hooks = LimePHP.request("get", path + "/ajax/modules/load/" + id, {}, "json");
            hooks.success = function(data) {
                var $elt = $(document.getElementsByClassName(id));
                insertAjax(data, $(document.getElementsByClassName(id)));
            }
            hooks.error = function(xhr, error, status) {
                $(document.getElementsByClassName(id)).replaceWith(
                    $("<p class='error' style='display:none;'></p>").text("Oh no! Something happened and we couldn't get this item.").fadeIn()
                );
                throw new LimePHP.error(error);
            }
            hooks.complete = function() {
                updateLoaders();
            }
            
            updateLoaders();
        }
        
        /**
         * Load a module by name with the specified parameters
         *
         * @param {String} name The name of the module
         * @param {jQuery} $obj The jQuery object to place code into
         * @param {Array} p The parameters to pass to the module
         * @param {Boolean} prepend Whether to prepend the loader
         * @param {Boolean} replace Whether to replace the original element
         * @param {Function} callback A callback to call when the module is loaded
         */
        exports.get = function(name, $obj, p, prepend, replace, complete) {            
            var params = JSON.stringify(p),
                hooks = LimePHP.request("post", LimePHP.path() + "/ajax/modules/get/", {
                    "name": name, "params": params
                }, "json");
            
            var rNum = Math.floor(Math.random() * 10000),
                fillCode = "<div class='module " + rNum + "'><div class='spinner'><div class='circle circle-1'></div><div class='circle circle-2'></div><div class='circle circle-3'></div><div class='circle circle-4'></div><div class='circle circle-5'></div><div class='circle circle-6'></div><div class='circle circle-7'></div><div class='circle circle-8'></div></div></div>";
            
            if (prepend) $obj.prepend(fillCode);
            else $obj.html(fillCode);
            
            hooks.success = function(data) {
                insertAjax(data, $obj, !replace);
                if (complete) complete();
            }
            hooks.complete = function() {
                updateLoaders();
            }
            
            updateLoaders();
        }
        
        /**
         * Loads a set of modules
         *
         * @param {Array} modules A list of modules
         * @param {String} path The base path to get the module from, default is LimePHP.path()
         */
        exports.loadall = function(modules, path) {
            for (var i = 0; i < modules.length; i++) {
                exports.load(modules[i], path);
            }
        }
    });
    
    /**
     * Do some things on load
     */
    LimePHP.listen(new LimePHP.Handler("load", function() {
        // Remove nojs class
        jQuery("body").removeClass("nojs");
        
        var $notice = $(".notice").hide().fadeIn();
        
        setTimeout(function() {
            $notice.slideUp();
            $notice.fadeOut({
                duration:400,
                queue:false
            });
        }, 10000);
        
        // Hide all adjacent module loaders
        updateLoaders();
    }));
    
    /**
     * Add the load event
     */
    /*jQuery(document).ready(function() {
        console.log("document is ready");
        LimePHP.call("load");
    });*/
    
    function updateLoaders() {
        var loaders = findAdjacent($(".module"));
        for (var i = 0; i < loaders.length; i++) {
            loaders[i].not(":last").css("display", "none");
            loaders[i].last().css("display", "");
        }
        
    }
    
    function findAdjacent(elems) {
        var rArr = [], currArr = $([]);
        elems.each(function() {
            var t = $(this);
            currArr = currArr.add(t);
            if (!elems.filter(t.next()).length) {
                rArr.push(currArr);
                currArr = $([]);
            }
        });
        return rArr;
    }
});