<?php

namespace com\carlgo11\tempfiles;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testArrayContents(){
        global $conf;
        $this->assertIsArray($conf);

        $vars = ['max-file-size', 'file-path', 'Encryption-Method', 'download-url', 'api-download-url'];
        foreach ($vars as $var){
            $this->assertArrayHasKey($var, $conf, 'Config.php doesn\'t include the key "'.$var.'".');
        }
    }

}
