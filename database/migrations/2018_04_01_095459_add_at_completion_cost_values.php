<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAtCompletionCostValues extends Migration
{
    public function up()
    {
        Schema::table('periods', function ($table) {
            $table->float('at_completion_likely', 12, 2)->nullable();
            $table->float('at_completion_optimistic', 12, 2)->nullable();
            $table->float('at_completion_pessimistic', 12, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('periods', function ($table) {
            $table->dropColumn('at_completion_likely');
            $table->dropColumn('at_completion_optimistic');
            $table->dropColumn('at_completion_pessimistic');
        });
    }
}
