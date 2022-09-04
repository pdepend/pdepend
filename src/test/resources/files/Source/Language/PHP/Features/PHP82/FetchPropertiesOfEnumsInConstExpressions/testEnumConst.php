<?php

enum A: string {
    case B = 'B';
    // This is currently not permitted
    const C = [self::B->value => self::B];
}
