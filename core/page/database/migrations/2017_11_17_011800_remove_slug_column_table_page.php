<?php

use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Slug\Services\SlugService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSlugColumnTablePage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $slugService = new SlugService(app(SlugInterface::class));

        foreach (DB::table('pages')->select(['slug', 'id'])->whereNull('deleted_at')->get() as $page) {
            app(SlugInterface::class)->firstOrCreate([
                'key' => $slugService->create($page->slug),
                'reference' => 'page',
                'reference_id' => $page->id,
            ]);
        }

        Schema::table('pages', function (Blueprint $table) {
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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('slug', 120);
        });
    }
}
