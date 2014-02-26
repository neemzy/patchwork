<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Patchwork\Model\AbstractModel;
use Patchwork\Model\SortableModel;
use Patchwork\Model\ClonableModel;
use Patchwork\Model\TogglableModel;
use Patchwork\Model\ImageModel;

class Pizza extends AbstractModel
{
    use SortableModel, ClonableModel, TogglableModel, ImageModel;

    const WIDTH = 480;
    const HEIGHT = 320;



    protected function asserts()
    {
        return array(
            'title' => new Assert\NotBlank(),
            'content' => new Assert\NotBlank(),
            'image' => new Assert\Image(),
            'position' => null,
            'active' => null
        );
    }

    

    public function __toString()
    {
        return $this->title;
    }
}
