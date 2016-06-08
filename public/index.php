<?php

  // Define application environment
  defined('APPLICATION_DIR') || define('APPLICATION_DIR', realpath(dirname(__DIR__)));

  require_once APPLICATION_DIR . '/vendor/autoload.php';

  $container = new \Slim\Container;
  $container['view'] = function($c) {
    $view_path = APPLICATION_DIR . '/assets/views';
    $view = new \Slim\Views\Twig($view_path);
    return $view;
  };

  $app = new \Slim\App($container);

  $app->get('/', function($request, $response, $args) {
    return $this->view->render($response, 'main.phtml');
  });

  $app->get('/pagesource/{url}', function($request, $response, $args) {
    $data = array();
    $response_code = 200;
    try {
      $url = base64_decode($args['url']);
      $page = new \Jayodeji\Taggr\Page();
      $data = $page->processWebPage($url);
    } catch (\Jayodeji\Taggr\Exception\InvalidUrl $e) {
      $response_code = 400;
    } catch (\Jayodeji\Taggr\Exception\PageCannotBeLoaded $e) {
      $response_code = 404;
    } catch (\Exception $e) {
      $response_code = 500;
    } finally {
      return $response->withJson($data, $response_code);
    }
  });

  $app->run();
