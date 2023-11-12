<?php

namespace Tests\Feature;

use App\Models\Patient;
use Database\Factories\PatientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        Patient::factory()->count(3)->create();
        $response = $this->get('/api/patients');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Patients found']);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'document_photo',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
    public function testStore(): void
    {
        $response = $this->post('/api/patient', PatientFactory::new()->make()->toArray());
        $response->assertStatus(201);
        $response->assertJson(['message' => 'Patient created']);
        $response->assertJsonStructure($this->responseStructure());
    }
    public function testShow(): void
    {
        $patient = Patient::factory()->create();
        $response = $this->get('/api/patient/' . $patient->id);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Patient found']);
        $response->assertJsonStructure($this->responseStructure());
    }
    public function testUpdate(): void
    {
        $patient = Patient::factory()->create();
        $response = $this->post('/api/patient/' . $patient->id, PatientFactory::new()->make()->toArray());
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Patient updated']);
        $response->assertJsonStructure($this->responseStructure(false));
    }
    public function testDestroy(): void
    {
        $patient = Patient::factory()->create();
        $response = $this->delete('/api/patient/' . $patient->id);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Patient deleted']);
        $response->assertJsonStructure($this->responseStructure(false));
    }
    public function testValidateRequest(): void
    {
        $response = $this->post('/api/patient', []);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'The name field is required.']);
        $response->assertJsonStructure($this->responseStructure(false));
    }
    private function responseStructure($data = true)
    {
        return $data ? [
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'document_photo',
                'created_at',
                'updated_at',
            ],
        ] : [
            'success',
            'message',
        ];
    }
}
