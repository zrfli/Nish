<?php

namespace Gosas\Core\Entity;

class OrderComment
{
    public $number;
    public $commentText;

    public function __construct(
        $number,
        $commentText
    ) {
        $this->number = $number;
        $this->commentText = $commentText;
    }
}
