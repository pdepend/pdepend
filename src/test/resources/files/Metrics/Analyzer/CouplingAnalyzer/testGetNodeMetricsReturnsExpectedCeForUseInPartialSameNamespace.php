<?php
namespace Com\Example;

use \Com\Frontend\Service;

class ServiceManager {

        public function register(\Com\Frontend\Service $s) {
        }

        public function getName() {
                return Service::TAG_CLASS;
        }
}