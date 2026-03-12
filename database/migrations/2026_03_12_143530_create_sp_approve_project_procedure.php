<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_approve_project');

        DB::unprepared("
        CREATE PROCEDURE sp_approve_project(IN pid INT, IN uid INT)
        BEGIN

        UPDATE projects
        SET status = 'approved'
        WHERE id = pid;

        INSERT INTO approvals(project_id, admin_id, status, created_at, updated_at)
        VALUES (pid, uid, 'approved', NOW(), NOW());

        INSERT INTO audit_logs(user_id, project_id, action, created_at, updated_at)
        VALUES (uid, pid, 'approved', NOW(), NOW());

        END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_approve_project');
    }
};
