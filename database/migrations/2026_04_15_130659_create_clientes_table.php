<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email', 100)->nullable();
            $table->string('telefone', 20);
            $table->string('celular', 20)->nullable();
            $table->string('cpf_cnpj', 18)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->text('endereco')->nullable();
            $table->string('cidade', 50)->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->text('observacoes')->nullable();
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};