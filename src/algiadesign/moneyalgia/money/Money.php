<?php

namespace algiadesign\moneyalgia\money;

class Money
{
    private $amount = 0;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount)
    {
        if (0 <= $amount) {
            $this->amount = $amount;
        }
    }
}
