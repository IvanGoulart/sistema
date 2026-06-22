<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToServicesTable extends Migration
{
  public function up()
  {
    Schema::table('services', function (Blueprint $table) {
      $table->decimal('price', 10, 2)->nullable()->after('description');
    });
  }

  public function down()
  {
    Schema::table('services', function (Blueprint $table) {
      $table->dropColumn('price');
    });
  }
}
