/**
 * LimePHP Javascript API
 *
 * Provides an API similar to the LimePHP system
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 0.0.1
 * @copyright Copyright (c) 2014, Tom Barham
 */

var LimePHP = LimePHP || {};
LimePHP.version = "0.0.1";
LimePHP.SERVER = {};

/**
 * Loader system
 *
 * Loads LimePHP and all required packages and then calls the load event
 */
(function() {
    var undefined;
    LimePHP.undefined = undefined;

    LimePHP.hasInitialized = false;
    
    /**
     * A list of packages to autoload
     *
     * @type Array
     */
    LimePHP.packages = ["lime"];
    
    /**
     * Loads the LimePHP system
     *
     * This should be called *after* packages have been added to
     * the LimePHP.packages array
     */
    LimePHP.load = function() {
        LimePHP.debug("[Core] Loading LimePHP...");
        
        var hooks = LimePHP.batchrequest($.map(LimePHP.packages, function(elt, i) {
            return LimePHP.path("res/js/packages/" + elt + ".js");
        }), "get");
        hooks.success = function() {
            LimePHP.debug("[Core] Finished loading all packages! Starting package initialization...");
            LimePHP.loadpackages();
            LimePHP.debug("[Core] Finished package initialization!");
            LimePHP.call("load");

            LimePHP.hasInitialized = true;
        }
    }
    
    /**
     * The LimePHP error class
     *
     * @param {String} msg The message for the error
     * @extends Error
     */
    LimePHP.error = function(msg) {
        this.name = "LimePHPError";
        this.message = msg || "An unspecified error occured";
    }
    LimePHP.error.prototype = new Error();
    LimePHP.error.prototype.constructor = LimePHP.error;
    
    /**
     * Debugger
     *
     * @param {String} text Text to debug
     */
    LimePHP.debug = function(text) {
        if (LimePHP.debug.enabled) console.log("[LimePHP] " + text);
    }
    LimePHP.debug.enabled = false;
}());

/**
 * Package/library management system
 */
(function() {
    var packages = {}, libraries = {};
    
    /**
     * Register a package
     *
     * @param {String} name The name of the package
     * @param {Function} callback The callback for the function
     * @returns {Boolean} Whether the operation was successful
     *
     * @throws {LimePHPError} The specified package already exists
     */
    LimePHP.register = function(name, callback) {
        if (packages[name]) throw new LimePHP.error("The specified package '" + name + "' already exists");
        
        LimePHP.debug("[Package] Registering package '" + name + "'");
        packages[name] = callback;
        if (LimePHP.hasInitialized) {
            LimePHP.debug("[Package] Lime is already started, initializing package now");
            LimePHP.loadpackage(name);
        }
        return true;
    }
    
    /**
     * Loads all packages that have been registered
     */
    LimePHP.loadpackages = function() {
        $.each(packages, function(name) {
            LimePHP.loadpackage(name);
        });
    }
    
    /**
     * Loads a specific package
     *
     * @param {String} name The name of the package
     *
     * @throws {LimePHPError} The specified package does not exist
     */
    LimePHP.loadpackage = function(name) {
        if (!packages[name]) throw new LimePHP.error("The specified package '" + name + "' does not exist");
        
        LimePHP.debug("[Package] Initializing package '" + name + "'");
        packages[name].call(LimePHP);
    }
    
    /**
     * Adds a library
     *
     * The function is passed one parameter - an empty object. LimePHP.library() returns this object.
     * Calls the event library.<name>
     *
     * @param {String} name The name of the library
     * @param {Function} callback The function to be called when the library is gotten
     * @returns {Boolean} Whether the operation was successful
     *
     * @throws {LimePHPError} The specified library already exists
     */
    LimePHP.addlibrary = function(name, callback) {
        if (libraries[name]) {
            LimePHP.debug("[WARN] [Package] The specified library '" + name + "' already exists");
            return false;
        }//throw new LimePHP.error("The specified library '" + name + "' already exists");
        
        LimePHP.debug("[Package] Adding library '" + name + "'");
        libraries[name] = {};
        callback(libraries[name]);
        
        LimePHP.call("library." + name);
        return true;
    }
    
    /**
     * Gets a library
     *
     * @param {String} name The name of the library to get
     * @param {Function?} loaded A function to call when the library has been loaded
     * @returns {Object} The value returned from the library, or false if the library is not found
     */
    LimePHP.library = function(name, loaded) {
        if (typeof loaded === "undefined") loaded = function() { }
        
        LimePHP.debug("[Package] Getting library '" + name + "'");
        if (typeof libraries[name] === "undefined") {
            LimePHP.debug("[Package] Attaching async handler for when library is loaded...");
            LimePHP.listen(new LimePHP.Handler("library." + name, function() {
                LimePHP.debug("[Package] Library '" + name + "' has been loaded, calling async handler");
                loaded(libraries[name]);
            }));
            return false;
        }
        
        loaded(libraries[name]);
        return libraries[name];
    }
}());

