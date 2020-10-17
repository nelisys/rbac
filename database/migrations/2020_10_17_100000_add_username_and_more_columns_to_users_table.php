<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernameAndMoreColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')
                ->nullable()
                ->change();

            $table->dropUnique('users_email_unique');

            $table->string('username')
                    ->after('password')
                    ->unique();

            $table->boolean('is_active')
                    ->after('username')
                    ->default(true);

            $table->json('details')
                    ->after('is_active')
                    ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(
                'username',
                'is_active',
                'details',
            );
        });
    }
}
