<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiTest extends TestCase
{
    /**
     * Test user registration.
     *
     * @return array
     */
    public function testUserRegistration()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'EMPLOYEE',
            'status' => 'ACTIVE',
            'phone_number' => '1234567890'
        ];

        $response = $this->post('/v1/register', $userData);
        
        $response->assertResponseStatus(201);
        $response->seeJsonStructure([
            'message',
            'user' => [
                'name',
                'email',
                'role',
                'status',
                'created_at',
                'updated_at',
                'id'
            ]
        ]);

        return json_decode($this->response->getContent(), true);
    }

    /**
     * Test user login.
     * 
     * @depends testUserRegistration
     * @return array
     */
    public function testUserLogin($userData)
    {
        $loginData = [
            'email' => $userData['user']['email'],
            'password' => 'password'
        ];

        $response = $this->post('/v1/login', $loginData);
        
        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);

        return array_merge($userData, json_decode($this->response->getContent(), true));
    }

    /**
     * Test getting user details.
     * 
     * @depends testUserLogin
     * @return void
     */
    public function testGetUserDetails($userData)
    {
        $token = $userData['access_token'];
        
        $response = $this->get('/v1/me', [
            'Authorization' => "Bearer {$token}"
        ]);
        
        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            'id',
            'name',
            'email',
            'role',
            'status',
            'created_at',
            'updated_at'
        ]);
    }

    /**
     * Test attendance check-in.
     * 
     * @depends testUserLogin
     * @return array
     */
    public function testCheckIn($userData)
    {
        $token = $userData['access_token'];
        
        $response = $this->post('/v1/attendance/check-in', [], [
            'Authorization' => "Bearer {$token}"
        ]);
        
        $response->assertResponseStatus(201);
        $response->seeJsonStructure([
            'message',
            'attendance' => [
                'id',
                'user_id',
                'check_in',
                'status',
                'created_at',
                'updated_at'
            ]
        ]);

        return array_merge($userData, [
            'attendance_id' => json_decode($this->response->getContent(), true)['attendance']['id']
        ]);
    }

    /**
     * Test attendance check-out.
     * 
     * @depends testCheckIn
     * @return void
     */
    public function testCheckOut($userData)
    {
        $token = $userData['access_token'];
        
        $response = $this->post('/v1/attendance/check-out', [], [
            'Authorization' => "Bearer {$token}"
        ]);
        
        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            'message',
            'attendance' => [
                'id',
                'user_id',
                'check_in',
                'check_out',
                'total_hours',
                'status',
                'created_at',
                'updated_at'
            ]
        ]);
    }
}
