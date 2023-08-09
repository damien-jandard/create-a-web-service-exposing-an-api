<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('api', name: 'app_customer_')]
class CustomerController extends AbstractController
{
    #[Route('/customers', name: 'list', methods: [Request::METHOD_GET])]
    public function getAllCustomers(CustomerRepository $customerRepository, SerializerInterface $serializer, Request $request, PaginatorInterface $paginator, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 5);
        $idCache = 'listOfCustomers-' . (int) $request->get('page', 1) . '-' . (int) $request->get('limit', 5);
        $customerList = $tagAwareCache->get(
            $idCache,
            function (ItemInterface $item) use ($paginator, $customerRepository, $page, $limit) 
            {
                $item->tag('customersCache');
                return $paginator->paginate(
                    $customerRepository->findAllWithPagination($this->getUser()), 
                    $page,
                    $limit
                );
            }
        );
        if ($page > ceil($customerList->getTotalItemCount() / 5)) {
            throw new HttpException(JsonResponse::HTTP_NOT_FOUND, 'The requested page does not exist.');
        }
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomersList = $serializer->serialize($customerList->getItems(), 'json', $context);
        return new JsonResponse($jsonCustomersList, Response::HTTP_OK, [], true);
    }

    #[Route('/customer/{id}', name: 'show', methods: [Request::METHOD_GET])]
    #[IsGranted('CUSTOMER_BELONGS_TO_ME', 'customer', 'Access denied, you do not have the necessary permissions to view this record.')]
    public function showCustomer(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }

    #[Route('/customer', name: 'add', methods: [Request::METHOD_POST])]
    public function addCustomer(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $tagAwareCache->invalidateTags(['customersCache']);
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $errors = $validator->validate($customer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $customer->setOwner($this->getUser())
            ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($customer);
        $em->flush();
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }

    #[Route('/customer/{id}', name: 'update', methods: [Request::METHOD_PUT])]
    #[IsGranted('CUSTOMER_BELONGS_TO_ME', 'customer', 'Access denied, you do not have the necessary permissions to update this record.')]
    public function updateCustomer(Customer $customer, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $tagAwareCache->invalidateTags(['customersCache']);
        $newCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $errors = $validator->validate($newCustomer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $customer->setEmail($newCustomer->getEmail())
            ->setFirstName($newCustomer->getFirstName())
            ->setLastName($newCustomer->getLastName())
            ->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($customer);
        $em->flush();
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }

    #[Route('/customer/{id}', name: 'delete', methods: [Request::METHOD_DELETE])]
    #[IsGranted('CUSTOMER_BELONGS_TO_ME', 'customer', 'Access denied, you do not have the necessary permissions to delete this record.')]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $tagAwareCache->invalidateTags(['customersCache']);
        $em->remove($customer);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
