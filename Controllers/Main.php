<?php
namespace Controllers;
use Naka507\Koa\Context;
class Main
{
    public static function index(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(TEMPLATE_PATH . "/index.html");
    }

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(TEMPLATE_PATH . "/add.html");
    }

}