<?php

namespace spec\BX\News;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NewsCategoryLinkSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('BX\News\NewsCategoryLink');
    }
}
