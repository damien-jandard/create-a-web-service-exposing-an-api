<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('api', name: 'app_product_')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'list', methods: [Request::METHOD_GET])]
    public function list(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, PaginatorInterface $paginator, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 5);
        $idCache = 'listOfProducts-' . (int) $request->get('page', 1) . '-' . (int) $request->get('limit', 5);
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
        if ($page > ceil($productList->getTotalItemCount() / 5)) {
            throw new HttpException(JsonResponse::HTTP_NOT_FOUND, 'The requested page does not exist.');
        }
        $context = SerializationContext::create()->setGroups('getProducts');
        $jsonProductList = $serializer->serialize($productList->getItems(), 'json', $context);
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('/product/{id}', name: 'show', methods: [Request::METHOD_GET])]
    public function show(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups('getProducts');
        $jsonProduct = $serializer->serialize($product, 'json', $context);
        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
