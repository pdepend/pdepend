<?php

class testPromotion
{
    public function __construct(
        public private(set) DateTimeInterface $created {
            set {
                $this->created = $value;
            }
        },
    ) {
    }
}
