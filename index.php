<?php 
// web/index.php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

//Silex Initialiazer file
require_once __DIR__.'/../vendor/autoload.php';

//Controller File
require_once __DIR__.'/Controller.php';


$app = new Silex\Application();
$app['debug'] = true;
// definitions

$blogPosts = array(
    1 => array(
        'date'      => '2011-03-29',
        'author'    => 'igorw',
        'title'     => 'Using Silex',
        'body'      => '...',
    ),
);
$app->error(function (\Exception $e, $code) use ($app){
    if($app['debug'])
    	return;
    else{
	    switch ($code) {
	        	case 404:
	            	$message = 'The requested page could not be .';
	            	break;
	        	default:
	        	    $message = 'We are not sorry, but something went terribly wrong.';
	    	}
	    return new Response($message);
	}
});
$app->get('/', function () use ($app) {
    return $app->redirect('/blog');
});
//JSON Format
$app->get('/users/{id}', function ($id) use ($app) {
    //$user = getUser($id);
	$user = false;
    if (!$user) {
        $error = array('message' => 'The user was not found.');
        return $app->json($error, 404);
    }

    return $app->json($user);
});
//Send Files
$app->get('/files/{path}', function ($path) use ($app) {
    if (!file_exists('files/' . $path)) {
        $app->abort(404);
    }

    return $app->sendFile('files/' . $path);
});
//Stream Files
$app->get('/images/{file}', function ($file) use ($app) {
    if (!file_exists(__DIR__.'/images/'.$file)) {
        return $app->abort(404, 'The image was not found.');
    }

    $stream = function () use ($file) {
        //readfile($file);
        $fh = fopen('images/'.$file, 'rb');
           while (!feof($fh)) {
             echo fread($fh, 1024);
             ob_flush();
             flush();
           }
           fclose($fh);
    };

    return $app->stream($stream, 200, array('Content-Type' => 'image/png'));
});
$app->get('/forwards', function () use ($app) {
    // redirect to /hello
    $subRequest = Request::create('/blog', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});
/*$app->get('/blog', function () use ($blogPosts) {
    $output = '';
    foreach ($blogPosts as $post) {
        $output .= $post['date'];
        $output .= '<br />';
        $output .= $post['author'];
        $output .= '<br />';
        $output .= $post['title'];
        $output .= '<br />';
        $output .= $post['body'];
        $output .= '<br />';
    }

    return $output;
});*/
$app->get('/blog', 'Controller::blog');

/*$app->get('/blog/{id}', function (Silex\Application $app, $id) use ($blogPosts) {
    if (!isset($blogPosts[$id])) {
        $app->abort(404, "Post $id does not exist.");
    }

    $post = $blogPosts[$id];

    return  "<h1>{$post['title']}</h1>".
            "<p>{$post['body']}</p>";
});*/
$app->get('/blog/{id}', 'Controller::blogwithID');

//POST Request
$app->post('/feedback', function () {
    $message = $request->get('message');
    mail('feedback@yoursite.com', '[YourSite] Feedback', $message);

    return ('Thank you for your feedback!');
});

$app->run();

?>