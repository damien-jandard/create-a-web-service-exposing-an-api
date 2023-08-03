<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'brand' => 'Apple',
                'model' => 'iPhone 12',
                'color' => 'Black',
                'memory' => '128GB',
                'os' => 'iOS',
                'description' => 'A powerful and sleek smartphone from Apple.',
                'price' => 799.99
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy S21',
                'color' => 'Phantom Gray',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'The latest flagship smartphone from Samsung.',
                'price' => 899.99
            ],
            [
                'brand' => 'Xiaomi',
                'model' => 'Redmi Note 10 Pro',
                'color' => 'Glacier Blue',
                'memory' => '128GB',
                'os' => 'Android',
                'description' => 'A feature-packed smartphone from Xiaomi.',
                'price' => 329.99
            ],
            [
                'brand' => 'OnePlus',
                'model' => 'OnePlus 9 Pro',
                'color' => 'Stellar Black',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'Experience the ultimate flagship OnePlus device.',
                'price' => 1049.99
            ],
            [
                'brand' => 'Google',
                'model' => 'Pixel 6',
                'color' => 'Kinda Coral',
                'memory' => '128GB',
                'os' => 'Android',
                'description' => 'Capture stunning photos with Google\'s Pixel 6.',
                'price' => 749.00
            ],
            [
                'brand' => 'Sony',
                'model' => 'Xperia 5 III',
                'color' => 'Frosted Black',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'A high-end Xperia smartphone from Sony.',
                'price' => 1099.00
            ],
            [
                'brand' => 'LG',
                'model' => 'Velvet 5G',
                'color' => 'Aurora White',
                'memory' => '128GB',
                'os' => 'Android',
                'description' => 'Sleek design and powerful performance in LG\'s Velvet 5G.',
                'price' => 549.99
            ],
            [
                'brand' => 'Nokia',
                'model' => '8.3 5G',
                'color' => 'Polar Night',
                'memory' => '64GB',
                'os' => 'Android',
                'description' => 'Experience the speed of 5G with Nokia 8.3.',
                'price' => 399.00
            ],
            [
                'brand' => 'Motorola',
                'model' => 'Moto G Power (2021)',
                'color' => 'Flash Gray',
                'memory' => '64GB',
                'os' => 'Android',
                'description' => 'Long-lasting battery and performance in Moto G Power.',
                'price' => 249.99
            ],
            [
                'brand' => 'Huawei',
                'model' => 'P40 Pro',
                'color' => 'Deep Sea Blue',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'Capture stunning images with Huawei\'s P40 Pro.',
                'price' => 799.00
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy S20 FE',
                'color' => 'Cloud Lavender',
                'memory' => '128GB',
                'os' => 'Android',
                'description' => 'The Fan Edition of Samsung\'s Galaxy S20 series.',
                'price' => 499.99
            ],
            [
                'brand' => 'Apple',
                'model' => 'iPhone 13',
                'color' => 'Midnight',
                'memory' => '256GB',
                'os' => 'iOS',
                'description' => 'The latest iPhone with advanced features.',
                'price' => 1099.00
            ],
            [
                'brand' => 'Google',
                'model' => 'Pixel 5a',
                'color' => 'Sorta Sage',
                'memory' => '128GB',
                'os' => 'Android',
                'description' => 'A budget-friendly Pixel phone with great camera.',
                'price' => 449.00
            ],
            [
                'brand' => 'OnePlus',
                'model' => 'OnePlus Nord CE',
                'color' => 'Charcoal Ink',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'A powerful and affordable OnePlus smartphone.',
                'price' => 399.00
            ],
            [
                'brand' => 'Xiaomi',
                'model' => 'Mi 11 Lite',
                'color' => 'Bob Green',
                'memory' => '128GB',
                'os' => 'Android',
                'description' => 'A lightweight and stylish Mi phone from Xiaomi.',
                'price' => 329.00
            ],
            [
                'brand' => 'Sony',
                'model' => 'Xperia 1 II',
                'color' => 'Mirror Lake Green',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'Professional-grade photography with Xperia 1 II.',
                'price' => 999.99
            ],
            [
                'brand' => 'Motorola',
                'model' => 'Moto G Stylus',
                'color' => 'Mystic Indigo',
                'memory' => '128GB',
                'os' => 'Android',
                'description' => 'Capture, edit, and multitask with Moto G Stylus.',
                'price' => 349.99
            ],
            [
                'brand' => 'Nokia',
                'model' => '6.2',
                'color' => 'Cyan Green',
                'memory' => '64GB',
                'os' => 'Android',
                'description' => 'A durable and reliable Nokia smartphone.',
                'price' => 249.00
            ],
            [
                'brand' => 'LG',
                'model' => 'Wing',
                'color' => 'Aurora Gray',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'A unique dual-screen design in LG\'s Wing.',
                'price' => 899.99
            ],
            [
                'brand' => 'Huawei',
                'model' => 'Mate 40 Pro',
                'color' => 'Mystic Silver',
                'memory' => '256GB',
                'os' => 'Android',
                'description' => 'Powerful performance and advanced camera technology in Mate 40 Pro.',
                'price' => 999.00
            ]
        ];

        foreach ($products as $p) {
            $product = new Product();
            $product->setBrand($p['brand'])
                ->setModel($p['model'])
                ->setColor($p['color'])
                ->setMemory($p['memory'])
                ->setOs($p['os'])
                ->setDescription($p['description'])
                ->setPrice($p['price']);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
