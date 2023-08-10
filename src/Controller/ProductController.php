<?php

namespace App\Controller;

use App\Entity\Product;
use OpenApi\Attributes as OA;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api', name: 'app_product_')]
#[OA\Tag(name: 'Products')]
class ProductController extends AbstractController
{
    /**
     * Display the list of products.
     */
    #[Route(
        '/products',
        name: 'list',
        methods: [Request::METHOD_GET]
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        required: false,
        description: 'The page we want to retrieve',
        schema: new OA\Schema(
            type: 'integer',
            default: 1
        )
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        required: false,
        description: 'The number of items we want to retrieve',
        schema: new OA\Schema(
            type: 'integer',
            default: 5
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Products list",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: Product::class,
                    groups: ['getProducts']
                )
            )
        ),
    )]
    public function getAllProducts(
        ProductRepository $productRepository,
        SerializerInterface $serializer,
        Request $request,
        PaginatorInterface $paginator,
        TagAwareCacheInterface $tagAwareCache
    ): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 5);
        $idCache = 'listOfProducts-' . $page . '-' . $limit;
        $productList = $tagAwareCache->get(
            $idCache,
            function (ItemInterface $item) use ($paginator, $productRepository, $page, $limit) 
            {
                $item->tag('productsCache')
                    ->expiresAfter(3600);
                return $paginator->paginate(
                    $productRepository->findAllWithPagination(), 
                    $page,
                    $limit
                );
            }
        );
        if ($page > ceil($productList->getTotalItemCount() / $limit)) {
            throw new HttpException(
                JsonResponse::HTTP_NOT_FOUND,
                'The requested page does not exist.'
            );
        }
        $context = SerializationContext::create()->setGroups('getProducts');
        $jsonProductList = $serializer->serialize($productList->getItems(), 'json', $context);
        return new JsonResponse(
            $jsonProductList,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Display the details of a product.
     */
    #[Route('/product/{id}', name: 'show', methods: [Request::METHOD_GET])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'The product identifier',
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Product details",
        content: new OA\JsonContent(
            ref: new Model(
                type: Product::class,
                groups: ['getProducts']
            )
        ),
    )]
    public function showProduct(
        Product $product,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $context = SerializationContext::create()->setGroups('getProducts');
        $jsonProduct = $serializer->serialize($product, 'json', $context);
        return new JsonResponse(
            $jsonProduct,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
