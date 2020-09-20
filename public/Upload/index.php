<?php

namespace com\carlgo11\tempfiles\api;
require_once __DIR__ . '/../../src/com/carlgo11/tempfiles/autoload.php';
require_once __DIR__ . '/../../src/com/carlgo11/tempfiles/api/Upload.php';

new Upload(filter_var($_SERVER['REQUEST_METHOD'], FILTER_SANITIZE_STRING));
