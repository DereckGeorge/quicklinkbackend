<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="QuickLink API",
 *     version="1.0.0",
 *     description="API for QuickLink healthcare platform",
 *     @OA\Contact(
 *         email="support@quicklink.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="QuickLink API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}