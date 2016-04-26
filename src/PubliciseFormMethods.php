<?php

namespace SmallHadronCollider\LaravelFormPresenter;

trait PubliciseFormMethods
{
    public function __call($name, $arguments)
    {
        if (in_array($name, ["type", "select", "check", "uncheck", "attach", "press"])) {
            return call_user_func_array([$this, $name], $arguments);
        }

        parent::__call($name, $arguments);
    }
}
