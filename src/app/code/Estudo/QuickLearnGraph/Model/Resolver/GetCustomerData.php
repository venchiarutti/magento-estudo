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

class GetCustomerData implements ResolverInterface
{
    private CustomerRepositoryInterface $customerRepository;
    private CustomerDataMapper $customerDataMapper;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerDataMapper
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerDataMapper = $customerDataMapper;
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

        return $this->customerDataMapper->map($customer);
    }
}
