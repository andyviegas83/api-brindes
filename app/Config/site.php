<?php

declare(strict_types=1);

return [
    'navigation' => [
        'institutional_links' => [
            ['label' => 'Quem Somos', 'slug' => 'quem-somos', 'path' => '/quem-somos'],
            ['label' => 'Catalogo', 'slug' => 'catalogo', 'path' => '/catalogo'],
            ['label' => 'Promocoes', 'slug' => 'promocoes', 'path' => '/promocoes'],
            ['label' => 'Lancamentos', 'slug' => 'lancamentos', 'path' => '/lancamentos'],
            ['label' => 'Fale Conosco', 'slug' => 'fale-conosco', 'path' => '/fale-conosco'],
            ['label' => 'FAQ', 'slug' => 'faq', 'path' => '/faq'],
        ],
    ],
    'homepage' => [
        'main_banners' => [
            [
                'id' => 'banner-01',
                'title' => 'Brindes personalizados para fortalecer sua marca',
                'image_url' => '/assets/images/mock/banner-01.webp',
                'link' => '/catalogo',
            ],
            [
                'id' => 'banner-02',
                'title' => 'Linha corporativa para eventos e campanhas',
                'image_url' => '/assets/images/mock/banner-02.webp',
                'link' => '/promocoes',
            ],
        ],
        'featured_categories' => [
            ['id' => 'cat-destaque-01', 'name' => 'Canecas', 'slug' => 'canecas', 'image_url' => '/assets/images/mock/category-canecas.webp'],
            ['id' => 'cat-destaque-02', 'name' => 'Mochilas', 'slug' => 'mochilas', 'image_url' => '/assets/images/mock/category-mochilas.webp'],
            ['id' => 'cat-destaque-03', 'name' => 'Ecobags', 'slug' => 'ecobags', 'image_url' => '/assets/images/mock/category-ecobags.webp'],
            ['id' => 'cat-destaque-04', 'name' => 'Squeezes', 'slug' => 'squeezes', 'image_url' => '/assets/images/mock/category-squeezes.webp'],
        ],
        'facilities_banner' => [
            'items' => [
                ['label' => 'Frete gratis', 'description' => 'Consulte regioes e condicoes.'],
                ['label' => 'Parcelamento', 'description' => 'Flexibilidade para pedidos corporativos.'],
                ['label' => 'Condicoes especiais', 'description' => 'Atendimento comercial para grandes volumes.'],
            ],
        ],
        'blog_banner' => [
            'title' => 'Ideias, tendencias e campanhas para sua marca',
            'link' => '/blog',
        ],
        'clients' => [
            ['name' => 'Cliente Alpha', 'logo_url' => '/assets/images/mock/client-alpha.webp'],
            ['name' => 'Cliente Beta', 'logo_url' => '/assets/images/mock/client-beta.webp'],
            ['name' => 'Cliente Gama', 'logo_url' => '/assets/images/mock/client-gama.webp'],
            ['name' => 'Cliente Delta', 'logo_url' => '/assets/images/mock/client-delta.webp'],
        ],
        'seo_content' => [
            'title' => 'Brindes personalizados',
            'body' => 'Oferecemos brindes personalizados para campanhas promocionais, eventos, equipes comerciais e acoes de relacionamento. A estrutura da API foi preparada para entregar conteudo consistente ao site, apoiar SEO tecnico e facilitar futuras integracoes com ERP/CRM.',
        ],
    ],
    'pages' => [
        'about' => [
            'title' => 'Quem Somos',
            'content' => 'Somos uma operacao especializada em brindes personalizados com foco comercial, atendimento consultivo e estrutura preparada para crescer com processos integrados.',
        ],
        'faq' => [
            'title' => 'FAQ',
            'items' => [
                ['question' => 'Qual o pedido minimo?', 'answer' => 'O pedido minimo varia conforme o tipo de produto e personalizacao.'],
                ['question' => 'Vocês atendem todo o Brasil?', 'answer' => 'Sim, com condicoes logisticas conforme a regiao e o volume.'],
            ],
        ],
        'contact' => [
            'title' => 'Fale Conosco',
            'description' => 'Use este endpoint para montar a pagina de contato com os dados institucionais e blocos comerciais.',
            'form' => [
                'enabled' => false,
                'fields' => ['name', 'email', 'phone', 'message'],
            ],
        ],
    ],
    'testimonials' => [
        ['name' => 'Mariana Costa', 'company' => 'Grupo Alpha', 'text' => 'Atendimento rapido, materiais bem apresentados e entrega alinhada ao cronograma.'],
        ['name' => 'Rodrigo Lima', 'company' => 'Beta Tech', 'text' => 'Os brindes reforcaram nossa acao comercial e chegaram com excelente acabamento.'],
        ['name' => 'Fernanda Souza', 'company' => 'Delta Eventos', 'text' => 'Processo claro e boa orientacao para escolher os itens certos para a campanha.'],
    ],
    'institutional_texts' => [
        'top_bar_message' => 'Seja bem-vindo à {company}',
        'footer_text' => 'Conteúdo institucional base. Substitua por textos reais do ERP/CRM quando o mapeamento estiver concluído.',
    ],
];
