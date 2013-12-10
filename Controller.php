<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
//Controllers for Router

	class Controller{
		function __construct(){
		}

		function __destruct(){

		}
		public function blog(Request $request, Application $app){
			$blogPosts = array(
			    1 => array(
			        'date'      => '2011-03-29',
			        'author'    => 'igorw',
			        'title'     => 'Using Silex',
			        'body'      => '...',
			    ),
			);
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
		}
		public function blogwithID(Request $request, Application $app, $id){
			$blogPosts = array(
			    1 => array(
			        'date'      => '2011-03-29',
			        'author'    => 'igorw',
			        'title'     => 'Using Silex',
			        'body'      => '...',
			    ),
			);
			if (!isset($blogPosts[$id])) {
			    $app->abort(404, "Post $id does not exist.");
			}

			$post = $blogPosts[$id];

			return  "<h1>{$post['title']}</h1>".
			        "<p>{$post['body']}</p>";
		}

	}
?>
