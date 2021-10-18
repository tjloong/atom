<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateAtomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->makePasswordNullable();
        $this->createTeamsTables();
        $this->createRolesTables();
        $this->createAbilitiesTables();
        $this->createFilesTable();
        $this->createRootUser();
    }

    /**
     * Make password nullable
     */
    public function makePasswordNullable($up = true)
    {
        if ($up) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('password')->nullable()->change();
            });
        }
        else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('password')->change();
            });    
        }
    }

    /**
     * Create roles table
     * 
     * @return void
     */
    public function createRolesTables($up = true)
    {
        if ($up) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->string('access')->nullable();
                $table->boolean('is_system')->nullable();
                $table->timestamps();
            });
    
            Schema::table('users', function(Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('remember_token');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            });
    
            DB::table('roles')->insert([
                [
                    'name' => 'Root',
                    'slug' => 'root',
                    'access' => 'root',
                    'is_system' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Administrator', 
                    'slug' => 'administrator',
                    'access' => 'global',
                    'is_system' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Restricted User', 
                    'slug' => 'restricted-user',
                    'access' => 'restrict',
                    'is_system' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);
        }
        else {
            Schema::table('users', function(Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
    
            Schema::dropIfExists('roles');    
        }
    }

    /**
     * Create abilities tables
     * 
     * @return void
     */
    public function createAbilitiesTables($up = true)
    {
        if ($up) {
            Schema::create('abilities', function (Blueprint $table) {
                $table->id();
                $table->string('module')->nullable();
                $table->string('name')->nullable();
                $table->timestamps();
            });
    
            Schema::create('abilities_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ability_id')->nullable();
                $table->unsignedBigInteger('role_id')->nullable();
    
                $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
    
            Schema::create('abilities_users', function (Blueprint $table) {
                $table->id();
                $table->string('access')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('ability_id')->nullable();
    
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
            });

            $abilities = [
                ['module' => 'user', 'name' => 'manage'],
                ['module' => 'role', 'name' => 'manage'],
                ['module' => 'team', 'name' => 'manage'],
            ];

            foreach ($abilities as $ability) {
                DB::table('abilities')->insert(
                    array_merge($ability, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()])
                );
            }
        }
        else {
            Schema::dropIfExists('abilities_users');
            Schema::dropIfExists('abilities_roles');
            Schema::dropIfExists('abilities');
        }
    }

    /**
     * Create teams tables
     * 
     * @return void
     */
    public function createTeamsTables($up = true)
    {
        if ($up) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
    
            Schema::create('teams_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
    
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });    
        }
        else {
            Schema::dropIfExists('teams_users');
            Schema::dropIfExists('teams');
        }
    }

    /**
     * Create files table
     * 
     * @return void
     */
    public function createFilesTable($up = true)
    {
        if ($up) {
            Schema::create('files', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('mime')->nullable();
                $table->decimal('size', 20, 2)->nullable();
                $table->text('url')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }
        else {
            Schema::dropIfExists('files');
        }
    }

    /**
     * Create root user login
     * 
     * @return void
     */
    public function createRootUser($up = true)
    {
        if ($up) {
            DB::table('users')->insert([
                'name' => 'Root',
                'email' => 'root@jiannius.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        else {
            DB::table('users')->where('email', 'root@jiannius.com')->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->createRootUser(false);
        $this->createTeamsTables(false);
        $this->createAbilitiesTables(false);
        $this->createRolesTables(false);
        $this->makePasswordNullable(false);
    }
}