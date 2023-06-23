<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Customers API",
 *     version="1.0.0",
 *     description="",
 *     @OA\Contact(
 *         email="f.paredes.o@gmail.com",
 *         name="Suporte da API"
 *     ),
 * ),
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * ),
 * @OA\Schema(
 *     schema="PagedInfo",
 *     @OA\Property(
 *         property="current_page",
 *         type="integer",
 *         description="The current page number",
 *     ),
 *    @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="The total number of pages",
 *     ),
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
