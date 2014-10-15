<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Patchwork\Model\AbstractModel;
use Patchwork\Model\ImageModel;
use Patchwork\Model\SortableModel;
use Patchwork\Model\TogglableModel;

class Pizza extends AbstractModel
{
    use ImageModel, SortableModel, TogglableModel;


    /**
     * Pizza name getter
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }



    /**
     * Pizza description getter
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }



    /**
     * Pizza image path getter
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }



    /**
     * Valorizes model validation metadata
     *
     * @return void
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addGetterConstraint('title', new Assert\NotBlank());
        $metadata->addGetterConstraint('content', new Assert\NotBlank());
        $metadata->addGetterConstraint('image', new Assert\NotBlank());
        $metadata->addGetterConstraint('image', new Assert\Image(['maxWidth' => 400]));
    }



    /**
     * Defines this model's string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
