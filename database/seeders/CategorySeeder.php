<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            "3D Printers & Supplies", "Accessories", "Action Figures", "Adhesives, Sealants & Tapes", "Advertising",
            "Agriculture & Forestry", "Animals", "Animation Art & Characters", "Antiquarian & Collectible",
            "Arcade, Jukeboxes & Pinball", "Audiobooks", "Autographs", "Automation, Motors & Drives",
            "Automotive Tools & Supplies", "Baby & Toddler Clothing", "Backyard Poultry Supplies",
            "Banks, Registers & Vending", "Bath", "Beads", "Beanbag Plush", "Bedding", "Bird Supplies",
            "Boats", "Bottles & Insulators", "Boxing, Martial Arts & MMA", "Building Materials & Supplies",
            "Building Toys", "Cars & Trucks", "Casino", "Cat Supplies",
            "Catalogs", "Children & Young Adults", "Classic Toys", "Cleaning & Janitorial Supplies", "Clocks",
            "Clothing & Shoe Care", "CNC, Metalworking & Manufacturing", "Collectible Card Games", "Comics",
            "Computer Cables & Connectors", "Computer Components & Parts", "Construction", "Cookbooks",
            "Costumes, Reenactment, Theater", "Credit, Charge Cards", "Cultures & Ethnicities", "Cycling",
            "Decorative Collectibles", "Desktops & All-In-Ones", "Toy Vehicles",
            "Dog Supplies", "Drives, Storage & Blank Media", "Educational", "Electrical Equipment & Supplies",
            "Electronic, Battery & Wind-Up", "Enterprise Networking, Servers", "Facility Maintenance & Safety",
            "Fantasy, Mythical & Magic", "Fast Food & Cereal Premiums", "Fasteners & Hardware", "Fiction & Literature",
            "Fish & Aquariums", "Fishing", "Fitness, Running & Yoga", "Food & Beverages", "Fuel & Energy", "Furniture",
            "Gadgets & Other Electronics", "Games", "Golf", "Greeting Cards & Party Supply", "Healthcare & Dental",
            "Heavy Equipment, Parts & Attachments", "Historical Memorabilia", "Holiday & Seasonal",
            "Holiday & Seasonal Decor", "Home Automation", "Home Decor", "Home Improvement",
            "Home Networking & Connectivity", "Home Telephones & Accessories", "Horse Supplies",
            "Household Supplies & Cleaning", "Hunting", "HVAC", "Hydraulics, Pneumatics, Pumps & Plumbing",
            "Indoor Games", "Keyboards, Mice & Pointers", "Kids & Teens at Home", "Kids' Clothing, Shoes & Accs",
            "Kitchen & Home", "Kitchen, Dining & Bar", "Knives, Swords & Blades", "Lamps, Lighting",
            "Lamps, Lighting & Ceiling Fans", "Laptop & Desktop Accessories", "Laptops & Netbooks",
            "Light Equipment & Tools", "Linens & Textiles (1930-Now)", "Magazine Back Issues", "Major Appliances",
            "Manuals & Resources", "Marbles", "Material Handling", "Men's Accessories", "Men's Clothing",
            "Men's Shoes", "Model Railroads & Trains", "Models & Kits",
            "Monitors & Projectors", "Motorcycles", "Multipurpose Batteries & Power", "Non-Sport Trading Cards",
            "Nonfiction", "Office", "Other Books", "Other Business & Industrial",
            "Other Clothing, Shoes & Accessories", "Other Computers & Networking", "Other Consumer Electronics",
            "Other Home & Garden", "Other Pet Supplies", "Other Sporting Goods", "Other Vehicles & Trailers",
            "Outdoor Sports", "Outdoor Toys & Structures", "Paper", "Parts & Accessories",
            "Pens & Writing Instruments", "Pet Memorials & Urns", "Pez, Keychains, Promo Glasses", "Phone Cards",
            "Photographic Images", "Lunchboxes", "Portable Audio & Headphones", "Postcards",
            "Power Protection, Distribution", "Preschool Toys & Pretend Play", "Printers, Scanners & Supplies",
            "Printing & Graphic Arts", "Puzzles", "Radio Communication",
            "Radio Control & Control Line", "Radio, Phonograph, TV, Phone", "Religion & Spirituality",
            "Reptile Supplies", "Restaurant & Food Service", "Retail & Services", "Robots, Monsters & Space Toys",
            "Rocks, Fossils & Minerals", "Rugs & Carpets", "Science & Medicine (1930-Now)", "Science Fiction & Horror",
            "Sewing (1930-Now)", "Slot Cars", "Small Animal Supplies", "Smart Glasses", "Software",
            "Souvenirs & Travel Memorabilia", "Stuffed Animals", "Surveillance & Smart Home Electronics",
            "Tablet & eBook Reader Accs", "Tablet & eBook Reader Parts", "Tablets & eBook Readers", "Team Sports",
            "Tennis & Racquet Sports", "Test, Measurement & Inspection", "Textbooks, Education", "Tobacciana",
            "Tools & Workshop Equipment", "Tools, Hardware & Locks", "Toy Soldiers", "Transportation",
            "TV & Movie Character Toys", "TV, Video & Home Audio", "Uniforms & Work Clothing",
            "Unisex Clothing, Shoes & Accs", "Vanity, Perfume & Shaving", "Vehicle Electronics & GPS", "Vintage",
            "Vintage & Antique Toys", "Vintage Computing", "Vintage Electronics", "Vintage, Retro, Mid-Century",
            "Virtual Reality", "Water Sports", "Websites & Businesses for Sale", "Wedding & Formal Occasion",
            "Wedding Supplies", "Wholesale & Bulk Lots", "Wholesale Lots", "Wholesale, Large & Small Lots",
            "Window Treatments & Hardware", "Winter Sports", "Women's Accessories", "Women's Bags & Handbags",
            "Women's Clothing", "Women's Shoes", "World & Traditional Clothing", "Yard, Garden & Outdoor Living",
        ];

        $descriptions = [
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

        foreach ($categories as $category) {
            $description = $descriptions[array_rand($descriptions)];
            Category::factory()->create([
                'name' => $category,
                'description' => $description,
                'status' => fake()->boolean(90),
            ]);
        }
    }
}
