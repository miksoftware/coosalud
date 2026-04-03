<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulta_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained()->cascadeOnDelete();
            $table->string('cedula');
            $table->string('tipo_documento')->nullable();
            $table->string('primer_nombre')->nullable();
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('departamento')->nullable();
            $table->string('municipio')->nullable();
            $table->string('direccion')->nullable();
            $table->string('regimen')->nullable();
            $table->string('poblacion_especial')->nullable();
            $table->string('grupo_etnico')->nullable();
            $table->string('paciente_riesgo')->nullable();
            $table->string('otros_riesgos')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefono_fijo')->nullable();
            $table->string('correo')->nullable();
            $table->string('estado_afiliado')->nullable();
            $table->string('sede')->nullable();
            $table->string('ips')->nullable();
            $table->string('status')->default('pending'); // pending, success, error
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_results');
    }
};
