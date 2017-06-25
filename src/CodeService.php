<?php

namespace CodeService;

use CodeService\Code\Analytic;
use CodeService\Code\Wrapper\AstWrapper;

class CodeService
{
    function ls($dir, $func, $pass = []) {
        $_check = preg_replace("/\/$/", "", $dir);
        if(in_array($_check, $pass)) {
            return false;
        }
        if(is_dir($dir)) {
            $handler = opendir($dir);
            while($file = readdir($handler)) {
                if($file[0] === "." || preg_match("/~$/", $file)) {
                    continue;
                }
                $_file = str_replace("//", "/", $dir . "/" . $file);
                if(is_dir($_file)) {
                    call_user_func([$this, "ls"], $_file, $func, $pass);
                } else {
                    if(in_array($_file, $pass)) {
                        //skip the file
                        continue;
                    }
                    call_user_func($func, $file, $_file);
                }
            }
        }
    }

    public function analysis($file, $version = null)
    {
        return Analytic::analytic($file, $version);
    }

    public function createGenerator()
    {
        return Analytic::analyticCode(join('', [
            '<?php', PHP_EOL,
            'namespace ', __FUNCTION__, ';', PHP_EOL,
            'class ', __FUNCTION__, '{}'
        ]));
    }
}
