<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name'); 
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->string('assigned_team_members'); 
            $table->enum('task_priority', ['Low', 'Medium', 'High'])->default('Low'); 
            $table->enum('status', ['In progress', 'On hold', 'Under approval','Completed','Cancelled','Planning'])->default('Under approval');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable(); 
            $table->string('estimated_time')->nullable(); 
            $table->foreignId('dependency_task_id')->nullable()->constrained('tasks')->onDelete('cascade'); // التبعيات 
            $table->string('actual_time_logged')->nullable(); 
            $table->text('task_description')->nullable(); 
            $table->timestamps();
        });
        DB::unprepared('
        CREATE TRIGGER check_dependency_task_id BEFORE INSERT ON tasks
        FOR EACH ROW
        BEGIN
            IF NEW.dependency_task_id IS NOT NULL AND NEW.dependency_task_id = NEW.id THEN
                SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Dependency task ID cannot be the same as the task ID.";
            END IF;
        END;
    ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) { 
            $table->dropForeign(['project_id']); 
            $table->dropForeign(['dependency_task_id']); 
            $table->dropColumn('project_id'); 
            $table->dropColumn('dependency_task_id');
        });
        Schema::dropIfExists('tasks');
        DB::unprepared('DROP TRIGGER IF EXISTS check_dependency_task_id;');
    }
};
