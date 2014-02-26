<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Patchwork\Model\AbstractModel;
use Patchwork\Model\SortableModel;
use Patchwork\Model\ClonableModel;
use Patchwork\Model\TogglableModel;
use Patchwork\Model\SlugModel;
use Patchwork\Model\ImageModel;
use Patchwork\Helper\Tools;

class Pizza extends AbstractModel
{
    use SortableModel, ClonableModel, TogglableModel, SlugModel, ImageModel;

    public function getSlug()
    {
        return Tools::vulgarize($this->title);
    }

    public function getWidth()
    {
        return 480;
    }

    public function getHeight()
    {
        return 320;
    }



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
