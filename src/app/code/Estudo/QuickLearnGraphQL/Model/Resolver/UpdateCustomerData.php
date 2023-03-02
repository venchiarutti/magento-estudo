<?php

namespace Estudo\QuickLearnGraphQL\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Estudo\QuickLearnGraphQL\Model\Mapper\CustomerDataMapper;
use Estudo\QuickLearnGraphQL\Model\Service\UpdateCustomerDataService;

class UpdateCustomerData implements ResolverInterface
{
    private CustomerRepositoryInterface $customerRepository;
    private CustomerDataMapper $customerDataMapper;
    private UpdateCustomerDataService $updateCustomerDataService;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerDataMapper,
        UpdateCustomerDataService $updateCustomerDataService
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerDataMapper = $customerDataMapper;
        $this->updateCustomerDataService = $updateCustomerDataService;
    }
    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return mixed|Value
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $customerId = $context->getUserId();

        if (!$customerId) {
            throw new GraphQlAuthenticationException(__("o usuario nao esta logado"));
        }

        $customer = $this->customerRepository->getById($customerId);

        $customer = $this->updateCustomerDataService->updateCustomerData($customer, $args["input"]);

        return $this->customerDataMapper->map($customer);
    }
}
