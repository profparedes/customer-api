<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\User;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Not authenticated request cannot get the customers.
     *
     * @return void
     */
    public function testNotAuthenticatedRequestCannotGetTheCustomers()
    {
        $response = $this->get('/api/customers', ['Accept' => 'application/json']);
        $response->assertStatus(401);
    }

    /**
     * Authenticated request can get the customers.
     *
     * @return void
     */
    public function testAuthenticatedUserRequestCanGetTheCustomers()
    {
        Customer::factory()->count(5)->create();

        $response = $this->get('/api/customers', [
            'Authorization' => 'Bearer ' . $this->getToken(),
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    /**
     * Authenticated user can search for a customer.
     *
     * @return void
     */
    public function testAuthenticatedUserCanSearchForACustomer()
    {
        Customer::factory()->create(['name' => 'Test User A']);
        Customer::factory()->create(['name' => 'Test User B']);
        Customer::factory()->create(['name' => 'Test User C']);

        $response = $this->get('/api/customers?name=Test User A', [
            'Authorization' => 'Bearer ' . $this->getToken(),
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    /**
     * Authenticated user can get a customer.
     *
     * @return void
     */
    public function testAuthenticatedUserCanGetACustomer()
    {
        $customer = Customer::factory()->create();

        $response = $this->get("/api/customers/{$customer->id}", [
            'Authorization' => 'Bearer ' . $this->getToken(),
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('id', $customer->id)
                 ->assertJsonPath('name', $customer->name)
                 ->assertJsonPath('cpf', $customer->cpf)
                 ->assertJsonPath('birth_date', $customer->birth_date->format('Y-m-d'))
                 ->assertJsonPath('gender', $customer->gender)
                 ->assertJsonPath('address', $customer->address)
                 ->assertJsonPath('state', $customer->state)
                 ->assertJsonPath('city', $customer->city);
    }

    /**
     * Authenticated user can store a customer.
     *
     * @return void
     */
    public function testAuthenticatedUserCanStoreACustomer()
    {
        $customerData = [
            'name' => 'Test User',
            'cpf' => '123.456.789-01',
            'birth_date' => '1980-01-01',
            'gender' => 'M',
            'address' => 'Test Address',
            'state' => 'Test State',
            'city' => 'Test City'
        ];

        $response = $this->post('/api/customers', $customerData, [
            'Authorization' => 'Bearer ' . $this->getToken(),
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('name', $customerData['name'])
                 ->assertJsonPath('cpf', $customerData['cpf'])
                 ->assertJsonPath('birth_date', $customerData['birth_date'])
                 ->assertJsonPath('gender', $customerData['gender'])
                 ->assertJsonPath('address', $customerData['address'])
                 ->assertJsonPath('state', $customerData['state'])
                 ->assertJsonPath('city', $customerData['city']);
    }

    /**
     *  Authenticated user can update a customer.
     *
     * @return void
     */
    public function tesAuthenticatedtUserCanUpdateACustomer()
    {
        $customer = Customer::factory()->create();

        $updateData = ['name' => 'Updated Name'];

        $response = $this->patch("/api/customers/{$customer->id}", $updateData, [
            'Authorization' => 'Bearer ' . $this->getToken(),
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('name', $updateData['name']);
    }

    /**
     *  Authenticated user can delete a customer
     *
     * @return void
     */
    public function testAuthenticatedUserCanDeleteACustomer()
    {
        Customer::factory()->count(5)->create();
        $customerCount = Customer::count();

        $customer = Customer::first();

        $response = $this->delete("/api/customers/{$customer->id}", [], [
            'Authorization' => 'Bearer ' . $this->getToken(),
        ]);

        $response->assertStatus(204);

        $newCustomerCount = Customer::count();

        $this->assertEquals($customerCount - 1, $newCustomerCount);
     }


    /**
     *  Generate an authenticated user JWT token
     *
     * @return string
     */
     private function getToken()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return $response->json()['token'];
    }
}