/**
 * Event management system
 */
(function() {
    /**
     * Defines a handler
     *
     * Three constants for priority are defined:
     *   LimePHP.Handler.PRIORITY_LOW    = 0
     *   LimePHP.Handler.PRIORITY_MEDIUM = 2
     *   LimePHP.Handler.PRIORITY_HIGH   = 4
     *
     * @class Handler
     * @param {String} eventname The name of the event to subscribe to
     * @param {Function} callback The callback to call when the event is called
     * @param {Integer} priority The priority for the event
     */
    LimePHP.Handler = function(eventname, callback, priority) {
        this.eventname = eventname;
        this.callback = callback;
        this.priority = priority || LimePHP.Handler.PRIORITY_MEDIUM;
    }
    LimePHP.Handler.PRIORITY_LOW = 0;
    LimePHP.Handler.PRIORITY_MEDIUM = 2;
    LimePHP.Handler.PRIORITY_HIGH = 4;
    
    var events = {};
    
    /**
     * Add an event handler
     *
     * @param {LimePHP.Handler} handler The handler to add
     */
    LimePHP.listen = function(handler) {
        if (typeof events[handler.eventname] === "undefined") events[handler.eventname] = [];
        
        LimePHP.debug("[Event] Adding an event handler to '" + handler.eventname + "'");
        events[handler.eventname].push(handler);
    }
    
    /**
     * Remove an event handler
     *
     * If handler is specified as a string, all events by that name will be removed. Otherwise only
     * removes the handler specified
     *
     * @param handler Name of event or handler to remove
     */
    LimePHP.remove = function(handler) {
        LimePHP.debug("[Event] Removing an event handler from '" + (handler.eventname || handler) + "'");
        $.each(events, function(name, event) {
            if (typeof handler === "string" && name === handler) events[name] = [];
            else {
                for (var i = 0; i < event.length; i++) {
                    if (event[i] == handler) {
                        event.splice(i, 1);
                        i--;
                    }
                }
            }
        });
    }
    
    /**
     * Call an event
     *
     * @param {String} name Name of event to call
     * @param {Array} args Arguments to pass to callback (optional)
     * @return {Array} Returned values from callbacks
     */
    LimePHP.call = function(name, args) {
        LimePHP.debug("[Event] Attempting to call the event '" + name + "' with " + (args ? args.length : 0) + " parameters");
        if (typeof events[name] === "undefined") return false;
        
        var e = events[name];
        e.sort(function(a, b) {
            return a.priority - b.priority;
        });
        
        var ret = [];
        for (var i = 0; i < e.length; i++) {
            var handler = e[i];
            var r = handler.callback.apply(handler, args);
            if (typeof r !== "undefined") ret.push(r);
        }
        return ret;
    }
}());

/**
 * Path management system
 */
