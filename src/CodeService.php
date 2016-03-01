<?php

namespace CodeService;

use CodeService\Code\Analytic;
use CodeService\Code\Wrapper\AstWrapper;

class CodeService
{
    public function analysis($file)
    {
        return Analytic::analytic($file);
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
