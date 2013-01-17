<?php

namespace Patchwork\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \RedBean_Facade as R;

abstract class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        return $this->route($app, function() use ($app)
        {
            $username = $app['request']->server->get('PHP_AUTH_USER', false);
            $password = $app['request']->server->get('PHP_AUTH_PW');

            if (( ! $username || ! $password) && preg_match('/Basic\s+(.*)$/i', $_SERVER['REDIRECT_REMOTE_USER'], $matches))
            {
                list($username, $password) = explode(':', base64_decode($matches[1]));
                $username = strip_tags($username);
                $password = strip_tags($password);
            }

            if (($username != BO_USER) || ($password != BO_PASS))
            {
                $response = new Response();
                $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Administration'));
                $response->setStatusCode(401, 'Please sign in.');
                return $response;
            }
        });
    }



    protected function route($app, $auth, $class)
    {
        $ctrl = $app['controllers_factory'];

        // List
        $ctrl->get('/list', function() use ($app, $class)
        {
            return $app['twig']->render('admin/'.$class.'/list.twig', array(
                $class.'s' => R::findAll($class, 'ORDER BY position')
            ));
        })->bind($class.'.list')->before($auth);

        // Move
        $ctrl->get('/move/{id}/{up}', function($id, $up) use ($app, $class)
        {
            $bean = R::load($class, $id);
            if (($up) && ($bean->position > 1))
            {
                $bean->position--;
                R::exec('UPDATE '.$class.' SET position=position+1 WHERE position=?', array($bean->position));
            }

            else if (( ! $up) && ($bean->position < R::count($class)))
            {
                $bean->position++;
                R::exec('UPDATE '.$class.' SET position=position-1 WHERE position=?', array($bean->position));
            }

            R::store($bean);
            return $app->redirect($app['url_generator']->generate($class.'.list'));
        })->bind($class.'.move')->assert('id', '\d+')->assert('up', '0|1')->before($auth);

        // Toggle
        $ctrl->get('/toggle/{id}', function($id) use ($app, $class)
        {
            $bean = R::load($class, $id);
            $bean->active = ! $bean->active;
            R::store($bean);
            return $app->redirect($app['url_generator']->generate($class.'.list'));
        })->bind($class.'.toggle')->assert('id', '\d+')->before($auth);

        // Delete
        $ctrl->get('/delete/{id}', function($id) use ($app, $class)
        {
            $app['session']->clearFlashes();
            $app['session']->setFlash('message', 'La suppression a bien été effectuée');
            $bean = R::load($class, $id);
            $dir = dirname(dirname(dirname(__DIR__))).'/assets/img/'.$class.'/';
            unlink($dir.$bean->image);
            $position = $bean->position;
            R::trash($bean);
            $beans = R::find($class, 'position > ?', array($position));
            foreach ($beans as $b)
            {
                $b->position--;
                R::store($b);
            }
            return $app->redirect($app['url_generator']->generate($class.'.list'));
        })->bind($class.'.delete')->assert('id', '\d+')->before($auth);

        // Post
        $ctrl->get('/post/{id}', function($id) use ($app, $class)
        {
            return $app['twig']->render('admin/'.$class.'/post.twig', array(
                $class => R::load($class, $id)
            ));
        })->bind($class.'.post')->assert('id', '\d+')->value('id', 0)->before($auth);

        // Image delete
        $ctrl->get('/delete_image/{id}', function($id) use ($app, $class) {
            $app['session']->clearFlashes();
            $app['session']->setFlash('message', 'L\'image a bien été supprimée');
            $bean = R::load($class, $id);
            $dir = dirname(dirname(dirname(__DIR__))).'/assets/img/'.$class.'/';
            unlink($dir.$bean->image);
            $bean->image = null;
            R::store($bean);
            return $app->redirect($app['url_generator']->generate($class.'.post', array('id' => $id)));
        })->bind($class.'.delete_image')->assert('id', '\d+')->before($auth);

        return $ctrl;
    }
}
