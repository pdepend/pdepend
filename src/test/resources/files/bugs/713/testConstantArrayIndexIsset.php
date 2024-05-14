<?php

define('FOO', []);

if (isset(FOO['bar'])) {}

if (isset($foo['bar'])) {}

if (isset(FOO['foo']['bar'])) {}

if (isset($foo['foo']['bar'])) {}

if (isset(FOO['foo']['bar'], $foo['foo']['bar'])) {}

if (isset($foo['foo']['bar'], FOO['foo']['bar'])) {}
