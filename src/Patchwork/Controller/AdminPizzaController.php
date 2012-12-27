<?php

namespace Patchwork\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use \RedBean_Facade as R;
use PHPImageWorkshop\ImageWorkshop;

class AdminPizzaController extends AdminController
{
    protected function route($app, $auth, $class = 'pizza')
    {
        $ctrl = parent::route($app, $auth, $class);

        // Submit
        $ctrl->post('/post/{id}', function(Request $request, $id) use ($app, $class)
        {
            $app['session']->clearFlashes();
            $app['session']->setFlash('message', 'La pizza a bien été enregistrée');

            $bean = R::load($class, $id);
            $data = array();
            $data_stripped = array();

            $asserts = array(
                'name' => new Assert\NotBlank(),
                'desc' => new Assert\NotBlank()
            );
            
            foreach ($asserts as $key => $assert)
            {
                $data[$key] = $request->get($key);
                $data_stripped[$key] = strip_tags($data[$key]);
            }

            $asserts = new Assert\Collection($asserts);
            $errors = $app['validator']->validateValue($data_stripped, $asserts);

            if (count($errors))
            {
                $app['session']->setFlash('error', true);
                $message = '<p>L\'enregistrement de la pizza a échoué pour les raisons suivantes :</p><ul>';
                foreach ($errors as $error)
                    $message .= '<li><strong>'.$app['translator']->trans($error->getPropertyPath()).'</strong> : '.$app['translator']->trans($error->getMessage()).'</li>';
                $message .= '</ul>';
                $app['session']->setFlash('message', $message);
                return $app['twig']->render('admin/'.$class.'/post.twig', array(
                    $class => $bean
                ));
            }

            foreach ($data as $key => $val)
                $bean->$key = $val;
            if ( ! $id)
            {
                $position = 0;
                $beans = R::findAll($class);
                if (is_array($beans))
                    foreach ($beans as $b)
                        $position = max($position, $b->position);
                $bean->position = $position + 1;
            }
            $id_bean = R::store($bean);

            if (($request->files->has('image')) && ($image = $request->files->get('image')))
            {
                $extension = strtolower($image->guessExtension());
                if ( ! in_array($extension, array('jpeg', 'png', 'gif')))
                {
                    $app['session']->setFlash('error', true);
                    $app['session']->setFlash('message', 'Seuls les formats JPEG, PNG et GIF sont autorisés');
                    return $app['twig']->render('admin/'.$class.'/post.twig', array(
                        $class => $bean
                    ));
                }
            
                $dir = dirname(dirname(dirname(__DIR__))).'/assets/img/'.$class.'/';
                $file = $id_bean.'.'.$extension;
                if ($bean->image)
                    unlink($dir.$bean->image);
                $image->move($dir, $file);
            
                $iw = ImageWorkshop::initFromPath($dir.$file);
                $iw->resizeInPixel(150, null, true, 0, 0, 'MM');
                $iw->save($dir, $file, false, null, 90);

                $bean->image = $file;
                R::store($bean);
            }
            return $app->redirect($app['url_generator']->generate($class.'.post', array('id' => $id_bean)));
        })->assert('id', '\d+')->value('id', 0)->before($auth);

        return $ctrl;
    }
}