(function() {
    /**
     * Get a path for the browser
     *
     * Paths will always be returned WITHOUT a trailing slash ('/'), and all inverse-
     * slashes will be converted to normal ('\' -> '/').
     *
     * @param {String} folder The path
     * @return {String} The final path
     */
    LimePHP.path = function(folder) {
        folder = String(folder || "").split("\\").join("//");
        
        if (folder.charAt(0) === "/") folder = folder.substring(1);
        var path = LimePHP.SERVER["root"] + folder;
        
        if (path.charAt(path.length - 1) === "/") path = path.slice(0, -1);
        return path;
    }
    
    /**
     * Requests a resource through AJAX
     *
     * @param {String} type         The type of request (GET, POST, PUT, DELETE)
     * @param {String} path         The path to request
     * @param {Object} data         The data to pass
     * @param {String} responseType The type of response (default is JSON)
     * @param {Object?} properties  Other properties to give to $.ajax
     *
     * @return {Object} The AJAX object to add hooks to
     */
    LimePHP.request = function(type, path, data, responseType, properties) {
        LimePHP.debug("[AJAX] Starting AJAX request on path '" + path + "' with type '" + type + "'");
        
        if (typeof properties === "undefined") properties = {};
        
        var rt = typeof responseType === "undefined" ? "json" : responseType.toLowerCase();
        var callbacks = {
            "before":     function() { return true; },
            "complete":   function() {              },
            "error":      function() {              },
            "success":    function() {              },
            "cancel":     function() { ajaxObj.abort(); }
        };
        
        var b = {
            "beforeSend": function(xhr, settings)      { return callbacks["before"].call(xhr, settings); },
            "complete":   function(xhr, status)        { return callbacks["complete"].call(xhr, status); },
            "error":      function(xhr, status, error) { return callbacks["error"].call(xhr, error, status); },
            "success":    function(data, status, xhr) {
                if (rt === "json" && typeof data["error"] !== "undefined") callbacks["error"].call(xhr, data["error"], "jsonerror");
                else callbacks["success"].call(xhr, data, status);
            },
            "crossDomain": true
        };
        if (typeof type !== "undefined") b["type"] = type;
        if (typeof path !== "undefined") b["url"] = path;
        if (typeof responseType !== "undefined") b["dataType"] = responseType;
        if (typeof data !== "undefined") b["data"] = data;
        
        var r = $.extend(true, {}, b, properties);
        
        var ajaxObj = $.ajax(r);

        callbacks.ajax = ajaxObj;
        
        return callbacks;
    }
    
    /**
     * Requests multiple items through AJAX
     *
     * Attached callbacks will be called after all responses have called that callback.
     * Each argument to the callback will be an array of responses recieved for that argument.
     *
     * @param {Array} items The items to load
     * @param {String} type The type of request (GET, POST, PUT, DELETE)
     * @param {String} path The path to request
     * @param {Object} data The data to pass
     * @param {String} responseType The type of response (default is JSON)
     * @param {Object} properties Other properties to give to $.ajax
     *
     * @return {Object} The AJAX object to add hooks to
     */
    LimePHP.batchrequest = function(items, type, path, responseType, properties) {
        LimePHP.debug("[AJAX] Starting batch request with " + items.length + " items");
        
        var callbacks = { }, defaults = { }, hooks = [], counts = { }, args = { };
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            
            if (typeof item !== "object") item = { "path": item };
            
            var b = {};
            if (typeof type !== "undefined") b["type"] = type;
            if (typeof path !== "undefined") b["path"] = path;
            if (typeof responseType !== "undefined") b["responseType"] = responseType;
            if (typeof properties !== "undefined") b["properties"] = properties;
            
            item = $.extend(true, b, item);
            
            hooks.push(LimePHP.request(item.type, item.path, item.responseType, item.properties));
        }
        
        $.each(hooks, function(id, r) {
            $.each(r, function(name, d) {
                callbacks[name] = d;
                defaults[name] = d;
                counts[name] = 0;
                args[name] = [];
                
                r[name] = function() {
                    
                    $.each(arguments, function(i, val) {
                        if (args[name].length - 1 < i) args[name][i] = [];
                        args[name][i].push(val);
                    });
                    
                    counts[name]++;
                    
                    if (counts[name] >= items.length) return callbacks[name].apply(this, args[name]);
                    else return defaults[name].apply(this, args[name]);
                }
            });
        });
        
        return callbacks;
    }
}());