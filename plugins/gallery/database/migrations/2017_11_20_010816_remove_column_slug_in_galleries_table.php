<?php

use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Slug\Services\SlugService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnSlugInGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $slugService = new SlugService(app(SlugInterface::class));
        foreach (DB::table('galleries')->select(['slug', 'id'])->whereNull('deleted_at')->get() as $gallery) {
            app(SlugInterface::class)->firstOrCreate([
                'key' => $slugService->create($gallery->slug),
                'reference' => 'gallery',
                'reference_id' => $gallery->id,
            ]);
        }

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('slug', 120);
        });
    }
}
