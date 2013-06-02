<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use PHPImageWorkshop\ImageWorkshop;

class Pizza extends \RedBean_SimpleModel
{
    public function getAsserts()
    {
        return array(
            'title' => new Assert\NotBlank(),
            'content' => new Assert\NotBlank()
        );
    }

    public function setImage($dir, $file)
    {
        $iw = ImageWorkshop::initFromPath($dir.$file);
        $iw->resizeInPixel(150, null, true, 0, 0, 'MM');
        $iw->save($dir, $file, false, null, 90);
        $this->image = $file;
    }

    public function __toString()
    {
        return $this->title;
    }
}
