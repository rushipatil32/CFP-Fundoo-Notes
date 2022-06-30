<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollaboratorControllerTest extends TestCase
{
    /** 
     * for successfull add Collaborator
     * to given noteid
     * @test
     */
    public function test_Successfull_AddCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjE5Nzc0LCJleHAiOjE2NTY2MjMzNzQsIm5iZiI6MTY1NjYxOTc3NCwianRpIjoib214MHJEd3VpcXBmS1JOSSIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.m88X5EKNJh-XYCqqFSQ_uNz-v0kLjJPSqNEzUh34cms'
        ])->json(
            'POST',
            '/api/addCollaboratorByNoteId',
            [
                "note_id" => "9",
                "email" => "rushpatil6632@gmail.com",
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Collaborator Created Sucessfully']);
    }

    /**
     * for Unsuccessfull add Collaborator
     * to given noteid
     * @test
     */
    public function test_Unsuccessfull_AddCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjIwMjk3LCJleHAiOjE2NTY2MjM4OTcsIm5iZiI6MTY1NjYyMDI5NywianRpIjoiYjg1akJIUWlJcWI4N2ttbCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.BhF_EDURmDT4BSoaLKlfki1Gr7JnV1L8n83h_Rq4Bis'
        ])->json(
            'POST',
            '/api/addCollaboratorByNoteId',
            [
                "note_id" => "9",
                "email" => "rushpatil6632@gmail.com",
            ]
        );
        $response->assertStatus(403)->assertJson(['message' => 'Collaborator Already created for this note and email']);
    }

      /**
     * @test 
     * for successfull Update Note 
     * By Collaborator
     * to given noteid
     */
    public function test_Successfull_UpdateCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjIwMjk3LCJleHAiOjE2NTY2MjM4OTcsIm5iZiI6MTY1NjYyMDI5NywianRpIjoiYjg1akJIUWlJcWI4N2ttbCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.BhF_EDURmDT4BSoaLKlfki1Gr7JnV1L8n83h_Rq4Bis'
        ])->json(
            'POST',
            '/api/updateCollaboratorById',
            [
                "note_id" => "9",
                "updated_title" => "IPl",
                "updated_description" => "Mumbai Indians",
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note updated Successfully']);
    }

    /**
     * for Unsuccessfull Update Note 
     * By Collaborator
     * to given noteid
     * @test 
     */
    public function test_Unsuccessfull_UpdateCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjIwOTU5LCJleHAiOjE2NTY2MjQ1NTksIm5iZiI6MTY1NjYyMDk1OSwianRpIjoidnVCdWtuV3pQdXZLUTYxQyIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EdJw8UCf0rmTuwpM5DuKUftLRGzrxH8bfBA3VSqU-e4'
        ])->json(
            'POST',
            '/api/updateCollaboratorById',
            [
                "note_id" => "15",
                "title" => "update title",
                "description" => "update desc",
            ]
        );
        $response->assertStatus(400)->assertJson(['message' => 'Invalid Note id']);
    }

    /** 
     * for successfull Remove Collaborator
     * to given noteid
     * @test
     */
    public function test_Successfull_DeleteCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjIxMzYxLCJleHAiOjE2NTY2MjQ5NjEsIm5iZiI6MTY1NjYyMTM2MSwianRpIjoiQlVYTlVDOHYzeUVDYVllZCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.3yg0keQljyS9VW4UrsHULbFEWAJH0p5Ddt7rqE42HVE'
        ])->json(
            'POST',
            '/api/deleteCollaboratorById',
            [
                "note_id" => "9",
                "email" => "rushipatil6632@gmail.com",
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'collaborator deleted successfully']);
    }

    /**
     * @test 
     * for Unsuccessfull Remove Collaborator
     * to given noteid
     */
    public function test_Unsuccessfull_DeleteCollaborator()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjIxNzg0LCJleHAiOjE2NTY2MjUzODQsIm5iZiI6MTY1NjYyMTc4NCwianRpIjoiSjdXQlg1aTR0MXM4dW1NMSIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.ySmtu_Tsuyylmk3I4NUgGptb9amSKRhO8XBguHZyVQg'
        ])->json(
            'POST',
            '/api/deleteCollaboratorById',
            [
                "note_id" => "2",
                "email" => "rushipatil6632@gmail.com",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Collaborater Not created']);
    }

}
