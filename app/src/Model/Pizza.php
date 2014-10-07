<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Patchwork\Model\AbstractModel;
use Patchwork\Model\ClonableModel;
use Patchwork\Model\ImageModel;
use Patchwork\Model\SortableModel;
use Patchwork\Model\TogglableModel;

class Pizza extends AbstractModel
{
    use ClonableModel, ImageModel, SortableModel, TogglableModel;

    /**
     * @var string Pizza name
     */
    public $title;

    /**
     * @var string Pizza description
     */
    public $content;

    /**
     * @var string Pizza image path
     */
    public $image;



    /**
     * Valorizes the model's validation metadata
     *
     * @return void
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new Assert\NotBlank());
        $metadata->addPropertyConstraint('content', new Assert\NotBlank());
        $metadata->addPropertyConstraint('image', new Assert\NotBlank());
        $metadata->addPropertyConstraint('image', new Assert\Image(['maxWidth' => 400]));
    }



    /**
     * Defines this bean's string representation
     *
     * @return string Bean as a string
     */
    public function __toString()
    {
        return $this->title;
    }
}
