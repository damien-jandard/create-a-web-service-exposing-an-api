<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api', name: 'app_customer_')]
class CustomerController extends AbstractController
{
    #[Route('/customers', name: 'list', methods: [Request::METHOD_GET])]
    public function getAllCustomers(CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customerList = $customerRepository->findBy(['owner' => $this->getUser()]);
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomersList = $serializer->serialize($customerList, 'json', $context);
        return new JsonResponse($jsonCustomersList, Response::HTTP_OK, [], true);
    }

    #[Route('/customer/{id}', name: 'show', methods: [Request::METHOD_GET])]
    public function showCustomer(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('CUSTOMER_BELONGS_TO_ME', $customer, 'Access denied, you do not have the necessary permissions to view this record.');
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }

    #[Route('/customer', name: 'add', methods: [Request::METHOD_POST])]
    public function addCustomer(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setOwner($this->getUser())
            ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($customer);
        $em->flush();
        $context = SerializationContext::create()->setGroups('getCustomers');
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }

    #[Route('/customer/{id}', name: 'delete', methods: [Request::METHOD_DELETE])]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('CUSTOMER_BELONGS_TO_ME', $customer, 'Access denied, you do not have the necessary permissions to delete this record.');
        $em->remove($customer);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
