<?php

namespace Pizza\Model;

use Symfony\Component\Validator\Constraints as Assert;
use PHPImageWorkshop\ImageWorkshop;
use Patchwork\Helper\RedBean as R;
use Patchwork\Model\AbstractModel;

class Pizza extends AbstractModel
{
    public function getAsserts()
    {
        return array(
            'title' => new Assert\NotBlank(),
            'content' => new Assert\NotBlank(),
            'position' => null,
            'active' => null,
            'image' => null
        );
    }



    public function setImage($file)
    {
        $dir = $this->getImageDir();

        $iw = ImageWorkshop::initFromPath($dir.$file);
        $iw->resizeInPixel(150, null, true, 0, 0, 'MM');
        $iw->save($dir, $file, false, null, 90);
        
        parent::setImage($file);
    }

    

    public function __toString()
    {
        return $this->title;
    }
}
