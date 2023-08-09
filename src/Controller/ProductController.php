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

#[Route('api', name: 'app_product_')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'list', methods: [Request::METHOD_GET])]
    public function list(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $productList = $paginator->paginate(
            $productRepository->findAllWithPagination(), 
            (int) $request->get('page', 1),
            (int) $request->get('limit', 5)
        );
        if ((int) $request->get('page', 1) > ceil($productList->getTotalItemCount() / 5)) {
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
