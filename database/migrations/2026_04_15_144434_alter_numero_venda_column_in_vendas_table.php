<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->string('numero_venda', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->string('numero_venda', 20)->change();
        });
    }
};