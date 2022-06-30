<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Successfull Registration
     * This test is to check user Registered Successfully or not
     * by using firstname, lastname, email and password as credentials
     * 
     * @test
     */
    public function test_successfull_register()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/register', [
                "firstname" => "Deven",
                "lastname" => "Mali",
                "email" => "@gmail.com",
                "password" => "pass@123",
                "password_confirmation" => "pass@123"
            ]);
        $response->assertStatus(201)->assertJson(['message' => 'User successfully registered']);
    }

    /**
     * Test to check the user is already registered
     * by using first_name, last_name, email and password as credentials
     * The email used is a registered email for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_register()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/register', [
                "firstname" => "Rushikesh",
                "lastname" => "Patil",
                "email" => "rushipatil6632@gmail.com",
                "password" => "rushi@123",
                "password_confirmation" => "rushi@123"
            ]);
        $response->assertStatus(401)->assertJson(['message' => 'The email has already taken.']);
    }

     /**
     * Test for successful Login
     * Login the user by using the email and password as credentials
     * 
     * @test
     */
    public function test_Successfull_login()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/login',
            [
                "email" => "rushipatil6632@gmail.com",
                "password" => "pass@123"
            ]
        );
        $response->assertStatus(200)->assertJson(['success' => 'Login successful']);
    }

    /**
     * Test for Unsuccessfull Login
     * Login the user by email and password
     * Wrong password for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_login()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/login',
            [
                "email" => "rushipatil6632@gmail.com",
                "password" => "rushi@123"
            ]
        );
        $response->assertStatus(403)->assertJson(['message' => 'Wrong password']);
    }

    /**
     * Test for Successfull Logout
     * Logout a user using the token generated at login
     * 
     * @test
     */
    public function test_Successfull_logout()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/logout', [
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0OTkyNTI2MCwiZXhwIjoxNjQ5OTI4ODYwLCJuYmYiOjE2NDk5MjUyNjAsImp0aSI6InI2VklQYlJkWXRscFFjU2EiLCJzdWIiOjQsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.pd5x0pBsl9vdZaKPl2QMh_9WKRPNv2r5sFDjAgW5VTE'
            ]);

            $response->assertStatus(200)->assertJson(['message' => 'User has been logged out']);
        }
    }

    /**
     * Test for unSuccessfull Logout
     * Logout a user using the token generated at login
     * Passing the wrong token for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_logout()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/logout', [
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0OTkyNTI2MCwiZXhwIjoxNjQ5OTI4ODYwLCJuYmYiOjE2NDk5MjUyNjAsImp0aSI6InI2VklQYlJkWXRscFFjU2EiLCJzdWIiOjQsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.pd5x0pBsl9vdZaKPl2QMh_9WKRPNv2r5sFDjAgW5VTE'
            ]);

            $response->assertStatus(400)->assertJson(['message' => 'Invalid token']);
        }
    }

    

    /**
     * Test for Successfull Forgot Password
     * Send a mail for forgot password of a registered user
     * 
     * @test
     */
    public function test_Successfull_forgotPassword()
    { 
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/forgotPassword', [
                "email" => "rushipatil6632@gmail.com"
            ]);

            $response->assertStatus(200)->assertJson(['message' => 'Password Reset link is send to your email']);
        
    }

    /**
     * Test for UnSuccessfull Forgot Password
     * Send a mail for forgot password of a registered user
     * Non-Registered email for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_forgotPassword()
    { 
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/forgotPassword', [
                "email" => "rushi@gmail.com"
            ]);

            $response->assertStatus(404)->assertJson(['message' => 'Not a Registered Email']);
        
    }

     /**
     * Test for Successfull Reset Password
     * Reset password using the token and 
     * setting the new password to be the password
     * 
     * @test
     */
    public function test_Successfull_resetPassword()
    { 
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/resetPassword', [
                "new_password" => "rushi@123",
                "password_confirmation" => "rushi@123",
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2ZvcmdvdFBhc3N3b3JkIiwiaWF0IjoxNjU2NDc1OTg5LCJleHAiOjE2NTY0Nzk1ODksIm5iZiI6MTY1NjQ3NTk4OSwianRpIjoib2R0N0dibnVGQ2hveGNaRiIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.W_H54IfcFDc7CrhEoUa5HwABEEuTFyAm-Pj62GXTx5A'
            ]);

            $response->assertStatus(200)->assertJson(['message' => 'Password Reset Successfull']);
    
    }

    /**
     * Test for unSuccessfull Reset Password
     * Reset password using the token and 
     * setting the new password to be the password
     * Wrong token is passed for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_resetPassword()
    { 
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/resetPassword', [
                "new_password" => "rushi@123",
                "password_confirmation" => "rushi@123",
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.OldsbsahbdshbdbvbwvXC9sb2NhbGhvc3Rc3dvcmQiLCJpYXQiOjE2NDk4MjgzMDgsImV4cCI6MTY0OTgzMTkwOCwibmJmIjoxNjQ5ODI4MzA4LCJqdGkiOiI1SURSMzdCY2lIR2VNak41Iiwic3ViIjozLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.d93SpbOT1aL1qAOH9qjWfP2OdqfeB27vMV2fkx1hShA'
            ]);

            $response->assertStatus(401)->assertJson(['message' => 'Invalid Authorization Token']);
    }
}