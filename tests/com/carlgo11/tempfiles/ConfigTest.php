<?php

namespace com\carlgo11\tempfiles;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testArrayContents(){
        global $conf;
        $this->assertIsArray($conf);

        $vars = ['max-file-size', 'file-path', 'Encryption-Method', 'download-url', 'storage', 'hash-cost'];
        foreach ($vars as $var){
            $this->assertArrayHasKey($var, $conf, 'Config.php doesn\'t include the key "'.$var.'".');
        }
    }

}
