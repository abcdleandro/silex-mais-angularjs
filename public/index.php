<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$ap['debug'] = true;

$app['db'] = function()
{
    return new PDO('mysql:host=localhost;dbname=silex', 'root', 'root');
};

// Index da aplicação
$app->get('/', function() use($app){
    $res = $app['db']->query('select * from pessoas');
    $res = $res->fetchAll(PDO::FETCH_ASSOC);
    return new Response(file_get_contents('../pessoas/templates/template.html'), 200);

    //return $app->json($res, 200);
});

// pessoas
$app->get( '/pessoas', function() use($app){
    $res = $app['db']->query('select * from pessoas');
    $res = $res->fetchAll(PDO::FETCH_ASSOC);

    return $app->json($res, 200);
});

// adicionando pessoas
$app->post( '/pessoas', function( Request $request ) use($app){
    $data = $request->getContent();
    parse_str($data, $out);

    $res = $app['db']->prepare("insert into pessoas(nome, cidade) value(:nome, :cidade)");
    $res->bindParam('nome', $out['nome']);
    $res->bindParam('cidade', $out['cidade']);

    $res->execute();

    return $app->json(array('success'=>true));
});

// seleciona pessoa para editar
$app->get('/pessoas/{id}', function($id) use ($app){
    $res = $app['db']->prepare("select * from pessoas where id = :id ");
    $res->bindParam('id', $id);

    $res->execute();
    $result = $res->fetch(PDO::FETCH_ASSOC);

    return $app->json($result);
});

// editar pessoa
$app->put('/pessoas/{id}', function( Request $request, $id ) use ($app){
    $data = $request->getContent();
    parse_str($data, $out);

    $res = $app['db']->prepare("update pessoas set nome=:nome, cidade=:cidade where id = :id ");
    $res->bindParam('nome', $out['nome']);
    $res->bindParam('cidade', $out['cidade']);
    $res->bindParam('id', $id);

    $res->execute();

    return $app->json( array('success'=>true) );
});

// Excluir pessoa
$app->delete( '/pessoas/{id}', function($id) use($app){
    $res = $app['db']->prepare("delete from pessoas where id = :id ");
    $res->bindParam('id', $id);
    $res->execute();

    return $app->json( array('success'=>true) );
});


$app->run();