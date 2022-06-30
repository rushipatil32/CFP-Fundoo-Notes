<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelControllerTest extends TestCase
{
    /**
     * Successful Create Label Test
     * Create a label using label_name and authorization token for a user
     * 
     * @test
     */

    public function test_Successfull_createLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA1ODYyMCwiZXhwIjoxNjQyMDYyMjIwLCJuYmYiOjE2NDIwNTg2MjAsImp0aSI6IlpoYXhSWm1vWHVoS09wWXMiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.CRDiDMD18ffF01PM4mlNQrVHaJ1QLyhlxy-J-OdLoBU'
        ])->json(
            'POST',
            '/api/createLabel',
            [
                "labelname" => "Label",
            ]
        );

        $response->assertStatus(200)->assertJson(['message' => 'Label successfully created']);
    }

    /**
     * UnSuccessful Create Label Test
     * Create a label using label_name and authorization token for a user
     * Using existing label name for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_createLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
        ])->json('POST', '/api/createlabel',
            [
                "labelname" => "new label",
            ]
        );

        $response->assertStatus(401)->assertJson(['message' => 'Invalid Authorization Token']);
    }


    public function test_Successfull_readLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
        ])->json('GET', '/api/readLabel');

        $response->assertStatus(200)->assertJson(['message' => 'Label Retrived Successfully']);
    }

    public function test_Unsuccessfull_readLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer J0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
        ])->json('GET', '/api/readLabel');

        $response->assertStatus(401)->assertJson(['message' => 'Invalid Authorization Token']);
    }
    /**
     * Successful Update Label Test
     * Update label using label_id, label_name and authorization
     * 
     * @test
     */
    public function test_Successfull_updateLabelById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
        ])->json('POST', '/api/updateLabelById',
        [
            "label_id" => 6,
            "labelname" => "Update",
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Label Successfully Updated']);
    }

     /**
     * UnSuccessful Update Label Test
     * Update label using label_id, label_name and authorization
     * Using existing label name for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_updateLabelById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
        ])->json('POST', '/api/updateLabelById',
        [
            "id" => 20,
            "labelname" => "Label update",
        ]);

        $response->assertStatus(404)->assertJson(['message' => 'Label Not Found']);
    }

    /**
     * Successful Delete Label Test
     * Delete Label using label_id and authorization token
     * @test
     */
    public function test_Successfull_DeleteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MjA0NzY3NywiZXhwIjoxNjQyMDUxMjc3LCJuYmYiOjE2NDIwNDc2NzcsImp0aSI6IlVzRXNPbG5LZDFRYk55ZUEiLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.JBmXbrnLVPErwkeLmiF2G3JBNIh1Odyx3CHD8aTzZU0'
        ])->json('POST', '/api/deleteLabelById',
        [
            "id" => 7,
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Label Successfully Deleted']);
    }

    /**
     * UnSuccessful Delete Label Test
     * Delete Label using label_id and authorization token
     * Giving wrong label_id for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_DeleteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjEzMDU1LCJleHAiOjE2NTY2MTY2NTUsIm5iZiI6MTY1NjYxMzA1NSwianRpIjoiUDY2bUNNNWZRSzJIdFBsMCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.wPAmxwMFtHqfa-v6ZU8S1hiMXZYiAeOv4StfNyv7EVY'
        ])->json('POST', '/api/deleteLabelById',
        [
            "id" => 20,
        ]);

        $response->assertStatus(404)->assertJson(['message' => 'Label Not Found']);
    }

}
