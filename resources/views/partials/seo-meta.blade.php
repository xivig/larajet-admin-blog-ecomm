{{-- Professional SEO Suite: Meta, Open Graph (FB, X, LinkedIn, WhatsApp), and Automatic JSON-LD Schema --}}
{!! \App\Services\SeoService::renderAll($seoModel ?? $post ?? $page ?? null) !!}
