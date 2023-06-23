<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Customer",
 *     required={"name", "cpf", "birth_date", "gender", "address", "state", "city"},
 *     @OA\Property(
 *         property="cpf",
 *         type="string",
 *         description="CPF do cliente",
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome do cliente",
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         format="date",
 *         description="Data de nascimento do cliente",
 *     ),
 *     @OA\Property(
 *         property="gender",
 *         type="string",
 *         description="Gênero do cliente",
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Endereço do cliente",
 *     ),
 *     @OA\Property(
 *         property="state",
 *         type="string",
 *         description="Estado do cliente",
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *         description="Cidade do cliente",
 *     ),
 * )
 */

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf',
        'name',
        'birth_date',
        'gender',
        'address',
        'state',
        'city',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];
}
