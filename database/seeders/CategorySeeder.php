<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Curated list of 10 meaningful editorial & engineering categories.
     *
     * @return array<int, array{name: string, description: string, status: bool}>
     */
    public static function catalog(): array
    {
        return [
            [
                'name' => 'Technology',
                'description' => 'Frontier compute architectures, systems languages, silicon accelerators, and modern web platform engineering.',
                'status' => true,
            ],
            [
                'name' => 'Cloud & Infrastructure',
                'description' => 'Multi-region Kubernetes topologies, edge routing, distributed storage engines, and practical cloud FinOps.',
                'status' => true,
            ],
            [
                'name' => 'AI & Machine Learning',
                'description' => 'Production LLM serving, quantized inference, retrieval-augmented generation, and model evaluation frameworks.',
                'status' => true,
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Zero Trust identity architectures, hardware passkeys, software supply-chain signing, and runtime kernel defense.',
                'status' => true,
            ],
            [
                'name' => 'Software Architecture',
                'description' => 'Modular monoliths, domain-driven design, event sourcing, API versioning, and high-throughput backend patterns.',
                'status' => true,
            ],
            [
                'name' => 'Business & Strategy',
                'description' => 'SaaS unit economics, enterprise pricing mechanics, platform ecosystems, and engineering executive leadership.',
                'status' => true,
            ],
            [
                'name' => 'Design & UX Systems',
                'description' => 'Accessible color tokens, optical typography scales, adaptive dark-mode contrast, and tactile interface motion.',
                'status' => true,
            ],
            [
                'name' => 'Data & Analytics',
                'description' => 'Columnar warehouses, change-data-capture streams, warehouse-native attribution, and query performance.',
                'status' => true,
            ],
            [
                'name' => 'DevOps & Reliability',
                'description' => 'Error-budget SLOs, OpenTelemetry distributed tracing, progressive canary rollouts, and resilience testing.',
                'status' => true,
            ],
            [
                'name' => 'Developer Productivity',
                'description' => 'Ephemeral preview environments, monorepo build caching, static analysis pipelines, and internal developer portals.',
                'status' => true,
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::catalog() as $item) {
            Category::updateOrCreate(
                ['name' => $item['name']],
                [
                    'description' => $item['description'],
                    'status' => $item['status'],
                ]
            );
        }
    }
}
