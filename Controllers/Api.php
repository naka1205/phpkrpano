<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Vtour;
class Api
{

    public static function table(Context $ctx, $next){
        $result = ( yield Vtour::data() );

        $ctx->status = 200;
        $ctx->body = json_encode( $result );
    }    

    public static function save(Context $ctx, $next){
        $result = ( yield Vtour::update($ctx->request->post) );

        $ctx->status = 200;
        $ctx->body = json_encode( $result );
    }  

    public static function add(Context $ctx, $next){
        $result = ( yield Vtour::add($ctx->request->post) );

        $ctx->status = 200;
        $ctx->body = json_encode( $result );
    }  

    public static function upload(Context $ctx, $next){
        $files = ( yield Vtour::upload($ctx->request->files,$ctx->request->post) );

        $result['status'] = "error";
        if ( !empty($files) ) {
            $result['status'] = "success";
        }
        $ctx->status = 200;
        $ctx->body = json_encode($result);
    } 

}