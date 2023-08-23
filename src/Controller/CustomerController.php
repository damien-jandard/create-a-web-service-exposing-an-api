<?php

namespace App\Controller;

use App\Entity\Customer;
use OpenApi\Attributes as OA;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api', name: 'app_customer_')]
#[OA\Tag(name: 'Customers')]
class CustomerController extends AbstractController
{
    /**
     * Display the list of clients.
     */
    #[Route(
        '/customers',
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
        description: "Customers list",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: Customer::class,
                    groups: ['getCustomers']
                )
            )
        ),
    )]
    public function getAllCustomers(
        CustomerRepository $customerRepository,
        SerializerInterface $serializer,
        Request $request,
        PaginatorInterface $paginator,
        TagAwareCacheInterface $tagAwareCache
    ): JsonResponse {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 5);
        $idCache = 'listOfCustomers-' . $page . '-' . $limit;
        $customerList = $tagAwareCache->get(
            $idCache,
            function (ItemInterface $item) use ($paginator, $customerRepository, $page, $limit) {
                $item->tag('customersCache');
                return $paginator->paginate(
                    $customerRepository->findAllWithPagination($this->getUser()),
                    $page,
                    $limit
                );
            }
        );
        if ($page > ceil($customerList->getTotalItemCount() / $limit)) {
            throw new HttpException(
                JsonResponse::HTTP_NOT_FOUND,
                'The requested page does not exist.'
            );
        }
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomersList = $serializer->serialize($customerList->getItems(), 'json', $context);
        return new JsonResponse(
            $jsonCustomersList,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Display the details of a customer.
     */
    #[Route(
        '/customers/{id}',
        name: 'show',
        methods: [Request::METHOD_GET]
    )]
    #[IsGranted(
        'CUSTOMER_BELONGS_TO_ME',
        subject: 'customer',
        message: 'Access denied, you do not have the necessary permissions to view this record.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'The customer identifier',
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Customer details",
        content: new OA\JsonContent(
            ref: new Model(
                type: Customer::class,
                groups: ['getCustomers']
            )
        ),
    )]
    public function showCustomer(
        Customer $customer,
        SerializerInterface $serializer
    ): JsonResponse {
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse(
            $jsonCustomer,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Add a customer.
     */
    #[Route(
        '/customers',
        name: 'add',
        methods: [Request::METHOD_POST]
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            ref: new Model(
                type: Customer::class,
                groups: ['addCustomer']
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Customer details",
        content: new OA\JsonContent(
            ref: new Model(
                type: Customer::class,
                groups: ['getCustomers']
            )
        ),
    )]
    public function addCustomer(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        TagAwareCacheInterface $tagAwareCache
    ): JsonResponse {
        $tagAwareCache->invalidateTags(['customersCache']);
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $errors = $validator->validate($customer);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST,
                [],
                true
            );
        }
        $customer->setOwner($this->getUser())
            ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($customer);
        $em->flush();
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse(
            $jsonCustomer,
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    /**
     * Update a customer.
     */
    #[Route(
        '/customers/{id}',
        name: 'update',
        methods: [Request::METHOD_PUT]
    )]
    #[IsGranted(
        'CUSTOMER_BELONGS_TO_ME',
        subject: 'customer',
        message: 'Access denied, you do not have the necessary permissions to update this record.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'The customer identifier',
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            ref: new Model(
                type: Customer::class,
                groups: ['updateCustomer']
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: "Customer details",
        content: new OA\JsonContent(
            ref: new Model(
                type: Customer::class,
                groups: ['getCustomers']
            )
        ),
    )]
    public function updateCustomer(
        Customer $customer,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        TagAwareCacheInterface $tagAwareCache
    ): JsonResponse {
        $tagAwareCache->invalidateTags(['customersCache']);
        $newCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $errors = $validator->validate($newCustomer);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST,
                [],
                true
            );
        }
        $customer->setEmail($newCustomer->getEmail())
            ->setFirstName($newCustomer->getFirstName())
            ->setLastName($newCustomer->getLastName())
            ->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($customer);
        $em->flush();
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse(
            $jsonCustomer,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Delete a customer.
     */
    #[Route(
        '/customers/{id}',
        name: 'delete',
        methods: [Request::METHOD_DELETE]
    )]
    #[IsGranted(
        'CUSTOMER_BELONGS_TO_ME',
        subject: 'customer',
        message: 'Access denied, you do not have the necessary permissions to delete this record.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'The customer identifier',
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: "Customer deleted"
    )]
    public function deleteCustomer(
        Customer $customer,
        EntityManagerInterface $em,
        TagAwareCacheInterface $tagAwareCache
    ): JsonResponse {
        $tagAwareCache->invalidateTags(['customersCache']);
        $em->remove($customer);
        $em->flush();
        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
