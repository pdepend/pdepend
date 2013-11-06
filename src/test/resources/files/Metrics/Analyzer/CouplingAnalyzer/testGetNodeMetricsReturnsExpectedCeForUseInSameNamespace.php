<?php
namespace Com\Example;

use \Com\Example\Service;

class ServiceManager {

        public function register(\Com\Example\Service $s) {
        }

        public function getName() {
                return Service::TAG_CLASS;
        }
}