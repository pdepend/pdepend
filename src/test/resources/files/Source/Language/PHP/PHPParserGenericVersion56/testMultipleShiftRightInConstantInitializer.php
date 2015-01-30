<?php

class testMultipleShiftRightInConstantInitializer
{
    const PROFILE_USER = 1>>2>>4>>8;
}
