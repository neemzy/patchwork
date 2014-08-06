<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Patchwork\Model\AbstractModel;
use Patchwork\Model\ClonableModel;
use Patchwork\Model\ImageModel;
use Patchwork\Model\SortableModel;
use Patchwork\Model\TogglableModel;

class Pizza extends AbstractModel
{
    use ClonableModel, ImageModel, SortableModel, TogglableModel;




    /**
     * Describes the model's assert list
     *
     * @return array Assert list
     */
    protected static function asserts()
    {
        return [
            'title' => new Assert\NotBlank(),
            'content' => new Assert\NotBlank(),

            'image' => [
                new Assert\NotBlank(),
                new Assert\Image(
                    [
                        'maxWidth' => 350
                    ]
                )
            ]
        ];
    }

    

    /**
     * Defines this pizza's string representation
     *
     * @return string Pizza as a string
     */
    public function __toString()
    {
        return $this->title;
    }
}
