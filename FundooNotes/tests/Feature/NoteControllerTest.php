<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{

    /**
     * Successful Create Note Test
     * Using Credentials Required and
     * using the authorization token
     * 
     * @test
     */
    public function test_Successfull_createNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjEwMTE1LCJleHAiOjE2NTY2MTM3MTUsIm5iZiI6MTY1NjYxMDExNSwianRpIjoiUWRkSWdLb0tiU0NWVHE1TCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.3wE6_UZjD-viBDeGXjnboPdnOuqvS1lRjYJTvJ4R27s'
        ])->json('POST', '/api/createNote',
        [
            "title" => "Keyboard-1",
            "description" => "working too good",
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Note successfully created']);
    }

    /**
     * UnSuccessful Create Note Test
     * Using Credentials Required and
     * using the authorization token
     * Wrong Credentials is used for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_createNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
        ])->json('POST', '/api/createnote',
        [
            "title" => "Income Tax",
            "description" => "Return filed",
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Invalid Authorization Token']);
    }

    /**
     * Successful readnote Test
     * Pass authorization token for a user
     * 
     * @test
     */
    public function test_Successfull_readNote()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
         ])->json('GET', '/api/readnote');

         $response->assertStatus(200)->assertJson(['message' => 'Notes Found Successfully']);
     }
     
     /**
     * Successful readnote Test
     * Pass authorization token for a user
     * 
     * @test
     */
     public function test_Unsuccessfull_readNote()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
         ])->json('GET', '/api/readnote');

         $response->assertStatus(401)->assertJson(['message' => 'Invalid Authorization Token']);
     }

     /**
     * Successful Update Note By ID Test
     * Update a note using id and authorization token
     * 
     * @test
     */
    public function test_Successfull_updateNoteById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
        ])->json('POST', '/api/updateNoteById',
        [
            "note_id" => 1,
            "title" => "title update",
            "description" => "description update",
        ]);
        $response->assertStatus(200)->assertJson(['message' => 'Note Successfully updated']);
    }

    /**
     * UnSuccessful Update Note By ID Test
     * Update a note using id and authorization token
     * Passing wrong note or noteId which is not for this user, for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_updateNoteById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNTQ4MzM4NiwiZXhwIjoxNjM1NDg2OTg2LCJuYmYiOjE2MzU0ODMzODYsImp0aSI6IlJ6VUpsWWdtQ2VUdmFYUUUiLCJzdWIiOjEwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.CjJ80kSAVmbT8rPHBfkxmgH94PmfEdMnSU63KsnrEb4'
        ])->json('POST', '/api/updateNoteById',
        [
            "note_id" => "8",
            "title" => "titleupdate",
            "description" => "description test one update",
        ]);
        $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
    }

    /**
     * Successful Delete Note By ID Test
     * Delete note by using id and authorization token
     * 
     * @test
     */
    public function test_Successfull_deleteNoteById()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
         ])->json('POST', '/api/deleteNoteById',
         [
             "note_id" => 2,
         ]);
         $response->assertStatus(201)->assertJson(['message' => 'Note Successfully deleted']);
     }

     /**
     * UnSuccessful Delete Note By ID Test
     * Delete note by using id and authorization token
     * Passing wrong note or noteId which is not for this user, for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_deleteNoteById()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
         ])->json('POST', '/api/deleteNoteById',
         [
             "note_id" => "15",
         ]);
         $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
     }
    
     /**
     * Successful Add NoteLabel Test
     * Add NoteLabel using the label_id, note_id and authorization token
     * 
     * @test
     */
     public function test_Successfull_addNoteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
        ])->json('POST', '/api/addNoteLabel',
        [
            "note_id" => 7,
            "label_id"=>7,
        ]);
        $response->assertStatus(200)->assertJson(['message' => 'Label and note added Successfully ']);
    }

    /**
     * UnSuccessful Add NoteLabel Test
     * Add NoteLabel using the label_id, note_id and authorization token
     * Using wrong label_id or note_id which is not of this user,
     * for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_addNoteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
        ])->json('POST', '/api/addNoteLabel',
        [
            "note_id" => 7,
            "label_id"=>7,
        ]);
        $response->assertStatus(409)->assertJson(['message' => 'Note Already Having This Label']);
    }

    /**
     * Successful Pin Note by ID Test
     * Pin Note by ID Using note_id and authorization token
     * 
     * @test
     */

    public function test_Successfull_PinNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjA1MDM0LCJleHAiOjE2NTY2MDg2MzQsIm5iZiI6MTY1NjYwNTAzNCwianRpIjoiS3NtVGxtNXBYUXpleWlvaCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.cRZvozpSSz9iBZA2UWuJPbWZHVesOy0qhCEkG4MDNBg'
        ])->json(
            'POST',
            '/api/pinNoteById',
            [
                "id" => "7"
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note pinned successfully']);
    }

    /**
     * UnSuccessful Pin Note by ID Test
     * Pin Note by ID Using note_id and authorization token
     * Using Wrong Credentials for UnSuccessful Test
     * 
     * @test
     */

    public function test_Unsuccessfull_PinNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/pinNoteById',
            [
                "id" => "14"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
    }

    /**
     * for Successfull archived of note
     * to given note Id
     * @test
     */
    public function test_Successfull_ArchivedNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/archiveNoteById',
            [
                "id" => "7"
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note Archived Sucessfully']);
    }

    /**
     * for UnSuccessfull archived of note
     * to given note Id
     * @test
     */
    public function test_Unsuccessfull_ArchivedNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/archiveNoteById',
            [
                "id" => "15"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
    }

    /**
     * for Successfull Colour of note
     * to given note Id
     * @test
     */
    public function test_Successfull_ColourNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/colourNoteById',
            [
                "id" => "7",
                "colour" => "Green",
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note coloured Sucessfully']);
    }
    /**
     * @test
     * for UnSuccessfull Colour of note
     * to given note Id
     */
    public function test_Unsuccessfull_Coloured_Note()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjExNDQxLCJleHAiOjE2NTY2MTUwNDEsIm5iZiI6MTY1NjYxMTQ0MSwianRpIjoibThZVkU5RjFRYWx2N3g5eiIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.AQnpOSR-NrtBlFBJD2oIdXZjav-41-iDvlyyIzsJgGA'
        ])->json(
            'POST',
            '/api/colourNoteById',
            [
                "id" => "15",
                "colour" => "blue",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
    }

    /**
     * for Successfull Search of note
     * to given anything
     * @test
     */
    public function test_Successfull_SearchNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/searchNotes',
            [
                "search" => "S"
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Found Notes Successfully']);
    }

     /**
     * @test
     * for UnSuccessfull Search of note
     * to given anything
     */
    public function test_Unsuccessfull_SearchNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjExOTE2LCJleHAiOjE2NTY2MTU1MTYsIm5iZiI6MTY1NjYxMTkxNiwianRpIjoiMEZwdTBrSXRpckpoTkFacyIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.j0_G9j4RqYXxE4daHl5qGgyMiNbl0MAoN_MWoDZq4Cg'
        ])->json(
            'POST',
            '/api/searchNotes',
            [
                "search" => "xyz"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
    }
}