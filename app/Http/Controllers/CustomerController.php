<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="PagedCustomers",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Customer"),
 *         description="A lista de cientes",
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         ref="#/components/schemas/PagedInfo",
 *         description="Paginação",
 *     ),
 * )
 */

class CustomerController extends Controller
{
    public const VALIDATION_ERROR_MESSAGES = [
        'cpf.unique' => 'Este CPF já está cadastrado',
        'cpf.size' => 'O campo de CPF esta incompleto'
    ];

    /**
     * @OA\Get(
     *     path="/customers",
     *     summary="Obter lista de clientes",
     *     tags={"Customers"},
     *     description="Retorna uma lista paginada de clientes. A lista pode ser filtrada por nome.",
     *     operationId="indexCustomer",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filtrar por nome",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Define o número de resultados por página",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             default=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida",
     *         @OA\JsonContent(ref="#/components/schemas/PagedCustomers")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Solicitação inválida"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $name = $request->query('name');
        $per_page = $request->query('per_page', 10);

        $query = Customer::query();

        if ($name) {
            $query->where('name', 'like', "%$name%");
        }

        $customers = $query->paginate($per_page);

        return CustomerResource::collection($customers);
    }

    /**
     * @OA\Get(
     *     path="/customers/{id}",
     *     summary="Mostra os detalhes de um cliente específico",
     *     tags={"Customers"},
     *     description="Retorna os detalhes de um cliente específico pelo ID",
     *     operationId="showCustomer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do cliente a ser visualizado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function show(Customer $customer)
    {
        return response()->json(new CustomerResource($customer));
    }

    /**
     * @OA\Post(
     *     path="/customers",
     *     summary="Cria um novo cliente",
     *     tags={"Customers"},
     *     description="Cria um novo cliente e retorna o cliente criado",
     *     operationId="storeCustomer",
     *     @OA\RequestBody(
     *         description="Dados do novo cliente",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitação inválida",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cpf' => 'required|unique:customers|size:14',
            'name' => 'required|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:M,F',
            'address' => 'required|max:255',
            'state' => 'required|max:100',
            'city' => 'required|max:100',
        ], self::VALIDATION_ERROR_MESSAGES);

        $customer = Customer::create($data);

        return response()->json(new CustomerResource($customer), 201);
    }

    /**
     * @OA\Put(
     *     path="/customers/{id}",
     *     summary="Atualiza um cliente existente",
     *     tags={"Customers"},
     *     description="Atualiza um cliente existente e retorna o cliente atualizado",
     *     operationId="updateCustomer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do cliente a ser atualizado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Dados do cliente a serem atualizados",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitação inválida",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *     )
     * )
     */
    public function update(Request $request, Customer $customer)
    {

        $data = $request->validate([
            'cpf' => 'sometimes', Rule::unique('customers')->ignore($customer->id),
            'name' => 'sometimes|max:255',
            'birth_date' => 'sometimes|date',
            'gender' => 'sometimes|in:M,F',
            'address' => 'sometimes|max:255',
            'state' => 'sometimes|max:100',
            'city' => 'sometimes|max:100',
        ], self::VALIDATION_ERROR_MESSAGES);

        $customer->fill($data);
        $customer->save();

        return response()->json(new CustomerResource($customer));
    }

    /**
     * @OA\Delete(
     *     path="/customers/{id}",
     *     summary="Exclui um cliente existente",
     *     tags={"Customers"},
     *     description="Exclui um cliente existente pelo ID",
     *     operationId="destroyCustomer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do cliente a ser excluído",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cliente excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitação inválida",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(null, 204);
    }
}
