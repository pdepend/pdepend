<?php

interface HasColor
{
    public function getColor(): string;
}

enum Suit: string implements HasColor
{
    case HEARTS = 'hearts';
    case DIAMONDS = 'diamonds';
    case CLUBS = 'clubs';
    case SPADES = 'spades';

    public function getColor(): string
    {
        return match ($this) {
            self::HEARTS, self::DIAMONDS => 'red',
            self::CLUBS, self::SPADES => 'black',
        };
    }
}

class UseEnum
{
    public function foo(): array
    {
        return Suit::cases();
    }

    public function getSuiteColor(Suit $suit): string
    {
        return $suit->getColor();
    }

    public function areDiamondsRed(): bool
    {
        return Suit::DIAMONDS->getColor() === 'red';
    }
}

enum SpecialCases
{
    case NULL;
    case DEFAULT;
    case NEW;
}
