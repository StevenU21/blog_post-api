<?php

namespace App\Console\Commands;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts whose publication date has arrived.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $updatedCount = Post::where('status', 'scheduled')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', Carbon::now()) 
                ->update(['status' => 'published']);

            if ($updatedCount > 0) {
                $this->info("$updatedCount scheduled posts published successfully.");
            } else {
                $this->info("No scheduled posts were ready for publishing.");
            }
        } catch (\Exception $e) {
            $this->error("Error publishing scheduled posts: " . $e->getMessage());
        }
    }
}
