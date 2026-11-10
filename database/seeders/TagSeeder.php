<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Curated list of 25 realistic editorial & technical tags.
     *
     * @return array<int, string>
     */
    public static function catalog(): array
    {
        return [
            'Laravel 13',
            'Kubernetes',
            'Zero Trust',
            'LLM Inference',
            'PostgreSQL',
            'Design Systems',
            'TypeScript',
            'OpenTelemetry',
            'Microservices',
            'FinOps',
            'Clean Architecture',
            'GraphQL',
            'WebAssembly',
            'Edge Computing',
            'DevSecOps',
            'Event Sourcing',
            'Docker',
            'CI/CD',
            'React',
            'SaaS Growth',
            'ClickHouse',
            'API Gateway',
            'Performance',
            'Dark Mode',
            'RAG Pipelines',
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::catalog() as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }
    }
}
