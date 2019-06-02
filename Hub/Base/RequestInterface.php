<?php
namespace Hub\Base;

interface RequestInterface
{
    public function process(array $options = []);
}
