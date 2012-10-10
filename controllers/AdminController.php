<?php

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $ctrl = $app['controllers_factory'];

        $auth = function() use ($app)
        {
            $username = $app['request']->server->get('PHP_AUTH_USER', false);
            $password = $app['request']->server->get('PHP_AUTH_PW');

		    if (( ! $username || ! $password) && preg_match('/Basic\s+(.*)$/i', $_SERVER['REDIRECT_REMOTE_USER'], $matches))
		    {
			    list($username, $password) = explode(':', base64_decode($matches[1]));
			    $username = strip_tags($username);
			    $password = strip_tags($password);
		    }

            if (($username != 'admin') || ($password != '@gestion1'))
            {
                $response = new Response();
                $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Administration'));
                $response->setStatusCode(401, 'Please sign in.');
                return $response;
            }
        };



        // Post list
        $ctrl->get('/', function() use ($app)
        {
            return $app['twig']->render('admin/list.twig', array(
                'posts' => R::findAll('post', 'ORDER BY position')
            ));
        })->bind('post.list')->before($auth);



        // Post move
        $ctrl->get('/move/{id}/{up}', function($id, $up) use ($app)
        {
            $post = R::load('post', $id);
            if (($up) && ($post->position > 1))
            {
                $post->position--;
                R::exec('UPDATE post SET position=position+1 WHERE position=?', array($post->position));
            }

            else if (( ! $up) && ($post->position < R::count('post')))
            {
                $post->position++;
                R::exec('UPDATE post SET position=position-1 WHERE position=?', array($post->position));
            }

            R::store($post);
            return $app->redirect($app['url_generator']->generate('post.list'));
        })->bind('post.move')->assert('id', '\d+')->assert('up', '0|1')->before($auth);



        // Post delete
        $ctrl->get('/delete/{id}', function($id) use ($app)
        {
            $app['session']->clearFlashes();
            $app['session']->setFlash('message', 'Le post a bien été supprimé');
            $post = R::load('post', $id);
		    $dir = dirname(__DIR__).'/assets/img/post/';
		    unlink($dir.$post->image);
            R::trash($post);
            return $app->redirect($app['url_generator']->generate('post.list'));
        })->bind('post.delete')->assert('id', '\d+')->before($auth);



        // Post form
        $ctrl->get('/form/{id}', function($id) use ($app)
        {
            return $app['twig']->render('admin/form.twig', array(
                'post' => R::load('post', $id)
            ));
        })->bind('post.form')->assert('id', '\d+')->value('id', 0)->before($auth);



        // Post form submit
        $ctrl->post('/form/{id}', function(Request $request, $id) use ($app)
        {
            $app['session']->clearFlashes();
            $app['session']->setFlash('message', 'Le post a bien été enregistré');

            $post = R::load('post', $id);

            $data = array_flip(array('title', 'content'));
            foreach ($data as $key => $null)
                $data[$key] = strip_tags($request->get($key));

            $asserts = new Assert\Collection(array(
                'title' => new Assert\NotBlank(),
                'content' => new Assert\NotBlank()
            ));
            $errors = $app['validator']->validateValue($data, $asserts);

            if (count($errors))
            {
                $app['session']->setFlash('error', true);
                $message = '<p>L\'enregistrement du post a échoué pour les raisons suivantes :</p><ul class="bullets">';
                foreach ($errors as $error)
                    $message .= '<li><strong>'.$app['translator']->trans($error->getPropertyPath()).'</strong> : '.$app['translator']->trans($error->getMessage()).'</li>';
                $message .= '</ul>';
                $app['session']->setFlash('message', $message);
                return $app['twig']->render('admin/form.twig', array(
                    'post' => $post
                ));
            }

            foreach ($data as $key => $val)
                $post->$key = $val;
            if ( ! $id)
            {
                $post->posted = date('Y-m-d H:i:s');
                $position = 0;
                $posts = R::findAll('post');
                if (is_array($posts))
                    foreach ($posts as $p)
                        $position = max($position, $p->position);
                $post->position = $position + 1;
            }
            $id_post = R::store($post);

		    if (($request->files->has('image')) && ($image = $request->files->get('image')))
		    {
			    $extension = strtolower($image->guessExtension());
			    if ( ! in_array($extension, array('jpeg', 'png', 'gif')))
			    {
				    $app['session']->setFlash('error', true);
				    $app['session']->setFlash('message', 'Seuls les formats JPEG, PNG et GIF sont autorisés');
                    return $app['twig']->render('admin/form.twig', array(
                        'post' => $post
                    ));
			    }
			
			    $dir = dirname(__DIR__).'/assets/img/post/';
			    $file = $id_post.'.'.$extension;
                unlink($dir.$post->image);
			    $image->move($dir, $file);
			
			    $iw = new PHPImageWorkshop\ImageWorkshop(array('imageFromPath' => $dir.$file));
                $iw->resizeInPixel(350, null, true, 0, 0, 'MM');
			    $iw->save($dir, $file, false, null, 90);

			    $post->image = $file;
			    R::store($post);
		    }
            return $app->redirect($app['url_generator']->generate('post.form', array('id' => $id_post)));
        })->assert('id', '\d+')->value('id', 0)->before($auth);



        // Post image delete
        $app->get('/post/delete_image/{id}', function($id) use ($app) {
            $app['session']->clearFlashes();
            $app['session']->setFlash('message', 'L\'image a bien été supprimée');
            $post = R::load('post', $id);
		    $dir = dirname(__DIR__).'/assets/img/post/';
		    unlink($dir.$post->image);
		    $post->image = null;
		    R::store($post);
            return $app->redirect($this->app['url_generator']->generate('post.form', array('id' => $id)));
        })->bind('post.delete_image')->assert('id', '\d+')->before($auth);



        return $ctrl;
    }
}
