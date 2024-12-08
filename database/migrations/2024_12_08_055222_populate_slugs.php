<?php

use App\Models\ExternalGroup;
use App\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Revolution\Bluesky\Embed\External;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Group::each(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->save();
        });

        ExternalGroup::each(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
