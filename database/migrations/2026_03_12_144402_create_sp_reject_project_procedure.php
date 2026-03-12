<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reject_project');

        DB::unprepared("
        CREATE PROCEDURE sp_reject_project(
            IN pid INT,
            IN uid INT,
            IN reason TEXT
        )
        BEGIN

        UPDATE projects
        SET status = 'rejected',
            updated_at = NOW()
        WHERE id = pid;

        INSERT INTO approvals(project_id, admin_id, status, reason, created_at, updated_at)
        VALUES (pid, uid, 'rejected', reason, NOW(), NOW());

        INSERT INTO audit_logs(user_id, project_id, action, created_at, updated_at)
        VALUES (uid, pid, 'rejected', NOW(), NOW());

        END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reject_project');
    }
};
