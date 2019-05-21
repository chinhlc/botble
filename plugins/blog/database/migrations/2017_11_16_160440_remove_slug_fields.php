<?php

use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Slug\Services\SlugService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSlugFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $slugService = new SlugService(app(SlugInterface::class));
        foreach (DB::table('posts')->select(['slug', 'id'])->whereNull('deleted_at')->get() as $post) {
            app(SlugInterface::class)->firstOrCreate([
                'key' => $slugService->create($post->slug),
                'reference' => 'post',
                'reference_id' => $post->id,
            ]);
        }

        foreach (DB::table('categories')->select(['slug', 'id'])->whereNull('deleted_at')->get() as $category) {
            app(SlugInterface::class)->firstOrCreate([
                'key' => $slugService->create($category->slug),
                'reference' => 'category',
                'reference_id' => $category->id,
            ]);
        }

        foreach (DB::table('tags')->select(['slug', 'id'])->get() as $tag) {
            app(SlugInterface::class)->firstOrCreate([
                'key' => $slugService->create($tag->slug),
                'reference' => 'tag',
                'reference_id' => $tag->id,
            ]);
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('tags', function (Blueprint $table) {
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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug', 120);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug', 120);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('slug', 120);
        });
    }
}
