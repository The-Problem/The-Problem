<?php

class AjaxTerminalRunPage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        return Templates::findtemplate("ajax");
    }
    public function permission() {
        //return LIME_ENV === LIME_ENV_DEV;
        return true;
    }
    public function subpages() {
        return true;
    }

    public function head(Head &$head) {
    }

    public function body() {
        if (LIME_ENV !== LIME_ENV_DEV || LIME_TERMINAL_MODE !== LIME_TERMINAL_ENABLED) {
            return array(
                "output" => "<span style='color:red;font-weight:bold'>The terminal can only be used in development mode.</span>\n"
            );
        }

        session_start();

        foreach ($_SESSION['t_vars'] as $__var__ => $__val__) {
            $$__var__ = $__val__;
        }

        foreach ($_SESSION['t_funcs'] as $___k => $__func__) {
            eval($__func__);
        }

        session_write_close();

        $__current_funcs__ = get_defined_functions();

        $out = "";
        try {
            ob_start();

            if (array_key_exists('code', $_POST)) {
                $code = $_POST['code'];

                if (eval($code) === FALSE) {
                    ob_end_flush();
                    return array("success" => false);
                }
            }

            $out = ob_get_flush();
        } catch (Exception $ex) {
            $out = "<span style='color:red'>$ex</span>";
        }

        session_start();
        $_SESSION['t_vars'] = get_defined_vars();

        $current_funcs = get_defined_functions();
        $defined_funcs = array_values(array_diff($current_funcs['user'], $__current_funcs__['user']));

        $_SESSION['t_funcs'] = array_merge(array_map(function($func) {
            $f = new ReflectionFunction($func);
            $filename = $f->getFileName();

            if (preg_match("/eval\\(\\)'d code$/", $filename) === 1) $source = $_POST['code'];
            else $source = file_get_contents($filename);

            $start_line = $f->getStartLine() - 1;
            $end_line = $f->getEndLine();
            $length = $end_line - $start_line;

            return implode("\n", array_slice(explode("\n", $source), $start_line, $length));
        }, $defined_funcs), (array)$_SESSION['t_funcs']);

        session_write_close();

        return array("output" => $out);
    }
}