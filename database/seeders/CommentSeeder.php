<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->all();
        $postIds = Post::pluck('id')->all();

        if (empty($userIds) || empty($postIds)) {
            return;
        }

        $comments = [
            "A great look into backstory!",
            "A lot of prep required before it is usable as an advent calendar.",
            "Absolutely adored this book. Can't wait to read the next one.",
            "Another great plus is that the hardcover's design looks amazing on my shelf!",
            "Cosy and warm. Does the job.",
            "Definitely a book I would recommend to fans of the series.",
            "Does what it says on the package.",
            "Easy to use. Helpful.",
            "Expensive for the quality.",
            "Good base for a Xmas Advent Calendar - needs quite a bit of sanding before painting",
            "Good quality.",
            "Great idea but a really rough finish.",
            "Great quality and will last many years.",
            "I bought this for the dog just to keep him a bit warmer in his bed and it's perfect for that",
            "I couldn't put this down!",
            "I had no idea what to expect from this when I started reading but was instantly hooked.",
            "I loved it and cannot wait for the second novel.",
            "I thought this was pricey for what it was but it's exactly what I was looking for.",
            "If you're a fan of the series you'll love this.",
            "Incredible.",
            "It was purchased as a gift for my Grandson who was delighted with the book.",
            "It's very soft and comfortable, it's just a little thin, I am happy with my blanket for my couch.",
            "I've just spent the last two days reading it and it was so worth the purchase!",
            "Keeps you warm.",
            "Love this new addition to my living room. It's so soft and warm.",
            "Loved it !! great pacing and storyline.",
            "Lovely and soft.",
            "Must-read/have.",
            "One of my favourite books!",
            "Package arrived early and was properly wrapped.",
            "Really soft really large & easy to wash & dries quickly.",
            "So good!",
            "Thank you so much for this item what a great way to personalise a gift.",
            "The book has perfectly evolved with the original audience target (young teens) who are still a fan.",
            "The box itself makes a good base and its quality is good enough once you've spent the time to finish it.",
            "The storytelling is excellent in this and the way the author describes bending is sublime.",
            "They look beautiful. The bigger boxes are quite big!",
            "This is a great Christmas idea.",
            "Very good service from this supplier.",
            "Very large & cosy. Just what I wanted.",
            "What I also love is how the air nomads are still there and thriving!",
            "Will 100% buy again. Thank you.",
        ];

        foreach ($comments as $comment) {
            Comment::create([
                'user_id' => $userIds[array_rand($userIds)],
                'post_id' => $postIds[array_rand($postIds)],
                'comment' => $comment,
            ]);
        }
    }
}
