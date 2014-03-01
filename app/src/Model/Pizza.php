<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Patchwork\Model\AbstractModel;
use Patchwork\Model\SortableModel;
use Patchwork\Model\ClonableModel;
use Patchwork\Model\TogglableModel;
use Patchwork\Model\SlugModel;
use Patchwork\Model\ImageModel;
use Patchwork\Tools;

class Pizza extends AbstractModel
{
    use SortableModel, ClonableModel, TogglableModel, SlugModel, ImageModel;

    public function slugify()
    {
        return Tools::vulgarize($this->title);
    }




    protected static function asserts()
    {
        return [
            'title' => new Assert\NotBlank(),
            'content' => new Assert\NotBlank()
        ];
    }

    

    public function __toString()
    {
        return $this->title;
    }
}
