<?php

namespace VoidDiscardToken;

class testVoidDiscardToken
{
    public function main($ticket)
    {
        (void) new Thread($ticket)->setClosed(true);
    }
}
