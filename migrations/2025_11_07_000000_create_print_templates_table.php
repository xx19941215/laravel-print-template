<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('org_id');
            $table->nullableMorphs('assoc'); // 关联资源
            $table->string('code')->comment('模板编码');
            $table->string('name')->comment('模板名称');
            $table->json('config')->nullable()->comment('模板配置');
            $table->integer('creator_id')->comment('创建人');
            $table->integer('modifier_id')->comment('修改人');
            $table->timestamp('modified_at')->nullable()->comment('修改时间');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_templates');
    }
};