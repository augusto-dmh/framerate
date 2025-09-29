<?php

namespace Database\Seeders;

use App\Models\Post;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use App\Models\Topic;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TopicSeeder::class);
        $topics = Topic::all();

        $users = User::factory(10)->create();

        $posts = Post::factory(200)
            ->withFixture()
            ->has(Comment::factory(20)->recycle($users))
            ->recycle([$users, $topics])
            ->create();

        User::factory()
            ->has(Post::factory(50)->recycle($topics)->withFixture())
            ->has(Comment::factory(100)->recycle($posts))
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'secret',
            ]);
    }
}
