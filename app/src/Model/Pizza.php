<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use PHPImageWorkshop\ImageWorkshop;
use Patchwork\Model\BaseModel;

class Pizza extends BaseModel
{
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
